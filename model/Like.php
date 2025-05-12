<?php
class Like {
    private $id;
    private $user_id;
    private $content_type;
    private $content_id;
    private $created_at;

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getContentType() {
        return $this->content_type;
    }

    public function getContentId() {
        return $this->content_id;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    public function setContentType($content_type) {
        $this->content_type = $content_type;
    }

    public function setContentId($content_id) {
        $this->content_id = $content_id;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }
}
?>