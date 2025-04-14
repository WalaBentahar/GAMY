<?php
session_start();

// 1. FORCE FRESH SESSION
session_regenerate_id(true);

// 2. DATABASE CONNECTION (PDO)
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=gamezone_db",
        "root",
        "",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    header("Location: login.php?error=2");
    exit();
}

// 3. PROCESS LOGIN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        // 4. HARDCODED TEST CREDENTIALS (temporary)
        if ($username === 'admin' && $password === 'admin123') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = 'admin';
            header("Location: dashboard.php");
            exit();
        }
        // END TEMPORARY CODE

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $admin['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            header("Location: login.php?error=1");
            exit();
        }
    } catch (PDOException $e) {
        header("Location: login.php?error=2");
        exit();
    }
}
?>