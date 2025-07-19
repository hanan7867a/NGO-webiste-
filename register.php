<?php
require 'configer.php';

$username = 'hanan'; // change to desired username
$password = password_hash('hanan0011', PASSWORD_DEFAULT); // secure password hash

$sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
if ($conn->query($sql) === TRUE) {
    echo "User registered successfully!";
} else {
    echo "Error: " . $conn->error;
}
?>
