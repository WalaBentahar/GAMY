<?php
session_start();
require_once '../../config.php'; // Include database connection

if (!isset($_SESSION['verified']) || !$_SESSION['verified']) {
    // User has not verified the reset code
    header("Location: send_reset_code.php");
    exit();
}

// Initialize variables
$error = $success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newPassword = htmlspecialchars(trim($_POST['new_password']));
    $confirmPassword = htmlspecialchars(trim($_POST['confirm_password']));

    if (!empty($newPassword) && !empty($confirmPassword)) {
        if ($newPassword === $confirmPassword) {
            // Update password in the database
            $email = $_SESSION['reset_email'];
            $pdo = config::getConnexion();
            $sql = "UPDATE users SET password = :password WHERE email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':password', $newPassword);
            $stmt->bindParam(':email', $email);

            if ($stmt->execute()) {
                $success = "Password has been reset successfully.";
                // Clear the reset session
                unset($_SESSION['reset_email'], $_SESSION['reset_code'], $_SESSION['verified']);
            } else {
                $error = "Failed to reset password. Please try again.";
            }
        } else {
            $error = "Passwords do not match.";
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<!<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Gaming Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-red: #ff2a2a;
            --dark-red: #a00000;
            --neon-glow: 0 0 10px rgba(255, 42, 42, 0.8);
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: 'Rajdhani', sans-serif;
            background: #000 url('https://images.unsplash.com/photo-1542751371-adc38448a05e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80') no-repeat center center;
            background-size: cover;
            color: white;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
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
        
        .reset-container {
            position: relative;
            z-index: 1;
            max-width: 500px;
            width: 100%;
            margin: 20px;
            padding: 40px 30px;
            background-color: rgba(20, 20, 20, 0.85);
            border: 2px solid var(--primary-red);
            border-radius: 10px;
            box-shadow: var(--neon-glow), 0 0 30px rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
            animation: pulse-border 2s infinite alternate;
        }
        
        @keyframes pulse-border {
            0% {
                box-shadow: 0 0 10px rgba(255, 42, 42, 0.8), 0 0 30px rgba(0, 0, 0, 0.8);
                border-color: #ff2a2a;
            }
            100% {
                box-shadow: 0 0 20px rgba(255, 42, 42, 0.8), 0 0 40px rgba(0, 0, 0, 0.8);
                border-color: #ff5555;
            }
        }
        
        .reset-title {
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
        
        .reset-title::after {
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
        
        .form-label {
            display: block;
            color: #ddd;
            margin-bottom: 10px;
            font-weight: 500;
            font-size: 16px;
            letter-spacing: 1px;
            text-shadow: 0 0 5px rgba(255, 255, 255, 0.2);
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
            box-shadow: 0 0 0 2px rgba(255, 42, 42, 0.3), 0 0 15px rgba(255, 42, 42, 0.2);
            background-color: rgba(40, 40, 40, 0.8);
        }
        
        .input-highlight {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 2px;
            width: 0;
            background-color: var(--primary-red);
            transition: width 0.4s ease;
            box-shadow: 0 0 5px var(--primary-red);
        }
        
        .game-input:focus ~ .input-highlight {
            width: 100%;
        }
        
        .reset-btn {
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
            position: relative;
            overflow: hidden;
            text-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
            box-shadow: 0 5px 15px rgba(255, 42, 42, 0.3);
        }
        
        .reset-btn::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to bottom right,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0) 45%,
                rgba(255, 255, 255, 0.3) 50%,
                rgba(255, 255, 255, 0) 55%,
                rgba(255, 255, 255, 0) 100%
            );
            transform: rotate(30deg);
            transition: all 0.5s ease;
        }
        
        .reset-btn:hover {
            background: linear-gradient(135deg, #ff3a3a 0%, #b00000 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 42, 42, 0.5);
            animation: pulse 1s infinite alternate;
        }
        
        .reset-btn:hover::before {
            left: 100%;
        }
        
        @keyframes pulse {
            0% {
                box-shadow: 0 8px 25px rgba(255, 42, 42, 0.5);
            }
            100% {
                box-shadow: 0 8px 35px rgba(255, 42, 42, 0.8);
            }
        }
        
        .error-message {
            background-color: rgba(255, 42, 42, 0.15);
            border-left: 4px solid var(--primary-red);
            padding: 12px 20px;
            margin-bottom: 25px;
            color: #ff9e9e;
            font-weight: 500;
            border-radius: 0 4px 4px 0;
            animation: shake 0.5s ease;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }
        
        .success-message {
            background-color: rgba(0, 255, 0, 0.15);
            border-left: 4px solid #00ff00;
            padding: 12px 20px;
            margin-bottom: 25px;
            color: #a0ffa0;
            font-weight: 500;
            border-radius: 0 4px 4px 0;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .password-strength {
            margin-top: 5px;
            height: 5px;
            background: #333;
            border-radius: 3px;
            overflow: hidden;
        }
        
        .strength-meter {
            height: 100%;
            width: 0;
            transition: width 0.3s, background 0.3s;
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
            .reset-container {
                padding: 30px 20px;
                margin: 15px;
            }
            
            .reset-title {
                font-size: 24px;
            }
            
            .reset-btn {
                padding: 14px 25px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <h1 class="reset-title">RESET YOUR PASSWORD</h1>
        
        <?php if (!empty($error)): ?>
            <div class="error-message">
                <p><?= $error ?></p>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success-message">
                <p><?= $success ?></p>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="new_password" class="form-label">NEW PASSWORD</label>
                <input type="password" id="new_password" name="new_password" class="game-input" placeholder="Enter your new password" required>
                <div class="input-highlight"></div>
                <div class="password-strength">
                    <div class="strength-meter" id="strength-meter"></div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password" class="form-label">CONFIRM PASSWORD</label>
                <input type="password" id="confirm_password" name="confirm_password" class="game-input" placeholder="Confirm your new password" required>
                <div class="input-highlight"></div>
            </div>
            
            <button type="submit" class="reset-btn">
                <span class="btn-text">RESET PASSWORD</span>
                <span class="btn-icon">→</span>
            </button>
        </form>
        
        <div class="form-footer">
            <a href="login.php">← Back to Login</a>
        </div>
    </div>

    <script>
        // Password strength indicator
        const passwordInput = document.getElementById('new_password');
        const strengthMeter = document.getElementById('strength-meter');
        
        passwordInput.addEventListener('input', function() {
            const strength = calculatePasswordStrength(this.value);
            updateStrengthMeter(strength);
        });
        
        function calculatePasswordStrength(password) {
            let strength = 0;
            
            // Length check
            if (password.length >= 8) strength += 1;
            if (password.length >= 12) strength += 1;
            
            // Character type checks
            if (/[A-Z]/.test(password)) strength += 1;
            if (/[0-9]/.test(password)) strength += 1;
            if (/[^A-Za-z0-9]/.test(password)) strength += 1;
            
            return Math.min(strength, 5);
        }
        
        function updateStrengthMeter(strength) {
            const colors = ['#ff0000', '#ff4000', '#ff8000', '#ffbf00', '#00ff00'];
            const width = (strength / 5) * 100;
            
            strengthMeter.style.width = width + '%';
            strengthMeter.style.background = colors[strength - 1] || '#333';
        }
    </script>
</body>
</html>