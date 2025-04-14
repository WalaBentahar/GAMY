<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// PDO Database Connection
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
    die("Database connection failed: " . $e->getMessage());
}

// Fetch complaints using PDO
try {
    $stmt = $pdo->query("SELECT * FROM complaints ORDER BY id DESC");
    $complaints = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching complaints: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></h2>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?= $_SESSION['message'] ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Subject</th>
                    <th>Email</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($complaints as $complaint): ?>
                    <tr>
                        <td><?= $complaint['id'] ?></td>
                        <td><?= htmlspecialchars($complaint['user_id']) ?></td>
                        <td><?= htmlspecialchars($complaint['subject']) ?></td>
                        <td><?= htmlspecialchars($complaint['user_email']) ?></td>
                        <td><?= $complaint['complaint_date'] ?></td>
                        <td>
                            <a href="edit.php?id=<?= $complaint['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete_complaint.php?id=<?= $complaint['id'] ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Delete complaint #<?= $complaint['id'] ?>?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>