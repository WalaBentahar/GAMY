<?php
require_once '../../config.php';
require_once '../../model/Post.php';

class PostsController {
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
            error_log("PostsController::logHistory error: " . $e->getMessage());
        }
    }

    public function index() {
        $filter = $_GET['filter'] ?? 'all';
        $search = $_GET['search'] ?? '';
        $query = "SELECT p.*, u.nom FROM posts p JOIN users u ON p.user_id = u.id WHERE 1=1";
        $params = [];

        if ($filter !== 'all') {
            $query .= " AND p.category = :category";
            $params[':category'] = $filter;
        }

        if ($search) {
            $query .= " AND (p.title LIKE :search OR p.author LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $query .= " ORDER BY p.created_at DESC";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($result)) {
                error_log("No posts found for query: $query, params: " . json_encode($params));
            }
            return $result;
        } catch (PDOException $e) {
            error_log("PostsController::index error: " . $e->getMessage());
            $_SESSION['error'] = 'Erreur lors du chargement des posts : ' . $e->getMessage();
            return [];
        }
    }

    public function addPost() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action']) || $_POST['action'] !== 'add-post') {
            return;
        }

        $user_id = $_POST['user_id'] ?? '';
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $category = $_POST['category'] ?? '';
        $photo_url = trim($_POST['photo_url'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (!$user_id || !$title || !$author || !$category) {
            $_SESSION['error'] = 'Les champs titre, pseudo et catégorie sont requis.';
            return;
        }

        if (!is_numeric($user_id)) {
            $_SESSION['error'] = 'L’ID utilisateur doit être un nombre.';
            return;
        }

        if ($photo_url && !filter_var($photo_url, FILTER_VALIDATE_URL)) {
            $_SESSION['error'] = 'L’URL de la photo est invalide.';
            return;
        }

        try {
            $stmt = $this->pdo->prepare("INSERT INTO posts (user_id, title, author, category, photo_url, description, created_at) VALUES (:user_id, :title, :author, :category, :photo_url, :description, NOW())");
            $stmt->execute([
                ':user_id' => $user_id,
                ':title' => $title,
                ':author' => $author,
                ':category' => $category,
                ':photo_url' => $photo_url ?: null,
                ':description' => $description ?: null
            ]);
            $postId = $this->pdo->lastInsertId();
            $this->logHistory($user_id, 'create_post', $postId, "Created post: $title");
            $_SESSION['success'] = 'Post ajouté avec succès.';
        } catch (PDOException $e) {
            error_log("PostsController::addPost error: " . $e->getMessage());
            $_SESSION['error'] = 'Erreur lors de l’ajout du post : ' . $e->getMessage();
        }
    }

public function deletePost() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action']) || $_POST['action'] !== 'delete-post') {
        return;
    }

    $post_id = $_POST['post_id'] ?? '';
    $user_id = $_POST['user_id'] ?? '';

    if (!$post_id || !$user_id) {
        $_SESSION['error'] = 'ID du post ou utilisateur manquant.';
        return;
    }

    try {
        // Check if user is admin
        $stmt = $this->pdo->prepare("SELECT role FROM users WHERE id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $is_admin = $user && $user['role'] === 'ADMIN';

        // Get post title for logging
        $stmt = $this->pdo->prepare("SELECT title FROM posts WHERE id = :id");
        $stmt->execute([':id' => $post_id]);
        $title = $stmt->fetch(PDO::FETCH_ASSOC)['title'] ?? 'Unknown';

        // Prepare SQL query based on user role
        if ($is_admin) {
            // Admins can delete any post
            $stmt = $this->pdo->prepare("DELETE FROM posts WHERE id = :id");
            $params = [':id' => $post_id];
        } else {
            // Non-admins can only delete their own posts
            $stmt = $this->pdo->prepare("DELETE FROM posts WHERE id = :id AND user_id = :user_id");
            $params = [':id' => $post_id, ':user_id' => $user_id];
        }

        $stmt->execute($params);
        if ($stmt->rowCount() > 0) {
            $this->logHistory($user_id, 'delete_post', $post_id, "Deleted post: $title");
            $_SESSION['success'] = 'Post supprimé avec succès.';
        } else {
            $_SESSION['error'] = 'Post non trouvé ou non autorisé.';
        }
    } catch (PDOException $e) {
        error_log("PostsController::deletePost error: " . $e->getMessage());
        $_SESSION['error'] = 'Erreur lors de la suppression : ' . $e->getMessage();
    }
}

    public function getPost($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT p.*, u.nom FROM posts p JOIN users u ON p.user_id = u.id WHERE p.id = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("PostsController::getPost error: " . $e->getMessage());
            $_SESSION['error'] = 'Erreur lors de la récupération du post : ' . $e->getMessage();
            return false;
        }
    }

public function updatePost() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action']) || $_POST['action'] !== 'update-post') {
        return;
    }

    $post_id = $_POST['id'] ?? '';
    $user_id = $_POST['user_id'] ?? '';
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $category = $_POST['category'] ?? '';
    $photo_url = trim($_POST['photo_url'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (!$post_id || !$user_id || !$title || !$author || !$category) {
        $_SESSION['error'] = 'Les champs titre, pseudo et catégorie sont requis.';
        return;
    }

    if ($photo_url && !filter_var($photo_url, FILTER_VALIDATE_URL)) {
        $_SESSION['error'] = 'L’URL de la photo est invalide.';
        return;
    }

    try {
        // Check if user is admin
        $stmt = $this->pdo->prepare("SELECT role FROM users WHERE id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $is_admin = $user && $user['role'] === 'ADMIN';

        // Prepare SQL query based on user role
        if ($is_admin) {
            // Admins can update any post
            $stmt = $this->pdo->prepare("
                UPDATE posts 
                SET title = :title, author = :author, category = :category, 
                    photo_url = :photo_url, description = :description 
                WHERE id = :id
            ");
            $params = [
                ':title' => $title,
                ':author' => $author,
                ':category' => $category,
                ':photo_url' => $photo_url ?: null,
                ':description' => $description ?: null,
                ':id' => $post_id
            ];
        } else {
            // Non-admins can only update their own posts
            $stmt = $this->pdo->prepare("
                UPDATE posts 
                SET title = :title, author = :author, category = :category, 
                    photo_url = :photo_url, description = :description 
                WHERE id = :id AND user_id = :user_id
            ");
            $params = [
                ':title' => $title,
                ':author' => $author,
                ':category' => $category,
                ':photo_url' => $photo_url ?: null,
                ':description' => $description ?: null,
                ':id' => $post_id,
                ':user_id' => $user_id
            ];
        }

        $stmt->execute($params);
        if ($stmt->rowCount() > 0) {
            $this->logHistory($user_id, 'update_post', $post_id, "Updated post: $title");
            $_SESSION['success'] = 'Post mis à jour avec succès.';
        } else {
            $_SESSION['error'] = 'Post non trouvé ou non autorisé.';
        }
    } catch (PDOException $e) {
        error_log("PostsController::updatePost error: " . $e->getMessage());
        $_SESSION['error'] = 'Erreur lors de la mise à jour : ' . $e->getMessage();
    }
}
}
?>