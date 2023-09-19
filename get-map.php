<?php 
$visible = [];



$sql = "SELECT * FROM tanks WHERE game_id=?;";
        
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "s", $param_game);
$param_game = $_REQUEST['game'];
    
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$tanks = mysqli_fetch_all($result, MYSQLI_ASSOC);

mysqli_stmt_close($stmt);


$sql = "SELECT map, size FROM games WHERE name=?;";
        
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "s", $_REQUEST['game']);

mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

$map = "";

$size = "";
mysqli_stmt_bind_result($stmt, $map, $size);
mysqli_stmt_fetch($stmt);
$size = intval($size);
//var_dump($map);
mysqli_stmt_close($stmt);


$sql = "SELECT x, y FROM tanks WHERE name=? AND game_id=?;";
        
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $_REQUEST['game']);
    
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

$x = $y = 0;
mysqli_stmt_bind_result($stmt, $x, $y);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

/*$size = 7;
$map = "EEEEEEEEEEEEEEEEWWWEEEEEEEEEEEEEEWEEEEEWEEEEEEEEE";

$tanks = array(
array("x"=>3, "y"=>0),
array("x"=>3, "y"=>1),
array("x"=>6, "y"=>1),
array("x"=>1, "y"=>4),
array("x"=>6, "y"=>4),
array("x"=>2, "y"=>5),
array("x"=>6, "y"=>5),
array("x"=>3, "y"=>6),
array("x"=>5, "y"=>6));

$tanks = array(
array("x"=>3, "y"=>0));

$x = 3;
$y = 4;*/




/*
W = wall
E = Empty
R = river
*/

function map_value($x, $y){
	global $size, $map;

	$char_pos = $x+$y*$size;
	return substr($map, $char_pos, 1);
}

function check_if_visible($target_x, $target_y, $player_x, $player_y)
{
	$x0 = $player_x;
	$y0 = $player_y;
	$x1 = $target_x;
	$y1 = $target_y;

	$dx = abs($x1 - $x0);
	if ($x0 < $x1){
		$sx = 1;
	} else{
		$sx = -1;
	}

	$dy = -abs($y1 - $y0);
	if ($y0 < $y1){
		$sy = 1;
	} else{
		$sy = -1;
	}

	$e = $dx + $dy;

	while ($x0 != $x1 Or $y0 != $y1) {

		$e2 = 2*$e;
		if ($e2 >= $dy){
			//echo "test1";
			if ($x0 == $x1){
				//echo "broke1";
				break;
			}
			$e = $e + $dy;
			$x0 = $x0 + $sx;
		}
		if ($e2 <= $dx){
			//echo "test2";
			if ($y0 == $y1){
				//echo "broke2";
				break;
			}
			$e = $e + $dx;
			$y0 = $y0 + $sy;
		}

		//echo $x0, $y0;
		//echo "<br>";


		if (map_value($x0, $y0)=="W"){
			return false;
		}
	}
	return true;
}

foreach ($tanks as $tank){
	if ($x < 0){
		if ($tank["name"] == $_SESSION['username']){
			array_push($visible, $tank);
			break;
		}
	}
	else if (check_if_visible($tank["x"], $tank["y"], $x, $y)){
		array_push($visible, $tank);
	}
}

//echo json_encode($visible);

//print_r($visible);


?>
