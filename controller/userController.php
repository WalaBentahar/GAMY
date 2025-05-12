<?php
require_once '../../model/user.php';
require_once '../../config.php';

class UserController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Add a new user
    public function addUser($nom, $prenom, $email, $phone, $pays, $password) {
        try {
            // Create and populate user object
            $user = new User();
            $user->setNom(htmlspecialchars($nom));
            $user->setPrenom(htmlspecialchars($prenom));
            $user->setEmail(filter_var($email, FILTER_SANITIZE_EMAIL));
            $user->setPhone(htmlspecialchars($phone));
            $user->setPays(htmlspecialchars($pays));
            $user->setRole("USER");
            $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
            $user->setCreatedAt(date('Y-m-d H:i:s')); // 👈 Set current date/time
    
            // Prepare SQL
            $sql = "INSERT INTO users (nom, prenom, email, phone, pays, password, role, created_at) 
                    VALUES (:nom, :prenom, :email, :phone, :pays, :password, :role, :created_at)";
            $stmt = $this->pdo->prepare($sql);
            
            // Bind parameters
            $stmt->bindValue(':nom', $user->getNom());
            $stmt->bindValue(':prenom', $user->getPrenom());
            $stmt->bindValue(':email', $user->getEmail());
            $stmt->bindValue(':phone', $user->getPhone());
            $stmt->bindValue(':pays', $user->getPays());
            $stmt->bindValue(':password', $user->getPassword());
            $stmt->bindValue(':role', $user->getRole());
            $stmt->bindValue(':created_at', $user->getCreatedAt());
    
            // Execute and return
            return $stmt->execute();
    
        } catch (Exception $e) {
            error_log("Error in addUser: " . $e->getMessage());
            throw $e; // Re-throw for controller to handle
        }
    }
    
    
    public function emailExists($email) {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // User login
    public function loginUser($email, $password) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $userData = $stmt->fetch();
        if ($userData && password_verify($password, $userData['password'])) {
            $user = new User();
            $user->setId($userData['id']);
            $user->setNom($userData['nom']);
            $user->setPrenom($userData['prenom']);
            $user->setEmail($userData['email']);
            $user->setPhone($userData['phone']);
            $user->setPays($userData['pays']);
            $user->setRole($userData['role']);
              if (session_status() === PHP_SESSION_NONE) {
            session_start();
                 }
        $_SESSION['user'] = $user;
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_role'] = $user->getRole();
        $_SESSION['user_name'] = $user->getPrenom(); 
        $_SESSION['user_initials'] = strtoupper(substr($user->getPrenom(), 0, 1) . substr($user->getNom(), 0, 1));
            return $user;
        }

        return false;
    }

    // Get all users
    public function getAllUsers() {
        $sql = "SELECT id, nom, prenom, email, phone, pays, role FROM users";
        $stmt = $this->pdo->query($sql);
        
        $users = [];
        while ($userData = $stmt->fetch()) {
            $user = new User();
            $user->setId($userData['id']);
            $user->setNom($userData['nom']);
            $user->setPrenom($userData['prenom']);
            $user->setEmail($userData['email']);
            $user->setPhone($userData['phone']);
            $user->setPays($userData['pays']);
            $user->setRole($userData['role']);
            
            $users[] = $user;
        }
        return $users;
    }

    // Get user by ID
    public function getUserById($id) {
        $sql = "SELECT id, nom, prenom, email, phone, pays, role , status FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $userData = $stmt->fetch();
        if ($userData) {
            $user = new User();
            $user->setId($userData['id']);
            $user->setNom($userData['nom']);
            $user->setPrenom($userData['prenom']);
            $user->setEmail($userData['email']);
            $user->setPhone($userData['phone']);
            $user->setPays($userData['pays']);
            $user->setRole($userData['role']);
            if(!$userData['status']){
                header("Location: banned.php");
            }
            return $user;
        }
        return null;
    }

    // Delete user
    public function deleteUser($id) {
        try {
            $sql = "DELETE FROM users WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error deleting user: " . $e->getMessage());
        }
    }

    // Update user
    public function updateUser($id, $prenom, $email, $phone, $pays, $password, $role) {
        try {
            $user = $this->getUserById($id);
            if (!$user) {
                throw new Exception("User not found");
            }
    
            $user->setPrenom($prenom);
            $user->setEmail($email);
            $user->setPhone($phone);
            $user->setPays($pays);
            $user->setRole($role);
    
            $sql = "UPDATE users SET 
                        prenom = :prenom, 
                        email = :email, 
                        phone = :phone, 
                        pays = :pays, 
                        role = :role";
    
            if (!empty($password)) {
                $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
                $sql .= ", password = :password";
            }
    
            $sql .= " WHERE id = :id";
    
            $stmt = $this->pdo->prepare($sql);
    
            $stmt->bindValue(':prenom', $user->getPrenom());
            $stmt->bindValue(':email', $user->getEmail());
            $stmt->bindValue(':phone', $user->getPhone());
            $stmt->bindValue(':pays', $user->getPays());
            $stmt->bindValue(':role', $user->getRole());
            $stmt->bindValue(':id', $user->getId());
    
            if (!empty($password)) {
                $stmt->bindValue(':password', $user->getPassword());
            }
    
            return $stmt->execute();
        } catch (Exception $e) {
            throw new Exception("Error updating user: " . $e->getMessage());
        }
    }
    // Add this method to your UserController class
public function changePassword($userId, $currentPassword, $newPassword) {
    try {
        // Get the user's current password hash
        $sql = "SELECT password FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        $userData = $stmt->fetch();

        if (!$userData) {
            throw new Exception("User not found");
        }

        // Verify current password
        if (!password_verify($currentPassword, $userData['password'])) {
            throw new Exception("Current password is incorrect");
        }

        // Validate new password
        if (strlen($newPassword) < 8) {
            throw new Exception("New password must be at least 8 characters long");
        }

        // Update password
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = :password WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':password', $newPasswordHash);
        $stmt->bindParam(':id', $userId);

        return $stmt->execute();
    } catch (Exception $e) {
        throw new Exception("Error changing password: " . $e->getMessage());
    }
}

    public function handleFormSubmission($postData) {
        if (isset($postData['nom'], $postData['prenom'], $postData['email'], 
            $postData['phone'], $postData['pays'], $postData['password'])) {
            
            $result = $this->addUser(
                $postData['nom'],
                $postData['prenom'],
                $postData['email'],
                $postData['phone'],
                $postData['pays'],
                $postData['password']
            );

            if ($result) {
                echo "<h1>User registered successfully!</h1>";
                echo "<p><strong>Name:</strong> " . htmlspecialchars($postData['nom']) . "</p>";
                echo "<p><strong>Email:</strong> " . htmlspecialchars($postData['email']) . "</p>";
            } else {
                echo "Error registering user.";
            }
        } else {
            echo "Incomplete registration data.";
        }
    }
    public function updateUserStatus($userId, $newStatus) {
        try {
            // Validate inputs
            if (!is_numeric($userId) || $userId <= 0) {
                throw new InvalidArgumentException("Invalid user ID");
            }
            
            $validStatuses = [0, 1]; // 0 = inactive/banned, 1 = active
            if (!in_array($newStatus, $validStatuses)) {
                throw new InvalidArgumentException("Invalid status value");
            }
    
            // Update in database
            $sql = "UPDATE users SET status = :new_status WHERE id = :user_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':new_status', $newStatus, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            
            $result = $stmt->execute();
            
            if (!$result) {
                throw new RuntimeException("Failed to update user status");
            }
            
            return true;
        } catch (PDOException $e) {
            throw new RuntimeException("Database error: " . $e->getMessage());
        }
    }
}
?>