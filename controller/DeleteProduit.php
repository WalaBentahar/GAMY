<?php
require_once  '../config.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id']; 

    $pdo = Config::getConnexion();
    $sql = "SELECT * FROM produits WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $produit = $stmt->fetch();

    if ($produit) {
        // Suppression du produit
        $sqlDelete = "DELETE FROM produits WHERE id = :id";
        $stmtDelete = $pdo->prepare($sqlDelete);
        $stmtDelete->execute(['id' => $id]);

        // Redirection vers la liste des produits après suppression
        header("Location: ../view/backoffice/produit.php");
        exit();
    } else {
        echo "Produit non trouvé.";
    }
} else {
    echo "ID non spécifié.";
}
?>
