<?php
require_once  '../../config.php';

class Stream {
    private $db;

    public function __construct() {
        $this->db = Config::getConnexion();
    }

    public function getAll($orderBy = 'title ASC') {
        $allowedOrders = ['title ASC', 'title DESC', 'id ASC', 'id DESC'];
        $cleanOrder = in_array($orderBy, $allowedOrders) ? $orderBy : 'title ASC';

        $query = "
            SELECT s.id, s.title, s.stream_id, c.name AS category_name
            FROM streams s
            LEFT JOIN categories c ON s.category_id = c.id
            ORDER BY $cleanOrder
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addStream($title, $stream_id, $category_id) {
        $query = "INSERT INTO streams (title, stream_id, category_id) VALUES (:title, :stream_id, :category_id)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':stream_id', $stream_id, PDO::PARAM_STR);
        $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteStream($id) {
        $query = "DELETE FROM streams WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getStreamById($id) {
        $query = "
            SELECT s.id, s.title, s.stream_id, c.name AS category_name
            FROM streams s
            LEFT JOIN categories c ON s.category_id = c.id
            WHERE s.id = :id
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function searchStreams($query, $orderBy = 'title ASC') {
        $allowedOrders = ['title ASC', 'title DESC', 'id ASC', 'id DESC'];
        $cleanOrder = in_array($orderBy, $allowedOrders) ? $orderBy : 'title ASC';

        $searchTerm = "%$query%";
        $query = "
            SELECT s.id, s.title, s.stream_id, c.name AS category_name
            FROM streams s
            LEFT JOIN categories c ON s.category_id = c.id
            WHERE s.title LIKE :search_term
            ORDER BY $cleanOrder
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':search_term', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStreamByYoutubeId($youtube_id) {
        $query = "SELECT * FROM streams WHERE stream_id = :stream_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':stream_id', $youtube_id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
        public function getAllStreams() {
        $stmt = $this->db->query("SELECT s.*, c.name AS category_name FROM streams s JOIN categories c ON s.category_id = c.id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
?>