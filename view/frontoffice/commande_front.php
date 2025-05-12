<?php
session_start();
require_once '../../config.php';
require_once '../../model/user.php';
require_once '../../controller/UserController.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$errors = [];
$success = false;

// Get current user data
$pdo = config::getConnexion();
$userController = new UserController($pdo);
$user = $userController->getUserById($_SESSION['user_id']);

$_SESSION['user'] = $user;



$idProduit = isset($_GET['id']) ? intval($_GET['id']) : 0;
$image = isset($_GET['image']) ? urldecode($_GET['image']) : '';
$nomProduit = isset($_GET['nom']) ? urldecode($_GET['nom']) : '';
$prixProduit = isset($_GET['prix']) ? urldecode($_GET['prix']) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $numr = $_POST['numr'];
    $adresse = $_POST['adresse'];
    $quantite = $_POST['quantite'];
    $idProduit = $_POST['idProduit'];

    try {
        $pdo = config::getConnexion();
        $sql = "INSERT INTO commande (nom, numr, adresse, date, quantite, id_produits)
                VALUES (:nom, :numr, :adresse, NOW(), :quantite, :id_produits)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':numr' => $numr,
            ':adresse' => $adresse,
            ':quantite' => $quantite,
            ':id_produits' => $idProduit
        ]);
        
        $sid = 'TON_ACCOUNT_SID';
        $token = 'TON_AUTH_TOKEN';
        $from = 'TON_NUMERO_TWILIO';
        $to = $numr;
        $message = "Bonjour $nom, votre commande de $quantite produit(s) a été enregistrée. Elle arrivera dans 2 jours à l'adresse suivante : $adresse. Merci pour votre achat.";
        $url = "https://api.twilio.com/2010-04-01/Accounts/$sid/Messages.json";
        $data = [
            'From' => $from,
            'To' => $to,
            'Body' => $message
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_USERPWD, "$sid:$token");
        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) {
            header("Location: commande_front.php?message=error_sms");
            exit();
        }

        header("Location: commande_front.php?message=success");
        exit();
    } catch (PDOException $e) {
        header("Location: commande_front.php?message=error");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commander un produit - GAMY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .cursor-glow {
            position: fixed;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(13, 80, 180, 0.8) 0%, rgba(13, 80, 180, 0) 70%);
            border-radius: 50%;
            filter: blur(30px);
            pointer-events: none;
            z-index: -1;
            transform: translate(-50%, -50%);
            animation: pulse-blue 3s infinite alternate;
        }

        @keyframes pulse-blue {
            0% { opacity: 0.7; transform: translate(-50%, -50%) scale(0.95); }
            100% { opacity: 0.9; transform: translate(-50%, -50%) scale(1.05); }
        }

        .container {
            position: relative;
            z-index: 1;
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
        }

        .hero {
            text-align: center;
            padding: 60px 20px;
        }

        .hero h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 3rem;
            color: var(--primary-red);
            text-shadow: 0 0 15px var(--primary-red);
            margin-bottom: 15px;
        }

        .hero p {
            font-size: 1.2rem;
            color: #ddd;
        }

        .product-info {
            text-align: center;
            margin: 20px auto;
            max-width: 500px;
        }

        .product-info img {
            max-width: 200px;
            border-radius: 10px;
            margin-bottom: 10px;
            border: 1px solid var(--primary-red);
        }

        .product-info h2 {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.8rem;
            color: var(--primary-red);
            margin: 5px 0;
        }

        .product-info p {
            font-size: 1.2rem;
            color: #ddd;
        }

        .form-section {
            background-color: rgba(30, 30, 30, 0.8);
            border-radius: 20px;
            padding: 40px;
            border: 2px solid var(--primary-red);
            box-shadow: var(--neon-glow);
            margin: 30px auto;
        }

        .form-section label {
            font-size: 1.1rem;
            color: #ddd;
            margin-bottom: 8px;
            display: block;
        }

        .form-section input,
        .form-section textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: none;
            background-color: rgba(20, 20, 20, 0.8);
            color: white;
            font-size: 1rem;
        }

        .form-section input:focus,
        .form-section textarea:focus {
            outline: none;
            box-shadow: 0 0 8px var(--primary-red);
        }

        .quantity-selector input[type="number"] {
            width: 100%;
            padding: 12px;
            background-color: rgba(20, 20, 20, 0.8);
            border-radius: 8px;
            border: none;
            color: white;
        }

        .submit-btn {
            background-color: var(--primary-red);
            color: white;
            font-size: 1.1rem;
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--neon-glow);
            width: 100%;
        }

        .submit-btn:hover {
            background-color: var(--dark-red);
            transform: scale(1.05);
        }

        .alert {
            text-align: center;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            font-size: 1.1rem;
        }

        .alert-success {
            background-color: rgba(0, 255, 0, 0.15);
            color: #a0ffa0;
            border: 1px solid #00ff00;
        }

        .alert-error {
            background-color: rgba(255, 42, 42, 0.15);
            color: #ff9e9e;
            border: 1px solid var(--primary-red);
        }

        .back-link {
            display: inline-block;
            margin: 20px 0;
            color: var(--primary-red);
            font-family: 'Rajdhani', sans-serif;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            text-shadow: 0 0 5px var(--primary-red);
        }

        #qrCodeContainer {
            text-align: center;
            margin-top: 20px;
        }

        #qrCodeContainer h3 {
            font-family: 'Orbitron', sans-serif;
            color: white;
            margin-bottom: 15px;
        }

        #qrCodeContainer img {
            border: 1px solid var(--primary-red);
            border-radius: 8px;
        }

        @media (max-width: 600px) {
            .hero h1 {
                font-size: 2.2rem;
            }

            .form-section {
                padding: 20px;
            }

            .product-info img {
                max-width: 150px;
            }
        }
    </style>
</head>
<body>
    <?php require 'navbar.php'; ?>

    <div class="container">
        <section class="hero">
            <h1>Commander un produit</h1>
            <p>Remplissez le formulaire ci-dessous pour passer votre commande</p>
        </section>

        <?php if (isset($_GET['message'])): ?>
            <div class="alert <?php echo $_GET['message'] === 'success' ? 'alert-success' : 'alert-error'; ?>">
                <?php echo $_GET['message'] === 'success' 
                    ? 'Votre commande a bien été passée ! Merci pour votre achat.' 
                    : 'Une erreur est survenue lors de la commande. Veuillez réessayer.'; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($image)): ?>
            <div class="product-info">
                <img src="<?php echo htmlspecialchars($image); ?>" alt="Image produit">
                <h2><?php echo htmlspecialchars($nomProduit); ?></h2>
                <p>Prix : <?php echo htmlspecialchars($prixProduit); ?> €</p>
            </div>
        <?php endif; ?>

        <section class="form-section">
            <form action="" method="POST">
                <label for="nom">Nom complet :</label>
                <input type="text" id="nom" name="nom" required>

                <label for="adresse">Adresse de livraison :</label>
                <input type="text" id="adresse" name="adresse" required>

                <label for="numr">Numéro de téléphone :</label>
                <input type="text" id="numr" name="numr" required>

                <label for="quantite">Quantité :</label>
                <input type="number" name="quantite" required min="1">

                <input type="hidden" name="idProduit" value="<?php echo htmlspecialchars($idProduit); ?>">

                <button type="submit" class="submit-btn">Passer la commande</button>
            </form>
            <div id="qrCodeContainer"></div>
            <a href="indexfront.php" class="back-link"><i class="fas fa-arrow-left me-1"></i> Retour à la page produits</a>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
     

        document.querySelector("form").addEventListener("submit", function(e) {
            e.preventDefault();
            const phone = document.getElementById("numr").value.trim();
            const message = encodeURIComponent("Votre commande a été expédiée et arrivera dans 2 jours.");
            if (!phone.startsWith("+")) {
                alert("Veuillez entrer le numéro au format international, ex : +216...");
                return;
            }
            const smsLink = `sms:${phone}?body=${message}`;
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?data=${encodeURIComponent(smsLink)}&size=200x200`;
            document.getElementById("qrCodeContainer").innerHTML = `
                <h3>Scannez ce QR Code pour envoyer le SMS :</h3>
                <img src="${qrUrl}" alt="QR Code vers SMS" />
                <br><br>
                <button onclick="document.querySelector('form').submit();" class="submit-btn">Confirmer et envoyer la commande</button>
            `;
        });
    </script>
</body>
</html>