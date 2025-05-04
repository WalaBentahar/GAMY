<?php
require_once __DIR__ . '/../models/Stream.php';
require_once __DIR__ . '/../models/Category.php';

class StreamController {
    private $streamModel;
    private $categoryModel;

    public function __construct() {
        $this->streamModel = new Stream();
        $this->categoryModel = new Category();
    }

    public function index() {
        $streams = $this->streamModel->getAll();
        $categories = $this->categoryModel->getAllCategories();
        include VIEWS_PATH . 'admin/streams/index.php';
    }

    public function create() {
        $categories = $this->categoryModel->getAllCategories();
        include VIEWS_PATH . 'admin/streams/add.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title']);
            $stream_input = trim($_POST['stream_input']);
            $category_id = (int)$_POST['category_id'];
    
            // Extract YouTube ID from URL
            $stream_id = $this->parseYoutubeId($stream_input);
            
            if (!$stream_id) {
                $_SESSION['error'] = "Invalid YouTube URL or ID format";
                header('Location: ' . ADMIN_URL . '/streams/add');
                exit;
            }

            if ($this->streamModel->addStream($title, $stream_id, $category_id)) {
                $_SESSION['success'] = "Stream added successfully";
            } else {
                $_SESSION['error'] = "Failed to add stream";
            }
            
            header('Location: ' . ADMIN_URL . '/streams');
            exit;
        }
    }

    public function delete($id) {
        if ($this->streamModel->deleteStream($id)) {
            $_SESSION['success'] = "Stream deleted successfully";
        } else {
            $_SESSION['error'] = "Failed to delete stream";
        }
        header('Location: ' . ADMIN_URL . '/streams');
        exit;
    }
    private function parseYoutubeId($input) {
        // Trim whitespace and special characters
        $clean_input = trim($input);
        
        // Match all YouTube URL formats
        $pattern = '~
            ^(?:https?://)?     # Optional protocol
            (?:www\.|m\.)?      # Optional subdomain
            (?:youtube\.com|youtu\.be)
            (?:/|/watch\?v=|/embed/|/v/|/live/|/shorts/|/videos/)
            ([a-zA-Z0-9_-]{11}) # Video ID
            (?:[&\?].*)?        # Optional query parameters
            $~x';
    
        preg_match($pattern, $clean_input, $matches);
        
        // Check standalone ID format
        if (!isset($matches[1])) {
            if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $clean_input)) {
                return $clean_input;
            }
            return false;
        }
        
        return $matches[1];
    }
}
?>