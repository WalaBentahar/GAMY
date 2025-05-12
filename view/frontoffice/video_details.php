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


if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID de la vidéo non spécifié.");
}

$videoId = intval($_GET['id']);

// Récupération des infos vidéo
$stmt = config::getConnexion()->prepare("SELECT * FROM videoguides WHERE id = ?");
$stmt->execute([$videoId]);
$video = $stmt->fetch(PDO::FETCH_ASSOC);

// Incrémenter les vues
config::getConnexion()->prepare("UPDATE videoguides SET views = views + 1 WHERE id = ?")->execute([$videoId]);

if (!$video) {
    die("Vidéo non trouvée.");
}

// Charger les descriptions
$stmt = config::getConnexion()->prepare("SELECT * FROM video_descriptions WHERE video_id = ?");
$stmt->execute([$videoId]);
$descriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Déterminer l'URL
$videoUrl = htmlspecialchars($video['content'] ?? '');

function getEmbedUrl($url) {
    if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
        parse_str(parse_url($url, PHP_URL_QUERY), $ytParams);
        $videoId = $ytParams['v'] ?? basename(parse_url($url, PHP_URL_PATH));
        return "https://www.youtube.com/embed/" . $videoId;
    } elseif (strpos($url, 'vimeo.com') !== false) {
        $videoId = basename(parse_url($url, PHP_URL_PATH));
        return "https://player.vimeo.com/video/" . $videoId;
    }
    return false;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Vidéo - GAMY</title>
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
        
        /* Container Styles */
        .video-container {
            position: relative;
            z-index: 1;
            max-width: 900px;
            width: 100%;
            margin: 20px auto;
            padding: 40px 30px;
            background-color: rgba(20, 20, 20, 0.85);
            border: 2px solid var(--primary-red);
            border-radius: 10px;
            box-shadow: var(--neon-glow), 0 0 30px rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
        }
        
        .section-title {
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
        
        .section-title::after {
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
        
        .video-player {
            width: 100%;
            border-radius: 5px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        
        iframe, video {
            width: 100%;
            height: 450px;
            border: none;
            border-radius: 5px;
        }
        
        .video-info {
            color: #ddd;
            font-size: 16px;
            margin-bottom: 20px;
        }
        
        .video-info strong {
            color: #fff;
            font-weight: 600;
        }
        
        .description-box {
            background-color: rgba(30, 30, 30, 0.8);
            border: 1px solid #333;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            color: #ddd;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .description-box:hover {
            border-color: var(--primary-red);
            box-shadow: 0 0 15px rgba(255, 42, 42, 0.3);
        }
        
        .error-message {
            background-color: rgba(255, 42, 42, 0.15);
            border-left: 4px solid var(--primary-red);
            padding: 12px 20px;
            margin-bottom: 25px;
            color: #ff9e9e;
            font-weight: 500;
            border-radius: 0 4px 4px 0;
            text-align: center;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: var(--primary-red);
            font-family: 'Rajdhani', sans-serif;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            text-shadow: 0 0 5px var(--primary-red);
        }
        
        @media (max-width: 768px) {
            .video-container {
                padding: 30px 20px;
                margin: 15px;
            }
            
            .section-title {
                font-size: 24px;
            }
            
            iframe, video {
                height: 250px;
            }
        }
    </style>
</head>
<body>
    <?php require 'navbar.php'; ?>

    <div class="container py-5">
        <div class="video-container">
            <h1 class="section-title"><?= htmlspecialchars($video['title']) ?></h1>
            
            <div class="video-info">
                <p><strong>Auteur :</strong> <?= htmlspecialchars($video['author']) ?></p>
                <p><strong>Vues :</strong> <?= $video['views'] ?></p>
            </div>

            <div class="video-player">
                <?php
                $embedUrl = getEmbedUrl($videoUrl);
                if ($embedUrl):
                ?>
                    <iframe src="<?= $embedUrl ?>" frameborder="0" allowfullscreen></iframe>
                <?php elseif (filter_var($videoUrl, FILTER_VALIDATE_URL)): ?>
                    <video controls>
                        <source src="<?= $videoUrl ?>" type="video/mp4">
                        Votre navigateur ne supporte pas la lecture vidéo.
                    </video>
                <?php else: ?>
                    <div class="error-message">
                        <p>Lien vidéo invalide</p>
                    </div>
                <?php endif; ?>
            </div>

            <h2 class="section-title">DESCRIPTIONS</h2>
            <?php if (!empty($descriptions)): ?>
                <?php foreach ($descriptions as $desc): ?>
                    <div class="description-box">
                        <p><?= nl2br(htmlspecialchars($desc['description'])) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="description-box">
                    <p>Aucune description pour cette vidéo.</p>
                </div>
            <?php endif; ?>

            <a href="form.php" class="back-link">← Retour à l'accueil</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>