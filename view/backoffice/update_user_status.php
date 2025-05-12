<?php
require_once '../../config.php';
require_once '../../controller/UserController.php';

// Check if user is admin (add this security check)

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
        $newStatus = filter_input(INPUT_POST, 'new_status', FILTER_VALIDATE_INT);
        
        $pdo = config::getConnexion();
        $userController = new UserController($pdo);
        
        // Update status through controller
        $success = $userController->updateUserStatus($userId, $newStatus);
        
        if ($success) {
            $_SESSION['flash_message'] = "User status updated successfully";
            header("Location: table.php");
            exit();
        }
    } catch (InvalidArgumentException $e) {
        $error = "Validation error: " . $e->getMessage();
    } catch (RuntimeException $e) {
        $error = "Operation failed: " . $e->getMessage();
    } catch (Exception $e) {
        $error = "An unexpected error occurred: " . $e->getMessage();
    }
    
    // If we got here, there was an error
    $_SESSION['flash_error'] = $error ?? "Unknown error occurred";
    header("Location: table.php");
    exit();
}

// If not POST request, redirect
header("Location: table.php");
exit();
?>