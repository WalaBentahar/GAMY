<?php
class Database {
    public static function connect() {
        try {
            return new PDO(
                'mysql:host=localhost;dbname=gamezone_db',
                'root',
                '',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
}
?>