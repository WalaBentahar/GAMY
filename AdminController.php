<?php
require_once __DIR__ . '/../models/Complaint.php';

class AdminController {
    private function checkAuth() {
        if (!isset($_SESSION['admin_logged_in'])) {
            header("Location: /gaming_website/admin/login");
            exit;
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_POST['password'] === "adminsupport") {
                $_SESSION['admin_logged_in'] = true;
                header("Location: /gaming_website/admin/dashboard");
                exit;
            }
            $error = "Invalid password!";
        }
        require __DIR__ . '/../views/admin/login.php';
    }

    public function dashboard() {
        $this->checkAuth();
        $complaints = Complaint::getAll();
        require __DIR__ . '/../views/admin/dashboard.php';
    }

    public function edit($id) {
        $this->checkAuth();
        $complaint = Complaint::getById($id);
        require __DIR__ . '/../views/admin/edit.php';
    }

    public function update($id) {
        $this->checkAuth();
        if (Complaint::update($id, $_POST)) {
            header("Location: /gaming_website/admin/dashboard?success=updated");
        } else {
            header("Location: /gaming_website/admin/edit/$id?error=update_failed");
        }
        exit;
    }

    public function delete($id) {
        $this->checkAuth();
        if (Complaint::delete($id)) {
            header("Location: /gaming_website/admin/dashboard?success=deleted");
        } else {
            header("Location: /gaming_website/admin/dashboard?error=delete_failed");
        }
        exit;
    }

    public function logout() {
        session_start();
        session_destroy();
        header("Location: /gaming_website/admin/login");
        exit;
    }
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
?>