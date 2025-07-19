<?php
session_start();
require 'configer.php';

// Check if admin is logged in
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = (int)$_GET['id'];

    // Fetch volunteer details
    $stmt = $conn->prepare("SELECT full_name, email, status FROM volunteers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($volunteer = $result->fetch_assoc()) {
        if ($volunteer['status'] !== 'Pending') {
            $_SESSION['admin_alert'] = "<div class='form-message error'>This application has already been processed.</div>";
        } else {
            // Update status
            $new_status = ($action === 'approve') ? 'Approved' : 'Rejected';
            $update_stmt = $conn->prepare("UPDATE volunteers SET status = ? WHERE id = ?");
            $update_stmt->bind_param("si", $new_status, $id);
            if ($update_stmt->execute()) {
                // Send email notification
                $to = $volunteer['email'];
                $subject = "Rah-e-Umeed Volunteer Application Status";
                $message = "Dear " . htmlspecialchars($volunteer['full_name']) . ",\n\n";
                $message .= "Thank you for applying to volunteer with Rah-e-Umeed.\n";
                if ($new_status === 'Approved') {
                    $message .= "We are thrilled to inform you that your application has been APPROVED! You are now part of our volunteer team. Please contact us at abdulhanan7867a@gmail.com for next steps.\n";
                } else {
                    $message .= "We regret to inform you that your application was not approved at this time. Thank you for your interest in Rah-e-Umeed.\n";
                }
                $message .= "\nBest regards,\nRah-e-Umeed Team";
                $headers = "From: abdulhanan7867a@gmail.com\r\n";
                
                if (mail($to, $subject, $message, $headers)) {
                    $_SESSION['admin_alert'] = "<div class='form-message'>Volunteer application " . strtolower($new_status) . " and notification sent.</div>";
                } else {
                    $_SESSION['admin_alert'] = "<div class='form-message error'>Application " . strtolower($new_status) . ", but failed to send email notification.</div>";
                }
                $update_stmt->close();
            } else {
                $_SESSION['admin_alert'] = "<div class='form-message error'>Error updating status: " . htmlspecialchars($conn->error) . "</div>";
            }
        }
    } else {
        $_SESSION['admin_alert'] = "<div class='form-message error'>Volunteer application not found.</div>";
    }
    $stmt->close();
} else {
    $_SESSION['admin_alert'] = "<div class='form-message error'>Invalid request.</div>";
}

$conn->close();
header("Location: admin portal.php");
exit();
?>