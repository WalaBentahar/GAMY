<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit();
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=gamezone_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("DELETE FROM complaints WHERE id = ?");
    $stmt->execute([$_GET['id']]);

    $_SESSION['message'] = "Complaint deleted!";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
}

header("Location: dashboard.php");
exit();
?>