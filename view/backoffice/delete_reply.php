<?php
require_once __DIR__ . '/../../models/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_id'])) {
    $db = Database::connect();
    $stmt = $db->prepare("DELETE FROM replies WHERE id = :id");
    $stmt->execute(['id' => $_POST['reply_id']]);
}

// Retourner à la page précédente
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit; 