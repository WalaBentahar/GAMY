<?php
require_once  '../../config.php';

$pdo = Config::getConnexion();
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT * FROM produits WHERE nom LIKE :searchTerm ORDER BY id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([':searchTerm' => '%' . $searchTerm . '%']);
$produits = $stmt->fetchAll();

foreach ($produits as $produit): ?>
  <tr>
    <td><?= htmlspecialchars($produit['id']) ?></td>
    <td><?= htmlspecialchars($produit['nom']) ?></td>
    <td><?= htmlspecialchars($produit['description']) ?></td>
    <td><?= htmlspecialchars($produit['prix']) ?> €</td>
    <td><?= htmlspecialchars($produit['categorie']) ?></td>
    <td><img src="<?= htmlspecialchars($produit['image']) ?>" style="width: 80px; border-radius: 8px;"></td>
    <td>
      <?php if ($produit['disponibilite']): ?>
        <i class="fas fa-check-circle" style="color:green;" title="Disponible"></i>
      <?php else: ?>
        <i class="fas fa-times-circle" style="color:red;" title="Indisponible"></i>
      <?php endif; ?>
    </td>
    <td><?= htmlspecialchars($produit['quantite']) ?></td>
    <td>
      <a href="../../controller/UpdateProduit.php?id=<?= $produit['id'] ?>"><i class="fas fa-edit"></i></a>
      <a href="../../controller/DeleteProduit.php?id=<?= $produit['id'] ?>"><i class="fas fa-trash"></i></a>
    </td>
  </tr>
<?php endforeach; ?>
