<?php
require_once  '../../config.php';
$pdo = Config::getConnexion();

$stmt = $pdo->query("SELECT * FROM produits");
$produits = $stmt->fetchAll();
?>
<!-- ...tableau HTML comme avant... -->


<table border="1">
    <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Prix</th>
        <th>disponibilite</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($produits as $produit): ?>
    <tr>
        <td><?= $produit['id'] ?></td>
        <td><?= $produit['nom'] ?></td>
        <td><?= $produit['prix'] ?> €</td>
        <td><?= $produit['disponible'] ? '✅' : '❌' ?></td>
        <td>
            <a href="update.php?id=<?= $produit['id'] ?>">Modifier</a> |
            <a href="delete.php?id=<?= $produit['id'] ?>" onclick="return confirm('Supprimer ce produit ?')">Supprimer</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
