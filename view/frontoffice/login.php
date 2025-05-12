<?php
session_start();
require_once '../../config.php';
require_once '../../model/user.php';
require_once '../../controller/userController.php';

$errors = []; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Vérification du reCAPTCHA
    $recaptcha_secret = '6LcB_y4rAAAAAAT-28XxrFZ7uGaVUQnNHGR9Saaq'; // À remplacer
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_data = [
        'secret' => $recaptcha_secret,
        'response' => $recaptcha_response,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];
    
    $recaptcha_options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($recaptcha_data)
        ]
    ];
    
    $recaptcha_context = stream_context_create($recaptcha_options);
    $recaptcha_result = file_get_contents($recaptcha_url, false, $recaptcha_context);
    $recaptcha_json = json_decode($recaptcha_result);
    
    if (!$recaptcha_json->success) {
        $errors[] = "Veuillez vérifier que vous n'êtes pas un robot.";
    }

    // Si le reCAPTCHA est valide, continuer avec la vérification des identifiants
    if (empty($errors)) {
        $email = htmlspecialchars(trim($_POST['email']));
        $password = htmlspecialchars(trim($_POST['password']));

        if (empty($email) || empty($password)) {
            $errors[] = "Tous les champs sont obligatoires.";
        } else {
            try {
                $pdo = config::getConnexion();
                $userController = new UserController($pdo);

                $user = $userController->loginUser($email, $password);

                if ($user) {
                    $_SESSION['user_id'] = $user->getId();
                    $_SESSION['user_name'] = $user->getPrenom();
                    $_SESSION['role'] = $user->getRole();
                    $_SESSION['logged_in'] = true;
                    
                    // Redirect based on role
                    if ($_SESSION['role'] === 'admin') {
                        header("Location: table.php");
                    } else {
                        header("Location: update_profile.php");
                    }
                    exit();
                } else {
                    $errors[] = "Email ou mot de passe incorrect.";
                }
            } catch (Exception $e) {
                $errors[] = "Une erreur est survenue lors de la connexion.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gamer Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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
        
        .login-container {
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
        
        .login-title {
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
        
        .login-title::after {
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
        
        .input-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .input-label {
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
        
        .login-btn {
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
            margin-top: 30px;
            position: relative;
            overflow: hidden;
            text-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
            box-shadow: 0 5px 15px rgba(255, 42, 42, 0.3);
        }
        
        .login-btn::before {
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
        
        .login-btn:hover {
            background: linear-gradient(135deg, #ff3a3a 0%, #b00000 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 42, 42, 0.5);
            animation: pulse 1s infinite alternate;
        }
        
        .login-btn:hover::before {
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
        
        .login-btn:active {
            transform: translateY(0);
        }
        
        .login-btn .btn-icon {
            font-weight: bold;
            transition: transform 0.3s ease;
        }
        
        .login-btn:hover .btn-icon {
            transform: translateX(8px);
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
        
        .remember-me {
            display: flex;
            align-items: center;
            margin-top: 15px;
        }
        
        .remember-me input {
            margin-right: 10px;
            accent-color: var(--primary-red);
        }
        
        /* Style pour reCAPTCHA */
        .g-recaptcha {
            margin: 20px 0;
            display: flex;
            justify-content: center;
        }
        
        @media (max-width: 768px) {
            .login-container {
                padding: 30px 20px;
                margin: 15px;
            }
            
            .login-title {
                font-size: 24px;
            }
            
            .login-btn {
                padding: 14px 25px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="login-title">LOGIN</h2>
        <form action="" method="post">
        <?php if (!empty($errors)): ?>
        <div class="error-message">
            <?php foreach ($errors as $error): ?>
            <p><?= $error ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
            <div class="input-group">
                <label for="email" class="input-label">EMAIL ADDRESS</label>
                <input type="email" id="email" name="email" class="game-input" placeholder="your@email.com" required>
                <div class="input-highlight"></div>
            </div>
            
            <div class="input-group">
                <label for="password" class="input-label">PASSWORD</label>
                <input type="password" id="password" name="password" class="game-input" placeholder="••••••••" required>
                <div class="input-highlight"></div>
            </div>
            
            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me</label>
            </div>
            
            <!-- Widget reCAPTCHA -->
            <div class="g-recaptcha" data-sitekey="6LcB_y4rAAAAAByweBKV-VLCLXkxyeWoQcqRxd8v" required></div>
            
            <button type="submit" class="login-btn">
                <span class="btn-text">LOGIN</span>
                <span class="btn-icon">→</span>
            </button>
        </form>
        
        <div class="form-footer">
            <a href="send_reset_code.php">Forgot password?</a> • 
            <a href="sign-in-out.php">Create new account</a>
        </div>
    </div>
</body>
</html>