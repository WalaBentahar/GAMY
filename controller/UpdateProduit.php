<?php
require_once  '../config.php';
$pdo = Config::getConnexion();

// Récupération du produit via ID passé dans l'URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $produit = $stmt->fetch();

    if (!$produit) {
        die("Produit non trouvé.");
    }
} else {
    die("ID manquant.");
}

// Si formulaire soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $categorie = $_POST['categorie'];
    $image = $_POST['image'];
    $quantite = $_POST['quantite'];
    $disponibilite = isset($_POST['disponibilite']) ? 1 : 0;

    $sql = "UPDATE produits SET 
        nom = :nom, 
        description = :description, 
        prix = :prix, 
        categorie = :categorie, 
        disponibilite = :disponibilite,
        image = :image,
        quantite = :quantite
    WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nom' => $nom,
        ':description' => $description,
        ':prix' => $prix,
        ':categorie' => $categorie,
        ':disponibilite' => $disponibilite,
        ':image' => $image,
        ':quantite' => $quantite,
        ':id' => $id
    ]);

    header("Location: ../view/backoffice/produit.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le Produit</title>
    <link rel="stylesheet" href="styleU.css"> 
</head>
<body>
<div class="admin-container">

    <div class="main-content">
        <h2>Modifier le Produit</h2>

        <div class="product-form">
            <form action="UpdateProduit.php?id=<?= $produit['id'] ?>" method="POST">
                <input type="hidden" name="id" value="<?= $produit['id'] ?>">

                <div class="form-group">
                    <label for="nom">Nom :</label>
                    <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($produit['nom']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Description :</label>
                    <textarea name="description" id="description" required><?= htmlspecialchars($produit['description']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="prix">Prix :</label>
                    <input type="number" step="0.01" name="prix" id="prix" value="<?= htmlspecialchars($produit['prix']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="categorie">Catégorie :</label>
                    <input type="text" name="categorie" id="categorie" value="<?= htmlspecialchars($produit['categorie']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="image">Lien de l’image :</label><br>
                    <img src="<?= htmlspecialchars($produit['image']) ?>" alt="Image actuelle" style="max-width: 150px; margin-bottom: 10px;"><br>
                    <input type="text" name="image" id="image" value="<?= htmlspecialchars($produit['image']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="quantite">Quantité :</label>
                    <input type="number" name="quantite" id="quantite" value="<?= htmlspecialchars($produit['quantite']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="disponibilite">Disponible :</label>
                    <input type="checkbox" name="disponibilite" <?= $produit['disponibilite'] ? 'checked' : '' ?>>
                </div>

                <button type="submit" class="submit-btn">Mettre à jour</button>
            </form>
        </div>
    </div>
</div>

<!-- Icons (FontAwesome) -->
<script src="https://kit.fontawesome.com/yourfontawesomekit.js" crossorigin="anonymous"></script>
</body>
</html>
