<?php

require_once("libraries/PerlinNoiseGenerator.php");

function random_map($size, $num_tanks, $rotation, $middle, $radius){
	$i = 0;
	
	// $zoom, octaves, $persistence $elevation 
	$noise2D = new Noise2D(0.1, 4, 0.5, 1);

	$path_map = array_fill(0, $size**2, 'O');

	$walkers = [];

	while($i < $num_tanks) {
		$x = $middle + intval(cos($i*$rotation) * $radius);
		$y = $middle + intval(sin($i*$rotation) * $radius);

		$walkers[$i] = [$x, $y];
		$path_map[$x+$y*$size] = $i;

		$i += 1;
	}

	$options = [[1, 0], [-1, 0], [0, 1], [0, -1]];


	while (count($walkers)>1){
		foreach ($walkers as $i => &$walker){
			$move = $options[array_rand($options)];

			$walker = array_map(function () {
					return array_sum(func_get_args());
				}, $walker, $move);
		

			$walker[0] = max(min($walker[0], $size-1), 0);
			$walker[1] = max(min($walker[1], $size-1), 0);

			$value = $path_map[$walker[0]+$walker[1]*$size];

			if ($value == "0" Or !array_key_exists($value, $walkers)){
				$path_map[$walker[0]+$walker[1]*$size] = $i;
			} else if ($value != $i){
				unset($walkers[$i]);
			}
		}
	}
	$string_map = "";
	foreach ($path_map as $i => &$value){
		if (gettype($value) == "integer"){
			$string_map = $string_map . "E";
			echo "E";
		} else {
			$x = $i % $size;
			$y = intval(($i-$x)/$size);
		
			$noise_cell = $noise2D->getGreyValue($x, $y);
			$new_value = "";
			if ($noise_cell > 120){
				$new_value = "W";
			} else if ($noise_cell < 40) {
				$new_value = "R";
			} else {
				$new_value = "E";
			}
			echo $new_value;
			$string_map = $string_map . $new_value;
		
		}

		//echo $value;
		if (($i+1)%$size==0){
			echo "<br>";
		}
	}

	return $string_map;
}

?>