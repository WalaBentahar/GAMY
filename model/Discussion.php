<?php

class Discussion {
    private $id;
    private $user_id;
    private $title;
    private $author;
    private $category;
    private $created_at;

    // --- Setters ---
    public function setId($id) {
        $this->id = $id;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setAuthor($author) {
        $this->author = $author;
    }

    public function setCategory($category) {
        $this->category = $category;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }

    // --- Getters ---
    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function getCategory() {
        return $this->category;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }
}
