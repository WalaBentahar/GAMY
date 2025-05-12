<?php
require_once  '../../config.php';

$pdo = Config::getConnexion();
$id = $_GET['id']; // Récupérer l'ID du produit via l'URL

// Préparer la requête SQL pour obtenir les informations du produit
$sql = "SELECT * FROM produits WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$produit = $stmt->fetch();

if (!$produit) {
    echo "Produit introuvable.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Modifier Produit</title>
</head>
<body>
    <h1>Modifier le produit</h1>
    <form action="updateProduit.php" method="POST">
        <input type="hidden" name="id" value="<?= $produit['id'] ?>" />
        <label for="nom">Nom</label>
        <input type="text" name="nom" value="<?= htmlspecialchars($produit['nom']) ?>" />

        <label for="description">Description</label>
        <textarea name="description"><?= htmlspecialchars($produit['description']) ?></textarea>

        <label for="prix">Prix</label>
        <input type="number" name="prix" value="<?= $produit['prix'] ?>" />

        <label for="categorie">Catégorie</label>
        <input type="text" name="categorie" value="<?= htmlspecialchars($produit['categorie']) ?>" />

        <label for="disponibilite">Disponible</label>
        <input type="checkbox" name="disponibilite" <?= $produit['disponibilite'] ? 'checked' : '' ?> />

        <button type="submit">Mettre à jour</button>
    </form>
</body>
</html>
