<?php
include('configer.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $field = $_POST['field'];
    $value = $_POST['value'];

    $allowed_fields = ['name', 'phone', 'amount'];
    $field_map = ['name' => 'ame', 'phone' => 'phone', 'amount' => 'amount'];

    if (!in_array($field, array_keys($field_map))) {
        http_response_code(400);
        echo "Invalid field";
        exit;
    }

    $db_field = $field_map[$field];

    $stmt = $conn->prepare("UPDATE donations SET $db_field = ? WHERE id = ?");
    $stmt->bind_param("si", $value, $id);

    if ($stmt->execute()) {
        echo "Updated";
    } else {
        http_response_code(500);
        echo "Error updating record";
    }

    $stmt->close();
    $conn->close();
}
?>
