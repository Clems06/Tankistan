<?php
session_start();
require_once "config.php";


$user = $_SESSION["username"];

$id = trim($_POST["id"]);
$game_id = trim($_POST["game"]);

function get_tank_data($sql_link, $tank_name, $game_name)
{
    $sql = "SELECT * FROM tanks WHERE name = ? AND game_id = ?;";
    $actions = $x = $y = $range = $name = "";

    if($stmt = mysqli_prepare($sql_link, $sql)){
        mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_game);
        $param_username = $tank_name;
        $param_game = $game_name;
    
        if(mysqli_stmt_execute($stmt)){

            $result = mysqli_stmt_get_result($stmt);
            
            if(mysqli_num_rows($result) == 1){      

                $row = mysqli_fetch_assoc($result);       

                return $row;
                //if(mysqli_stmt_fetch($stmt)){
            }
        }
    }
};

$tank = get_tank_data($link, $user, $game_id);


$name = $tank["name"];
$x = $tank["x"];
$y = $tank["y"];
$range = $tank["bullet_range"];

if ($tank["actions"] == 0 or $x<0 or $y<0){
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
if ($id <= 4){
    if ($id==1){
        $sql = "UPDATE tanks SET actions = actions - 1, y = y - 1 WHERE name = ? AND game_id=?;";
        $new_x = $x;
        $new_y = $y - 1;
    } elseif ($id==2) {
        $sql = "UPDATE tanks SET actions = actions - 1, y = y + 1 WHERE name = ? AND game_id=?;";
        $new_x = $x;
        $new_y = $y + 1;
    } elseif ($id==3) {
        $sql = "UPDATE tanks SET actions = actions - 1, x = x - 1 WHERE name = ? AND game_id=?;";
        $new_x = $x - 1;
        $new_y = $y;
    } elseif ($id==4) {
        $sql = "UPDATE tanks SET actions = actions - 1, x = x + 1 WHERE name = ? AND game_id=?;";
        $new_x = $x + 1;
        $new_y = $y;
    }

    $check_if_empty = "SELECT id FROM tanks WHERE x = ? AND y = ? AND game_id=?;";
    if($stmt = mysqli_prepare($link, $check_if_empty)){
        mysqli_stmt_bind_param($stmt, "sss", $new_x, $new_y, $game_id);
        if(mysqli_stmt_execute($stmt)){

            mysqli_stmt_store_result($stmt);
            
            if(mysqli_stmt_num_rows($stmt) > 0){                    
                exit("Not allowed");
            }
            mysqli_stmt_close($stmt);
        }
    }

    $check_map = "SELECT map, size FROM games WHERE name=?;";
    if($stmt = mysqli_prepare($link, $check_map)){
        mysqli_stmt_bind_param($stmt, "s", $game_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_row($result);
        [$map, $size] = $row;
        $map_cell = $map[$new_x+$size*$new_y];
        if ($map_cell=="W" Or $map_cell=="R"){
            exit("Not allowed");
        }
        mysqli_stmt_close($stmt);
            
    }


} elseif ($id==5 or $id==6) {
    $other_name = $_POST["other"];

    $other = get_tank_data($link, $other_name, $game_id);
    $other_x = $other["x"];
    $other_y = $other["y"];

    if ($other_x - $x > $range or $other_y - $y > $range){
        exit("Not allowed");
    };

    $sql = "UPDATE tanks SET actions = actions - 1 WHERE name = ? AND game_id=?;";
};

$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "ss", $param_username, $game_id);
$param_username = $user;
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($id == 5 or $id == 6){
    if ($id==5){
        if ($other["health"] == 1){
            $sql = "UPDATE tanks SET health = 0, x = -2, y = -2 WHERE name = ? AND game_id=?;";
        } else {
            $sql = "UPDATE tanks SET health = health - 1 WHERE name = ? AND game_id=?;";
        }
    } elseif ($id==6) {
        $sql = "UPDATE tanks SET actions = actions + 1 WHERE name = ? AND game_id=?;";
    }

    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $param_username, $game_id);
    $param_username = $other_name;
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

$sql = "INSERT INTO logs (author, action_id, other, game_id) VALUES (?, ?, ?, ?);";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "ssss", $user, $id, $other_name, $game_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

require 'get-map.php';
echo json_encode($visible);
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