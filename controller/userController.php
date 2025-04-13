<?php

require_once '../../model/user.php';

class UserController {
    private $user;
    private $pdo;

    public function __construct($pdo) {
        $this->pdo=$pdo;
        $this->user = new User($pdo);
    }
    
    public function deleteUser($id) {
        try {
            $this->user->deleteUser($id);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    

    // Gestion de l'inscription
    public function addUser($postData) {
        if (isset($postData['nom'], $postData['prenom'], $postData['email'],$postData['phone'], $postData['pays'], $postData['password'])) {
            $nom = $postData['nom'];
            $prenom = $postData['prenom'];
            $email = $postData['email'];
            $phone = $postData['phone'];
            $pays = $postData['pays'];
            $password = $postData['password'];

            // Affichage des données (ou logique supplémentaire)
            echo "<h1>Données reçues du formulaire Sign Up :</h1>";
            echo "<p><strong>Nom :</strong> " . htmlspecialchars($nom) . "</p>";
            echo "<p><strong>Prénom :</strong> " . htmlspecialchars($prenom) . "</p>";
            echo "<p><strong>Email :</strong> " . htmlspecialchars($email) . "</p>";
            echo "<p><strong>Phone :</strong> " . htmlspecialchars($phone) . "</p>";
            echo "<p><strong>Pays :</strong> " . htmlspecialchars($pays) . "</p>";
            echo "<p><strong>Mot de passe :</strong> (caché pour des raisons de sécurité)</p>";

            // Vous pouvez également insérer l'utilisateur dans la base de données
            // $this->userModel->addUser($nom, $prenom, $email, $pays, $password);
        } else {
            echo "Données incomplètes pour l'inscription.";
        }
    }
    public function getUserById($id)
            {
                try {
                    $sql = "SELECT * FROM users WHERE id = :id";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                    return $stmt->fetch(PDO::FETCH_ASSOC);
                } catch (Exception $e) {
                    die("Error fetching user: " . $e->getMessage());
                }
            }
    public function updateUser($id, $prenom, $email, $phone, $pays, $password, $role) {
                try {
                    $sql = "UPDATE users SET prenom = :prenom, email = :email, phone = :phone, pays = :pays, password = :password, role = :role WHERE id = :id";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->bindParam(':prenom', $prenom);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':phone', $phone);
                    $stmt->bindParam(':pays', $pays);
                    $stmt->bindParam(':password', $password);
                    $stmt->bindParam(':role', $role);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                } catch (Exception $e) {
                    die("Error updating user: " . $e->getMessage());
                }
    }
}
?>
