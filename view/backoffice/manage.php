<?php
session_start();
require_once '../../config.php';
require_once '../../model/User.php';
require_once '../../controller/UserController.php';
require_once '../../controller/PostsController.php';
require_once '../../controller/CommentsController.php';
require_once '../../controller/LikesController.php';

// Suppress notices for AJAX responses
error_reporting(E_ALL & ~E_NOTICE);

// Check if user is logged in and is admin
$pdo = config::getConnexion();
$userController = new UserController($pdo);
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user = $userController->getUserById($_SESSION['user_id']);
if ($user->getRole() !== 'ADMIN') {
    $_SESSION['error'] = 'Accès non autorisé.';
    header("Location: forum.php");
    exit();
}

// Initialize controllers
$postsController = new PostsController();
$commentsController = new CommentsController();
$likesController = new LikesController();

// Handle post update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update-post') {
    try {
        $postsController->updatePost();
        // Success/error messages are set in PostsController
    } catch (Exception $e) {
        $_SESSION['error'] = 'Erreur lors de la mise à jour : ' . $e->getMessage();
    }
    header("Location: manage.php");
    exit();
}

// Handle post deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete-post') {
    try {
        $postsController->deletePost();
        // Success/error messages are set in PostsController
    } catch (Exception $e) {
        $_SESSION['error'] = 'Erreur lors de la suppression : ' . $e->getMessage();
    }
    header("Location: manage.php");
    exit();
}

// Fetch stats
try {
    // Post counts by category
    $stmt = $pdo->query("SELECT category, COUNT(*) as count FROM posts GROUP BY category");
    $postCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $postStats = ['FPS' => 0, 'RPG' => 0, 'MOBA' => 0, 'Survie' => 0];
    foreach ($postCounts as $row) {
        if (array_key_exists($row['category'], $postStats)) {
            $postStats[$row['category']] = $row['count'];
        }
    }
    $totalPosts = array_sum(array_values($postStats));

    // Total comments
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM comments");
    $totalComments = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Total users
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Total likes
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM likes");
    $totalLikes = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Fetch all posts with comments
    $posts = $pdo->query("SELECT p.*, u.nom as author FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

    // Fetch history
    $history = $pdo->query("SELECT h.*, u.nom as user_name FROM historique h JOIN users u ON h.user_id = u.id ORDER BY h.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = 'Erreur de base de données : ' . $e->getMessage();
    $postStats = ['FPS' => 0, 'RPG' => 0, 'MOBA' => 0, 'Survie' => 0];
    $totalPosts = 0;
    $totalComments = 0;
    $totalUsers = 0;
    $totalLikes = 0;
    $posts = [];
    $history = [];
}

// Handle export requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['export'])) {
    $action = $_POST['export'];
    
    // Prepare stats data
    $statsData = [
        ['Stat', 'Value'],
        ['Total Posts', $totalPosts],
        ['Posts FPS', $postStats['FPS']],
        ['Posts RPG', $postStats['RPG']],
        ['Posts MOBA', $postStats['MOBA']],
        ['Posts Survie', $postStats['Survie']],
        ['Total Comments', $totalComments],
        ['Total Users', $totalUsers],
        ['Total Likes', $totalLikes]
    ];

    if ($action === 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="forum_stats_' . date('Ymd_His') . '.csv"');
        $output = fopen('php://output', 'w');
        foreach ($statsData as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    } elseif ($action === 'pdf') {
        require_once '../../vendor/tecnickcom/tcpdf/tcpdf.php';
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('GAMY Forum');
        $pdf->SetAuthor($user->getNom());
        $pdf->SetTitle('Forum Statistics');
        $pdf->SetSubject('Forum Stats Export');
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'GAMY Forum Statistics', 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', '', 12);
        $html = '<table border="1" cellpadding="5" style="background-color:#121212;color:#f8f9fa;">
                    <tr style="background-color:#ff3333;color:#000000;">
                        <th>Stat</th>
                        <th>Value</th>
                    </tr>';
        foreach (array_slice($statsData, 1) as $row) {
            $html .= '<tr><td>' . htmlspecialchars($row[0]) . '</td><td>' . htmlspecialchars($row[1]) . '</td></tr>';
        }
        $html .= '</table>';
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('forum_stats_' . date('Ymd_His') . '.pdf', 'D');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GAMY | Backoffice Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Orbitron:wght@700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #ff3333; /* Vibrant red */
            --primary-dark: #cc0000; /* Darker red */
            --primary-light: #ff6666; /* Lighter red */
            --accent-color: #ff1a1a; /* Bright red for accents */
            --danger-color: #ff4d4d;
            --success-color: #4ade80;
            --warning-color: #fbbf24;
            --info-color: #38bdf8;
            --dark-bg: #121212; /* True dark background */
            --dark-surface: #1e1e1e; /* Card background */
            --dark-border: #333333; /* Border color */
            --text-primary: #f8f9fa; /* Primary text */
            --text-secondary: #adb5bd; /* Secondary text */
            --sidebar-width: 280px;
            --navbar-height: 70px;
            --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
        }

        body {
            font-family: 'Rajdhani', sans-serif;
            background-color: var(--dark-bg);
            color: var(--text-primary);
            overflow-x: hidden;
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: var(--dark-bg);
        }
        ::-webkit-scrollbar-thumb {
            background: var(--primary-dark);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-color);
        }

        /* Sidebar styling */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: linear-gradient(135deg, #1e1e1e, #141414);
            color: white;
            padding-top: var(--navbar-height);
            box-shadow: var(--box-shadow);
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-right: 1px solid var(--dark-border);
        }
        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(180deg, var(--primary-dark) 0%, transparent 100%);
            opacity: 0.05;
            pointer-events: none;
        }
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .sidebar-header h4 {
            color: var(--primary-color);
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
            margin-bottom: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .sidebar-collapsed {
            left: calc(-1 * var(--sidebar-width));
        }
        .sidebar .nav-link {
            color: var(--text-secondary);
            padding: 12px 20px;
            margin: 8px 15px;
            border-radius: 8px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            font-family: 'Rajdhani', sans-serif;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(255, 51, 51, 0.1);
            color: var(--primary-light);
            transform: translateX(5px);
        }
        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 51, 51, 0.35);
        }
        .sidebar .nav-link i {
            margin-right: 10px;
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
        }
        .sidebar-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        /* Navbar styling */
        .navbar {
            height: var(--navbar-height);
            background-color: var(--dark-surface);
            box-shadow: var(--box-shadow);
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-width);
            z-index: 1030;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-bottom: 1px solid var(--dark-border);
        }
        .navbar-expanded {
            left: 0;
        }
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color);
            font-size: 1.5rem;
            letter-spacing: 0.5px;
            font-family: 'Orbitron', sans-serif;
        }
        .navbar .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            border: none;
            color: var(--text-primary);
            padding: 10px 15px;
            border-radius: 8px;
        }
        .navbar .form-control::placeholder {
            color: var(--text-secondary);
        }
        .navbar .form-control:focus {
            background-color: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 0 2px rgba(255, 51, 51, 0.25);
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid var(--primary-color);
            box-shadow: 0 2px 8px rgba(255, 51, 51, 0.3);
        }
        .dropdown-menu {
            background-color: var(--dark-surface);
            border: 1px solid var(--dark-border);
            box-shadow: var(--box-shadow);
            border-radius: 8px;
            overflow: hidden;
        }
        .dropdown-item {
            color: var(--text-primary);
            padding: 10px 15px;
        }
        .dropdown-item:hover, .dropdown-item:focus {
            background-color: rgba(255, 51, 51, 0.1);
            color: var(--primary-light);
        }
        .dropdown-divider {
            border-top: 1px solid var(--dark-border);
        }

        /* Main content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: calc(var(--navbar-height) + 20px) 30px 30px 30px;
            min-height: 100vh;
            background-color: var(--dark-bg);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        .main-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at top right, rgba(255, 51, 51, 0.02), transparent 70%);
            pointer-events: none;
        }
        .main-content-expanded {
            margin-left: 0;
        }

        /* Section title */
        .section-title {
            color: var(--text-primary);
            text-transform: uppercase;
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
            letter-spacing: 2px;
            font-size: 1.8rem;
            margin-bottom: 20px;
            text-align: center;
            text-shadow: 0 0 10px rgba(255, 51, 51, 0.5);
            position: relative;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--primary-color), transparent);
            box-shadow: 0 0 10px var(--primary-color);
        }

        /* Cards */
        .card {
            background-color: var(--dark-surface);
            border: none;
            border-radius: 12px;
            box-shadow: var(--box-shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 24px;
            overflow: hidden;
            border: 1px solid var(--dark-border);
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        .card-header {
            background-color: var(--dark-surface);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-weight: 600;
            color: var(--text-primary);
            padding: 16px 20px;
            font-family: 'Orbitron', sans-serif;
        }
        .card-title {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 0;
        }
        .card-body {
            padding: 20px;
            text-align: center;
        }

        /* Stats cards */
        .stats-card {
            position: relative;
            overflow: hidden;
        }
        .stats-card::after {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 4px;
            height: 100%;
            background-color: var(--primary-color);
            border-radius: 4px 0 0 4px;
        }
        .stats-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--primary-color);
            font-family: 'Orbitron', sans-serif;
        }
        .stats-card .value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            text-shadow: 0 0 5px rgba(255, 51, 51, 0.3);
            font-family: 'Rajdhani', sans-serif;
        }

        /* Alerts */
        .error-message {
            background-color: rgba(255, 77, 77, 0.15);
            border-left: 4px solid var(--danger-color);
            padding: 12px 20px;
            margin-bottom: 20px;
            color: var(--text-primary);
            font-weight: 500;
            border-radius: 0 8px 8px 0;
            text-align: center;
            font-family: 'Rajdhani', sans-serif;
        }
        .success-message {
            background-color: rgba(74, 222, 128, 0.15);
            border-left: 4px solid var(--success-color);
            padding: 12px 20px;
            margin-bottom: 20px;
            color: var(--text-primary);
            font-weight: 500;
            border-radius: 0 8px 8px 0;
            text-align: center;
            font-family: 'Rajdhani', sans-serif;
        }

        /* Table styling */
        .table {
            color: var(--text-primary);
            margin-bottom: 0;
            background-color: var(--dark-surface);
        }
        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--text-primary);
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
            border-bottom: 1px solid var(--dark-border);
            background-color: var(--primary-color);
            font-family: 'Orbitron', sans-serif;
        }
        .table td {
            vertical-align: middle;
            border-color: var(--dark-border);
            padding: 12px 16px;
        }
        .table tbody tr {
            transition: all 0.2s;
        }
        .table tbody tr:hover {
            background-color: rgba(255, 51, 51, 0.05);
        }
        .table a {
            color: var(--primary-light);
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        .table a:hover {
            color: var(--primary-color);
            text-shadow: 0 0 5px rgba(255, 51, 51, 0.5);
        }
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--dark-border);
        }

        /* Comments list */
        .comment-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .comment-list li {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--dark-border);
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 10px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            color: var(--text-primary);
        }
        .comment-list li:hover {
            border-color: var(--primary-color);
            box-shadow: 0 0 10px rgba(255, 51, 51, 0.3);
        }
        .comment-list li strong {
            color: var(--primary-light);
            font-family: 'Rajdhani', sans-serif;
        }
        .comment-list li small {
            color: var(--text-secondary);
            font-size: 0.8rem;
        }

        /* Buttons */
        .btn {
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-family: 'Rajdhani', sans-serif;
        }
        .btn i {
            font-size: 1.1em;
        }
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 51, 51, 0.25);
        }
        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            box-shadow: 0 6px 20px rgba(255, 51, 51, 0.35);
            transform: translateY(-2px);
        }
        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
            color: white;
        }
        .btn-danger:hover, .btn-danger:focus {
            background-color: #e53e3e;
            border-color: #e53e3e;
            box-shadow: 0 6px 20px rgba(255, 77, 77, 0.35);
            transform: translateY(-2px);
        }

        /* Modal styling */
        .modal-content {
            background-color: var(--dark-surface);
            border: 1px solid var(--dark-border);
            border-radius: 12px;
            box-shadow: var(--box-shadow);
            color: var(--text-primary);
        }
        .modal-header {
            background-color: var(--dark-surface);
            border-bottom: 1px solid var(--dark-border);
            padding: 16px 20px;
        }
        .modal-title {
            color: var(--primary-color);
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
        }
        .modal-body {
            padding: 20px;
        }
        .modal-footer {
            border-top: 1px solid var(--dark-border);
            padding: 16px 20px;
        }
        .form-control, .form-select {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--dark-border);
            color: var(--text-primary);
            border-radius: 8px;
            padding: 12px 15px;
            font-family: 'Rajdhani', sans-serif;
        }
        .form-control:focus, .form-select:focus {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(255, 51, 51, 0.15);
            color: var(--text-primary);
        }
        .form-label {
            color: var(--text-primary);
            font-weight: 500;
            margin-bottom: 8px;
            font-family: 'Rajdhani', sans-serif;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .sidebar {
                left: calc(-1 * var(--sidebar-width));
                z-index: 1050;
            }
            .sidebar.show {
                left: 0;
            }
            .navbar {
                left: 0;
            }
            .main-content {
                margin-left: 0;
            }
            .overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1040;
                display: none;
            }
            .overlay.show {
                display: block;
            }
        }
        @media (max-width: 576px) {
            .main-content {
                padding: calc(var(--navbar-height) + 15px) 15px 15px 15px;
            }
            .stats-card h3 {
                font-size: 1.2rem;
            }
            .stats-card .value {
                font-size: 1.5rem;
            }
            .section-title {
                font-size: 1.5rem;
            }
            .navbar-brand {
                font-size: 1.2rem;
            }
            .table th, .table td {
                font-size: 0.8rem;
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <?php require_once 'dashnav.php'; ?>

    <div class="main-content">
        <!-- Alerts -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <!-- Stats -->
        <h2 class="section-title">Statistiques</h2>
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="card stats-card">
                    <div class="card-body">
                        <h3>Total Posts</h3>
                        <div class="value"><?php echo $totalPosts; ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card stats-card">
                    <div class="card-body">
                        <h3>Posts FPS</h3>
                        <div class="value"><?php echo $postStats['FPS']; ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card stats-card">
                    <div class="card-body">
                        <h3>Posts RPG</h3>
                        <div class="value"><?php echo $postStats['RPG']; ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card stats-card">
                    <div class="card-body">
                        <h3>Posts MOBA</h3>
                        <div class="value"><?php echo $postStats['MOBA']; ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card stats-card">
                    <div class="card-body">
                        <h3>Posts Survie</h3>
                        <div class="value"><?php echo $postStats['Survie']; ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card stats-card">
                    <div class="card-body">
                        <h3>Total Comments</h3>
                        <div class="value"><?php echo $totalComments; ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card stats-card">
                    <div class="card-body">
                        <h3>Total Users</h3>
                        <div class="value"><?php echo $totalUsers; ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card stats-card">
                    <div class="card-body">
                        <h3>Total Likes</h3>
                        <div class="value"><?php echo $totalLikes; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Buttons -->
        <div class="text-center mb-4">
            <form method="POST" action="" class="d-inline">
                <input type="hidden" name="export" value="csv">
                <button type="submit" class="btn btn-primary"><i class="fas fa-file-csv"></i> Export CSV</button>
            </form>
            <form method="POST" action="" class="d-inline">
                <input type="hidden" name="export" value="pdf">
                <button type="submit" class="btn btn-primary"><i class="fas fa-file-pdf"></i> Export PDF</button>
            </form>
        </div>

        <!-- History Table -->
        <h2 class="section-title">Historique des Actions</h2>
        <?php if (empty($history)): ?>
            <div class="error-message">Aucun historique trouvé.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Utilisateur</th>
                            <th>Action</th>
                            <th>Contenu ID</th>
                            <th>Description</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $record): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($record['id']); ?></td>
                                <td><?php echo htmlspecialchars($record['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($record['action']); ?></td>
                                <td><?php echo htmlspecialchars($record['content_id']); ?></td>
                                <td><?php echo htmlspecialchars($record['description']); ?></td>
                                <td><?php echo htmlspecialchars($record['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- Posts Table -->
        <h2 class="section-title">Tous les Posts</h2>
        <?php if (empty($posts)): ?>
            <div class="error-message">Aucun post trouvé.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Auteur</th>
                            <th>Catégorie</th>
                            <th>Créé le</th>
                            <th>Photo URL</th>
                            <th>Description</th>
                            <th>Commentaires</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $post): ?>
                            <?php
                            $comments = $commentsController->getCommentsByPost($post['id']);
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($post['id']); ?></td>
                                <td><?php echo htmlspecialchars($post['title']); ?></td>
                                <td><?php echo htmlspecialchars($post['author']); ?></td>
                                <td><?php echo htmlspecialchars($post['category']); ?></td>
                                <td><?php echo htmlspecialchars($post['created_at']); ?></td>
                                <td>
                                    <?php if ($post['photo_url']): ?>
                                        <a href="<?php echo htmlspecialchars($post['photo_url']); ?>" target="_blank">Voir</a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($post['description'] ?? '-'); ?></td>
                                <td>
                                    <?php if (empty($comments)): ?>
                                        Aucun commentaire
                                    <?php else: ?>
                                        <ul class="comment-list">
                                            <?php foreach ($comments as $comment): ?>
                                                <li>
                                                    <strong><?php echo htmlspecialchars($comment['author']); ?>:</strong>
                                                    <?php echo htmlspecialchars($comment['content']); ?>
                                                    <br><small><?php echo htmlspecialchars($comment['created_at']); ?></small>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateModal<?php echo $post['id']; ?>">
                                        <i class="fas fa-edit"></i> Modifier
                                    </button>
                                    <form method="POST" action="" class="d-inline" onsubmit="return confirm('Voulez-vous vraiment supprimer ce post ?');">
                                        <input type="hidden" name="action" value="delete-post">
                                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                        <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                            <!-- Update Modal -->
                            <div class="modal fade" id="updateModal<?php echo $post['id']; ?>" tabindex="-1" aria-labelledby="updateModalLabel<?php echo $post['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="updateModalLabel<?php echo $post['id']; ?>">Modifier le Post</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form method="POST" action="">
                                            <div class="modal-body">
                                                <input type="hidden" name="action" value="update-post">
                                                <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                                                <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                                                <div class="mb-3">
                                                    <label for="title<?php echo $post['id']; ?>" class="form-label">Titre</label>
                                                    <input type="text" class="form-control" id="title<?php echo $post['id']; ?>" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="author<?php echo $post['id']; ?>" class="form-label">Auteur</label>
                                                    <input type="text" class="form-control" id="author<?php echo $post['id']; ?>" name="author" value="<?php echo htmlspecialchars($post['author']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="category<?php echo $post['id']; ?>" class="form-label">Catégorie</label>
                                                    <select class="form-select" id="category<?php echo $post['id']; ?>" name="category" required>
                                                        <option value="FPS" <?php echo $post['category'] === 'FPS' ? 'selected' : ''; ?>>FPS</option>
                                                        <option value="RPG" <?php echo $post['category'] === 'RPG' ? 'selected' : ''; ?>>RPG</option>
                                                        <option value="MOBA" <?php echo $post['category'] === 'MOBA' ? 'selected' : ''; ?>>MOBA</option>
                                                        <option value="Survie" <?php echo $post['category'] === 'Survie' ? 'selected' : ''; ?>>Survie</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="photo_url<?php echo $post['id']; ?>" class="form-label">Photo URL</label>
                                                    <input type="url" class="form-control" id="photo_url<?php echo $post['id']; ?>" name="photo_url" value="<?php echo htmlspecialchars($post['photo_url'] ?? ''); ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="description<?php echo $post['id']; ?>" class="form-label">Description</label>
                                                    <textarea class="form-control" id="description<?php echo $post['id']; ?>" name="description" rows="4"><?php echo htmlspecialchars($post['description'] ?? ''); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>