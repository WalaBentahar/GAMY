<?php
require_once '../../config.php';

class VideoGuide {
    private $id, $title, $author, $content;

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getTitle() { return $this->title; }
    public function setTitle($title) { $this->title = $title; }

    public function getAuthor() { return $this->author; }
    public function setAuthor($author) { $this->author = $author; }

    public function getContent() { return $this->content; }
    public function setContent($content) { $this->content = $content; }

    public static function getTags($guideId) {
        $conn = config::getConnexion();
        $stmt = $conn->prepare("SELECT tag FROM video_guide_tags WHERE video_guide_id = ?");
        $stmt->execute([$guideId]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'tag');
    }

    public static function getRecommended($currentGuideId, $tags, $author) {
        $conn = config::getConnexion();

        if (empty($tags)) return [];

        $placeholders = implode(',', array_fill(0, count($tags), '?'));
        $params = array_merge($tags, [$author, $currentGuideId]);

        $sql = "
            SELECT vg.* FROM videoguides vg
            JOIN video_guide_tags vgt ON vg.id = vgt.video_guide_id
            WHERE (vgt.tag IN ($placeholders) OR vg.author = ?)
              AND vg.id != ?
            GROUP BY vg.id
            ORDER BY vg.views DESC
            LIMIT 5
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Database Methods
    public static function getAll() {
        $stmt = config::getConnexion()->query("SELECT * FROM videoguides");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $stmt = config::getConnexion()->prepare("SELECT * FROM videoguides WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function add($title, $author, $content) {
        $stmt = config::getConnexion()->prepare("INSERT INTO videoguides (title, author, content) VALUES (?, ?, ?)");
        $stmt->execute([$title, $author, $content]);
    }

    public static function update($id, $title, $author, $content) {
        $stmt = config::getConnexion()->prepare("UPDATE videoguides SET title=?, author=?, content=? WHERE id=?");
        $stmt->execute([$title, $author, $content, $id]);
    }

    public static function delete($id) {
        $stmt = config::getConnexion()->prepare("DELETE FROM videoguides WHERE id=?");
        $stmt->execute([$id]);
    }

    public static function search($query) {
        $stmt = config::getConnexion()->prepare("SELECT * FROM videoguides WHERE title LIKE ? OR author LIKE ?");
        $wildcardQuery = "%" . $query . "%";
        $stmt->execute([$wildcardQuery, $wildcardQuery]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>