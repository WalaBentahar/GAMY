<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit();
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=gamezone_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("UPDATE complaints SET 
                          user_id = ?, 
                          subject = ?, 
                          message = ?, 
                          user_email = ? 
                          WHERE id = ?");

    $stmt->execute([
        $_POST['user_id'],
        $_POST['subject'],
        $_POST['message'],
        $_POST['user_email'],
        $_POST['id']
    ]);

    $_SESSION['message'] = "Complaint updated!";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
}

header("Location: dashboard.php");
exit();
?>