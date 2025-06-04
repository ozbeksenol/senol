<?php
namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DiscountController extends AbstractController
{
    #[Route('/discounts', methods: ['POST'])]
    public function calculate(Request $request, ProductRepository $productRepo): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        if (!isset($payload['items']) || !is_array($payload['items'])) {
            return $this->json(['error' => 'Invalid payload'], 400);
        }
        $items = [];
        foreach ($payload['items'] as $itemData) {
            if (!isset($itemData['product_id'], $itemData['quantity'])) {
                return $this->json(['error' => 'Invalid item payload'], 400);
            }
            $product = $productRepo->find($itemData['product_id']);
            if (!$product) {
                return $this->json(['error' => 'Product not found'], 400);
            }
            $items[] = [
                'product' => $product,
                'quantity' => $itemData['quantity'],
            ];
        }
        $total = 0.0;
        foreach ($items as $item) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }
        $discount = 0.0;
        // Rule 1: Order total >= 1000 => 10% discount
        if ($total >= 1000) {
            $discount += $total * 0.10;
        }
        // Rule 2: Category 2 buy 6 get 1 free
        foreach ($items as $item) {
            if ($item['product']->getCategory() === 2 && $item['quantity'] >= 6) {
                $discount += $item['product']->getPrice();
            }
        }
        // Rule 3: Category 1 buy >=2 => 20% off cheapest
        $category1Prices = [];
        foreach ($items as $item) {
            if ($item['product']->getCategory() === 1) {
                for ($i=0;$i<$item['quantity'];$i++) {
                    $category1Prices[] = $item['product']->getPrice();
                }
            }
        }
        if (count($category1Prices) >= 2) {
            sort($category1Prices);
            $discount += $category1Prices[0] * 0.20;
        }
        return $this->json([
            'total' => $total,
            'discount' => $discount,
            'payable' => $total - $discount,
        ]);
    }
}
