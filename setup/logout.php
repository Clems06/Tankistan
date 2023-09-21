<?php
// Initialize the session
require_once '../config.php';
require_once 'remember_me.php';
session_start();
logout();
// Redirect to login page
header("location: login.php");
exit;
?>