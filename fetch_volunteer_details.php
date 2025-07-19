<?php
header('Content-Type: application/json');
include('configer.php');

// Enable error reporting and logging
ini_set('display_errors', 0); // Disable display to prevent output corruption
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');
error_reporting(E_ALL);

// Start output buffering to catch any accidental output
ob_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    error_log("Invalid or missing ID: " . ($_GET['id'] ?? 'null'));
    echo json_encode(['error' => 'Invalid or missing ID']);
    ob_end_flush();
    exit;
}

$id = intval($_GET['id']);

// Prepare and execute query
$stmt = $conn->prepare("SELECT id, full_name, email, phone, reason, submitted_at FROM volunteers WHERE id = ?");
if ($stmt === false) {
    http_response_code(500);
    error_log("Prepare failed: " . $conn->error);
    echo json_encode(['error' => 'Database query preparation failed']);
    ob_end_flush();
    exit;
}

$stmt->bind_param("i", $id);
if (!$stmt->execute()) {
    http_response_code(500);
    error_log("Execute failed: " . $stmt->error);
    echo json_encode(['error' => 'Database query execution failed']);
    ob_end_flush();
    exit;
}

$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    echo json_encode($row);
} else {
    http_response_code(404);
    error_log("Volunteer not found for ID: " . $id);
    echo json_encode(['error' => 'Volunteer not found']);
}

$stmt->close();
$conn->close();

// Flush and clean output buffer
ob_end_flush();
?>