<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../../vendor/autoload.php';
require_once '../../config.php';

$conn = config::getConnexion();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// Handle response submission
if (isset($_POST['submit_response'])) {
    $complaint_id = $_POST['complaint_id'];
    $admin_response = $_POST['admin_response'];
    
    try {
        // 1. Get the user's email and complaint details
        $stmt = $conn->prepare("SELECT * FROM complaints WHERE id = :id");
        $stmt->bindParam(':id', $complaint_id);
        $stmt->execute();
        $complaint = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // 2. Save response to database
        $query = "INSERT INTO responses (complaint_id, admin_response, response_date) 
                  VALUES (:complaint_id, :admin_response, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':complaint_id', $complaint_id);
        $stmt->bindParam(':admin_response', $admin_response);
        $stmt->execute();
        
        // 3. Mark complaint as resolved
        $stmt = $conn->prepare("UPDATE complaints SET resolved = 1 WHERE id = :id");
        $stmt->bindParam(':id', $complaint_id);
        $stmt->execute();
        
        if (isset($_POST['send_email']) && $_POST['send_email'] == '1') {
            $mail = new PHPMailer(true);
            
            try {
                // Server settings with debugging
                $mail->SMTPDebug = 3; // Verbose debug output
                $mail->Debugoutput = function($str, $level) {
                    file_put_contents(__DIR__ . '/smtp_debug.log', "[$level] $str\n", FILE_APPEND);
                };
                
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'azzizzarrouk0045@gmail.com'; // REPLACE WITH YOUR GMAIL
                $mail->Password = 'eabc jgdz rgsw yimp';    // REPLACE WITH APP PASSWORD
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                
                // Recipients
                $mail->setFrom('your.email@gmail.com', 'Gamix Support'); // MUST match your Gmail
                $mail->addAddress($complaint['email'], $complaint['name']);
                $mail->addReplyTo('support@gamix.com', 'Gamix Support');
                
                // Content
                $mail->isHTML(true);
                $mail->Subject = "Re: Your Support Ticket #$complaint_id";
                $mail->Body = "
                    <h2 style='color:#ff0000'>Gamix Support Response</h2>
                    <p><strong>Ticket #$complaint_id</strong></p>
                    <div style='background:#f5f5f5;padding:15px;border-left:3px solid #ff0000;margin:10px 0'>
                        <p><strong>Your message:</strong><br>
                        " . nl2br(htmlspecialchars($complaint['message'])) . "</p>
                        <hr>
                        <p><strong>Our response:</strong><br>
                        " . nl2br(htmlspecialchars($admin_response)) . "</p>
                    </div>
                    <p>Thank you for contacting Gamix Support!</p>
                ";
                $mail->AltBody = "Response to your ticket #$complaint_id:\n\n" . $admin_response;
                
                // Final verification before sending
                file_put_contents(__DIR__ . '/email_debug.log', 
                    "Attempting to send to: " . $complaint['email'] . "\n" .
                    "Using SMTP: " . $mail->Host . ":" . $mail->Port . "\n",
                    FILE_APPEND
                );
                
                if (!$mail->send()) {
                    throw new Exception("Mailer Error: " . $mail->ErrorInfo);
                }
                
                file_put_contents(__DIR__ . '/email_debug.log', "Email sent successfully!\n", FILE_APPEND);
                
            } catch (Exception $e) {
                $error_msg = "Email failed: " . $e->getMessage();
                file_put_contents(__DIR__ . '/email_errors.log', date('Y-m-d H:i:s') . " - " . $error_msg . "\n", FILE_APPEND);
                // Continue execution even if email fails
            }
        }
        
        header("Location: dashboard.php?success=1");
        exit();
        
    } catch (Exception $e) {
        $error_msg = "System error: " . $e->getMessage();
        file_put_contents(__DIR__ . '/email_errors.log', date('Y-m-d H:i:s') . " - " . $error_msg . "\n", FILE_APPEND);
        header("Location: dashboard.php?error=" . urlencode($error_msg));
        exit();
    }
}

// Handle edit action
if (isset($_POST['edit_complaint'])) {
    $id = $_POST['id'];
    $message = $_POST['message'];
    
    try {
        $stmt = $conn->prepare("UPDATE complaints SET message = :message WHERE id = :id");
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

// Fetch complaints with responses
$query = "SELECT c.*, r.admin_response, r.response_date 
          FROM complaints c 
          LEFT JOIN responses r ON c.id = r.complaint_id 
          ORDER BY c.submission_date DESC";
$complaints = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Handle delete action
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        // First delete any responses
        $stmt = $conn->prepare("DELETE FROM responses WHERE complaint_id = ?");
        $stmt->execute([$id]);
        
        // Then delete the complaint
        $stmt = $conn->prepare("DELETE FROM complaints WHERE id = ?");
        $stmt->execute([$id]);
        
        header("Location: dashboard.php");
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
