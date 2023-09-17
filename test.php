<?php

$map = "EEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEWEEEEEEEEEEEEWWWEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE";

$size = 15;

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
			echo "Wall!";
			return false;
		}
	}
	return true;
}

var_dump(check_if_visible(5, 8, 10, 7));

?>