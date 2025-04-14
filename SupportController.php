<?php
require_once __DIR__ . '/../models/Complaint.php';

class SupportController {
    public function form() {
        require __DIR__ . '/../views/support/form.php';
    }

    public function submit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (Complaint::create($_POST)) {
                header("Location: /gaming_website/support/success");
                exit;
            }
        }
        die("Submission failed");
    }

    public function success() {
        require __DIR__ . '/../views/support/success.php';
    }
}
?>