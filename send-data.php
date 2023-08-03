<?php
require_once "config.php";

$sql = "UPDATE tanks SET x = ?, y= ?, actions = ?, health = ? WHERE name = ?";

if($stmt = mysqli_prepare($link, $sql)){
    $new_x = trim($_POST["new_x"]);
    $new_y = trim($_POST["new_y"]);
    $new_actions = trim($_POST["new_actions"]);
    $new_health = trim($_POST["new_health"]);
    $user = trim($_POST["user"]);
	mysqli_stmt_bind_param($stmt, "sssss", $new_x, $new_y, $new_actions, $new_health, $user);

    if(mysqli_stmt_execute($stmt)){
        echo "We did it!";
    } else{
        echo "Oops! Something went wrong. Please try again later.";
        }

    mysqli_stmt_close($stmt);
}
?>