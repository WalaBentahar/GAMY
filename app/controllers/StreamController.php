<?php
require_once __DIR__ . '/../models/Stream.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Video.php';
class StreamController {
    private $streamModel;
    private $categoryModel;

    public function __construct() {
        $this->streamModel = new Stream();
        $this->categoryModel = new Category();
    }

    public function index() {
        // Handle sorting
        $sort = $_GET['sort'] ?? 'title_asc';
        $validSorts = [
            'title_asc' => 'title ASC',
            'title_desc' => 'title DESC',
            'newest' => 'id DESC',
            'oldest' => 'id ASC'
        ];
        $orderBy = $validSorts[$sort] ?? 'title ASC';
    
        // Handle ADMIN section FIRST
        if (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) {
            // Admin streams CRUD (no search/sorting)
            $streams = $this->streamModel->getAll('title ASC'); // Default admin sorting
            $categories = $this->categoryModel->getAllCategories();
            include VIEWS_PATH . 'admin/streams/index.php';
            return;
        }
    
        // Handle FRONTEND (search/sorting)
        if (isset($_GET['search_query'])) {
            $searchQuery = trim($_GET['search_query']);
            $streamModel = new Stream();
            $videoModel = new Video();
            $streams = $streamModel->searchStreams($searchQuery, $orderBy);
            $videos = $videoModel->searchVideos($searchQuery, $orderBy);
        } else {
            // Regular frontend listing
            $streamModel = new Stream();
            $videoModel = new Video();
            $streams = $streamModel->getAll($orderBy);
            $videos = $videoModel->getAllVideos($orderBy);
        }
    
        include VIEWS_PATH . 'streams/index.php';
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
            $stream_id = $this->parseYoutubeId($stream_input);
    
            // Check if stream_id already exists
            $existing = $this->streamModel->getStreamByYoutubeId($stream_id);
            if ($existing) {
                $_SESSION['error'] = "This stream already exists in the database";
                header('Location: ' . ADMIN_URL . '/streams/add');
                exit;
            }
            
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
    public function live() {  // Renommez showLive() en live()
        $stream_id = $_GET['id'] ?? null;
        $stream = $this->streamModel->getStreamById($stream_id);
        
        if (!$stream) {
            header('Location: ' . BASE_URL . 'streams');
            exit;
        }
        
        include VIEWS_PATH . 'streams/live.php';
    }
    
}
?>