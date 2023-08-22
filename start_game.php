<?php

$all_tanks = mysqli_query($link, "SELECT * FROM tanks WHERE game_id='". $_REQUEST['game'] ."' ORDER BY RAND();");
$total_num = mysqli_num_rows($all_tanks);

$side = ceil(sqrt($total_num * 25 + 100));
if ($side % 2 == 0){
	$side += 1;
}
$middle = intval(($side - 1)/2);
$radius = intval($side/4);
$rotation = 2*pi()/$total_num;

mysqli_query($link, "UPDATE games SET started=TRUE, size=". strval($side) . " WHERE name='". $_REQUEST['game'] ."';");

$i = 0;
while($row = mysqli_fetch_object($all_tanks)) {
	$sql = "UPDATE tanks SET x=?, y=? WHERE name=?;";
	$tank_name = $row->name;
	$x = $middle + intval(cos($i*$rotation) * $radius);
	$y = $middle + intval(sin($i*$rotation) * $radius);

	if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "sss", $x, $y, $tank_name);
    
        if(mysqli_stmt_execute($stmt)){
        	echo "<br>Succesfully added " . $tank_name . "to pos: " . strval($x) . ", " . strval($y);
        }
    }
    $i += 1;
}
header("Refresh:0");


?>