


<?php
require_once '../../config.php';
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Ajouter un utilisateur
    public function addUser($nom, $prenom, $email, $phone, $pays, $password) {
        $sql = "INSERT INTO users (nom, prenom, email, phone, pays, password) 
                VALUES (:nom, :prenom, :email, :phone, :pays, :password)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':pays', $pays);
        $stmt->bindParam(':password', $password);

        return $stmt->execute();
    }

    // Connexion d'un utilisateur
    public function loginUser($email, $password) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            // Supprimer le mot de passe avant de retourner les données
            unset($user['password']);
            return $user;
        }

        return false; // Connexion échouée
    }

    // Récupérer tous les utilisateurs
    public function getAllUsers() {
        $sql = "SELECT id, nom, prenom, email, phone, pays FROM users";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    // Récupérer un utilisateur par ID
    public function getUserById($id) {
        $sql = "SELECT id, nom, prenom, email, phone, pays FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Supprimer un utilisateur par ID
    public function deleteUser($id) {
        try {
            $sql = "DELETE FROM users WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error deleting user: " . $e->getMessage());
        }
    }
    
}
?>
