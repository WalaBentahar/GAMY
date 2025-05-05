<?php
require_once __DIR__ . '/../models/Database.php';

class ChatController {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    public function sendMessage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $youtube_id = $_POST['youtube_id'];
            $message = $_POST['message'];
    
            // Ajoutez 'Guest' comme username par défaut
            $query = "INSERT INTO stream_chat (youtube_id, message, username) VALUES (?, ?, 'Guest')";
            $stmt = $this->db->prepare($query);
            
            if ($stmt->execute([$youtube_id, htmlspecialchars($message)])) {
                $messageId = $this->db->lastInsertId();
                $stmt = $this->db->prepare("SELECT * FROM stream_chat WHERE id = ?");
                $stmt->execute([$messageId]);
                $newMessage = $stmt->fetch(PDO::FETCH_ASSOC);
    
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => [
                        'username' => $newMessage['username'] ?? 'Guest', // Fallback
                        'text' => $newMessage['message'] // Renommez la clé pour plus de clarté
                    ]
                ]);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => 'Erreur lors de l\'envoi du message'
                ]);
            }
            exit;
        }
    }

    public function getMessages($youtube_id) {
        $query = "SELECT * FROM stream_chat 
                 WHERE youtube_id = ? AND is_deleted = 0 
                 ORDER BY created_at DESC 
                 LIMIT 50";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$youtube_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>