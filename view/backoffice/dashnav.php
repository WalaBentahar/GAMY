    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4>Admin Dashboard</h4>
        </div>
        <div class="px-3 py-2">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="table.php">
                        <i class="mdi mdi-view-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="produit.php">
                        <i class="mdi mdi-view-dashboard"></i>
                        <span>Products</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="commande_back.php">
                        <i class="mdi mdi-view-dashboard"></i>
                        <span>Commands</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">
                        <i class="mdi mdi-view-dashboard"></i>
                        <span>Support</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="dashboard_guide.php">
                        <i class="mdi mdi-view-dashboard"></i>
                        <span>Guide</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="stats_admin.php">
                        <i class="mdi mdi-view-dashboard"></i>
                        <span>Guide stats</span>
                    </a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link" href="stream_dashboard.php">
                        <i class="mdi mdi-view-dashboard"></i>
                        <span>Videos</span>
                    </a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        <i class="mdi mdi-view-dashboard"></i>
                        <span>Streams</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage.php">
                        <i class="mdi mdi-view-dashboard"></i>
                        <span>Forum</span>
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
                <a class="navbar-brand" href="#">ADMIN DASHBOARD </a>
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
                        <li><a class="dropdown-item" href="../frontoffice/logout.php">Sign out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
