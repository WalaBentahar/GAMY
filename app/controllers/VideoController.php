<?php
require_once __DIR__ . '/../models/Video.php';
require_once __DIR__ . '/../models/Stream.php';

class VideoController {
    private $videoModel;
    private $streamModel;

    public function __construct() {
        $this->videoModel = new Video();
        $this->streamModel = new Stream();
    }

    public function index() {
        try {
            $videos = $this->videoModel->getAllVideos();
            $streams = $this->streamModel->getAll();
            
            // Use existing streams/index.php view with your styling
            include VIEWS_PATH . 'streams/index.php';
        } catch(PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    }
    
}
?>