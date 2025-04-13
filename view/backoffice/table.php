<?php

    require_once '../../config.php';
    $pdo = config::getConnexion();

    // Get the column and sort direction from the URL parameters
    $sortColumn    = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'nom';       // Default column is 'nom'
    $sortDirection = isset($_GET['sort_direction']) ? $_GET['sort_direction'] : 'ASC'; // Default direction is 'ASC'

    // Toggle sort direction
    $nextSortDirection = $sortDirection === 'ASC' ? 'DESC' : 'ASC';

    // Check if a search term was submitted
    $searchEmail = isset($_GET['search_email']) ? $_GET['search_email'] : '';

    // Modify SQL query based on search input
    if ($searchEmail) {
        // Use LIKE for partial matching, and bind the value safely
        $sql  = "SELECT * FROM users WHERE email LIKE :email ORDER BY $sortColumn $sortDirection";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':email', '%' . $searchEmail . '%', PDO::PARAM_STR); // Ensure email search is handled with wildcards
    } else {
        // Default SQL query if no search term is provided
        $sql  = "SELECT * FROM users ORDER BY $sortColumn $sortDirection";
        $stmt = $pdo->query($sql);
    }

    // Execute the query and fetch the results
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
              <?php
                  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
                      require_once '../../config.php';                    // Ensure the database connection file is included
                      require_once '../../controller/UserController.php'; // Include the controller

                      // Initialize the PDO connection
                      $pdo = config::getConnexion();

                      // Instantiate the UserController with $pdo
                      $userController = new UserController($pdo);

                      // Get the user ID to delete
                      $delete_id = intval($_POST['delete_id']);

                      // Call the deleteUser method
                      $userController->deleteUser($delete_id);

                      // Redirect to refresh the table
                      header("Location: table.php");
                      exit();
                  }
              ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Skydash Admin</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="assets/images/favicon.png" />
    <style>th a {
    color: #007bff;
    text-decoration: none;
    font-weight: bold;
}

th a:hover {
    text-decoration: underline;
}

/* Highlight the sorted column */
th a.active-sort {
    color: #0056b3;
}
.search-bar {
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.search-bar input {
    width: 250px;
    padding: 8px;
    font-size: 14px;
}

.search-bar button {
    padding: 8px 15px;
    font-size: 14px;
}
</style>
  </head>
  <body>
    <div class="container-scroller">
      <!-- partial:../../partials/_navbar.html -->
      <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
  <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
    <a class="navbar-brand brand-logo me-5" href="../../index.html"><img src="assets/images/logo.svg" class="me-2" alt="logo" /></a>
    <a class="navbar-brand brand-logo-mini" href="../../index.html"><img src="assets/images/logo-mini.svg" alt="logo" /></a>
  </div>
  <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
    <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
      <span class="icon-menu"></span>
    </button>
    <ul class="navbar-nav mr-lg-2">
      <li class="nav-item nav-search d-none d-lg-block">
        <div class="input-group">
          <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
            <span class="input-group-text" id="search">
              <i class="icon-search"></i>
            </span>
          </div>
          <input type="text" class="form-control" id="navbar-search-input" placeholder="Search now" aria-label="search" aria-describedby="search">
        </div>
      </li>
    </ul>
    <ul class="navbar-nav navbar-nav-right">
      <li class="nav-item dropdown">
        <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-bs-toggle="dropdown">
          <i class="icon-bell mx-0"></i>
          <span class="count"></span>
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
          <p class="mb-0 font-weight-normal float-left dropdown-header">Notifications</p>
          <a class="dropdown-item preview-item">
            <div class="preview-thumbnail">
              <div class="preview-icon bg-success">
                <i class="ti-info-alt mx-0"></i>
              </div>
            </div>
            <div class="preview-item-content">
              <h6 class="preview-subject font-weight-normal">Application Error</h6>
              <p class="font-weight-light small-text mb-0 text-muted"> Just now </p>
            </div>
          </a>
          <a class="dropdown-item preview-item">
            <div class="preview-thumbnail">
              <div class="preview-icon bg-warning">
                <i class="ti-settings mx-0"></i>
              </div>
            </div>
            <div class="preview-item-content">
              <h6 class="preview-subject font-weight-normal">Settings</h6>
              <p class="font-weight-light small-text mb-0 text-muted"> Private message </p>
            </div>
          </a>
          <a class="dropdown-item preview-item">
            <div class="preview-thumbnail">
              <div class="preview-icon bg-info">
                <i class="ti-user mx-0"></i>
              </div>
            </div>
            <div class="preview-item-content">
              <h6 class="preview-subject font-weight-normal">New user registration</h6>
              <p class="font-weight-light small-text mb-0 text-muted"> 2 days ago </p>
            </div>
          </a>
        </div>
      </li>
      <li class="nav-item nav-profile dropdown">
        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" id="profileDropdown">
          <img src="assets/images/faces/face28.jpg" alt="profile" />
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
          <a class="dropdown-item">
            <i class="ti-settings text-primary"></i> Settings </a>
          <a class="dropdown-item">
            <i class="ti-power-off text-primary"></i> Logout </a>
        </div>
      </li>
      <li class="nav-item nav-settings d-none d-lg-flex">
        <a class="nav-link" href="#">
          <i class="icon-ellipsis"></i>
        </a>
      </li>
    </ul>
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
      <span class="icon-menu"></span>
    </button>
  </div>
</nav>
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:../../partials/_sidebar.html -->
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item">
      <a class="nav-link" href="../../index.html">
        <i class="icon-grid menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>
  </ul>
</nav>
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
            <div class="row">
              <div class="">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">List of Users</h4>
                    <p class="card-description"> Add class <code>.table</code>
                    </p>
                    <div class="search-bar">
                        <form method="GET" action="">
                            <input type="text" name="search_email" placeholder="Search by email" class="form-control" value="<?php echo isset($_GET['search_email']) ? htmlspecialchars($_GET['search_email']) : '';?>">
                            <button type="submit" class="btn btn-info">Search</button>
                        </form>
                    </div>
                    <div class="table-responsive">
                    <table class="table">
                      <thead>
                          <tr>
                              <th><a href="?sort_column=id&sort_direction=<?php echo $nextSortDirection?>">ID</a></th>
                              <th><a href="?sort_column=nom&sort_direction=<?php echo $nextSortDirection?>">Name</a></th>
                              <th><a href="?sort_column=prenom&sort_direction=<?php echo $nextSortDirection?>">First Name</a></th>
                              <th><a href="?sort_column=email&sort_direction=<?php echo $nextSortDirection?>">Email</a></th>
                              <th><a href="?sort_column=phone&sort_direction=<?php echo $nextSortDirection?>">Phone</a></th>
                              <th><a href="?sort_column=pays&sort_direction=<?php echo $nextSortDirection?>">Country</a></th>
                              <th><a href="?sort_column=role&sort_direction=<?php echo $nextSortDirection?>">Role</a></th>
                              <th>Status</th>
                              <th>Actions</th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php foreach ($users as $user): ?>
                              <tr>
                                  <td><?php echo htmlspecialchars($user['id']);?></td>
                                  <td><?php echo htmlspecialchars($user['nom']);?></td>
                                  <td><?php echo htmlspecialchars($user['prenom']);?></td>
                                  <td><?php echo htmlspecialchars($user['email']);?></td>
                                  <td><?php echo htmlspecialchars($user['phone']);?></td>
                                  <td><?php echo htmlspecialchars($user['pays']);?></td>
                                  <td><?php echo htmlspecialchars($user['role']);?></td>
                                  <td>
                                      <?php if ($user['status'] == 1): ?>
                                          <span class="badge badge-success">Active</span>
                                      <?php else: ?>
                                          <span class="badge badge-danger">Banned</span>
                                      <?php endif; ?>
                                  </td>
                                  <td>
                                      <!-- Ban/Unban User -->
                                      <form method="POST" action="update_user_status.php" style="display:inline;">
                                          <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']);?>">
                                          <input type="hidden" name="new_status" value="<?php echo $user['status'] == 1 ? 0 : 1;?>">
                                          <button type="submit" class="btn btn-sm <?php echo $user['status'] == 1 ? 'btn-warning' : 'btn-success';?>">
                                              <?php echo $user['status'] == 1 ? 'Ban' : 'Unban';?>
                                          </button>
                                      </form>

                                      <!-- Edit User -->
                                      <form method="GET" action="edit_user.php" style="display:inline;">
                                          <input type="hidden" name="edit_id" value="<?php echo htmlspecialchars($user['id']);?>">
                                          <button type="submit" class="btn btn-primary btn-sm">Edit</button>
                                      </form>

                                      <!-- Delete User -->
                                      <form method="POST" action="" style="display:inline;">
                                          <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($user['id']);?>">
                                          <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                      </form>
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
 </div>
 </div>

 </body>

