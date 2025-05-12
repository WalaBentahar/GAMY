<?php
require_once '../../config.php';

class Guide {
    private $id, $title, $author, $content;

    // Getters/Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getTitle() { return $this->title; }
    public function setTitle($title) { $this->title = $title; }

    public function getAuthor() { return $this->author; }
    public function setAuthor($author) { $this->author = $author; }

    public function getContent() { return $this->content; }
    public function setContent($content) { $this->content = $content; }

    // Database Methods
    public static function getAll() {
        $stmt = config::getConnexion()->query("SELECT * FROM guides");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $stmt = config::getConnexion()->prepare("SELECT * FROM guides WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function add($title, $author, $content) {
        $stmt = config::getConnexion()->prepare("INSERT INTO guides (title, author, content) VALUES (?, ?, ?)");
        $stmt->execute([$title, $author, $content]);
    }

    public static function update($id, $title, $author, $content) {
        $stmt = config::getConnexion()->prepare("UPDATE guides SET title=?, author=?, content=? WHERE id=?");
        $stmt->execute([$title, $author, $content, $id]);
    }

    public static function delete($id) {
        $stmt = config::getConnexion()->prepare("DELETE FROM guides WHERE id=?");
        $stmt->execute([$id]);
    }

    public static function search($query) {
        $stmt = config::getConnexion()->prepare("SELECT * FROM guides WHERE title LIKE ? OR author LIKE ?");
        $wildcardQuery = "%" . $query . "%";
        $stmt->execute([$wildcardQuery, $wildcardQuery]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>