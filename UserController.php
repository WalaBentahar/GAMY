<?php

class UserController {
    private $userModel;

    public function __construct() {
        require_once __DIR__ . '/../models/UserModel.php';
        $this->userModel = new UserModel();
    }

    /**
     * Gère la connexion des utilisateurs
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $user = $this->userModel->authenticate($email, $password);
            
            if ($user) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ];
                
                header('Location: /');
                exit();
            } else {
                $error = "Identifiants incorrects";
            }
        }
        
        require __DIR__ . '/../views/front/auth/login.php';
    }

    /**
     * Gère la déconnexion
     */
    public function logout() {
        session_destroy();
        header('Location: /');
        exit();
    }
}