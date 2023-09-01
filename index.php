<?php
define("index_check", true);

// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: setup/login.php");
    exit;
}

require_once "config.php";

if (array_key_exists('delete_game', $_REQUEST)){
       
    $sql = "SELECT * FROM games WHERE name=? AND owner=?;";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $param_game, $_SESSION["username"]);
    $param_game = $_REQUEST['game'];
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if(!mysqli_num_rows($result)){
       header("location: ?");
    }
    mysqli_stmt_close($stmt);

    $sql = "DELETE FROM tanks WHERE game_id=?;";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $param_game);
    $param_game = $_REQUEST['delete_game'];
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "DELETE FROM games WHERE name=?;";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $param_game);
    $param_game = $_REQUEST['delete_game'];
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("location: ?");
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
    	<a href="http://<?php echo $_SERVER['HTTP_HOST'] ;?>"><img id="home" src="static/home.png" alt="home"></img></a>
        <div class="title"><h1 id="text_title">Tankistan</h1></div>
        <a class="logout" href="setup/logout.php">Log out</a>
        <?php if (array_key_exists('game', $_REQUEST)){include "keys.php";} ?>
    </div>
    <?php if (array_key_exists('game', $_REQUEST)){require "game.php";}
    	else if (array_key_exists('new_game', $_REQUEST) and $_REQUEST["new_game"]){require "new_game.php";}
    	else {require "join_game.php";} 
    ?>
    
</body>
