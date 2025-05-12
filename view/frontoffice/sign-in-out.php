<?php
require_once '../../config.php'; 
require_once '../../model/user.php';   
require_once '../../controller/userController.php';  

// Initialize the User class with the PDO connection
$pdo = config::getConnexion();
$userModel = new User();
$userController = new UserController($pdo);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $pays = htmlspecialchars(trim($_POST['pays']));
    $password = trim($_POST['password']); // Don't htmlspecialchars passwords
    $confirm_password = trim($_POST['confirm_password']);
    
    $errors = [];
    
    // Field validations
    if (empty($nom)) {
        $errors[] = "Le champ 'Nom' est requis.";
    } elseif (!preg_match('/^[\p{L}\s\-]{2,50}$/u', $nom)) {
        $errors[] = "Le nom ne doit contenir que des lettres et espaces (2-50 caractères).";
    }
    
    if (empty($prenom)) {
        $errors[] = "Le champ 'Prénom' est requis.";
    } elseif (!preg_match('/^[\p{L}\s\-]{2,50}$/u', $prenom)) {
        $errors[] = "Le prénom ne doit contenir que des lettres et espaces (2-50 caractères).";
    }
    
    if (empty($email)) {
        $errors[] = "Le champ 'Email' est requis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Veuillez fournir une adresse e-mail valide.";
    } else {
        try {
            if ($userController->emailExists($email)) {
                $errors[] = "Cette adresse email est déjà utilisée.";
            }
        } catch (Exception $e) {
            $errors[] = "Erreur de vérification de l'email. Veuillez réessayer.";
            error_log("Email check error: " . $e->getMessage());
        }
    }
    
    if (empty($phone)) {
        $errors[] = "Le champ 'Téléphone' est requis.";
    } elseif (!preg_match('/^[\d\s\-+]{8,15}$/', $phone)) {
        $errors[] = "Format de téléphone invalide (8-15 chiffres).";
    }
    
    if (empty($pays)) {
        $errors[] = "Le champ 'Pays' est requis.";
    }
    
    if (empty($password)) {
        $errors[] = "Le champ 'Mot de passe' est requis.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    // If no errors, add the user
    if (empty($errors)) {
        try {
            $isAdded = $userController->addUser($nom, $prenom, $email, $phone, $pays, $password);
            if ($isAdded) {
                $successMessage = "Inscription réussie !";
                // Clear form or redirect if needed
            } else {
                $errors[] = "Une erreur est survenue lors de l'inscription. Veuillez réessayer.";
            }
        } catch (Exception $e) {
            $errors[] = "Erreur lors de l'inscription : " . $e->getMessage();
            error_log("Registration error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gaming Sign Up Form</title>
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
        
        .gaming-form {
            position: relative;
            z-index: 1;
            max-width: 800px;
            width: 100%;
            margin: 20px;
            padding: 30px;
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
        
        .form-title {
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
        
        .form-title::after {
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
        
        .input-single {
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
        
        .gaming-input {
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
        
        .gaming-input:focus {
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
        
        .gaming-input:focus ~ .input-highlight {
            width: 100%;
        }
        
        .gaming-button {
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
        
        .gaming-button::before {
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
        
        .gaming-button:hover {
            background: linear-gradient(135deg, #ff3a3a 0%, #b00000 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 42, 42, 0.5);
            animation: pulse 1s infinite alternate;
        }
        
        .gaming-button:hover::before {
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
        
        .gaming-button:active {
            transform: translateY(0);
        }
        
        .gaming-button .button-icon {
            font-weight: bold;
            transition: transform 0.3s ease;
        }
        
        .gaming-button:hover .button-icon {
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
        
        .boxshado-single {
            padding: 30px;
            background-color: rgba(26, 26, 26, 0.7);
            border-radius: 8px;
            border: 1px solid rgba(255, 42, 42, 0.2);
        }
        
        .form-footer {
            text-align: center;
            margin-top: 20px;
            color: #aaa;
            font-size: 14px;
        }
        
        .form-footer a {
            color: var(--primary-red);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .form-footer a:hover {
            text-shadow: 0 0 5px var(--primary-red);
        }
        
        @media (max-width: 768px) {
            .gaming-form {
                padding: 20px;
                margin: 15px;
            }
            
            .form-title {
                font-size: 24px;
            }
            
            .boxshado-single {
                padding: 20px;
            }
            
            .gaming-button {
                padding: 14px 25px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <form action="sign-in-out.php" method="post" class="gaming-form">
        <div class="checkout-single-wrapper">
            <div class="checkout-single boxshado-single">
                <h4 class="form-title">JOIN THE BATTLE</h4>
                
                <!-- Display Errors -->
                <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <?php foreach ($errors as $error): ?>
                    <p><?= $error ?></p>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Display Success Message -->
                <?php if (!empty($successMessage)): ?>
                    <div class="success-message">
                    <p><?= $successMessage ?></p>
                     </div>
                <?php endif; ?>

                <div class="checkout-single-form">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="input-single">
                                <span class="input-label">Prénom*</span>
                                <input type="text" name="prenom" id="userFirstName" placeholder="Prénom" class="gaming-input">
                                <div class="input-highlight"></div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-single">
                                <span class="input-label">Nom*</span>
                                <input type="text" name="nom" id="userLastName" placeholder="Nom" class="gaming-input">
                                <div class="input-highlight"></div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="input-single">
                                <span class="input-label">Email Address*</span>
                                <input type="text" name="email" id="email22" placeholder="email" class="gaming-input">
                                <div class="input-highlight"></div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="input-single">
                                <span class="input-label">Phone*</span>
                                <input type="number" name="phone" id="phone" placeholder="phone" class="gaming-input">
                                <div class="input-highlight"></div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="input-single">
                                <span class="input-label">Votre Pays*</span>
                                <input name="pays" id="country" placeholder="Saisissez votre pays" class="gaming-input">
                                <div class="input-highlight"></div>
                            </div>
                        </div>
                        
                        <div class="col-lg-12">
                            <div class="input-single">
                                <span class="input-label">Mot de passe*</span>
                                <input type="password" name="password" id="towncity" class="gaming-input">
                                <div class="input-highlight"></div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="input-single">
                                <span class="input-label">Retappez votre mot de passe*</span>
                                <input type="password" name="confirm_password" id="towncity" class="gaming-input">
                                <div class="input-highlight"></div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <button type="submit" class="gaming-button">
                                <span class="button-text">CREATE ACCOUNT</span>
                                <span class="button-icon">→</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-footer">
                    Already have an account? <a href="login.php">Log In</a>
                </div>
            </div>
        </div>
    </form>
</body>
</html>
