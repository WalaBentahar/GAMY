<?php
session_start();

// Check if user is logged in and authorized
if (!isset($_SESSION['user_id'])) {
    header("Location: ../front/login.php");
    exit();
}

require_once '../config.php';

try {
    $pdo = Config::getConnexion();

    // Check if id is provided
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        header("Location: ../view/backoffice/commande_back.php?error=" . urlencode("ID de commande invalide."));
        exit();
    }

    $commande_id = intval($_GET['id']);

    // Delete the command
    $stmt = $pdo->prepare("DELETE FROM commande WHERE id_c = :id_c");
    $stmt->bindParam(':id_c', $commande_id, PDO::PARAM_INT);
    $stmt->execute();

    // Check if any rows were affected
    if ($stmt->rowCount() > 0) {
        header("Location: ../view/backoffice/commande_back.php?success=" . urlencode("Commande supprimée avec succès."));
    } else {
        header("Location: ../view/backoffice/commande_back.php?error=" . urlencode("Aucune commande trouvée avec cet ID."));
    }
    exit();

} catch (PDOException $e) {
    // Log the error for debugging
    error_log("DeleteCommande Error: " . $e->getMessage(), 3, __DIR__ . '/error.log');
    header("Location: ../view/backoffice/commande_back.php?error=" . urlencode("Erreur lors de la suppression: " . $e->getMessage()));
    exit();
}
?>