<?php
class Comment {
    private $id;
    private $post_id;
    private $user_id;
    private $author;
    private $content;
    private $created_at;

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getPostId() {
        return $this->post_id;
    }

    public function getUserId() {
        return $this->user_id;
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

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setPostId($post_id) {
        $this->post_id = $post_id;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;
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
}
?>