<?php
require_once  '../../config.php';

if (isset($_POST['search'])) {
    $search = trim($_POST['search']);

    try {
        $pdo = Config::getConnexion();
        $sql = "SELECT * FROM produits WHERE nom LIKE :search";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['search' => "%$search%"]);
        $results = $stmt->fetchAll();

        if (count($results) > 0) {
            foreach ($results as $row) {
                $disponibilite = $row['disponibilite'];
                echo '<div class="product-card">';
                echo '<img src="' . htmlspecialchars($row['image']) . '" class="product-image" alt="' . htmlspecialchars($row['nom']) . '" />';
                echo '<div class="product-info">';
                echo '<h3 class="product-title">' . htmlspecialchars($row['nom']) . '</h3>';
                echo '<p class="product-description">' . htmlspecialchars($row['description']) . '</p>';
                echo '<div class="product-price">' . htmlspecialchars($row['prix']) . ' €</div>';
                echo '<div class="product-category">' . htmlspecialchars($row['categorie']) . '</div>';
                echo '<div class="quantity-selector">';
                echo '<label>disponibilite :</label>';
                echo $disponibilite ? '<i class="fas fa-check-circle" style="color:green;"></i>' : '<i class="fas fa-times-circle" style="color:red;"></i>';
                echo '</div>';
                echo '<div class="quantity-selector">';
                echo '<label>Quantité :</label>';
                echo '<input type="number" value="1" min="1" max="10" />';
                echo '<a class="add-to-cart" href="http://localhost/gamy/view/front%20office/commande_front.php?image=' . urlencode($row['image']) . '&nom=' . urlencode($row['nom']) . '&prix=' . urlencode($row['prix']) . '">Add to Cart</a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<p>Aucun produit trouvé pour : <strong>' . htmlspecialchars($search) . '</strong></p>';
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>
