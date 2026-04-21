<?php
session_start();


$_SESSION = [];


session_destroy();

// Redirection vers login
header("Location: login.php");
exit;
?>