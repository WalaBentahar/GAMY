<?php
require_once  '../../config.php';

$pdo = Config::getConnexion();

// Tri (optionnel si tu veux le garder)
$orderBy = 'id';
$orderDir = 'ASC';

if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'name_asc':
            $orderBy = 'nom';
            $orderDir = 'ASC';
            break;
        case 'name_desc':
            $orderBy = 'nom';
            $orderDir = 'DESC';
            break;
        case 'id_desc':
            $orderBy = 'id';
            $orderDir = 'DESC';
            break;
        default:
            $orderBy = 'id';
            $orderDir = 'ASC';
    }
}

$sql = "SELECT * FROM produits ORDER BY $orderBy $orderDir";
$stmt = $pdo->query($sql);
$produits = $stmt->fetchAll();

// Récupération des noms et quantités
$sql = "SELECT nom, quantite FROM produits";
$stmt = $pdo->query($sql);

$noms = [];
$quantites = [];

foreach ($produits as $produit) {
    $noms[] = $produit['nom'];
    $quantites[] = $produit['quantite'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .main-content {
      padding: 20px;
    }

    h1 {
      font-size: 2rem;
      margin-bottom: 20px;
      color: #c7f705;
    }

    .user-info {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 20px;
    }

    .user-info img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #c7f705;
    }

    .search-options input {
      background-color: #1e1e1e;
      border: 1px solid #333;
      color: #fff;
      border-radius: 5px;
    }

    .sort-options select {
      background-color: #1e1e1e;
      border: 1px solid #333;
      color: #fff;
      border-radius: 5px;
      padding: 5px 10px;
    }

    .users-table {
      width: 100%;
      background-color: #1f1f1f;
      color: #fff;
      border-collapse: collapse;
      margin-top: 20px;
    }

    .users-table thead {
      background-color: #2c2c2c;
    }

    .users-table th, .users-table td {
      padding: 12px;
      text-align: center;
      border: 1px solid #333;
    }

    .users-table img {
      max-width: 100%;
      height: auto;
    }

    a i {
      color:rgb(247, 5, 5);
      font-size: 1.1rem;
      margin: 0 5px;
      transition: color 0.2s;
    }

    a i:hover {
      color: #a2cc04;
    }

    ::placeholder {
      color: #aaa;
    }
</style>
</head>
<body>
    <!-- Overlay for mobile -->
    <div class="overlay" id="overlay"></div>
  <?php require 'dashnav.php'?>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="container-fluid py-4">
        <div class="user-info">
          <img src="perso.jpg" alt="Admin" />
          <span>Administrateur</span>
        </div>
        <form class="product-form" method="POST" action="../../controller/createProduit.php"
      style="max-width: 600px; margin: 30px auto; background-color: #1e1e1e; padding: 30px; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.6); color: #f1f1f1; font-family: 'Segoe UI', sans-serif;">

  <div class="form-group" style="margin-bottom: 20px;">
    <label for="nom" style="display:block; margin-bottom: 8px;">Nom du produit</label>
    <input type="text" id="nom" name="nom" required placeholder="Nom du produit"
           style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #444; background-color: #2a2a2a; color: #fff;" />
  </div>

  <div class="form-group" style="margin-bottom: 20px;">
    <label for="description" style="display:block; margin-bottom: 8px;">Description</label>
    <textarea id="description" name="description" rows="4" required placeholder="Description détaillée"
              style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #444; background-color: #2a2a2a; color: #fff;"></textarea>
  </div>

  <div class="form-group" style="margin-bottom: 20px;">
    <label for="prix" style="display:block; margin-bottom: 8px;">Prix (€)</label>
    <input type="number" id="prix" name="prix" min="0" step="0.01" required placeholder="Prix du produit"
           style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #444; background-color: #2a2a2a; color: #fff;" />
  </div>

  <div class="form-group" style="margin-bottom: 20px;">
    <label for="categorie" style="display:block; margin-bottom: 8px;">Catégorie</label>
    <input type="text" id="categorie" name="categorie" required placeholder="Catégorie du produit"
           style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #444; background-color: #2a2a2a; color: #fff;" />
  </div>

  <div class="form-group" style="margin-bottom: 20px;">
    <label for="id" style="display:block; margin-bottom: 8px;">ID</label>
    <input type="number" id="id" name="id" required placeholder="ID unique du produit"
           style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #444; background-color: #2a2a2a; color: #fff;" />
  </div>

  <div class="form-group" style="margin-bottom: 20px;">
    <label for="image" style="display:block; margin-bottom: 8px;">Image (URL ou chemin)</label>
    <input type="text" name="image" id="image" required placeholder="URL de l'image"
           style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #444; background-color: #2a2a2a; color: #fff;" />
  </div>

  <div class="form-group" style="margin-bottom: 20px;">
    <label for="disponibilite" style="display:block; margin-bottom: 8px;">Disponibilité</label>
    <select id="disponibilite" name="disponibilite" required
            style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #444; background-color: #2a2a2a; color: #fff;">
      <option value="1">Disponible</option>
      <option value="0">Indisponible</option>
    </select>
  </div>

  <div class="form-group" style="margin-bottom: 25px;">
    <label for="quantite" style="display:block; margin-bottom: 8px;">Quantité</label>
    <input type="number" id="quantite" name="quantite" min="0" required placeholder="Quantité du produit"
           style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #444; background-color: #2a2a2a; color: #fff;" />
  </div>

  <button type="submit"
          style="width: 100%; background-color:rgb(5, 247, 5); color: #000; font-weight: bold; padding: 12px; border: none; border-radius: 6px; cursor: pointer; transition: background-color 0.2s;">
    Ajouter le produit
  </button>
</form>

      <!-- 🧠 Barre de recherche dynamique -->
      <div class="search-options">
        <input type="text" id="searchInput" placeholder="🔍 Rechercher un produit..." style="padding: 8px; width: 300px; margin-bottom: 20px;" />
      </div>
      
      <div class="sort-options">
          <form method="get">
            <select name="sort" onchange="this.form.submit()">
              <option value="id_asc" <?= isset($_GET['sort']) && $_GET['sort'] == 'id_asc' ? 'selected' : '' ?>>ID Croissant</option>
              <option value="id_desc" <?= isset($_GET['sort']) && $_GET['sort'] == 'id_desc' ? 'selected' : '' ?>>ID Décroissant</option>
              <option value="name_asc" <?= isset($_GET['sort']) && $_GET['sort'] == 'name_asc' ? 'selected' : '' ?>>Nom Croissant</option>
              <option value="name_desc" <?= isset($_GET['sort']) && $_GET['sort'] == 'name_desc' ? 'selected' : '' ?>>Nom Décroissant</option>
            </select>
          </form>
        </div>

      <table class="users-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Description</th>
            <th>Prix</th>
            <th>Catégorie</th>
            <th>Image</th>
            <th>disponibilite</th>
            <th>Quantité</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="productTableBody">
          <?php foreach ($produits as $produit): ?>
            <tr>
              <td><?= htmlspecialchars($produit['id']) ?></td>
              <td><?= htmlspecialchars($produit['nom']) ?></td>
              <td><?= htmlspecialchars($produit['description']) ?></td>
              <td><?= htmlspecialchars($produit['prix']) ?> €</td>
              <td><?= htmlspecialchars($produit['categorie']) ?></td>
              <td><img src="<?= htmlspecialchars($produit['image']) ?>" style="width: 80px; border-radius: 8px;"></td>
              <td>
                <?php if ($produit['disponibilite']): ?>
                  <i class="fas fa-check-circle" style="color:green;" title="Disponible"></i>
                <?php else: ?>
                  <i class="fas fa-times-circle" style="color:red;" title="Indisponible"></i>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($produit['quantite']) ?></td>
              <td>
                <a href="../../controller/UpdateProduit.php?id=<?= $produit['id'] ?>"><i class="fas fa-edit"></i></a>
                <a href="../../controller/DeleteProduit.php?id=<?= $produit['id'] ?>"><i class="fas fa-trash"></i></a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
        <div class="stats-container" style="max-width: 700px; margin: 40px auto;">
        <canvas id="produitChart"></canvas>
      </div>
      </div>
 
      
    </div>

  <!-- ✅ Script AJAX -->
  <script>
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('productTableBody');

    searchInput.addEventListener('keyup', function () {
      const query = searchInput.value;

      fetch(`searchProduit.php?search=${encodeURIComponent(query)}`)
        .then(response => response.text())
        .then(data => {
          tableBody.innerHTML = data;
        })
        .catch(error => {
          console.error('Erreur AJAX:', error);
        });
    });
  </script>
  
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    
    <script>
        // Toggle sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });

    </script>
      <script>
    const ctx = document.getElementById('produitChart').getContext('2d');
    new Chart(ctx, {
      type: 'pie',
      data: {
        labels: <?= json_encode($noms) ?>,
        datasets: [{
          label: 'Quantité',
          data: <?= json_encode($quantites) ?>,
          backgroundColor: [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
            '#9966FF', '#FF9F40', '#00E676', '#C9CBCF'
          ],
          borderColor: '#ffffff',
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'right',
          },
          title: {
            display: true,
            text: 'Répartition des quantités par produit'
          }
        }
      }
    });
  </script>
</body>
</html>