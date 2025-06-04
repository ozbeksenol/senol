<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'App\\Repository\\ProductRepository')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column]
    private string $name;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $price;

    #[ORM\Column(type: 'integer')]
    private int $category;

    #[ORM\Column(type: 'integer')]
    private int $stock;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getCategory(): int
    {
        return $this->category;
    }

    public function setCategory(int $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;
        return $this;
    }
}
