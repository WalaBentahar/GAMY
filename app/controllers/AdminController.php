<?php
class AdminController {
    public function dashboard() {
        require_once __DIR__ . '/../models/Video.php';
        require_once __DIR__ . '/../models/Category.php';
        $videoModel = new Video();
        $categoryModel = new Category();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['delete'])) {
                $videoModel->deleteVideo($_POST['id']);
            } else {
                $title = $_POST['title'];
                $videoUrl = $_POST['video_url']; // Corrected field name
                $categoryId = $_POST['category_id'];

                try {
                    $embedCode = $this->convertUrlToEmbedCode($videoUrl);
                    $videoModel->addVideo($title, $embedCode, $categoryId);
                } catch (Exception $e) {
                    $_SESSION['error'] = $e->getMessage();
                }
            }
        }

        $videos = $videoModel->getAllVideos();
        $categories = $categoryModel->getAllCategories();
        include __DIR__ . '/../views/admin/dashboard.php';
    }

    private function convertUrlToEmbedCode($url) {
        if (empty($url)) {
            throw new Exception("YouTube URL is required.");
        }

        $videoId = '';
        if (strpos($url, 'youtube.com/watch?v=') !== false) {
            parse_str(parse_url($url, PHP_URL_QUERY), $params);
            $videoId = $params['v'] ?? '';
        } elseif (strpos($url, 'youtu.be/') !== false) {
            $videoId = substr(parse_url($url, PHP_URL_PATH), 1);
        }

        $videoId = preg_replace('/[^a-zA-Z0-9_-]/', '', $videoId);
        if (empty($videoId)) {
            throw new Exception("Invalid YouTube URL.");
        }

        return '<iframe width="560" height="315" src="https://www.youtube.com/embed/'.$videoId.'" frameborder="0" allowfullscreen></iframe>';
    }
}

?>