<?php
require_once '../../config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pdo = config::getConnexion();
    $userId = $_POST['user_id'];
    $newStatus = $_POST['new_status'];

    try {
        $sql = "UPDATE users SET status = :new_status WHERE id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':new_status', $newStatus, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: table.php");
            exit();
        } else {
            echo "Error updating status.";
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}