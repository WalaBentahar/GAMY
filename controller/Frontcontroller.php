<?php
require_once '../../model/guide.php';
require_once '../../model/videoguide.php';

class FrontController {
    public $videoGuides = [];
    public $guides = [];
    public $errors = [];
    public $guide = null;
    public $success = false;
    public $recommendedVideos = [];

    public function handleRequest() {
        // Initialize data
        $this->videoGuides = VideoGuide::getAll();
        $this->guides = Guide::getAll();
        $this->success = isset($_GET['success']);
        
        if (!empty($this->videoGuides)) {
            $firstVideo = $this->videoGuides[0];
            $tags = VideoGuide::getTags($firstVideo['id']);
            $this->recommendedVideos = VideoGuide::getRecommended($firstVideo['id'], $tags, $firstVideo['author']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($_POST['action']) && $_POST['action'] === 'update') {
                $this->updateGuide();
            } elseif (!empty($_POST['action']) && $_POST['action'] === 'search') {
                $this->searchGuides();
            } else {
                $this->submitGuide();
            }
        } elseif (!empty($_GET['action'])) {
            if ($_GET['action'] === 'edit') {
                $this->showEditForm();
            } elseif ($_GET['action'] === 'delete') {
                $this->deleteGuide();
            }
        }
    }

    private function submitGuide() {
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $type = $_POST['type'] ?? 'text';
        $content = trim($_POST['content'] ?? '');

        if (empty($title) || empty($author) || empty($content)) {
            $this->errors[] = 'Tous les champs doivent être remplis.';
            return;
        }

        try {
            if ($type === 'video') {
                VideoGuide::add($title, $author, $content);
            } else {
                Guide::add($title, $author, $content);
            }
            $this->success = true;
            $this->videoGuides = VideoGuide::getAll();
            $this->guides = Guide::getAll();
            $this->recommendedVideos = [];
            if (!empty($this->videoGuides)) {
                $firstVideo = $this->videoGuides[0];
                $tags = VideoGuide::getTags($firstVideo['id']);
                $this->recommendedVideos = VideoGuide::getRecommended($firstVideo['id'], $tags, $firstVideo['author']);
            }
        } catch (Exception $e) {
            $this->errors[] = 'Erreur lors de l\'ajout du guide : ' . $e->getMessage();
        }
    }

    private function showEditForm() {
        $id = $_GET['id'] ?? null;
        $type = $_GET['type'] ?? 'text';

        if (!$id) {
            $this->errors[] = 'ID manquant';
            return;
        }

        try {
            $this->guide = $type === 'video' ? VideoGuide::getById($id) : Guide::getById($id);
            if (!$this->guide) {
                $this->errors[] = 'Guide introuvable.';
                return;
            }
            if ($type === 'video') {
                $tags = VideoGuide::getTags($id);
                $this->recommendedVideos = VideoGuide::getRecommended($id, $tags, $this->guide['author']);
            }
        } catch (Exception $e) {
            $this->errors[] = 'Erreur : ' . $e->getMessage();
        }
    }

    private function deleteGuide() {
        $id = $_GET['id'] ?? null;
        $type = $_GET['type'] ?? 'text';

        if (!$id) {
            $this->errors[] = 'ID manquant';
            return;
        }

        try {
            if ($type === 'video') {
                VideoGuide::delete($id);
            } else {
                Guide::delete($id);
            }
            $this->success = true;
            $this->videoGuides = VideoGuide::getAll();
            $this->guides = Guide::getAll();
            $this->recommendedVideos = [];
            if (!empty($this->videoGuides)) {
                $firstVideo = $this->videoGuides[0];
                $tags = VideoGuide::getTags($firstVideo['id']);
                $this->recommendedVideos = VideoGuide::getRecommended($firstVideo['id'], $tags, $firstVideo['author']);
            }
        } catch (Exception $e) {
            $this->errors[] = 'Erreur lors de la suppression : ' . $e->getMessage();
        }
    }

    private function updateGuide() {
        $id = $_POST['id'] ?? null;
        $type = $_POST['type'] ?? 'text';
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if (empty($id) || empty($title) || empty($author) || empty($content)) {
            $this->errors[] = 'Tous les champs doivent être remplis.';
            return;
        }

        try {
            if ($type === 'video') {
                VideoGuide::update($id, $title, $author, $content);
            } else {
                Guide::update($id, $title, $author, $content);
            }
            $this->success = true;
            $this->guide = null;
            $this->videoGuides = VideoGuide::getAll();
            $this->guides = Guide::getAll();
            $this->recommendedVideos = [];
            if (!empty($this->videoGuides)) {
                $firstVideo = $this->videoGuides[0];
                $tags = VideoGuide::getTags($firstVideo['id']);
                $this->recommendedVideos = VideoGuide::getRecommended($firstVideo['id'], $tags, $firstVideo['author']);
            }
        } catch (Exception $e) {
            $this->errors[] = 'Erreur lors de la mise à jour : ' . $e->getMessage();
        }
    }

    private function searchGuides() {
        $query = trim($_POST['search'] ?? '');
        if (!empty($query)) {
            $this->videoGuides = VideoGuide::search($query);
            $this->guides = Guide::search($query);
        }
        $this->recommendedVideos = [];
        if (!empty($this->videoGuides)) {
            $firstVideo = $this->videoGuides[0];
            $tags = VideoGuide::getTags($firstVideo['id']);
            $this->recommendedVideos = VideoGuide::getRecommended($firstVideo['id'], $tags, $firstVideo['author']);
        }
    }
}
?>