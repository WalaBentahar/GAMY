<?php 
session_start();
if ($_SESSION['role'] !== 'ADMIN') {
  header("Location: access_denied.php");
  exit();
}

require_once '../../config.php';
$pdo = config::getConnexion();

$sortColumn = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'nom';
$sortDirection = isset($_GET['sort_direction']) ? $_GET['sort_direction'] : 'ASC';
$nextSortDirection = $sortDirection === 'ASC' ? 'DESC' : 'ASC';
$searchEmail = isset($_GET['search_email']) ? $_GET['search_email'] : '';

if ($searchEmail) {
    $sql = "SELECT * FROM users WHERE email LIKE :email ORDER BY $sortColumn $sortDirection";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':email', '%' . $searchEmail . '%', PDO::PARAM_STR);
} else {
    $sql = "SELECT * FROM users ORDER BY $sortColumn $sortDirection";
    $stmt = $pdo->query($sql);
}

$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get user statistics
$activeUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 1")->fetchColumn();
$bannedUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 0")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$adminUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'ADMIN'")->fetchColumn();
$regularUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'USER'")->fetchColumn();

// Get monthly user registration data for chart
$registrationData = $pdo->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS count 
    FROM users 
    GROUP BY month 
    ORDER BY month DESC 
    LIMIT 12
")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    require_once '../../config.php';
    require_once '../../controller/userController.php';
    $pdo = config::getConnexion();
    $userController = new UserController($pdo);
    $delete_id = intval($_POST['delete_id']);
    $userController->deleteUser($delete_id);
    header("Location: table.php");
    exit();
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
</head>
<body>
    <!-- Overlay for mobile -->
    <div class="overlay" id="overlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4>Admin Dashboard</h4>
        </div>
        <div class="px-3 py-2">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="../../index.php">
                        <i class="mdi mdi-view-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="table.php">
                        <i class="mdi mdi-account-multiple"></i>
                        <span>Users</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#stats-section">
                        <i class="mdi mdi-chart-bar"></i>
                        <span>Statistics</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="mdi mdi-cog"></i>
                        <span>Settings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="mdi mdi-bell"></i>
                        <span>Notifications</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="mdi mdi-file-document"></i>
                        <span>Reports</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">
                        <i class="mdi mdi-logout"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <span>&copy; 2025 Admin Panel</span>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <button class="sidebar-toggle me-3" id="sidebarToggle">
                    <i class="mdi mdi-menu"></i>
                </button>
                <a class="navbar-brand" href="#">User Management</a>
            </div>
            
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://static.vecteezy.com/system/resources/thumbnails/035/857/779/small/people-face-avatar-icon-cartoon-character-png.png" alt="Admin" class="user-avatar me-2">
                        <span class="d-none d-md-inline text-light"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownUser">
                        <li><a class="dropdown-item" href="../frontoffice/update_profile.php">Profile</a></li>
                        <li><a class="dropdown-item" href="#">Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">Sign out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="container-fluid py-4">
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stats-card total-users">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-2">Total Users</h6>
                                    <h3><?php echo $totalUsers; ?></h3>
                                    <span class="badge bg-primary">+5% from last month</span>
                                </div>
                                <div class="stats-icon">
                                    <i class="mdi mdi-account-multiple"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card active-users">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-2">Active Users</h6>
                                    <h3><?php echo $activeUsers; ?></h3>
                                    <span class="badge bg-success"><?php echo round(($activeUsers/$totalUsers)*100); ?>% of total</span>
                                </div>
                                <div class="stats-icon text-success">
                                    <i class="mdi mdi-account-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card banned-users">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-2">Banned Users</h6>
                                    <h3><?php echo $bannedUsers; ?></h3>
                                    <span class="badge bg-danger"><?php echo round(($bannedUsers/$totalUsers)*100); ?>% of total</span>
                                </div>
                                <div class="stats-icon text-danger">
                                    <i class="mdi mdi-account-off"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card admin-users">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-2">Admin Users</h6>
                                    <h3><?php echo $adminUsers; ?></h3>
                                    <span class="badge bg-warning text-dark"><?php echo round(($adminUsers/$totalUsers)*100); ?>% of total</span>
                                </div>
                                <div class="stats-icon text-warning">
                                    <i class="mdi mdi-shield-account"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Charts Section -->
            <div class="row mb-4" id="stats-section">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">User Registrations</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="registrationsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">User Distribution</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="distributionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Users Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Users Management</h5>
                            <div>
                                <a href="add_user.php" class="btn btn-primary me-2">
                                    <i class="mdi mdi-plus"></i> Add User
                                </a>
                                <button class="btn btn-secondary" onclick="generatePDF()">
                                    <i class="mdi mdi-file-pdf"></i> Export PDF
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="search-form mb-3">
                                <form method="GET" action="" class="row g-3">
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <span class="input-group-text bg-transparent border-0">
                                                <i class="mdi mdi-magnify text-secondary"></i>
                                            </span>
                                            <input type="text" name="search_email" placeholder="Search by email" class="form-control border-0" value="<?php echo htmlspecialchars($searchEmail); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            Search
                                        </button>
                                    </div>
                                    <div class="col-md-2">
                                        <a href="table.php" class="btn btn-secondary w-100">
                                            Reset
                                        </a>
                                    </div>
                                </form>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><a href="?sort_column=id&sort_direction=<?php echo $nextSortDirection; ?>&search_email=<?php echo urlencode($searchEmail); ?>">ID <i class="mdi mdi-arrow-<?php echo $sortColumn == 'id' ? ($sortDirection == 'ASC' ? 'up' : 'down') : 'up-down'; ?>"></i></a></th>
                                            <th><a href="?sort_column=nom&sort_direction=<?php echo $nextSortDirection; ?>&search_email=<?php echo urlencode($searchEmail); ?>">Name <i class="mdi mdi-arrow-<?php echo $sortColumn == 'nom' ? ($sortDirection == 'ASC' ? 'up' : 'down') : 'up-down'; ?>"></i></a></th>
                                            <th><a href="?sort_column=prenom&sort_direction=<?php echo $nextSortDirection; ?>&search_email=<?php echo urlencode($searchEmail); ?>">First Name <i class="mdi mdi-arrow-<?php echo $sortColumn == 'prenom' ? ($sortDirection == 'ASC' ? 'up' : 'down') : 'up-down'; ?>"></i></a></th>
                                            <th><a href="?sort_column=email&sort_direction=<?php echo $nextSortDirection; ?>&search_email=<?php echo urlencode($searchEmail); ?>">Email <i class="mdi mdi-arrow-<?php echo $sortColumn == 'email' ? ($sortDirection == 'ASC' ? 'up' : 'down') : 'up-down'; ?>"></i></a></th>
                                            <th><a href="?sort_column=phone&sort_direction=<?php echo $nextSortDirection; ?>&search_email=<?php echo urlencode($searchEmail); ?>">Phone <i class="mdi mdi-arrow-<?php echo $sortColumn == 'phone' ? ($sortDirection == 'ASC' ? 'up' : 'down') : 'up-down'; ?>"></i></a></th>
                                            <th><a href="?sort_column=pays&sort_direction=<?php echo $nextSortDirection; ?>&search_email=<?php echo urlencode($searchEmail); ?>">Country <i class="mdi mdi-arrow-<?php echo $sortColumn == 'pays' ? ($sortDirection == 'ASC' ? 'up' : 'down') : 'up-down'; ?>"></i></a></th>
                                            <th><a href="?sort_column=role&sort_direction=<?php echo $nextSortDirection; ?>&search_email=<?php echo urlencode($searchEmail); ?>">Role <i class="mdi mdi-arrow-<?php echo $sortColumn == 'role' ? ($sortDirection == 'ASC' ? 'up' : 'down') : 'up-down'; ?>"></i></a></th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                                <td><?php echo htmlspecialchars($user['nom']); ?></td>
                                                <td><?php echo htmlspecialchars($user['prenom']); ?></td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                                <td><?php echo htmlspecialchars($user['pays']); ?></td>
                                                <td>
                                                    <span class="badge <?php echo $user['role'] == 'ADMIN' ? 'bg-danger' : 'bg-primary'; ?>">
                                                        <?php echo htmlspecialchars($user['role']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($user['status'] == 1): ?>
                                                        <span class="badge bg-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Banned</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <form method="POST" action="update_user_status.php" class="me-2">
                                                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                                                            <input type="hidden" name="new_status" value="<?php echo $user['status'] == 1 ? 0 : 1; ?>">
                                                            <button type="submit" class="btn btn-sm <?php echo $user['status'] == 1 ? 'btn-warning' : 'btn-success'; ?>">
                                                                <?php echo $user['status'] == 1 ? '<i class="mdi mdi-block-helper"></i>' : '<i class="mdi mdi-check"></i>'; ?>
                                                            </button>
                                                        </form>
                                                        <form method="GET" action="edit_user.php" class="me-2">
                                                            <input type="hidden" name="edit_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                                                            <button type="submit" class="btn btn-primary btn-sm">
                                                                <i class="mdi mdi-pencil"></i>
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="">
                                                            <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">
                                                                <i class="mdi mdi-delete"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    
    <script>
        // Toggle sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });
        
        // Initialize charts
        document.addEventListener('DOMContentLoaded', function() {
            // Registration Chart
            const regCtx = document.getElementById('registrationsChart').getContext('2d');
            const registrationChart = new Chart(regCtx, {
                type: 'line',
                data: {
                    labels: [<?php echo implode(',', array_map(function($item) { return "'" . $item['month'] . "'"; }, array_reverse($registrationData))); ?>],
                    datasets: [{
                        label: 'User Registrations',
                        data: [<?php echo implode(',', array_map(function($item) { return $item['count']; }, array_reverse($registrationData))); ?>],
                        backgroundColor: 'rgba(67, 97, 238, 0.2)',
                        borderColor: 'rgba(67, 97, 238, 1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            // Distribution Chart
            const distCtx = document.getElementById('distributionChart').getContext('2d');
            const distributionChart = new Chart(distCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Active Users', 'Banned Users', 'Admin Users', 'Regular Users'],
                    datasets: [{
                        data: [<?php echo $activeUsers; ?>, <?php echo $bannedUsers; ?>, <?php echo $adminUsers; ?>, <?php echo $regularUsers; ?>],
                        backgroundColor: [
                            'rgba(76, 201, 240, 0.7)',
                            'rgba(247, 37, 133, 0.7)',
                            'rgba(248, 150, 30, 0.7)',
                            'rgba(67, 97, 238, 0.7)'
                        ],
                        borderColor: [
                            'rgba(76, 201, 240, 1)',
                            'rgba(247, 37, 133, 1)',
                            'rgba(248, 150, 30, 1)',
                            'rgba(67, 97, 238, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                        }
                    }
                }
            });
        });
        
        function generatePDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            
            doc.text('Users List', 14, 15);
            doc.autoTable({
                head: [['ID', 'Name', 'Email', 'Phone', 'Country', 'Role', 'Status']],
                body: [
                    <?php foreach ($users as $user): ?>
                        [
                            '<?php echo $user['id']; ?>',
                            '<?php echo $user['nom'] . ' ' . $user['prenom']; ?>',
                            '<?php echo $user['email']; ?>',
                            '<?php echo $user['phone']; ?>',
                            '<?php echo $user['pays']; ?>',
                            '<?php echo $user['role']; ?>',
                            '<?php echo $user['status'] == 1 ? 'Active' : 'Banned'; ?>'
                        ],
                    <?php endforeach; ?>
                ],
                startY: 25,
                styles: {
                    fillColor: [255, 255, 255],
                    textColor: [0, 0, 0],
                    fontSize: 10
                },
                headStyles: {
                    fillColor: [67, 97, 238],
                    textColor: [255, 255, 255]
                },
                alternateRowStyles: {
                    fillColor: [245, 245, 245]
                }
            });
            
            doc.save('users-list-<?php echo date('Y-m-d'); ?>.pdf');
        }
    </script>
</body>
</html>