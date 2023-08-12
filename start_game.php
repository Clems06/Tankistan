<?php

require_once("config.php");

$all_tanks = mysqli_query($link, "SELECT * FROM users ORDER BY RAND();");
$total_num = mysqli_num_rows($all_tanks);

$side = ceil(sqrt($total_num * 25));
if ($side % 2 == 1){
	$side += 1;
}
$middle = intval(($side - 1)/2);
$radius = intval($side/4);
$rotation = 2*pi()/$total_num;


file_put_contents("game-data.txt", json_encode(["started" => true, "size" => $side,]));

$i = 0;
while($row = mysqli_fetch_object($all_tanks)) {
	$sql = "INSERT INTO tanks (name, x, y) VALUES (?, ?, ?);";
	$tank_name = $row->username;
	$x = $middle + intval(cos($i*$rotation) * $radius);
	$y = $middle + intval(sin($i*$rotation) * $radius);

	if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "sss", $tank_name, $x, $y);
    
        if(mysqli_stmt_execute($stmt)){
        	echo "Succesfully added " . $tank_name . "to pos: " . strval($x) . ", " . strval($y) . "\n";
        }
    }
    $i += 1;
}



?>