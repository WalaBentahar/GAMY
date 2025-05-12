<?php

class Cart {
    private $items = [];
    private $userId;
    private $createdAt;
    
    public function addItem(Product $product, int $quantity = 1): void {
        $this->items[$product->getId()] = [
            'product' => $product,
            'quantity' => $quantity
        ];
    }
    
    public function getTotal(): float {
        return array_reduce($this->items, fn($carry, $item) => 
            $carry + ($item['product']->getPrice() * $item['quantity']), 0);
    }
    
    public function applyGamingDiscount(string $promoCode): void {
        // Logique des réductions esport
    }
}