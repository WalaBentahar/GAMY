<?php 
session_start();
if ($_SESSION['role'] !== 'ADMIN') {
  header("Location: access_denied.php");
  exit();
}
require_once '../../controller/supportController.php';

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
<style>
           /* Menu Items */
           .menu h3 {
            color: var(--primary);
            font-size: 0.8rem;
            text-transform: uppercase;
            margin: 1.5rem 0 1rem;
            padding: 0 1rem;
        }
        
        .menu-item {
            display: flex;
            align-items: center;
            padding: 0.8rem 1rem;
            margin: 0.2rem 0;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            color: var(--light);
        }
        
        .menu-item:hover, .menu-item.active {
            background: rgba(255, 0, 0, 0.2);
            color: white;
        }
        
        .menu-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
            color: var(--primary);
        }
        
        /* Main Content Area */
        .main-content {
            padding: 2rem;
            background: #0d0d0d;
        }
        
        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--primary);
        }
        
        .header h1 {
            font-size: 1.8rem;
            color: var(--primary);
            text-shadow: 0 0 15px rgba(255, 0, 0, 0.7);
        }
        
        /* Stats Cards */
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: #1a1a1a;
            border-radius: 10px;
            padding: 1.5rem;
            border: 1px solid var(--primary);
            transition: transform 0.3s;
            box-shadow: 0 0 10px rgba(255, 0, 0, 0.2);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 15px rgba(255, 0, 0, 0.4);
        }
        
        .stat-card h3 {
            font-size: 0.9rem;
            color: #ccc;
            margin-bottom: 0.5rem;
        }
        
        .stat-card p {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary);
        }
        
        /* Tickets Table */
        .tickets {
            background: #1a1a1a;
            border-radius: 10px;
            padding: 1.5rem;
            border: 1px solid var(--primary);
            box-shadow: 0 0 10px rgba(255, 0, 0, 0.2);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: #262626;
            color: var(--primary);
            text-transform: uppercase;
            font-size: 0.8rem;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid var(--primary);
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #333;
            color: #e6e6e6;
        }
        
        tr:hover {
            background: rgba(255, 0, 0, 0.05);
        }
        
        /* Status Badges */
        .status {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status.pending {
            background: rgba(255, 0, 0, 0.2);
            color: #ff6666;
            border: 1px solid #ff3333;
        }
        
        .status.resolved {
            background: rgba(0, 170, 0, 0.2);
            color: #00cc00;
            border: 1px solid #00aa00;
        }
        
        /* Action Buttons */
        .actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        
        .btn-edit {
            background: rgba(255, 0, 0, 0.1);
            color: #ff9999;
            border: 1px solid #ff6666;
        }
        
        .btn-edit:hover {
            background: rgba(255, 0, 0, 0.3);
            color: white;
        }
        
        .btn-delete {
            background: rgba(255, 51, 51, 0.1);
            color: var(--danger);
            border: 1px solid var(--danger);
        }
        
        .btn-delete:hover {
            background: rgba(255, 51, 51, 0.3);
            color: white;
        }
        
        .btn-response {
            background: rgba(0, 170, 0, 0.1);
            color: var(--success);
            border: 1px solid var(--success);
        }
        
        .btn-response:hover {
            background: rgba(0, 170, 0, 0.3);
            color: white;
        }
        
        /* Modals */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
        }
        
        .modal-content {
            background-color: #1a1a1a;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid var(--primary);
            border-radius: 10px;
            width: 50%;
            box-shadow: 0 0 20px rgba(255, 0, 0, 0.5);
        }
        
        .modal-header {
            padding: 10px 0;
            border-bottom: 1px solid var(--primary);
            margin-bottom: 20px;
        }
        
        .modal-header h2 {
            color: var(--primary);
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: var(--primary);
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #ccc;
        }
        
        .form-group textarea {
            width: 100%;
            padding: 10px;
            background-color: #2b2b2b;
            border: 1px solid var(--primary);
            border-radius: 5px;
            color: white;
            min-height: 100px;
        }
        
        .modal-footer {
            padding-top: 20px;
            border-top: 1px solid var(--primary);
            text-align: right;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .btn-primary:hover {
            background-color: #cc0000;
        }
        
        .response-details {
            background-color: #2b2b2b;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            border-left: 3px solid var(--success);
        }
        
        .response-details p {
            margin: 5px 0;
        }
        
        .response-date {
            color: #aaa;
            font-size: 0.8rem;
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
     <!-- Stats Cards -->
     <div class="stats">
        <div class="stat-card">
            <h3>Total Tickets</h3>
            <p><?php echo count($complaints); ?></p>
        </div>
        <div class="stat-card">
            <h3>Pending</h3>
            <p><?php echo count(array_filter($complaints, function($c) { return !$c['resolved']; })); ?></p>
        </div>
        <div class="stat-card">
            <h3>Resolved</h3>
            <p><?php echo count(array_filter($complaints, function($c) { return $c['resolved']; })); ?></p>
        </div>
        <div class="stat-card">
            <h3>Response Rate</h3>
            <p><?php 
                $total = count($complaints);
                $responded = count(array_filter($complaints, function($c) { return !empty($c['response_text']); }));
                echo $total > 0 ? round(($responded / $total) * 100) . '%' : '0%';
            ?></p>
        </div>
    </div>
    
    <!-- Tickets Table -->
    <div class="tickets">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($complaints as $complaint): ?>
                <tr>
                    <td><?php echo $complaint['id']; ?></td>
                    <td><?php echo htmlspecialchars($complaint['name']); ?></td>
                    <td><?php echo htmlspecialchars($complaint['email']); ?></td>
                    <td><?php echo htmlspecialchars(substr($complaint['message'], 0, 50)) . (strlen($complaint['message']) > 50 ? '...' : ''); ?></td>
                    <td><?php echo date('M d, Y', strtotime($complaint['submission_date'])); ?></td>
                    <td>
                        <span class="status <?php echo $complaint['resolved'] ? 'resolved' : 'pending'; ?>">
                            <?php echo $complaint['resolved'] ? 'Resolved' : 'Pending'; ?>
                        </span>
                        <?php if (!empty($complaint['response_text'])): ?>
                            <div class="response-details">
                                <p><?php echo htmlspecialchars($complaint['response_text']); ?></p>
                                <p class="response-date">Responded on: <?php echo date('M d, Y', strtotime($complaint['response_date'])); ?></p>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="actions">
                            <button class="btn btn-edit" onclick="openEditModal(<?php echo $complaint['id']; ?>, '<?php echo addslashes($complaint['message']); ?>')">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-delete" onclick="if(confirm('Are you sure you want to delete this ticket?')) { window.location.href='?delete=<?php echo $complaint['id']; ?>'; }">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                            <?php if (!$complaint['resolved']): ?>
                            <button class="btn btn-response" onclick="openResponseModal(<?php echo $complaint['id']; ?>)">
                                <i class="fas fa-reply"></i> Respond
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
        </div>
    </div>
    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" onclick="closeModal('editModal')">&times;</span>
                <h2>Edit Ticket</h2>
            </div>
            <form action="" method="POST">
                <input type="hidden" name="id" id="editId">
                <div class="form-group">
                    <label for="editMessage">Message</label>
                    <textarea id="editMessage" name="message" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-delete" onclick="closeModal('editModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary" name="edit_complaint">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Response Modal -->

<!-- In dashboard.php, modify the response modal -->
<div id="responseModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <span class="close" onclick="closeModal('responseModal')">&times;</span>
            <h2>Respond to Ticket</h2>
        </div>
        <form action="" method="POST">
            <input type="hidden" name="complaint_id" id="responseId">
            <div class="form-group">
                <label for="admin_response">Response Message</label>
                <textarea id="admin_response" name="admin_response" required></textarea>
            </div>
            <div class="form-group">
                <input type="checkbox" id="send_email" name="send_email" checked value="1">
                <label for="send_email">Send email to user</label>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-delete" onclick="closeModal('responseModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" name="submit_response">Send Response</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Modal functions
    function openEditModal(id, message) {
        document.getElementById('editId').value = id;
        document.getElementById('editMessage').value = message;
        document.getElementById('editModal').style.display = 'block';
    }
    
    function openResponseModal(id) {
        document.getElementById('responseId').value = id;
        document.getElementById('responseModal').style.display = 'block';
    }
    
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.className === 'modal') {
            event.target.style.display = 'none';
        }
    }
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