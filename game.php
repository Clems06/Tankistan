<?php

if (!(defined('index_check')) And index_check){
    exit();
}


$sql = "SELECT * FROM games WHERE name=?;";
        
if($stmt = mysqli_prepare($link, $sql)){
    mysqli_stmt_bind_param($stmt, "s", $param_game);
    $param_game = $_REQUEST['game'];
    
    if(mysqli_stmt_execute($stmt)){
    	$result = mysqli_stmt_get_result($stmt);
        if(mysqli_num_rows($result)){                  
            
            //mysqli_stmt_close($stmt);
        } else {
            exit('<div>Game not found<div><a href="index.php">Go back</a>');
        }
    } else {
        exit("Encountered problem");
    }
}

$game = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if ($game["started"]){
	$battlefield_size = $game["size"];
	require 'battlefield.php';
} else if (array_key_exists('start', $_REQUEST) and $_REQUEST["start"] and $game["owner"] == $_SESSION["username"]){
	require "start_game.php";
} else{
	$sql = "SELECT * FROM tanks WHERE name=? AND game_id=?;";
        
	$stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $username, $param_game);
	$username = $_SESSION["username"];
	mysqli_stmt_execute($stmt);
	mysqli_stmt_store_result($stmt);
	
	if(mysqli_stmt_num_rows($stmt) == 0){
		mysqli_stmt_close($stmt);
		$sql = "INSERT INTO tanks (game_id, name) VALUES (?, ?);";
		$stmt = mysqli_prepare($link, $sql);
		mysqli_stmt_bind_param($stmt, "ss", $param_game, $username);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
	} else {
		mysqli_stmt_close($stmt);
	}
	require 'waiting.php';
}


?>