<?php
namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Repository\CustomerRepository;
use App\Repository\ProductRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OrderController extends AbstractController
{
    #[Route('/orders', methods: ['GET'])]
    public function list(OrderRepository $repository): JsonResponse
    {
        $orders = $repository->findAll();
        $data = [];
        foreach ($orders as $order) {
            $items = [];
            foreach ($order->getItems() as $item) {
                $items[] = [
                    'product_id' => $item->getProduct()->getId(),
                    'quantity' => $item->getQuantity(),
                    'unit_price' => $item->getUnitPrice(),
                ];
            }
            $data[] = [
                'id' => $order->getId(),
                'customer_id' => $order->getCustomer()->getId(),
                'created_at' => $order->getCreatedAt()->format('c'),
                'items' => $items,
            ];
        }
        return $this->json($data);
    }

    #[Route('/orders', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, ProductRepository $productRepo, CustomerRepository $customerRepo, ValidatorInterface $validator): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        if (!isset($payload['customer_id'], $payload['items']) || !is_array($payload['items'])) {
            return $this->json(['error' => 'Invalid payload'], 400);
        }
        $customer = $customerRepo->find($payload['customer_id']);
        if (!$customer) {
            return $this->json(['error' => 'Customer not found'], 400);
        }
        $order = new Order();
        $order->setCustomer($customer);
        foreach ($payload['items'] as $itemData) {
            if (!isset($itemData['product_id'], $itemData['quantity'])) {
                return $this->json(['error' => 'Invalid item payload'], 400);
            }
            $product = $productRepo->find($itemData['product_id']);
            if (!$product) {
                return $this->json(['error' => 'Product not found'], 400);
            }
            if ($product->getStock() < $itemData['quantity']) {
                return $this->json(['error' => 'Insufficient stock for product '.$product->getId()], 400);
            }
            $product->setStock($product->getStock() - $itemData['quantity']);
            $orderItem = new OrderItem();
            $orderItem->setProduct($product)
                ->setQuantity($itemData['quantity'])
                ->setUnitPrice($product->getPrice());
            $order->addItem($orderItem);
            $em->persist($product);
        }
        $errors = $validator->validate($order);
        if (count($errors) > 0) {
            return $this->json(['error' => (string) $errors], 400);
        }
        $em->persist($order);
        $em->flush();
        return $this->json(['id' => $order->getId()], 201);
    }

    #[Route('/orders/{id}', methods: ['DELETE'])]
    public function delete(int $id, OrderRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $order = $repo->find($id);
        if (!$order) {
            return $this->json(['error' => 'Order not found'], 404);
        }
        $em->remove($order);
        $em->flush();
        return $this->json(null, 204);
    }
}
