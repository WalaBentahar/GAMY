<?php
require_once  '../../config.php';
require_once  '../../model/Video.php';
require_once  '../../model/Category.php';
require_once  '../../model/Stream.php';

class AdminController {
    private $videoModel;
    private $categoryModel;
    private $streamModel;

    public function __construct() {
        $this->videoModel = new Video();
        $this->categoryModel = new Category();
        $this->streamModel = new Stream();
    }

    public function dashboard() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['delete'])) {
                try {
                    $this->videoModel->deleteVideo($_POST['id']);
                    $_SESSION['success'] = 'Video deleted successfully.';
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Failed to delete video: ' . $e->getMessage();
                }
            } else {
                $title = $_POST['title'] ?? '';
                $videoUrl = $_POST['video_url'] ?? '';
                $categoryId = $_POST['category_id'] ?? '';

                try {
                    if (empty($title) || empty($videoUrl) || empty($categoryId)) {
                        throw new Exception('All fields are required.');
                    }
                    $embedCode = $this->convertUrlToEmbedCode($videoUrl);
                    $this->videoModel->addVideo($title, $embedCode, $categoryId);
                    $_SESSION['success'] = 'Video added successfully.';
                } catch (Exception $e) {
                    $_SESSION['error'] = $e->getMessage();
                }
            }
            header('Location: ../../view/backoffice/stream_dashboard.php');
            exit;
        }

        return [
            'videos' => $this->videoModel->getAllVideos(),
            'categories' => $this->categoryModel->getAllCategories()
        ];
    }

    public function addStream() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $streamInput = $_POST['stream_input'] ?? '';
            $categoryId = $_POST['category_id'] ?? '';

            try {
                if (empty($title) || empty($streamInput) || empty($categoryId)) {
                    throw new Exception('All fields are required.');
                }
                $streamId = $this->extractStreamId($streamInput);
                $this->streamModel->addStream($title, $streamId, $categoryId);
                $_SESSION['success'] = 'Stream added successfully.';
                header('Location: ../../view/backoffice/index.php');
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
        }

        return [
            'categories' => $this->categoryModel->getAllCategories()
        ];
    }

    public function manageStreams() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
            try {
                $this->streamModel->deleteStream($_POST['id']);
                $_SESSION['success'] = 'Stream deleted successfully.';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Failed to delete stream: ' . $e->getMessage();
            }
            header('Location: ../../view/backoffice/stream_dashboard.php');
            exit;
        }

        return [
            'streams' => $this->streamModel->getAll()
        ];
    }

    private function convertUrlToEmbedCode($url) {
        if (empty($url)) {
            throw new Exception('YouTube URL is required.');
        }

        $videoId = '';
        if (strpos($url, 'youtube.com/watch?v=') !== false) {
            parse_str(parse_url($url, PHP_URL_QUERY), $params);
            $videoId = $params['v'] ?? '';
        } elseif (strpos($url, 'youtu.be/') !== false) {
            $videoId = substr(parse_url($url, PHP_URL_PATH), 1);
        } elseif (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
            $videoId = $url;
        }

        $videoId = preg_replace('/[^a-zA-Z0-9_-]/', '', $videoId);
        if (empty($videoId)) {
            throw new Exception('Invalid YouTube URL or ID.');
        }

        return '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $videoId . '" frameborder="0" allowfullscreen></iframe>';
    }

    private function extractStreamId($input) {
        if (empty($input)) {
            throw new Exception('YouTube stream URL or ID is required.');
        }

        $streamId = '';
        if (strpos($input, 'youtube.com/watch?v=') !== false) {
            parse_str(parse_url($input, PHP_URL_QUERY), $params);
            $streamId = $params['v'] ?? '';
        } elseif (strpos($input, 'youtu.be/') !== false) {
            $streamId = substr(parse_url($input, PHP_URL_PATH), 1);
        } elseif (preg_match('/^[a-zA-Z0-9_-]{11}$/', $input)) {
            $streamId = $input;
        }

        $streamId = preg_replace('/[^a-zA-Z0-9_-]/', '', $streamId);
        if (empty($streamId)) {
            throw new Exception('Invalid YouTube stream URL or ID.');
        }

        return $streamId;
    }
}
?>