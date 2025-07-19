<?php
session_start();
require 'configer.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $reason = trim($_POST['reason']);

    // Basic validation
    if (empty($full_name) || empty($phone) || empty($email) || empty($reason)) {
        $_SESSION['volunteer_alert'] = "<div class='form-message error'>All fields are required.</div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['volunteer_alert'] = "<div class='form-message error'>Invalid email format.</div>";
    } else {
        // Store in database
        $stmt = $conn->prepare("INSERT INTO volunteers (full_name, phone, email, reason, status) VALUES (?, ?, ?, ?, 'Pending')");
        $stmt->bind_param("ssss", $full_name, $phone, $email, $reason);
        if ($stmt->execute()) {
            $_SESSION['volunteer_alert'] = "<div class='form-message'>Form successfully submitted! You'll receive confirmation soon.</div>";
            $_SESSION['volunteer_email'] = $email; // Store email for status check
        } else {
            $_SESSION['volunteer_alert'] = "<div class='form-message error'>Error: " . htmlspecialchars($conn->error) . "</div>";
        }
        $stmt->close();
    }
    $conn->close();
    header("Location: index.php");
    exit();
}
?>