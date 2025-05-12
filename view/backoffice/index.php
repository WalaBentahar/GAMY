<?php
require_once  '../../controller/AdminController.php';
$controller = new AdminController();
$data = $controller->manageStreams();
$streams = $data['streams'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GAMY | Admin - Manage Streams</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #ff3333;
            --primary-dark: #cc0000;
            --primary-light: #ff6666;
            --accent-color: #ff1a1a;
            --danger-color: #ff4d4d;
            --success-color: #4ade80;
            --warning-color: #fbbf24;
            --info-color: #38bdf8;
            --dark-bg: #121212;
            --dark-surface: #1e1e1e;
            --dark-border: #333333;
            --text-primary: #f8f9fa;
            --text-secondary: #adb5bd;
            --sidebar-width: 280px;
            --navbar-height: 70px;
            --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--dark-bg);
            color: var(--text-primary);
            overflow-x: hidden;
        }

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

        .main-content {
            margin-left: var(--sidebar-width);
            padding-top: calc(var(--navbar-height) + 20px);
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
        }
        
        .card-title {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 0;
        }
        
        .card-body {
            padding: 20px;
        }

        .form-control, .form-select {
            background-color: var(--dark-surface);
            border: 1px solid var(--dark-border);
            color: var(--text-primary);
            border-radius: 8px;
            padding: 12px 15px;
        }
        
        .form-control:focus, .form-select:focus {
            background-color: var(--dark-surface);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(255, 51, 51, 0.15);
            color: var(--text-primary);
        }
        
        .form-control::placeholder {
            color: var(--text-secondary);
        }

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
        }
        
        .btn-danger:hover, .btn-danger:focus {
            background-color: #e53e3e;
            border-color: #e53e3e;
        }

        .admin-videos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .admin-video-card {
            background: var(--dark-surface);
            border: 1px solid var(--dark-border);
            border-radius: 12px;
            overflow: hidden;
        }

        .video-embed iframe {
            width: 100%;
            height: 200px;
        }

        .video-info {
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .video-title {
            color: var(--primary-color);
            margin: 0;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .video-category {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .alert {
            background-color: rgba(255, 0, 0, 0.1);
            border-color: var(--danger-color);
            color: var(--text-primary);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background-color: rgba(0, 255, 0, 0.1);
            border-color: var(--success-color);
            color: var(--text-primary);
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
        }
    </style>
</head>
<body>
    <!-- Overlay for mobile -->
    <div class="overlay" id="overlay"></div>

    <?php require __DIR__ . '/dashnav.php'; ?>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="container-fluid py-4">
            <!-- Alerts -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php elseif (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Manage Streams</h3>
                    <a href="/wala_project/project/view/backoffice/add.php" class="btn btn-primary">
                        <i class="mdi mdi-plus"></i> Add New Stream
                    </a>
                </div>
                <div class="card-body">
                    <div class="admin-videos-grid">
                        <?php foreach ($streams as $stream): ?>
                            <div class="admin-video-card">
                                <div class="video-embed">
                                    <iframe 
                                        width="100%" 
                                        height="200" 
                                        src="https://www.youtube.com/embed/<?= htmlspecialchars($stream['stream_id']) ?>" 
                                        frameborder="0" 
                                        allowfullscreen>
                                    </iframe>
                                </div>
                                <div class="video-info">
                                    <div>
                                        <h3 class="video-title"><?= htmlspecialchars($stream['title']) ?></h3>
                                        <span class="video-category"><?= htmlspecialchars($stream['category_name'] ?? 'Unknown') ?></span>
                                    </div>
                                    <form method="POST" action="/wala_project/project/view/backoffice/manage_streams.php">
                                        <input type="hidden" name="id" value="<?= $stream['id'] ?>">
                                        <button type="submit" name="delete" class="btn btn-danger btn-sm">
                                            <i class="mdi mdi-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('overlay').classList.toggle('show');
            document.getElementById('mainContent').classList.toggle('main-content-expanded');
            document.querySelector('.navbar').classList.toggle('navbar-expanded');
        });

        document.getElementById('overlay').addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('show');
            document.getElementById('overlay').classList.remove('show');
            document.getElementById('mainContent').classList.remove('main-content-expanded');
            document.querySelector('.navbar').classList.remove('navbar-expanded');
        });
    </script>
</body>
</html>
?>