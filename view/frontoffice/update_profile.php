<?php
session_start();
require_once '../../config.php';
require_once '../../model/user.php';
require_once '../../controller/UserController.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize variables
$errors = [];
$success = false;

// Get current user data
$pdo = config::getConnexion();
$userController = new UserController($pdo);
$user = $userController->getUserById($_SESSION['user_id']);

// Store user in session for navbar access
$_SESSION['user'] = $user;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $userId = $_SESSION['user_id'];
        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $phone = trim($_POST['phone']);
        $pays = trim($_POST['pays']);
        
        // Password change fields
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validation for profile fields
        if (empty($nom)) {
            $errors[] = "Le nom est obligatoire.";
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\-]{2,50}$/', $nom)) {
            $errors[] = "Le nom doit contenir entre 2 et 50 caractères alphabétiques.";
        }

        if (empty($prenom)) {
            $errors[] = "Le prénom est obligatoire.";
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\-]{2,50}$/', $prenom)) {
            $errors[] = "Le prénom doit contenir entre 2 et 50 caractères alphabétiques.";
        }

        if (!empty($phone) && !preg_match('/^[\d\s\-+]{8,15}$/', $phone)) {
            $errors[] = "Le format du téléphone est invalide.";
        }

        if (empty($pays)) {
            $errors[] = "Le pays est obligatoire.";
        }

        // Password change validation
        if (!empty($currentPassword) || !empty($newPassword) || !empty($confirmPassword)) {
            if (empty($currentPassword)) {
                $errors[] = "Current password is required to change password.";
            }
            if (empty($newPassword)) {
                $errors[] = "New password is required.";
            }
            if ($newPassword !== $confirmPassword) {
                $errors[] = "New passwords do not match.";
            }
            if (strlen($newPassword) < 8) {
                $errors[] = "New password must be at least 8 characters long.";
            }
        }

        if (empty($errors)) {
            $user->setNom(htmlspecialchars($nom, ENT_QUOTES, 'UTF-8'));
            $user->setPrenom(htmlspecialchars($prenom, ENT_QUOTES, 'UTF-8'));
            $user->setPhone(htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'));
            $user->setPays(htmlspecialchars($pays, ENT_QUOTES, 'UTF-8'));

            $success = $userController->updateUser(
                $user->getId(),
                $user->getPrenom(),
                $user->getEmail(),
                $user->getPhone(),
                $user->getPays(),
                null, // Don't change password through updateUser
                $user->getRole()
            );

            // Handle password change if all fields are provided
            if (!empty($currentPassword) && !empty($newPassword) && $success) {
                $passwordChanged = $userController->changePassword(
                    $userId,
                    $currentPassword,
                    $newPassword
                );
                
                if (!$passwordChanged) {
                    $errors[] = "Failed to change password. Current password may be incorrect.";
                    $success = false;
                } else {
                    $successMessage = "Profile and password updated successfully!";
                }
            } else {
                $successMessage = "Profile updated successfully!";
            }

            if ($success) {
                $_SESSION['user_name'] = $user->getPrenom();
                // Update session user data
                $_SESSION['user'] = $user;
            }
        }
    } catch (Exception $e) {
        $errors[] = "Erreur: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile - Gaming Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-red: #ff2a2a;
            --dark-red: #a00000;
            --neon-glow: 0 0 10px rgba(255, 42, 42, 0.8);
            --bg-dark: #0d0d0d;
            --bg-darker: #080808;
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: 'Rajdhani', sans-serif;
            background: #000 url('https://images.unsplash.com/photo-1542751371-adc38448a05e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80') no-repeat center center;
            background-size: cover;
            color: white;
            min-height: 100vh;
            position: relative;
            padding-top: 70px; 
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 0;
        }
        
        /* Navbar Styles */
        .navbar-gaming {
            background-color: var(--bg-darker);
            border-bottom: 1px solid var(--primary-red);
            box-shadow: var(--neon-glow);
            font-family: 'Orbitron', sans-serif;
            padding: 0.8rem 1rem;
        }
        
        .navbar-brand-gaming {
            color: white !important;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        
        .nav-link-gaming {
            color: #ddd !important;
            font-weight: 500;
            letter-spacing: 1px;
            margin: 0 8px;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .nav-link-gaming:hover {
            color: white !important;
        }
        
        .nav-link-gaming.active {
            color: white !important;
            text-shadow: 0 0 5px var(--primary-red);
        }
        
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        
        /* Profile Container Styles */
        .profile-container {
            position: relative;
            z-index: 1;
            max-width: 600px;
            width: 100%;
            margin: 20px auto;
            padding: 40px 30px;
            background-color: rgba(20, 20, 20, 0.85);
            border: 2px solid var(--primary-red);
            border-radius: 10px;
            box-shadow: var(--neon-glow), 0 0 30px rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
        }
        
        .profile-title {
            color: #fff;
            text-transform: uppercase;
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
            letter-spacing: 3px;
            font-size: 28px;
            margin-bottom: 30px;
            text-align: center;
            position: relative;
            padding-bottom: 15px;
            text-shadow: 0 0 10px var(--primary-red);
        }
        
        .profile-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--primary-red), transparent);
            box-shadow: 0 0 10px var(--primary-red);
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
        }
        
        .form-col {
            flex: 1;
        }
        
        .form-label {
            display: block;
            color: #ddd;
            margin-bottom: 10px;
            font-weight: 500;
            font-size: 16px;
            letter-spacing: 1px;
        }
        
        .game-input {
            width: 100%;
            padding: 14px 20px;
            background-color: rgba(30, 30, 30, 0.8);
            border: 1px solid #333;
            color: #fff;
            font-family: 'Rajdhani', sans-serif;
            font-size: 16px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .game-input:focus {
            outline: none;
            border-color: var(--primary-red);
            box-shadow: 0 0 0 2px rgba(255, 42, 42, 0.3);
            background-color: rgba(40, 40, 40, 0.8);
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #777;
        }
        
        .password-toggle:hover {
            color: var(--primary-red);
        }
        
        .update-btn {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
            color: white;
            border: none;
            padding: 16px 35px;
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 20px;
        }
        
        .update-btn:hover {
            background: linear-gradient(135deg, #ff3a3a 0%, #b00000 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 42, 42, 0.5);
        }
        
        .error-message {
            background-color: rgba(255, 42, 42, 0.15);
            border-left: 4px solid var(--primary-red);
            padding: 12px 20px;
            margin-bottom: 25px;
            color: #ff9e9e;
            font-weight: 500;
            border-radius: 0 4px 4px 0;
        }
        
        .success-message {
            background-color: rgba(0, 255, 0, 0.15);
            border-left: 4px solid #00ff00;
            padding: 12px 20px;
            margin-bottom: 25px;
            color: #a0ffa0;
            font-weight: 500;
            border-radius: 0 4px 4px 0;
        }
        
        .form-footer {
            text-align: center;
            margin-top: 30px;
            color: #aaa;
            font-size: 14px;
        }
        
        .form-footer a {
            color: var(--primary-red);
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        
        .form-footer a:hover {
            text-shadow: 0 0 5px var(--primary-red);
        }
        
        @media (max-width: 768px) {
            .profile-container {
                padding: 30px 20px;
                margin: 15px;
            }
            
            .profile-title {
                font-size: 24px;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-gaming fixed-top">
        <div class="container">
            <a class="navbar-brand navbar-brand-gaming" href="#">
                <i class="fas fa-gamepad me-2"></i>Gaming Portal
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link nav-link-gaming active" href="/"><i class="fas fa-home me-1"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-gaming" href="/games"><i class="fas fa-joystick me-1"></i> Games</a>
                    </li>
                    <?php if ($_SESSION['user']->getRole() === 'ADMIN'): ?>
                        <li class="nav-item">
                            <a class="nav-link nav-link-gaming" href="../backoffice/table.php"><i class="fas fa-lock me-1"></i> Admin</a>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link nav-link-gaming dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" data-bs-toggle="dropdown">
                                <div class="user-avatar">
                                    <?= substr($_SESSION['user']->getPrenom(), 0, 1) . substr($_SESSION['user']->getNom(), 0, 1) ?>
                                </div>
                                <?= htmlspecialchars($_SESSION['user']->getPrenom()) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="update_profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="profile-container">
            <h1 class="profile-title">UPDATE PROFILE</h1>
            
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($successMessage)): ?>
                <div class="success-message">
                    <p><?= htmlspecialchars($successMessage) ?></p>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="prenom" class="form-label">FIRST NAME</label>
                            <input type="text" id="prenom" name="prenom" class="game-input" 
                                   value="<?= htmlspecialchars($user->getPrenom() ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="nom" class="form-label">LAST NAME</label>
                            <input type="text" id="nom" name="nom" class="game-input" 
                                   value="<?= htmlspecialchars($user->getNom() ?? '') ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="phone" class="form-label">PHONE NUMBER</label>
                    <input type="number" id="phone" name="phone" class="game-input" 
                           value="<?= htmlspecialchars($user->getPhone() ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="pays" class="form-label">COUNTRY</label>
                    <input type="text" id="pays" name="pays" class="game-input" 
                           value="<?= htmlspecialchars($user->getPays() ?? '') ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="current_password" class="form-label">CURRENT PASSWORD (for password change)</label>
                    <input type="password" id="current_password" name="current_password" class="game-input">
                    <span class="password-toggle"><i class="fas fa-eye"></i></span>
                </div>
                
                <div class="form-group">
                    <label for="new_password" class="form-label">NEW PASSWORD</label>
                    <input type="password" id="new_password" name="new_password" class="game-input">
                    <span class="password-toggle"><i class="fas fa-eye"></i></span>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password" class="form-label">CONFIRM NEW PASSWORD</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="game-input">
                    <span class="password-toggle"><i class="fas fa-eye"></i></span>
                </div>
                
                <button type="submit" class="update-btn">
                    <span class="btn-text">UPDATE PROFILE</span>
                    <span class="btn-icon">→</span>
                </button>
            </form>
            
            <div class="form-footer">
                <?php if ($_SESSION['user']->getRole() === 'ADMIN'): ?>
                    <a href="../backoffice/table.php">← Back to Dashboard</a>
                <?php else: ?>
                    <a href="../backoffice/access_denied.php">← Back to Dashboard</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password toggle visibility
        document.querySelectorAll('.password-toggle').forEach(toggle => {
            toggle.addEventListener('click', function() {
                const input = this.previousElementSibling;
                if (input.type === 'password') {
                    input.type = 'text';
                    this.innerHTML = '<i class="fas fa-eye-slash"></i>';
                } else {
                    input.type = 'password';
                    this.innerHTML = '<i class="fas fa-eye"></i>';
                }
            });
        });
    </script>
</body>
</html>