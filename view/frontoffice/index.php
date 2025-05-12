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

require_once '../../controller/StreamController.php';

try {
    $controller = new StreamController();
    $data = $controller->index();
    $videos = $data['videos'];
    $streams = $data['streams'];
    $search_query = isset($_GET['search_query']) ? trim($_GET['search_query']) : '';
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'title_asc';
} catch (Exception $e) {
    error_log("Streams Error: " . $e->getMessage(), 3, __DIR__ . '/error.log');
    $videos = [];
    $streams = [];
    $error_message = "Unable to load videos and streams. Please try again later.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GAMY | Gaming Videos & Live Streams</title>
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
            z-index: -1;
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

        h2 {
            font-family: 'Orbitron', sans-serif;
            color: var(--primary-red);
            text-shadow: var(--neon-glow);
            margin-bottom: 20px;
        }

        .search-results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .clear-search {
            color: var(--primary-red);
            text-decoration: none;
            font-size: 1rem;
        }

        .clear-search:hover {
            text-shadow: var(--neon-glow);
        }

        .search-form {
            margin-bottom: 20px;
        }

        .search-form .form-control {
            background-color: rgba(20, 20, 20, 0.8);
            border: 1px solid var(--primary-red);
            color: white;
            border-radius: 8px 0 0 8px;
        }

        .search-form .form-control:focus {
            background-color: rgba(20, 20, 20, 0.8);
            border-color: var(--primary-red);
            box-shadow: var(--neon-glow);
            color: white;
        }

        .search-form .btn-search {
            background-color: var(--primary-red);
            border: 1px solid var(--primary-red);
            border-radius: 0 8px 8px 0;
            color: white;
            transition: all 0.3s ease;
        }

        .search-form .btn-search:hover {
            background-color: var(--dark-red);
            box-shadow: var(--neon-glow);
        }

        .sorting-options {
            margin-bottom: 20px;
        }

        .sorting-options select {
            padding: 10px;
            background-color: rgba(20, 20, 20, 0.8);
            border: 1px solid var(--primary-red);
            border-radius: 8px;
            color: white;
            font-size: 1rem;
        }

        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .video-card {
            background-color: rgba(20, 20, 20, 0.8);
            border: 1px solid var(--primary-red);
            border-radius: 8px;
            overflow müd hidden;
            transition: transform 0.3s ease;
        }

        .video-card:hover {
            transform: scale(1.05);
            box-shadow: var(--neon-glow);
        }

        .video-embed {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
            height: 0;
        }

        .video-embed iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .video-info {
            padding: 15px;
        }

        .video-title {
            font-size: 1.2rem;
            margin: 0 0 10px;
            color: #ddd;
        }

        .live-badge {
            background-color: var(--primary-red);
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            margin-left: 10px;
        }

        .video-category {
            color: #aaa;
            font-size: 0.9rem;
        }

        .stream-link {
            text-decoration: none;
            color: inherit;
        }

        .no-results-section, .no-results {
            text-align: center;
            padding: 40px;
            background-color: rgba(20, 20, 20, 0.8);
            border: 1px solid var(--primary-red);
            border-radius: 8px;
            margin: 20px 0;
        }

        .no-results-section i, .no-results i {
            font-size: 2rem;
            color: var(--primary-red);
            margin-bottom: 10px;
        }

        .live-streams-heading {
            margin-top: 40px;
        }

        .alert-danger {
            background-color: rgba(255, 0, 0, 0.1);
            border-color: var(--primary-red);
            color: #ff9999;
        }
    </style>
</head>
<body>
    <?php require 'navbar.php' ?>

    <div class="container">
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['search_query'])): ?>
            <div class="search-results-header">
                <h2>Search results for "<?= htmlspecialchars($_GET['search_query']) ?>"</h2>
                <a href="index.php" class="clear-search"><i class="fas fa-times"></i> Clear search</a>
            </div>
        <?php endif; ?>

        <!-- Search Form -->
        <div class="search-form">
            <form method="get" action="index.php" class="input-group">
                <input type="text" name="search_query" class="form-control" placeholder="Search videos & streams..." value="<?= htmlspecialchars($search_query) ?>">
                <button type="submit" class="btn btn-search"><i class="fas fa-search"></i></button>
            </form>
        </div>

        <div class="sorting-options">
            <form method="get" action="index.php">
                <?php if (isset($_GET['search_query'])): ?>
                    <input type="hidden" name="search_query" value="<?= htmlspecialchars($_GET['search_query']) ?>">
                <?php endif; ?>
                <select name="sort" onchange="this.form.submit()">
                    <option value="title_asc" <?= ($sort === 'title_asc') ? 'selected' : '' ?>>A-Z</option>
                    <option value="title_desc" <?= ($sort === 'title_desc') ? 'selected' : '' ?>>Z-A</option>
                    <option value="newest" <?= ($sort === 'newest') ? 'selected' : '' ?>>Newest</option>
                    <option value="oldest" <?= ($sort === 'oldest') ? 'selected' : '' ?>>Oldest</option>
                </select>
            </form>
        </div>

        <?php if (!isset($_GET['search_query'])): ?>
            <h2>Gaming Videos</h2>
        <?php endif; ?>

        <div class="video-grid">
            <?php if (!empty($videos)): ?>
                <?php foreach ($videos as $video): ?>
                    <div class="video-card">
                        <div class="video-embed">
                            <?= htmlspecialchars_decode($video['embed_code']) ?>
                        </div>
                        <div class="video-info">
                            <h3 class="video-title"><?= htmlspecialchars($video['title']) ?></h3>
                            <span class="video-category"><?= htmlspecialchars($video['category_name'] ?? 'Uncategorized') ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php elseif (isset($_GET['search_query'])): ?>
                <div class="no-results-section">
                    <i class="fas fa-video-slash"></i>
                    <p>No videos found</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!isset($_GET['search_query'])): ?>
            <h2 class="live-streams-heading">Live Streams</h2>
        <?php endif; ?>

        <div class="video-grid">
            <?php if (!empty($streams)): ?>
                <?php foreach ($streams as $stream): ?>
                    <a href="live.php?id=<?= $stream['id'] ?>" class="stream-link">
                        <div class="video-card live-stream-card">
                            <div class="video-embed">
                                <iframe 
                                    width="100%" 
                                    height="200" 
                                    src="https://www.youtube.com/embed/<?= htmlspecialchars($stream['stream_id']) ?>" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen>
                                </iframe>
                            </div>
                            <div class="video-info">
                                <h3 class="video-title">
                                    <?= htmlspecialchars($stream['title']) ?>
                                    <span class="live-badge">LIVE</span>
                                </h3>
                                <span class="video-category"><?= htmlspecialchars($stream['category_name'] ?? 'Uncategorized') ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php elseif (isset($_GET['search_query'])): ?>
                <div class="no-results-section">
                    <i class="fas fa-broadcast-tower-slash"></i>
                    <p>No streams found</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if (isset($_GET['search_query']) && empty($videos) && empty($streams)): ?>
            <div class="no-results">
                <i class="fas fa-search"></i>
                <p>No results found for "<?= htmlspecialchars($_GET['search_query']) ?>"</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>