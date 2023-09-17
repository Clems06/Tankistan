<?php

if (!(defined('index_check'))){
    exit();
}

require_once("random-map.php");

$sql = "SELECT * FROM tanks WHERE game_id=? ORDER BY RAND();";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "s", $param_game);
$param_game = $_REQUEST['game'];
mysqli_stmt_execute($stmt);
$all_tanks = mysqli_stmt_get_result($stmt);

$total_num = mysqli_num_rows($all_tanks);

$side = intval(ceil(sqrt($total_num * 25 + 100)));
if ($side % 2 == 0){
	$side += 1;
}
$middle = intval(($side - 1)/2);
$radius = intval($side/4);
$rotation = 2*pi()/$total_num;


$random_map = false;

$sql = "SELECT random_map FROM games WHERE name=?;";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "s",$param_game);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);                
mysqli_stmt_bind_result($stmt, $random_map);
mysqli_stmt_fetch($stmt);


$sql = "UPDATE games SET started=TRUE, size=?, map=? WHERE name=?;";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "sss", $side, $map, $_REQUEST['game']);
if ($random_map){
    $map = random_map($side, $total_num, $rotation, $middle, $radius);
} else {
    $map = str_repeat("E", $side**2);
}

mysqli_stmt_execute($stmt);

$i = 0;
while($row = mysqli_fetch_object($all_tanks)) {
	$sql = "UPDATE tanks SET x=?, y=? WHERE name=? AND game_id=?;";
	$tank_name = $row->name;
	$x = $middle + intval(cos($i*$rotation) * $radius);
	$y = $middle + intval(sin($i*$rotation) * $radius);

	if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "ssss", $x, $y, $tank_name, $_REQUEST['game']);
    
        if(mysqli_stmt_execute($stmt)){
        	echo "<br>Succesfully added " . $tank_name . "to pos: " . strval($x) . ", " . strval($y);
        }
    }
    $i += 1;
}
header("Refresh:0");


?>