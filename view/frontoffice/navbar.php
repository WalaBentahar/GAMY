<nav class="navbar navbar-expand-lg navbar-gaming fixed-top">
        <div class="container">
            <a class="navbar-brand navbar-brand-gaming" href="#">
                <i class="fas fa-gamepad me-2"></i>Gaming Portal
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link nav-link-gaming active" href="/"> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-gaming" href="indexfront.php"><i class="fas fa-joystick me-1"></i> Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-gaming" href="form.php"><i class="fas fa-joystick me-1"></i> guides</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-gaming" href="support.php"><i class="fas fa-joystick me-1"></i> Support</a>
                    </li>
                     <li class="nav-item">
                        <a class="nav-link nav-link-gaming" href="forum.php"><i class="fas fa-joystick me-1"></i> Forum</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-gaming" href="index.php"><i class="fas fa-joystick me-1"></i> Lives</a>
                    </li>
                    <?php if ($_SESSION['user']->getRole() === 'ADMIN'): ?>
                        <li class="nav-item">
                            <a class="nav-link nav-link-gaming" href="../backoffice/table.php"> Admin</a>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link nav-link-gaming dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" data-bs-toggle="dropdown">
                                <div class="user-avatar">
                                    <?= substr($_SESSION['user']->getPrenom(), 0, 1) . substr($_SESSION['user']->getNom(), 0, 1) ?>
                                </div>
                                <?= htmlspecialchars($_SESSION['user']->getPrenom()) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="update_profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>