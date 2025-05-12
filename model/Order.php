<?php

class Order {
    private $id;
    private $userId;
    private $items;
    private $status; // "pending", "shipped", "delivered"
    private $shippingAddress;
    private $gamingGoodiesIncluded = []; // Goodies offerts
    
    public function calculateTotal(): float {
        // Logique de calcul avec taxes
    }
    
    public function addGamingGoodie(string $goodie): void {
        $this->gamingGoodiesIncluded[] = $goodie;
    }
}