<?php

class Reply {
    private $id;
    private $discussion_id;
    private $author;
    private $content;
    private $created_at;

    // --- Setters ---
    public function setId($id) {
        $this->id = $id;
    }

    public function setDiscussionId($discussion_id) {
        $this->discussion_id = $discussion_id;
    }

    public function setAuthor($author) {
        $this->author = $author;
    }

    public function setContent($content) {
        $this->content = $content;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }

    // --- Getters ---
    public function getId() {
        return $this->id;
    }

    public function getDiscussionId() {
        return $this->discussion_id;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function getContent() {
        return $this->content;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }
}
