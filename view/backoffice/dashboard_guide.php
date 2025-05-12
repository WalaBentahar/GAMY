
<?php
session_start();
require_once '../../controller/AdminGuideController.php';

$controller = new AdminGuideController();
$controller->handleRequest();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
    }
    
    .card-title {
        color: var(--primary-color);
        font-weight: 700;
        margin-bottom: 0;
    }
    
    .card-body {
        padding: 20px;
    }

    /* Stats cards */
    .stats-card {
        border-radius: 12px;
        position: relative;
        overflow: hidden;
        color:white !important;
    }
    
    .stats-card::after {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        width: 4px;
        height: 100%;
        border-radius: 4px 0 0 4px;
    }

    .stats-card.total-users::after {
        background-color: var(--primary-color);
    }

    .stats-card.active-users::after {
        background-color: var(--success-color);
    }

    .stats-card.banned-users::after {
        background-color: var(--danger-color);
    }

    .stats-card.admin-users::after {
        background-color: var(--warning-color);
    }

    .stats-card h3 {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 5px;
    }
    
    .stats-card h6 {
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.8;
    }

    .stats-icon {
        font-size: 2.8rem;
        opacity: 0.8;
        color: var(--primary-color);
        filter: drop-shadow(0 4px 6px rgba(255, 51, 51, 0.3));
    }

    /* Table styling */
    .table {
        color: var(--text-primary);
        margin-bottom: 0;
    }

    .table th {
        border-top: none;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 1px;
        border-bottom: 1px solid var(--dark-border);
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
        background-color: rgba(255, 255, 255, 0.05);
    }
    
    .table a {
        color: var(--text-primary);
        text-decoration: none;
        display: flex;
        align-items: center;
    }
    
    .table a:hover {
        color: var(--primary-color);
    }
    
    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }

    .badge {
        padding: 6px 10px;
        font-weight: 500;
        font-size: 0.75rem;
        border-radius: 6px;
    }
    
    .bg-primary {
        background-color: var(--primary-color) !important;
    }
    
    .bg-success {
        background-color: var(--success-color) !important;
    }
    
    .bg-danger {
        background-color: var(--danger-color) !important;
    }
    
    .bg-warning {
        background-color: var(--warning-color) !important;
    }

    /* Form controls */
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
    
    .btn-secondary {
        background-color: rgba(255, 255, 255, 0.1);
        color: var(--text-primary);
    }
    
    .btn-secondary:hover, .btn-secondary:focus {
        background-color: rgba(255, 255, 255, 0.15);
        color: white;
    }
    
    .btn-danger {
        background-color: var(--danger-color);
        border-color: var(--danger-color);
    }
    
    .btn-danger:hover, .btn-danger:focus {
        background-color: #e53e3e;
        border-color: #e53e3e;
    }
    
    .btn-success {
        background-color: var(--success-color);
        border-color: var(--success-color);
    }
    
    .btn-success:hover, .btn-success:focus {
        background-color: #38a169;
        border-color: #38a169;
    }
    
    .btn-warning {
        background-color: var(--warning-color);
        border-color: var(--warning-color);
        color: #1a202c;
    }
    
    .btn-warning:hover, .btn-warning:focus {
        background-color: #d69e2e;
        border-color: #d69e2e;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 0.875rem;
    }

    /* Toggle button */
    .sidebar-toggle {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.2s;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
    }
    
    .sidebar-toggle:hover {
        background-color: rgba(255, 51, 51, 0.1);
        color: var(--primary-color);
    }

    /* Chart container */
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    
    /* Search form styling */
    .search-form {
        background-color: var(--dark-surface);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
        border: 1px solid var(--dark-border);
    }
    
    /* Animation */
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(255, 51, 51, 0.4);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(255, 51, 51, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(255, 51, 51, 0);
        }
    }
    
    .pulse {
        animation: pulse 2s infinite;
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
        .stats-card h3 {
            font-size: 24px;
        }
        
        .stats-icon {
            font-size: 2rem;
        }
        
        .navbar-brand {
            font-size: 1.2rem;
        }
    }
</style>
<script>
  window.addEventListener("DOMContentLoaded", () => {
    lucide.createIcons();
  });
</script>
 <style>
    /* Modal Styling */
.modal-content {
    background-color: var(--dark-surface);
    border: 1px solid var(--primary-color);
    border-radius: 12px;
    box-shadow: var(--box-shadow);
}

.modal-header, .modal-footer {
    border-color: var(--dark-border);
}

.modal-title {
    color: var(--primary-color);
    font-weight: 700;
}

.btn-close-white {
    filter: invert(1);
}

.modal-body h3 {
    color: var(--primary-light);
    font-weight: 600;
}

.modal-body p {
    color: var(--text-primary);
}

.modal-body iframe, .modal-body video {
    border-radius: 8px;
}
 </style>
</head>
<body>
    <!-- Overlay for mobile -->
    <div class="overlay" id="overlay"></div>
  <?php require 'dashnav.php'?>

   <div class="main-content bg-dark text-white min-vh-100 py-4" id="mainContent">
    <div class="container">
        <h1 class="mb-4">TABLEAU DE BORD ADMIN</h1>

        <?php if ($controller->success): ?>
            <div class="alert alert-success">Action réalisée avec succès.</div>
        <?php elseif ($controller->deleted): ?>
            <div class="alert alert-success">Supprimé avec succès.</div>
        <?php endif; ?>

        <!-- Formulaire d'ajout / modification -->
        <h2 class="mt-5"><?php echo $controller->editGuide ? 'MODIFIER LE GUIDE' : 'AJOUTER UN GUIDE'; ?></h2>
        <form method="POST" action="dashboard_guide.php?action=<?php echo $controller->editGuide ? 'edit' : 'add'; ?>" class="bg-secondary p-4 rounded shadow">
            <?php if ($controller->editGuide): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($controller->editGuide['id']); ?>">
                <input type="hidden" name="type" value="<?php echo htmlspecialchars($_GET['type'] ?? 'text'); ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label for="title" class="form-label">TITRE</label>
                <input type="text" id="title" name="title" class="form-control" required value="<?php echo htmlspecialchars($controller->editGuide['title'] ?? ''); ?>">
            </div>

            <div class="mb-3">
                <label for="author" class="form-label">AUTEUR</label>
                <input type="text" id="author" name="author" class="form-control" required value="<?php echo htmlspecialchars($controller->editGuide['author'] ?? ''); ?>">
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">TYPE</label>
                <select id="type" name="type" class="form-select">
                    <option value="text" <?php echo (!empty($_GET['type']) && $_GET['type'] === 'text') || empty($controller->editGuide) ? 'selected' : ''; ?>>Texte</option>
                    <option value="video" <?php echo (!empty($_GET['type']) && $_GET['type'] === 'video') ? 'selected' : ''; ?>>Vidéo</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="content" class="form-label">CONTENU</label>
                <textarea id="content" name="content" class="form-control" required rows="5"><?php echo htmlspecialchars($controller->editGuide['content'] ?? ''); ?></textarea>
            </div>

            <button type="submit" class="btn btn-success">
                <?php echo $controller->editGuide ? 'METTRE À JOUR' : 'AJOUTER'; ?>
            </button>
        </form>

        <!-- Formulaire de recherche -->
        <h2 class="mt-5">RECHERCHER</h2>
        <form method="GET" action="dashboard_guide.php" class="row g-3 align-items-end bg-secondary p-4 rounded shadow">
            <input type="hidden" name="action" value="search">
            <div class="col-md-5">
                <input type="text" name="query" class="form-control" placeholder="Titre ou Auteur..." value="<?php echo htmlspecialchars($_GET['query'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <select name="filter" class="form-select">
                    <option value="all" <?php echo ($_GET['filter'] ?? '') === 'all' ? 'selected' : ''; ?>>Tous</option>
                    <option value="text" <?php echo ($_GET['filter'] ?? '') === 'text' ? 'selected' : ''; ?>>Texte</option>
                    <option value="video" <?php echo ($_GET['filter'] ?? '') === 'video' ? 'selected' : ''; ?>>Vidéo</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i>RECHERCHER</button>
            </div>
        </form>

        <!-- Guides Textuels -->
        <h2 class="mt-5">GUIDES TEXTUELS</h2>
        <?php if (!empty($controller->guides)): ?>
            <div class="table-responsive">
                <table class="table table-dark table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Auteur</th>
                            <th>Contenu</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($controller->guides as $guide): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($guide['id']); ?></td>
                                <td><?php echo htmlspecialchars($guide['title']); ?></td>
                                <td><?php echo htmlspecialchars($guide['author']); ?></td>
                                <td><?php echo htmlspecialchars(substr($guide['content'], 0, 50)); ?>...</td>
                                <td>
                                    <a href="dashboard_guide.php?action=edit&id=<?php echo $guide['id']; ?>&type=text" class="btn btn-warning btn-sm">MODIFIER</a>
                                    <a href="dashboard_guide.php?action=delete&id=<?php echo $guide['id']; ?>&type=text" class="btn btn-danger btn-sm" onclick="return confirm('Confirmer la suppression ?')">SUPPRIMER</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted">Aucun guide trouvé.</p>
        <?php endif; ?>

        <!-- Guides Vidéo -->
        <h2 class="mt-5">GUIDES VIDÉO</h2>
        <div class="mb-3 d-flex flex-wrap gap-2">
            <a href="dashboard_guide.php?action=history" class="btn btn-secondary">VOIR L'HISTORIQUE</a>
            <a href="export_pdf.php" target="_blank" class="btn btn-outline-light">EXPORTER EN PDF</a>
            <a href="export_excel.php" target="_blank" class="btn btn-outline-light">EXPORTER EN EXCEL</a>
            <a href="stats_admin.php" class="btn btn-info text-dark">VOIR LES STATISTIQUES</a>
        </div>
    <?php if (!empty($controller->videoGuides)): ?>
    <div class="table-responsive">
        <table class="table table-dark table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Vidéo</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require_once '../../model/VideoGuide.php';
                foreach ($controller->videoGuides as $video):
                    $tags = VideoGuide::getTags($video['id']);
                    $tagsString = implode(', ', array_column($tags, 'tag'));
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($video['id']); ?></td>
                        <td><?php echo htmlspecialchars($video['title']); ?></td>
                        <td><?php echo htmlspecialchars($video['author']); ?></td>
                        <td>
                            <?php if (filter_var($video['content'], FILTER_VALIDATE_URL)): ?>
                                <a href="<?php echo htmlspecialchars($video['content']); ?>" target="_blank" class="link-light">Voir la vidéo</a>
                            <?php else: ?>
                                <span class="text-warning">URL Invalide</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="dashboard_guide.php?action=edit&id=<?php echo $video['id']; ?>&type=video" class="btn btn-warning btn-sm">MODIFIER</a>
                            <a href="dashboard_guide.php?action=delete&id=<?php echo $video['id']; ?>&type=video" class="btn btn-danger btn-sm" onclick="return confirm('Confirmer la suppression ?')">SUPPRIMER</a>
                            <button class="btn btn-info btn-sm video-details-btn" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#videoDetailsModal"
                                    data-id="<?php echo htmlspecialchars($video['id']); ?>"
                                    data-title="<?php echo htmlspecialchars($video['title']); ?>"
                                    data-author="<?php echo htmlspecialchars($video['author']); ?>"
                                    data-content="<?php echo htmlspecialchars($video['content']); ?>"
                                    data-tags="<?php echo htmlspecialchars($tagsString); ?>">VOIR LES DÉTAILS</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p class="text-muted">Aucune vidéo trouvée.</p>
<?php endif; ?>
    </div>
</div>

    
<!-- Video Details Modal -->
<div class="modal fade" id="videoDetailsModal" tabindex="-1" aria-labelledby="videoDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background-color: var(--dark-surface); border: 1px solid var(--primary-color); box-shadow: var(--box-shadow);">
            <div class="modal-header border-bottom border-dark">
                <h5 class="modal-title text-white" id="videoDetailsModalLabel">Détails de la Vidéo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-white">
                <h3 id="video-title" class="mb-3"></h3>
                <p><strong>Auteur :</strong> <span id="video-author"></span></p>
                <p><strong>Tags :</strong> <span id="video-tags"></span></p>
                <div id="video-player" class="mb-3"></div>
            </div>
            <div class="modal-footer border-top border-dark">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('videoDetailsModal');
        modal.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const title = button.getAttribute('data-title');
            const author = button.getAttribute('data-author');
            const content = button.getAttribute('data-content');
            const tags = button.getAttribute('data-tags');

            // Update modal content
            document.getElementById('video-title').textContent = title;
            document.getElementById('video-author').textContent = author;
            document.getElementById('video-tags').textContent = tags || 'Aucun tag';

            const videoPlayer = document.getElementById('video-player');
            videoPlayer.innerHTML = '';

            // Handle video embed
            const youtubeRegex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/;
            const vimeoRegex = /vimeo\.com\/(\d+)/;
            let embedUrl = '';

            if (youtubeRegex.test(content)) {
                const match = content.match(youtubeRegex);
                embedUrl = `https://www.youtube.com/embed/${match[1]}`;
                videoPlayer.innerHTML = `<iframe width="100%" height="315" src="${embedUrl}" frameborder="0" allowfullscreen></iframe>`;
            } else if (vimeoRegex.test(content)) {
                const match = content.match(vimeoRegex);
                embedUrl = `https://player.vimeo.com/video/${match[1]}`;
                videoPlayer.innerHTML = `<iframe width="100%" height="315" src="${embedUrl}" frameborder="0" allowfullscreen></iframe>`;
            } else if (content && /^https?:\/\//.test(content)) {
                videoPlayer.innerHTML = `<video width="100%" height="315" controls><source src="${content}" type="video/mp4">Votre navigateur ne supporte pas la lecture vidéo.</video>`;
            } else {
                videoPlayer.innerHTML = '<p class="text-warning">URL vidéo invalide</p>';
            }
        });
    });
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    
    <script>
        // Toggle sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });

    </script>
</body>
</html>