<?php
require_once '../../config.php';
require_once '../../model/Comment.php';

class CommentsController {
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
            error_log("CommentsController::logHistory error: " . $e->getMessage());
        }
    }

    public function getCommentsByPost($post_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT c.*, u.nom FROM comments c JOIN users u ON c.user_id = u.id WHERE c.post_id = :post_id ORDER BY c.created_at ASC");
            $stmt->execute([':post_id' => $post_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("CommentsController::getCommentsByPost error: " . $e->getMessage());
            return [];
        }
    }

    public function addComment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action']) || $_POST['action'] !== 'add-comment') {
            return;
        }

        $post_id = $_POST['post_id'] ?? '';
        $user_id = $_POST['user_id'] ?? '';
        $author = trim($_POST['author'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if (!$post_id || !$user_id || !$author || !$content) {
            $_SESSION['error'] = 'Tous les champs sont requis.';
            return;
        }

        try {
            $stmt = $this->pdo->prepare("INSERT INTO comments (post_id, user_id, author, content, created_at) VALUES (:post_id, :user_id, :author, :content, NOW())");
            $stmt->execute([
                ':post_id' => $post_id,
                ':user_id' => $user_id,
                ':author' => $author,
                ':content' => $content
            ]);
            $commentId = $this->pdo->lastInsertId();
            $this->logHistory($user_id, 'create_comment', $commentId, "Commented: " . substr($content, 0, 50) . (strlen($content) > 50 ? '...' : ''));
            $_SESSION['success'] = 'Commentaire ajouté avec succès.';
        } catch (PDOException $e) {
            error_log("CommentsController::addComment error: " . $e->getMessage());
            $_SESSION['error'] = 'Erreur lors de l’ajout du commentaire : ' . $e->getMessage();
        }
    }

    public function deleteComment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action']) || $_POST['action'] !== 'delete-comment') {
            return;
        }

        $comment_id = $_POST['comment_id'] ?? '';
        $user_id = $_POST['user_id'] ?? '';

        if (!$comment_id || !$user_id) {
            $_SESSION['error'] = 'ID du commentaire ou utilisateur manquant.';
            return;
        }

        try {
            // Get comment content for logging
            $stmt = $this->pdo->prepare("SELECT content FROM comments WHERE id = :id");
            $stmt->execute([':id' => $comment_id]);
            $content = $stmt->fetch(PDO::FETCH_ASSOC)['content'] ?? 'Unknown';
            $logContent = substr($content, 0, 50) . (strlen($content) > 50 ? '...' : '');

            $stmt = $this->pdo->prepare("DELETE FROM comments WHERE id = :id AND user_id = :user_id");
            $stmt->execute([':id' => $comment_id, ':user_id' => $user_id]);
            if ($stmt->rowCount() > 0) {
                $this->logHistory($user_id, 'delete_comment', $comment_id, "Deleted comment: $logContent");
                $_SESSION['success'] = 'Commentaire supprimé avec succès.';
            } else {
                $_SESSION['error'] = 'Commentaire non trouvé ou non autorisé.';
            }
        } catch (PDOException $e) {
            error_log("CommentsController::deleteComment error: " . $e->getMessage());
            $_SESSION['error'] = 'Erreur lors de la suppression : ' . $e->getMessage();
        }
    }

    public function getComment($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT c.*, u.nom FROM comments c JOIN users u ON c.user_id = u.id WHERE c.id = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("CommentsController::getComment error: " . $e->getMessage());
            $_SESSION['error'] = 'Erreur lors de la récupération du commentaire : ' . $e->getMessage();
            return false;
        }
    }

    public function updateComment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action']) || $_POST['action'] !== 'update-comment') {
            return;
        }

        $comment_id = $_POST['id'] ?? '';
        $user_id = $_POST['user_id'] ?? '';
        $author = trim($_POST['author'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if (!$comment_id || !$user_id || !$author || !$content) {
            $_SESSION['error'] = 'Tous les champs sont requis.';
            return;
        }

        try {
            $stmt = $this->pdo->prepare("UPDATE comments SET author = :author, content = :content WHERE id = :id AND user_id = :user_id");
            $stmt->execute([
                ':author' => $author,
                ':content' => $content,
                ':id' => $comment_id,
                ':user_id' => $user_id
            ]);
            if ($stmt->rowCount() > 0) {
                $logContent = substr($content, 0, 50) . (strlen($content) > 50 ? '...' : '');
                $this->logHistory($user_id, 'update_comment', $comment_id, "Updated comment: $logContent");
                $_SESSION['success'] = 'Commentaire mis à jour avec succès.';
            } else {
                $_SESSION['error'] = 'Commentaire non trouvé ou non autorisé.';
            }
        } catch (PDOException $e) {
            error_log("CommentsController::updateComment error: " . $e->getMessage());
            $_SESSION['error'] = 'Erreur lors de la mise à jour : ' . $e->getMessage();
        }
    }
}
?>