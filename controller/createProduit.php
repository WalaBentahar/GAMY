<?php
require_once  '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $categorie = $_POST['categorie'];
    $disponibilite = $_POST['disponibilite'];
    $image = $_POST['image'];
    $quantite = $_POST['quantite'];

    try {
        $pdo = Config::getConnexion();
$sql = "INSERT INTO produits (nom, description, prix, categorie, disponibilite, image, quantite)
        VALUES (:nom, :description, :prix, :categorie, :disponibilite, :image, :quantite)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':description' => $description,
            ':prix' => $prix,
            ':categorie' => $categorie,
            ':disponibilite' => $disponibilite,
            ':image' => $image,
            ':quantite' => $quantite
        ]);

        header("Location: ../view/backoffice/produit.php");
        exit();
    } catch (PDOException $e) {
        echo "Erreur lors de l'ajout : " . $e->getMessage();
    }
}
?>
