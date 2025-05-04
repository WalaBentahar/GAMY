<?php
require_once __DIR__ . '/Database.php';
class Video {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    // Get all videos with category names
    public function getAllVideos() {
        $query = "SELECT videos.*, categories.name AS category_name 
                  FROM videos 
                  JOIN categories ON videos.category_id = categories.id";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add video
    public function addVideo($title, $embed_code, $category_id) {
        $query = "INSERT INTO videos (title, embed_code, category_id) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$title, $embed_code, $category_id]);
    }

    // Delete video
    public function deleteVideo($id) {
        $query = "DELETE FROM videos WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
    }
}
?>