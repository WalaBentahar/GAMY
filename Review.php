<?php

class Review {
    private $id;
    private $productId;
    private $userId;
    private $rating; // 1-5
    private $comment;
    private $isVerifiedGamer = false; // Vérifié comme vrai joueur
    private $platformUsed; // Plateforme sur laquelle le produit est utilisé
    
    public function approve(): void {
        // Modération des avis
    }
}