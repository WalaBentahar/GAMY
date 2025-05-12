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

require_once '../../controller/FrontController.php';

$controller = new FrontController();
$controller->handleRequest();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soumettre un guide - GAMY</title>
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
        .guide-container {
            position: relative;
            z-index: 1;
            max-width: 1200px;
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
        }
        
        .game-input, select, textarea {
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
        
        select {
            appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="white" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 10px top 50%;
        }
        
        textarea {
            resize: vertical;
            min-height: 150px;
        }
        
        .game-input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary-red);
            box-shadow: 0 0 0 2px rgba(255, 42, 42, 0.3);
            background-color: rgba(40, 40, 40, 0.8);
        }
        
        .submit-btn {
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
        
        .submit-btn:hover {
            background: linear-gradient(135deg, #ff3a3a 0%, #b00000 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 42, 42, 0.5);
        }
        
        .error-message {
            background-color: rgba(255, 45, 42, 0.15);
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
        
        .search-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
        }
        
        .search-bar .game-input {
            flex: 1;
        }
        
        .guide-card {
            background-color: rgba(30, 30, 30, 0.8);
            border: 1px solid #333;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        
        .guide-card:hover {
            border-color: var(--primary-red);
            box-shadow: 0 0 15px rgba(255, 42, 42, 0.3);
        }
        
        .guide-card h3 {
            color: #fff;
            font-family: 'Orbitron', sans-serif;
            font-size: 22px;
            margin-bottom: 15px;
            text-shadow: 0 0 5px var(--primary-red);
        }
        
        .guide-card p {
            color: #ddd;
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .action-btn {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            font-family: 'Rajdhani', sans-serif;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .action-btn.edit {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        }
        
        .action-btn.delete {
            background: linear-gradient(135deg, #dc3545 0%, #a71d2a 100%);
        }
        
        .action-btn.view {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 42, 42, 0.4);
        }
        
        .action-btn.edit:hover {
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
        }
        
        .action-btn.delete:hover {
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }
        
        .action-btn.view:hover {
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.4);
        }
        
        iframe, video {
            width: 100%;
            height: 315px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        @media (max-width: 768px) {
            .guide-container {
                padding: 30px 20px;
                margin: 15px;
            }
            
            .section-title {
                font-size: 24px;
            }
            
            .search-bar {
                flex-direction: column;
            }
            
            .actions {
                flex-direction: column;
                align-items: stretch;
            }
            
            .action-btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <?php require 'navbar.php'; ?>

    <div class="container py-5">
        <div class="guide-container">
            <h1 class="section-title"><?= !empty($controller->guide) ? 'MODIFIER LE GUIDE' : 'AJOUTER UN GUIDE' ?></h1>

            <?php if (!empty($controller->errors)): ?>
                <div class="error-message">
                    <?php foreach ($controller->errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php elseif ($controller->success): ?>
                <div class="success-message">
                    <p>Opération réussie !</p>
                </div>
            <?php endif; ?>

            <!-- Search Bar -->
            <div class="search-bar">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="search">
                    <input type="text" name="search" class="game-input" placeholder="Rechercher un guide ou une vidéo..." value="<?= htmlspecialchars($_POST['search'] ?? '') ?>">
                    <button type="submit" class="submit-btn">
                        <span class="btn-text">RECHERCHER</span>
                        <span class="btn-icon"><i class="fas fa-search"></i></span>
                    </button>
                </form>
            </div>

            <!-- Form Section -->
            <form method="POST" action="">
                <?php if (!empty($controller->guide)): ?>
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($controller->guide['id']) ?>">
                    <input type="hidden" name="type" value="<?= htmlspecialchars($_GET['type'] ?? 'text') ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="title" class="form-label">TITRE</label>
                    <input type="text" id="title" name="title" class="game-input" placeholder="Ex: Astuces FIFA 23" required value="<?= htmlspecialchars($controller->guide['title'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="author" class="form-label">AUTEUR</label>
                    <input type="text" id="author" name="author" class="game-input" placeholder="Votre pseudo" required value="<?= htmlspecialchars($controller->guide['author'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="type" class="form-label">TYPE</label>
                    <select id="type" name="type" class="game-input">
                        <option value="text" <?= (!empty($_GET['type']) && $_GET['type'] === 'text') ? 'selected' : '' ?>>Texte</option>
                        <option value="video" <?= (!empty($_GET['type']) && $_GET['type'] === 'video') ? 'selected' : '' ?>>Vidéo</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="content" class="form-label">CONTENU</label>
                    <textarea id="content" name="content" class="game-input" placeholder="Votre texte ou lien vidéo (YouTube, Vimeo, MP4)..." required><?= htmlspecialchars($controller->guide['content'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="submit-btn">
                    <span class="btn-text"><?= !empty($controller->guide) ? 'METTRE À JOUR' : 'PUBLIER LE GUIDE' ?></span>
                    <span class="btn-icon">→</span>
                </button>
            </form>

            <!-- Recommended Videos -->
            <?php if (!empty($controller->recommendedVideos)): ?>
                <h2 class="section-title">VIDÉOS RECOMMANDÉES</h2>
                <?php foreach ($controller->recommendedVideos as $video): ?>
                    <div class="guide-card">
                        <h3><?= htmlspecialchars($video['title']) ?></h3>
                        <p><strong>Auteur :</strong> <?= htmlspecialchars($video['author']) ?></p>
                        <?php
                        $url = htmlspecialchars($video['content'] ?? '');
                        if (filter_var($url, FILTER_VALIDATE_URL)):
                            if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false):
                                parse_str(parse_url($url, PHP_URL_QUERY), $ytParams);
                                $videoId = $ytParams['v'] ?? basename(parse_url($url, PHP_URL_PATH));
                                $embedUrl = "https://www.youtube.com/embed/" . $videoId;
                            elseif (strpos($url, 'vimeo.com') !== false):
                                $videoId = basename(parse_url($url, PHP_URL_PATH));
                                $embedUrl = "https://player.vimeo.com/video/" . $videoId;
                            else:
                                $embedUrl = $url;
                            endif;
                        ?>
                            <?php if (strpos($embedUrl, 'youtube.com') !== false || strpos($embedUrl, 'vimeo.com') !== false): ?>
                                <iframe src="<?= $embedUrl ?>" frameborder="0" allowfullscreen></iframe>
                            <?php else: ?>
                                <video controls>
                                    <source src="<?= $embedUrl ?>" type="video/mp4">
                                    Votre navigateur ne supporte pas la lecture vidéo.
                                </video>
                            <?php endif; ?>
                        <?php else: ?>
                            <p>URL invalide pour cette vidéo.</p>
                        <?php endif; ?>
                        <div class="actions">
                            <a href="video_details.php?id=<?= $video['id'] ?>" class="action-btn view">VOIR DÉTAILS</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Published Videos -->
            <h2 class="section-title">VIDÉOS PUBLIÉES</h2>
            <?php if (!empty($controller->videoGuides)): ?>
                <?php foreach ($controller->videoGuides as $video): ?>
                    <div class="guide-card">
                        <h3><?= htmlspecialchars($video['title']) ?></h3>
                        <p><strong>Auteur :</strong> <?= htmlspecialchars($video['author']) ?></p>
                        <?php
                        $url = htmlspecialchars($video['content'] ?? '');
                        if (filter_var($url, FILTER_VALIDATE_URL)):
                            if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false):
                                parse_str(parse_url($url, PHP_URL_QUERY), $ytParams);
                                $videoId = $ytParams['v'] ?? basename(parse_url($url, PHP_URL_PATH));
                                $embedUrl = "https://www.youtube.com/embed/" . $videoId;
                            elseif (strpos($url, 'vimeo.com') !== false):
                                $videoId = basename(parse_url($url, PHP_URL_PATH));
                                $embedUrl = "https://player.vimeo.com/video/" . $videoId;
                            else:
                                $embedUrl = $url;
                            endif;
                        ?>
                            <?php if (strpos($embedUrl, 'youtube.com') !== false || strpos($embedUrl, 'vimeo.com') !== false): ?>
                                <iframe src="<?= $embedUrl ?>" frameborder="0" allowfullscreen></iframe>
                            <?php else: ?>
                                <video controls>
                                    <source src="<?= $embedUrl ?>" type="video/mp4">
                                    Votre navigateur ne supporte pas la lecture vidéo.
                                </video>
                            <?php endif; ?>
                        <?php else: ?>
                            <p>URL invalide pour cette vidéo.</p>
                        <?php endif; ?>
                        <div class="actions">
                            <form method="GET" action="" style="display:inline;">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="id" value="<?= $video['id'] ?>">
                                <input type="hidden" name="type" value="video">
                                <button type="submit" class="action-btn edit">MODIFIER</button>
                            </form>
                            <form method="GET" action="" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette vidéo ?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $video['id'] ?>">
                                <input type="hidden" name="type" value="video">
                                <button type="submit" class="action-btn delete">SUPPRIMER</button>
                            </form>
                            <a href="video_details.php?id=<?= $video['id'] ?>" class="action-btn view">VOIR DÉTAILS</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucune vidéo publiée pour le moment.</p>
            <?php endif; ?>

            <!-- Published Text Guides -->
            <h2 class="section-title">GUIDES TEXTES PUBLIÉS</h2>
            <?php if (!empty($controller->guides)): ?>
                <?php foreach ($controller->guides as $guideText): ?>
                    <div class="guide-card">
                        <h3><?= htmlspecialchars($guideText['title']) ?></h3>
                        <p><strong>Auteur :</strong> <?= htmlspecialchars($guideText['author']) ?></p>
                        <p><?= nl2br(htmlspecialchars($guideText['content'])) ?></p>
                        <div class="actions">
                            <form method="GET" action="" style="display:inline;">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="id" value="<?= $guideText['id'] ?>">
                                <input type="hidden" name="type" value="text">
                                <button type="submit" class="action-btn edit">MODIFIER</button>
                            </form>
                            <form method="GET" action="" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce guide ?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $guideText['id'] ?>">
                                <input type="hidden" name="type" value="text">
                                <button type="submit" class="action-btn delete">SUPPRIMER</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun guide texte publié pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>