<?php
class Category {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    public function getAllCategories() {
        $query = "SELECT * FROM categories";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>