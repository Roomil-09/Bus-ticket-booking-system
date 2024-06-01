<?php
session_start();

// Unset all of the session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the home page or any other page after logout
header("Location: home.php");
exit;
?>
