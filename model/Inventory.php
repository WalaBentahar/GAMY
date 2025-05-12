<?php

class Inventory {
    private $products = [];
    private $categories = [];
    
    public function addProduct(Product $product): void {
        $this->products[$product->getId()] = $product;
        
        // Mise à jour automatique des catégories
        if (!in_array($product->getCategory(), $this->categories)) {
            $this->categories[] = $product->getCategory();
        }
    }
    
    public function getFeaturedProducts(): array {
        return array_filter($this->products, fn($p) => $p->isFeatured());
    }
    
    public function getProductsByCategory(string $category): array {
        return array_filter($this->products, fn($p) => $p->getCategory() === $category);
    }
}