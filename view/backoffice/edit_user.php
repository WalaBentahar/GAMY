<?php
// Include necessary files
require_once '../../config.php';
require_once '../../controller/userController.php';

// Initialize the PDO connection
$pdo = config::getConnexion();

// Instantiate the UserController with the PDO connection
$userController = new UserController($pdo);

// Check if `edit_id` is passed via GET, else redirect to the table page
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    // Fetch user data from the database
    $user = $userController->getUserById($edit_id);
    if (!$user) {
        // If user not found, redirect back to the table page
        header("Location: table.php");
        exit();
    }
} else {
    // Redirect if no `edit_id` is provided in the URL
    header("Location: table.php");
    exit();
}

// Handle form submission to update user details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $edit_id = intval($_POST['edit_id']);
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $pays = $_POST['pays'];
    $password = $_POST['password']; // Optionally hash the password before saving
    $role = $_POST['role'];

    // Optional: Add validation for input fields here

    // Update the user's information
    $userController->updateUser($edit_id, $prenom, $email, $phone, $pays, $password, $role);

    // Redirect to the table page after update
    header("Location: ../../table.php");
    exit();
}
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <style>
      body {
    font-family: 'Arial', sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 0;
}

.form-wrapper {
    max-width: 600px;
    margin: 40px auto;
    padding: 30px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

h1 {
    text-align: center;
    color: #333;
    font-size: 28px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

label {
    font-weight: bold;
    font-size: 16px;
    color: #555;
    display: block;
    margin-bottom: 8px;
}

input[type="text"],
input[type="email"],
input[type="password"] {
    width: 100%;
    padding: 12px;
    margin-top: 5px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
    transition: border-color 0.3s;
}

input[type="text"]:focus,
input[type="email"]:focus,
input[type="password"]:focus {
    border-color: #5c9ded;
    outline: none;
}

input[type="text"]:disabled,
input[type="email"]:disabled,
input[type="password"]:disabled {
    background-color: #f1f1f1;
    cursor: not-allowed;
}

button {
    width: 100%;
    padding: 12px;
    background-color: #5c9ded;
    color: #fff;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #4688c3;
}

button:focus {
    outline: none;
}

/ Responsive Design */
@media (max-width: 768px) {
    .form-wrapper {
        margin: 20px;
        padding: 20px;
    }

    h1 {
        font-size: 24px;
    }

    .form-group input {
        font-size: 14px;
    }

    button {
        font-size: 14px;
    }
}
</style>
</head>
<body>
    <div class="form-wrapper">
        <h1>Edit User</h1>
        <form method="POST" action="">
            <!-- Hidden field to hold the user ID -->
            <input type="hidden" name="edit_id" value="<?= htmlspecialchars($user['id']); ?>">

            <div class="form-group">
                <label for="prenom">Prénom:</label>
                <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($user['prenom']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']); ?>" required>
            </div>

            <div class="form-group">
                <label for="pays">Pays:</label>
                <input type="text" id="pays" name="pays" value="<?= htmlspecialchars($user['pays']); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter new password (optional)">
            </div>

            <div class="form-group">
                <label for="role">Role:</label>
                <input type="text" id="role" name="role" value="<?= htmlspecialchars($user['role']); ?>" required>
            </div>

            <div class="form-actions">
                <button type="submit">Update</button>
            </div>
        </form>
    </div>
</body>
</html>