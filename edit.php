<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit();
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=gamezone_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM complaints WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $complaint = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Complaint</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Edit Complaint #<?= htmlspecialchars($complaint['id']) ?></h2>
        <form action="update_complaint.php" method="POST">
            <input type="hidden" name="id" value="<?= $complaint['id'] ?>">
            
            <div class="mb-3">
                <label>User ID</label>
                <input type="text" name="user_id" class="form-control" 
                       value="<?= htmlspecialchars($complaint['user_id']) ?>">
            </div>

            <div class="mb-3">
                <label>Subject</label>
                <input type="text" name="subject" class="form-control"
                       value="<?= htmlspecialchars($complaint['subject']) ?>">
            </div>

            <div class="mb-3">
                <label>Message</label>
                <textarea name="message" class="form-control" rows="5"><?= 
                    htmlspecialchars($complaint['message']) 
                ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Save</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>