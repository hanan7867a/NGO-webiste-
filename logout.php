<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Prevent caching of this page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Redirect to login page after logout
header("Location: login.php");
exit();
?>
