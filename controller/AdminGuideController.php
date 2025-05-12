<?php
require_once '../../model/Guide.php';
require_once '../../model/VideoGuide.php';
require_once '../../config.php';

class AdminGuideController {
    public $guides = [];
    public $videoGuides = [];
    public $historique = [];
    public $success = false;
    public $deleted = false;
    public $editGuide = null;

    public function handleRequest() {
        $action = $_GET['action'] ?? '';
        $this->success = isset($_GET['success']);
        $this->deleted = isset($_GET['deleted']);

        switch ($action) {
            case 'add':
                $this->addGuide();
                break;
            case 'edit':
                $this->editGuide();
                break;
            case 'delete':
                $this->deleteGuide();
                break;
            case 'search':
                $this->searchGuides();
                break;
            case 'history':
                $this->showHistory();
                break;
            default:
                $this->showDashboard();
        }
    }

    private function showDashboard() {
        $this->guides = Guide::getAll();
        $this->videoGuides = VideoGuide::getAll();
    }

    private function addGuide() {
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $type = $_POST['type'] ?? 'text';
        $content = trim($_POST['content'] ?? '');

        if (empty($title) || empty($author) || empty($content)) {
            die('Tous les champs doivent être remplis.');
        }

        $pdo = config::getConnexion();

        if ($type === 'video') {
            $stmt = $pdo->prepare("INSERT INTO videoguides (title, author, content) VALUES (?, ?, ?)");
            $stmt->execute([$title, $author, $content]);
            $newVideoId = $pdo->lastInsertId();

            $hist = $pdo->prepare("INSERT INTO historique (action, video_id, title, author) VALUES (?, ?, ?, ?)");
            $hist->execute(['ajout', $newVideoId, $title, $author]);
        } else {
            Guide::add($title, $author, $content);
        }

        $this->success = true;
        $this->showDashboard();
    }

    private function editGuide() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id']);
            $title = trim($_POST['title']);
            $author = trim($_POST['author']);
            $content = trim($_POST['content']);
            $type = $_POST['type'];

            if ($type === 'video') {
                VideoGuide::update($id, $title, $author, $content);
            } else {
                Guide::update($id, $title, $author, $content);
            }

            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("INSERT INTO historique (action, video_id, title, author) VALUES (?, ?, ?, ?)");
            $stmt->execute(['modification', $id, $title, $author]);

            $this->success = true;
            $this->showDashboard();
        } else {
            $id = intval($_GET['id']);
            $type = $_GET['type'];
            $this->editGuide = $type === 'video' ? VideoGuide::getById($id) : Guide::getById($id);
            $this->guides = Guide::getAll();
            $this->videoGuides = VideoGuide::getAll();
        }
    }

    private function deleteGuide() {
        $id = intval($_GET['id']);
        $type = $_GET['type'];
        $pdo = config::getConnexion();

        if ($type === 'video') {
            $stmt = $pdo->prepare("SELECT * FROM videoguides WHERE id = ?");
            $stmt->execute([$id]);
            $video = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($video) {
                $hist = $pdo->prepare("INSERT INTO historique (action, video_id, title, author) VALUES (?, ?, ?, ?)");
                $hist->execute(['suppression', $id, $video['title'], $video['author']]);
            }

            VideoGuide::delete($id);
        } else {
            Guide::delete($id);
        }

        $this->deleted = true;
        $this->showDashboard();
    }

    private function searchGuides() {
        $query = trim($_GET['query'] ?? '');
        $typeFilter = $_GET['filter'] ?? 'all';

        if ($typeFilter === 'text' || $typeFilter === 'all') {
            $this->guides = Guide::search($query);
        }
        if ($typeFilter === 'video' || $typeFilter === 'all') {
            $this->videoGuides = VideoGuide::search($query);
        }
    }

    private function showHistory() {
        $pdo = config::getConnexion();
        $stmt = $pdo->query("SELECT * FROM historique ORDER BY created_at DESC");
        $this->historique = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>