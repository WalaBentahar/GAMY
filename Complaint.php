<?php
require_once __DIR__ . '/../config/Database.php';

class Complaint {
    private static $pdo;

    public static function init() {
        require_once __DIR__ . '/../config/Database.php';
        self::$pdo = Database::connect();
    }

    public static function create($data) {
        try {
            $stmt = self::$pdo->prepare(
                "INSERT INTO complaints (user_id, user_email, subject, message, complaint_date) 
                 VALUES (:user_id, :email, :subject, :message, :date)"
            );
            
            return $stmt->execute([
                ':user_id' => $data['userID'],
                ':email' => $data['email'],
                ':subject' => $data['subject'],
                ':message' => $data['message'],
                ':date' => $data['date']
            ]);
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }

    public static function getAll() {
        return self::$pdo->query("SELECT * FROM complaints")->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function getById($id) {
        self::init();
        $stmt = self::$pdo->prepare("SELECT * FROM complaints WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function update($id, $data) {
        $pdo = self::connect();
        $stmt = $pdo->prepare(
            "UPDATE complaints SET 
            user_id = ?, 
            user_email = ?, 
            subject = ?, 
            message = ?, 
            complaint_date = ? 
            WHERE id = ?"
        );
        return $stmt->execute([
            $data['userID'],
            $data['email'],
            $data['subject'],
            $data['message'],
            $data['date'],
            $id
        ]);
    }
    
    public static function delete($id) {
        $pdo = self::connect();
        $stmt = $pdo->prepare("DELETE FROM complaints WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

// Initialize connection when file loads
Complaint::init();
?>