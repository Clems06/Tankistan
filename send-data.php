<?php
session_start();
require_once "config.php";


$user = $_SESSION["username"];

$id = trim($_POST["id"]);

function get_tank_data($sql_link, $tank_name)
{
    $sql = "SELECT actions, x, y, bullet_range FROM tanks WHERE name = ?;";
    $actions = $x = $y = $range = "";

    if($stmt = mysqli_prepare($sql_link, $sql)){
        mysqli_stmt_bind_param($stmt, "s", $param_username);
        $param_username = $tank_name;
    
        if(mysqli_stmt_execute($stmt)){

            mysqli_stmt_store_result($stmt);
            
            if(mysqli_stmt_num_rows($stmt) == 1){                    

                mysqli_stmt_bind_result($stmt, $actions, $x, $y, $range);
                mysqli_stmt_fetch($stmt);
                return [$actions, $x, $y, $range];
                //if(mysqli_stmt_fetch($stmt)){
            }
        }
    }
};

[$actions, $x, $y, $range] = get_tank_data($link, $user);

if ($actions == 0 or $x<0 or $y<0){
    exit("Not allowed");
};

/*
Ids:
1) Move UP
2) Move DOWN
3) Move LEFT
4) Move RIGHT
5) Attack
6) Give action
*/
if ($id==1){
    $sql = "UPDATE tanks SET actions = actions - 1, y = y - 1 WHERE name = ?;";
} elseif ($id==2) {
    $sql = "UPDATE tanks SET actions = actions - 1, y = y + 1 WHERE name = ?;";
} elseif ($id==3) {
    $sql = "UPDATE tanks SET actions = actions - 1, x = x - 1 WHERE name = ?;";
} elseif ($id==4) {
    $sql = "UPDATE tanks SET actions = actions - 1, x = x + 1 WHERE name = ?;";
} elseif ($id==5 or $id==6) {
    $other = $_POST["other"];
    [$other_actions, $other_x, $other_y, $other_range] = get_tank_data($link, $other);

    if ($other_x - $x > $range or $other_y - $y > $range){
        exit("Not allowed");
    };

    $sql = "UPDATE tanks SET actions = actions - 1 WHERE name = ?;";
};

$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "s", $param_username);
$param_username = $user;
mysqli_stmt_execute($stmt);

if ($id==5){
    $sql = "UPDATE tanks SET health = health - 1 WHERE name = ?;";
} elseif ($id==6) {
    $sql = "UPDATE tanks SET actions = actions + 1 WHERE name = ?;";
}

$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "s", $param_username);
$param_username = $other;
mysqli_stmt_execute($stmt);

/*
if($stmt = mysqli_prepare($link, $sql)){
    $new_x = trim($_POST["new_x"]);
    $new_y = trim($_POST["new_y"]);
    $new_actions = trim($_POST["new_actions"]);
    $new_health = trim($_POST["new_health"]);
    $user = trim($_POST["user"]);
	mysqli_stmt_bind_param($stmt, "sssss", $new_x, $new_y, $new_actions, $new_health, $user);

    if(mysqli_stmt_execute($stmt)){
        echo "We did it!";
        echo $_SESSION["username"];
    } else{
        echo "Oops! Something went wrong. Please try again later.";
        }

    mysqli_stmt_close($stmt);
}*/
?>