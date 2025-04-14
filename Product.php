<?php

class Product {
    // Identifiant et informations de base
    private $id;
    private $sku;
    private $name;
    private $slug;
    
    // Détails du produit
    private $description;
    private $price;
    private $discountedPrice;
    private $stockQuantity;
    private $isFeatured = false;
    
    // Catégorie gaming
    private $category; // "Clavier", "Souris", "Figurine", etc.
    private $gamingPlatforms = []; // PC, PS5, Xbox, Nintendo...
    private $compatibility = [];
    
    // Media
    private $mainImage;
    private $galleryImages = [];
    private $videoUrl; // Pour les démos produits
    
    // Spécifications techniques
    private $specs = [
        'brand' => '',
        'color' => '',
        'weight' => '',
        'dimensions' => ''
    ];
    
    // Attributs spécifiques gaming
    private $rgbLighting = false;
    private $isEsportsCertified = false;
    private $releaseDate;
    
    // Getters/Setters
    public function getId(): int {
        return $this->id;
    }

    public function getFormattedPrice(): string {
        return number_format($this->price, 2) . ' €';
    }

    public function hasDiscount(): bool {
        return $this->discountedPrice < $this->price;
    }

    // Méthodes métier gaming
    public function addCompatibility(string $platform): void {
        if (!in_array($platform, $this->compatibility)) {
            $this->compatibility[] = $platform;
        }
    }

    public function getPlatformBadges(): string {
        $badges = [];
        foreach ($this->gamingPlatforms as $platform) {
            $badges[] = '<span class="platform-badge ' . strtolower($platform) . '">' . $platform . '</span>';
        }
        return implode(' ', $badges);
    }

    public function isInStock(): bool {
        return $this->stockQuantity > 0;
    }

    // Pour l'API/JSON
    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'mainImage' => $this->mainImage,
            'platforms' => $this->gamingPlatforms,
            'rgb' => $this->rgbLighting
        ];
    }
}