<?php
require_once "config.php";

$index_start = 1;

// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: setup/login.php");
    exit;
}

?>



<!DOCTYPE html>
<html>
<head>
    <title>Tankistan</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/speech-bubbles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
</head>
<body>
    <div id="top">
        <div class="title"><h1 id="text_title">Tankistan</h1></div>
        <a class="logout" href="setup/logout.php">Log out</a>
        <?php if (array_key_exists('game', $_REQUEST)){include "keys.php";} ?>
    </div>
    <?php if (array_key_exists('game', $_REQUEST)){require "game.php";}
    	else if (array_key_exists('new_game', $_REQUEST) and $_REQUEST["new_game"]){require "new_game.php";}
    	else {require "join_game.php";} 
    ?>
    
</body>
