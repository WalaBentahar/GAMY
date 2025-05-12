<?php
session_start();
require_once '../../config.php';
require_once '../../model/VideoGuide.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID de la vidéo non spécifié.");
}

$videoId = intval($_GET['id']);

// Récupération des infos vidéo
$stmt = config::getConnexion()->prepare("SELECT * FROM videoguides WHERE id = ?");
$stmt->execute([$videoId]);
$video = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$video) {
    die("Vidéo non trouvée.");
}

$tags = VideoGuide::getTags($videoId);
$recommendations = VideoGuide::getRecommended($videoId, $tags, $video['author']);

// Ajouter une nouvelle description
$success = isset($_GET['success']);
$deleted = isset($_GET['deleted']);
$updated = isset($_GET['updated']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $desc = trim($_POST['description']);
    if (!empty($desc)) {
        $stmt = config::getConnexion()->prepare("INSERT INTO video_descriptions (video_id, description) VALUES (?, ?)");
        $stmt->execute([$videoId, $desc]);
        header("Location: admin_video_details.php?id=$videoId&success=1");
        exit;
    }
}

// Supprimer une description
if (isset($_GET['action'], $_GET['desc_id']) && $_GET['action'] === 'delete') {
    $descId = intval($_GET['desc_id']);
    $stmt = config::getConnexion()->prepare("DELETE FROM video_descriptions WHERE id = ? AND video_id = ?");
    $stmt->execute([$descId, $videoId]);
    header("Location: admin_video_details.php?id=$videoId&deleted=1");
    exit;
}

// Modifier une description
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $descId = intval($_POST['desc_id']);
    $desc = trim($_POST['description']);
    if (!empty($desc)) {
        $stmt = config::getConnexion()->prepare("UPDATE video_descriptions SET description = ? WHERE id = ? AND video_id = ?");
        $stmt->execute([$desc, $descId, $videoId]);
        header("Location: admin_video_details.php?id=$videoId&updated=1");
        exit;
    }
}

// Charger les descriptions
$stmt = config::getConnexion()->prepare("SELECT * FROM video_descriptions WHERE video_id = ?");
$stmt->execute([$videoId]);
$descriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Détails Vidéo (Admin) - GAMY</title>
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
            height: 400px;
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
        
        textarea {
            width: 100%;
            padding: 14px 20px;
            background-color: rgba(30, 30, 30, 0.8);
            border: 1px solid #333;
            color: #fff;
            font-family: 'Rajdhani', sans-serif;
            font-size: 16px;
            border-radius: 5px;
            transition: all 0.3s ease;
            resize: vertical;
            min-height: 100px;
        }
        
        textarea:focus {
            outline: none;
            border-color: var(--primary-red);
            box-shadow: 0 0 0 2px rgba(255, 42, 42, 0.3);
            background-color: rgba(40, 40, 40, 0.8);
        }
        
        .submit-btn {
            background: linear-gradient(135deg, var(--primary-red) 0%, --dark-red) 100%);
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
            margin: 5px;
        }
        
        .submit-btn:hover {
            background: linear-gradient(135deg, #ff3a3a 0%, #b00000 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 42, 42, 0.4);
        }
        
        .action-btn {
            background: linear-gradient(135deg, #dc3545 0%, #a71d2a 100%);
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
            margin: 5px;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
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
        
        .success-message {
            background-color: rgba(0, 255, 0, 0.15);
            border-left: 4px solid #00ff00;
            padding: 12px 20px;
            margin-bottom: 25px;
            color: #a0ffa0;
            font-weight: 500;
            border-radius: 0 4px 4px 0;
            text-align: center;
        }
        
        .recommendation-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .recommendation-list li {
            background-color: rgba(30, 30, 30, 0.8);
            border: 1px solid #333;
            border-radius: 5px;
            padding: 10px 15px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }
        
        .recommendation-list li:hover {
            border-color: var(--primary-red);
            box-shadow: 0 0 10px rgba(255, 42, 42, 0.3);
        }
        
        .recommendation-list a {
            color: var(--primary-red);
            text-decoration: none;
            font-weight: 600;
        }
        
        .recommendation-list a:hover {
            text-shadow: 0 0 5px var(--primary-red);
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
            
            .submit-btn, .action-btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <?php require 'dashnav.php'; ?>

    <div class="container py-5">
        <div class="video-container">
            <h1 class="section-title"><?php echo htmlspecialchars($video['title']); ?></h1>
            
            <div class="video-info">
                <p><strong>Auteur :</strong> <?php echo htmlspecialchars($video['author']); ?></p>
                <p><strong>Vues :</strong> <?php echo $video['views']; ?></p>
            </div>

            <div class="video-player">
                <?php
                $url = htmlspecialchars($video['content'] ?? '');
                $embedUrl = getEmbedUrl($url);
                if ($embedUrl):
                ?>
                    <iframe src="<?php echo $embedUrl; ?>" frameborder="0" allowfullscreen></iframe>
                <?php elseif (filter_var($url, FILTER_VALIDATE_URL)): ?>
                    <video controls>
                        <source src="<?php echo $url; ?>" type="video/mp4">
                        Votre navigateur ne supporte pas la lecture vidéo.
                    </video>
                <?php else: ?>
                    <div class="error-message">
                        <p>URL vidéo invalide</p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($success): ?>
                <div class="success-message">
                    <p>Description ajoutée avec succès.</p>
                </div>
            <?php elseif ($deleted): ?>
                <div class="success-message">
                    <p>Description supprimée avec succès.</p>
                </div>
            <?php elseif ($updated): ?>
                <div class="success-message">
                    <p>Description mise à jour avec succès.</p>
                </div>
            <?php endif; ?>

            <h2 class="section-title">AJOUTER UNE DESCRIPTION</h2>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label for="description" class="form-label">DESCRIPTION</label>
                    <textarea id="description" name="description" required placeholder="Ajouter une description..."></textarea>
                </div>
                <button type="submit" class="submit-btn">AJOUTER</button>
            </form>

            <h2 class="section-title">DESCRIPTIONS EXISTANTES</h2>
            <?php if (!empty($descriptions)): ?>
                <?php foreach ($descriptions as $desc): ?>
                    <div class="description-box">
                        <form method="POST">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="desc_id" value="<?php echo $desc['id']; ?>">
                            <div class="form-group">
                                <textarea name="description" required><?php echo htmlspecialchars($desc['description']); ?></textarea>
                            </div>
                            <button type="submit" class="submit-btn">METTRE À JOUR</button>
                            <a href="admin_video_details.php?id=<?php echo $videoId; ?>&action=delete&desc_id=<?php echo $desc['id']; ?>" class="action-btn" onclick="return confirm('Supprimer cette description ?')">SUPPRIMER</a>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="description-box">
                    <p>Aucune description pour cette vidéo.</p>
                </div>
            <?php endif; ?>

            <h2 class="section-title">RECOMMANDATIONS</h2>
            <?php if (!empty($recommendations)): ?>
                <ul class="recommendation-list">
                    <?php foreach ($recommendations as $rec): ?>
                        <li>
                            <a href="admin_video_details.php?id=<?php echo $rec['id']; ?>"><?php echo htmlspecialchars($rec['title']); ?></a>
                            par <?php echo htmlspecialchars($rec['author']); ?> (<?php echo $rec['views']; ?> vues)
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Aucune recommandation disponible.</p>
            <?php endif; ?>

            <a href="dashboard_guide.php" class="back-link">← Retour au Tableau de Bord</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>