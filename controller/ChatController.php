<?php
// Write to log immediately
$debug_log = 'C:/wamp64/www/wala_project/project/logs/debug.log';
file_put_contents($debug_log, "ChatController start: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

// Suppress errors to prevent HTML output
ini_set('display_errors', '0');
error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('error_log', 'C:/wamp64/www/wala_project/project/logs/error.log');

// Start output buffering
ob_start();

try {
    $config_path = '../config.php';
    file_put_contents($debug_log, "Config path checked: $config_path\n", FILE_APPEND);
    if (!file_exists($config_path)) {
        throw new Exception("Config file not found at: $config_path");
    }
    require_once '../config.php';
    file_put_contents($debug_log, "Config.php included successfully\n", FILE_APPEND);
} catch (Exception $e) {
    file_put_contents($debug_log, "Config error: " . $e->getMessage() . "\n", FILE_APPEND);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'error' => 'Configuration error: ' . $e->getMessage()]);
    ob_end_flush();
    exit;
}

class ChatController {
    private $db;

    public function __construct() {
        try {
            file_put_contents($GLOBALS['debug_log'], "Connecting to database\n", FILE_APPEND);
            $this->db = Config::getConnexion();
            file_put_contents($GLOBALS['debug_log'], "Database connected\n", FILE_APPEND);
        } catch (Exception $e) {
            file_put_contents($GLOBALS['debug_log'], "DB connection error: " . $e->getMessage() . "\n", FILE_APPEND);
            $this->sendError('Database connection failed: ' . $e->getMessage());
        }
    }

    public function handleRequest() {
        ob_clean();
        file_put_contents($GLOBALS['debug_log'], "Request: " . print_r($_SERVER, true) . "\n", FILE_APPEND);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->sendMessage();
        } elseif (isset($_GET['youtube_id'])) {
            $this->sendJson($this->getMessages($_GET['youtube_id']));
        } else {
            $this->sendError('Invalid request');
        }
    }

    private function sendMessage() {
        $youtube_id = $_POST['youtube_id'] ?? '';
        $message = $_POST['message'] ?? '';

        file_put_contents($GLOBALS['debug_log'], "Sending message: youtube_id=$youtube_id, message=$message\n", FILE_APPEND);

        if (empty($youtube_id) || empty($message)) {
            $this->sendError('Missing YouTube ID or message');
        }

        try {
            $query = "INSERT INTO stream_chat (youtube_id, message, username, created_at) VALUES (:youtube_id, :message, 'Guest', NOW())";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':youtube_id', $youtube_id, PDO::PARAM_STR);
            $stmt->bindValue(':message', htmlspecialchars($message), PDO::PARAM_STR);

            if ($stmt->execute()) {
                $messageId = $this->db->lastInsertId();
                $stmt = $this->db->prepare("SELECT id, youtube_id, message, username, created_at FROM stream_chat WHERE id = :id");
                $stmt->execute([':id' => $messageId]);
                $newMessage = $stmt->fetch(PDO::FETCH_ASSOC);

                $this->sendJson([
                    'success' => true,
                    'message' => [
                        'username' => $newMessage['username'] ?? 'Guest',
                        'text' => $newMessage['message']
                    ]
                ]);
            } else {
                $this->sendError('Failed to send message');
            }
        } catch (Exception $e) {
            file_put_contents($GLOBALS['debug_log'], "Send message error: " . $e->getMessage() . "\n", FILE_APPEND);
            $this->sendError('Database error: ' . $e->getMessage());
        }
    }

    private function getMessages($youtube_id) {
        try {
            file_put_contents($GLOBALS['debug_log'], "Fetching messages for youtube_id=$youtube_id\n", FILE_APPEND);
            $query = "
                SELECT id, youtube_id, message, username, created_at
                FROM stream_chat 
                WHERE youtube_id = :youtube_id AND is_deleted = 0 
                ORDER BY created_at DESC 
                LIMIT 50
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':youtube_id', $youtube_id, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            file_put_contents($GLOBALS['debug_log'], "Get messages error: " . $e->getMessage() . "\n", FILE_APPEND);
            $this->sendError('Database error: ' . $e->getMessage());
            return [];
        }
    }

    private function sendJson($data) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        ob_end_flush();
        exit;
    }

    private function sendError($message) {
        $this->sendJson([
            'success' => false,
            'error' => $message
        ]);
    }
}

try {
    $controller = new ChatController();
    $controller->handleRequest();
} catch (Exception $e) {
    file_put_contents($debug_log, "Main error: " . $e->getMessage() . "\n", FILE_APPEND);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
    ob_end_flush();
    exit;
}
?>