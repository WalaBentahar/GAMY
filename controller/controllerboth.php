
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../vendor/autoload.php';
require_once '../config.php';

$conn = config::getConnexion();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Handle form submission
if (isset($_POST['submit_complaint'])) {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    try {
        $query = "INSERT INTO complaints (user_id, name, email, message, complaint_date, submission_date) 
                  VALUES (:user_id, :name, :email, :message, NOW(), NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':message', $message);
        $stmt->execute();

        header("Location: ../view/frontoffice/thank_you.php");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
// Handle response checking
if (isset($_POST['action']) && $_POST['action'] == 'check_responses') {
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

    try {
        if ($user_id <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid user ID']);
            ob_end_flush();
            exit;
        }

        $query = "SELECT c.id, c.message, r.admin_response, r.response_date 
                  FROM complaints c 
                  LEFT JOIN responses r ON c.id = r.complaint_id 
                  WHERE c.user_id = :user_id
                  ORDER BY c.submission_date DESC";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format dates for display
        foreach ($results as &$result) {
            if ($result['response_date']) {
                $result['response_date'] = date('M d, Y H:i', strtotime($result['response_date']));
            }
        }

        header('Content-Type: application/json');
        echo json_encode($results);
        ob_end_flush();
        exit;

    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        ob_end_flush();
        exit;
    }
}

?>