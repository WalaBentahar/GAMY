<?php

class User {
    private $id;
    private $username;
    private $email;
    private $gamerTag;
    private $platformPreferences = [];
    private $wishlist = [];
    private $orderHistory = [];
    
    public function addToWishlist(Product $product): void {
        $this->wishlist[$product->getId()] = $product;
    }
    
    public function getGamerProfile(): array {
        return [
            'gamerTag' => $this->gamerTag,
            'platforms' => $this->platformPreferences
        ];
    }
}