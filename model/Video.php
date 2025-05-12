<?php
require_once '../../config.php';

class Video {
    private $db;

    public function __construct() {
        $this->db = Config::getConnexion();
    }

    public function getAllVideos($orderBy = 'title ASC') {
        $allowedOrders = ['title ASC', 'title DESC', 'created_at DESC', 'created_at ASC'];
        $cleanOrder = in_array($orderBy, $allowedOrders) ? $orderBy : 'title ASC';

        $query = "
            SELECT v.id, v.title, v.embed_code, c.name AS category_name, v.created_at
            FROM videos v
            LEFT JOIN categories c ON v.category_id = c.id
            ORDER BY $cleanOrder
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addVideo($title, $embed_code, $category_id) {
        $query = "INSERT INTO videos (title, embed_code, category_id, created_at) VALUES (:title, :embed_code, :category_id, NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':embed_code', $embed_code, PDO::PARAM_STR);
        $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteVideo($id) {
        $query = "DELETE FROM videos WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function searchVideos($query, $orderBy = 'title ASC') {
        $allowedOrders = ['title ASC', 'title DESC', 'created_at DESC', 'created_at ASC'];
        $cleanOrder = in_array($orderBy, $allowedOrders) ? $orderBy : 'title ASC';

        $searchTerm = "%$query%";
        $sql = "
            SELECT v.id, v.title, v.embed_code, c.name AS category_name, v.created_at
            FROM videos v
            LEFT JOIN categories c ON v.category_id = c.id
            WHERE v.title LIKE :search_term
            ORDER BY $cleanOrder
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':search_term', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>