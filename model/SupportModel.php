
<?php
class SupportModel {
    // Private properties
    private $id;
    private $user_id;
    private $name;
    private $email;
    private $message;
    private $complaint_date;
    private $submission_date;
    private $resolved;

    // Getter methods
    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getComplaintDate() {
        return $this->complaint_date;
    }

    public function getSubmissionDate() {
        return $this->submission_date;
    }

    public function getResolved() {
        return $this->resolved;
    }

    // Setter methods
    public function setId($id) {
        $this->id = $id;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function setComplaintDate($complaint_date) {
        $this->complaint_date = $complaint_date;
    }

    public function setSubmissionDate($submission_date) {
        $this->submission_date = $submission_date;
    }

    public function setResolved($resolved) {
        $this->resolved = $resolved;
    }
}
?>
