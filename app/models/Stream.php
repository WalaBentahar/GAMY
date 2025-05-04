<?php
require_once __DIR__ . '/Database.php';

class Stream {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    public function getAll() {
        $query = "SELECT streams.*, categories.name AS category_name 
                  FROM streams 
                  JOIN categories ON streams.category_id = categories.id";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addStream($title, $stream_id, $category_id) {
        $query = "INSERT INTO streams (title, stream_id, category_id) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$title, $stream_id, $category_id]);
    }

    public function deleteStream($id) {
        $query = "DELETE FROM streams WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
    }

    public function verifyStream($stream_id) {
        // First check if video exists
        $api_url = "https://www.googleapis.com/youtube/v3/videos?part=id&id=$stream_id&key=" . YOUTUBE_API_KEY;
        $response = file_get_contents($api_url);
        $data = json_decode($response, true);
        
        if (empty($data['items'])) {
            $_SESSION['error'] = "YouTube video not found";
            return false;
        }
    
        // Then check for live details
        $live_api_url = "https://www.googleapis.com/youtube/v3/videos?part=liveStreamingDetails&id=$stream_id&key=" . YOUTUBE_API_KEY;
        $live_response = file_get_contents($live_api_url);
        $live_data = json_decode($live_response, true);
    
        return !empty($live_data['items'][0]['liveStreamingDetails']);
    }
    public function getEmbedUrl() {
        return "https://www.youtube.com/embed/" . $this->stream_id;
    }
}
?>