<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'GAMY' ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav>
        <div class="logo">
            <img src="<?= BASE_URL ?>logo.png" alt="GAMY Logo" class="logo-image" style="height: 40px;">
            <?php if(strpos($_SERVER['REQUEST_URI'], '/admin/') !== false): ?>
                <span class="admin-badge">ADMIN</span>
            <?php endif; ?>
        </div>

        <button class="menu-toggle" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>

        <ul class="nav-links">
            <?php if(strpos($_SERVER['REQUEST_URI'], '/admin/') !== false): ?>
                <!-- Admin Navigation -->
                <li><a href="<?= ADMIN_URL ?>/dashboard" class="<?= ($page ?? '') === 'dashboard' ? 'active' : '' ?>"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="<?= ADMIN_URL ?>/videos" class="<?= ($page ?? '') === 'videos' ? 'active' : '' ?>"><i class="fas fa-film"></i> Videos</a></li>
                <li><a href="<?= ADMIN_URL ?>/streams" class="<?= ($page ?? '') === 'streams' ? 'active' : '' ?>"><i class="fas fa-video"></i> Streams</a></li>
                <li><a href="<?= BASE_URL ?>" class="admin-btn"><i class="fas fa-sign-out-alt"></i> Exit Admin</a></li>
            <?php else: ?>
                <!-- Frontend Navigation -->
                <li><a href="<?= BASE_URL ?>" class="<?= ($page ?? '') === 'home' ? 'active' : '' ?>"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="#shop"><i class="fas fa-shopping-cart"></i> Shop</a></li>
                <li><a href="#guides"><i class="fas fa-book"></i> Guides</a></li>
                <li><a href="#community"><i class="fas fa-users"></i> Community</a></li>
                <li><a href="#streams"><i class="fas fa-video"></i> Streams</a></li>
                <li><a href="#support"><i class="fas fa-headset"></i> Support</a></li>
                <li><a href="<?= ADMIN_URL ?>/dashboard" class="admin-btn"><i class="fas fa-lock"></i> Admin</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main class="container">