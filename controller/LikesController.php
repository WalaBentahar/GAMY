<?php
require_once '../../config.php';
require_once '../../model/Like.php';

class LikesController {
    private $pdo;

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    private function logHistory($userId, $action, $contentId, $description) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO historique (user_id, action, content_id, description, created_at) VALUES (:user_id, :action, :content_id, :description, NOW())");
            $stmt->execute([
                ':user_id' => $userId,
                ':action' => $action,
                ':content_id' => $contentId,
                ':description' => $description
            ]);
        } catch (PDOException $e) {
            error_log("LikesController::logHistory error: " . $e->getMessage());
        }
    }

    public function toggleLike() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
            return ['success' => false, 'error' => 'Requête invalide.'];
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $user_id = $input['user_id'] ?? '';
        $content_type = $input['content_type'] ?? '';
        $content_id = $input['content_id'] ?? '';

        if (!$user_id || !$content_type || !$content_id) {
            error_log("LikesController::toggleLike missing parameters: " . json_encode($input));
            return ['success' => false, 'error' => 'Paramètres manquants.'];
        }

        if (!in_array($content_type, ['post', 'comment'])) {
            error_log("LikesController::toggleLike invalid content_type: $content_type");
            return ['success' => false, 'error' => 'Type de contenu invalide.'];
        }

        if (!is_numeric($user_id) || !is_numeric($content_id)) {
            error_log("LikesController::toggleLike invalid numeric values: user_id=$user_id, content_id=$content_id");
            return ['success' => false, 'error' => 'Identifiants invalides.'];
        }

        try {
            $this->pdo->beginTransaction();

            // Check if like exists
            $stmt = $this->pdo->prepare("SELECT id FROM likes WHERE user_id = :user_id AND content_type = :content_type AND content_id = :content_id");
            $stmt->execute([
                ':user_id' => $user_id,
                ':content_type' => $content_type,
                ':content_id' => $content_id
            ]);
            $like = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($like) {
                // Unlike
                $likeId = $like['id'];
                $stmt = $this->pdo->prepare("DELETE FROM likes WHERE id = :id");
                $stmt->execute([':id' => $likeId]);
                $this->logHistory($user_id, 'remove_like', $likeId, "Removed like on $content_type ID: $content_id");
                $action = 'removed';
            } else {
                // Like
                $stmt = $this->pdo->prepare("INSERT INTO likes (user_id, content_type, content_id) VALUES (:user_id, :content_type, :content_id)");
                $stmt->execute([
                    ':user_id' => $user_id,
                    ':content_type' => $content_type,
                    ':content_id' => $content_id
                ]);
                $likeId = $this->pdo->lastInsertId();
                $this->logHistory($user_id, 'create_like', $likeId, "Added like on $content_type ID: $content_id");
                $action = 'added';
            }

            // Get updated like count
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as likes FROM likes WHERE content_type = :content_type AND content_id = :content_id");
            $stmt->execute([
                ':content_type' => $content_type,
                ':content_id' => $content_id
            ]);
            $counts = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->pdo->commit();
            return [
                'success' => true,
                'action' => $action,
                'counts' => ['likes' => $counts['likes']]
            ];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("LikesController::toggleLike error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Erreur de base de données : ' . $e->getMessage()];
        } catch (Exception $e) {
            error_log("LikesController::toggleLike general error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Erreur serveur : ' . $e->getMessage()];
        }
    }

    public function getLikeCounts($content_type, $content_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as likes FROM likes WHERE content_type = :content_type AND content_id = :content_id");
            $stmt->execute([
                ':content_type' => $content_type,
                ':content_id' => $content_id
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("LikesController::getLikeCounts error: " . $e->getMessage());
            return ['likes' => 0];
        }
    }

    public function getUserLike($user_id, $content_type, $content_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT id FROM likes WHERE user_id = :user_id AND content_type = :content_type AND content_id = :content_id");
            $stmt->execute([
                ':user_id' => $user_id,
                ':content_type' => $content_type,
                ':content_id' => $content_id
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ? 'liked' : 'unliked';
        } catch (PDOException $e) {
            error_log("LikesController::getUserLike error: " . $e->getMessage());
            return 'unliked';
        }
    }
}
?>