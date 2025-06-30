<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $country = $_POST['country'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $background_color = $_POST['background_color'] ?? 'rgb(75, 74, 74)';

    // Validation
    if (empty($name) || empty($email) || empty($country)) {
        $_SESSION['error'] = "All fields except password are required.";
        header("Location: profile.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Please enter a valid email address.";
        header("Location: profile.php");
        exit();
    }

    // Check if email already exists for another user
    $emailCheckStmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $emailCheckStmt->bind_param("si", $email, $user_id);
    $emailCheckStmt->execute();
    $emailResult = $emailCheckStmt->get_result();
    
    if ($emailResult->num_rows > 0) {
        $_SESSION['error'] = "This email address is already registered to another account.";
        header("Location: profile.php");
        exit();
    }

    try {
        $conn->begin_transaction();

        // Handle password update if provided
        if (!empty($new_password)) {
            if (empty($current_password)) {
                throw new Exception("Current password is required to set a new password.");
            }

            if ($new_password !== $confirm_password) {
                throw new Exception("New passwords do not match.");
            }

            if (strlen($new_password) < 6) {
                throw new Exception("New password must be at least 6 characters long.");
            }

            // Verify current password
            $passStmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $passStmt->bind_param("i", $user_id);
            $passStmt->execute();
            $passResult = $passStmt->get_result();
            $userPass = $passResult->fetch_assoc();

            if (!password_verify($current_password, $userPass['password'])) {
                throw new Exception("Current password is incorrect.");
            }

            // Update with new password
            $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
            $updateStmt = $conn->prepare("UPDATE users SET name = ?, email = ?, country = ?, password = ? WHERE id = ?");
            $updateStmt->bind_param("ssssi", $name, $email, $country, $hashedPassword, $user_id);
        } else {
            // Update without password change
            $updateStmt = $conn->prepare("UPDATE users SET name = ?, email = ?, country = ? WHERE id = ?");
            $updateStmt->bind_param("sssi", $name, $email, $country, $user_id);
        }

        $updateStmt->execute();

        if ($updateStmt->affected_rows === 0) {
            throw new Exception("No changes were made to your profile.");
        }

        // Update session variables
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['country'] = $country;

        // Set background color cookie
        setcookie('user_color', $background_color, time() + (86400 * 30), '/'); // 30 days

        $conn->commit();

        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: profile.php");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        
        error_log("Error updating profile: " . $e->getMessage());
        
        $_SESSION['error'] = $e->getMessage();
        header("Location: profile.php");
        exit();
    }

} else {
    header("Location: profile.php");
    exit();
}
?>
