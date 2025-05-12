<?php
require_once  '../../config.php';

class Category {
    private $db;

    public function __construct() {
        $this->db = Config::getConnexion();
    }

    public function getAllCategories() {
        $query = "SELECT id, name FROM categories ORDER BY name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>