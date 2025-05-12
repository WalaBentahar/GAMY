<?php
class Database {
    private static $conn;

    public static function connect() {
        if (!self::$conn) {
            try {
                self::$conn = new PDO(
                    "mysql:host=localhost;dbname=forum_gamer;charset=utf8",
                    "root",
                    "",
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
                
                // Create audit_log table if it doesn't exist
                self::$conn->exec("CREATE TABLE IF NOT EXISTS audit_log (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    table_name VARCHAR(50) NOT NULL,
                    record_id INT NOT NULL,
                    action VARCHAR(20) NOT NULL,
                    old_data TEXT,
                    new_data TEXT,
                    user_id INT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            } catch (PDOException $e) {
                // Log error to file
                error_log("DB Connection Error: " . $e->getMessage(), 3, __DIR__ . '/../logs/db_errors.log');
                die("<h2 style='color:red;'>Connexion échouée :</h2><pre>" . $e->getMessage() . "</pre>");
            }
        }

        return self::$conn;
    }

    public static function logChange($tableName, $recordId, $action, $oldData = null, $newData = null, $userId = null) {
        try {
            $db = self::connect();
            
            // Debug logging
            error_log("Logging change: Table=$tableName, Record=$recordId, Action=$action");
            
            $stmt = $db->prepare("INSERT INTO audit_log (table_name, record_id, action, old_data, new_data, user_id) 
                                 VALUES (:table_name, :record_id, :action, :old_data, :new_data, :user_id)");
            
            $result = $stmt->execute([
                'table_name' => $tableName,
                'record_id' => $recordId,
                'action' => $action,
                'old_data' => $oldData ? json_encode($oldData, JSON_UNESCAPED_UNICODE) : null,
                'new_data' => $newData ? json_encode($newData, JSON_UNESCAPED_UNICODE) : null,
                'user_id' => $userId
            ]);

            if (!$result) {
                error_log("Failed to log change: " . print_r($stmt->errorInfo(), true));
            }

            return $result;
        } catch (Exception $e) {
            error_log("Error in logChange: " . $e->getMessage());
            return false;
        }
    }

    public static function checkAuditLog() {
        try {
            $db = self::connect();
            $stmt = $db->query("SELECT COUNT(*) as count FROM audit_log");
            $result = $stmt->fetch();
            return $result['count'];
        } catch (Exception $e) {
            error_log("Error checking audit log: " . $e->getMessage());
            return 0;
        }
    }

    public static function createLikesTable() {
        $db = self::connect();
        $sql = "CREATE TABLE IF NOT EXISTS likes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            content_type ENUM('discussion', 'reply') NOT NULL,
            content_id INT NOT NULL,
            reaction_type ENUM('like', 'dislike') NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_reaction (user_id, content_type, content_id)
        )";
        $db->exec($sql);
    }

    public static function initializeTables() {
        self::createUsersTable();
        self::createDiscussionsTable();
        self::createRepliesTable();
        self::createAuditLogTable();
        self::createLikesTable();
    }
}
