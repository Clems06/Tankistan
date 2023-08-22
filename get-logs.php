<?php
require 'config.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){
	$game_logs = mysqli_query($link, "SELECT * FROM logs WHERE game_id='". $_POST['game'] ."';");
} else {
	$game_logs = mysqli_query($link, "SELECT * FROM logs WHERE game_id='". $_REQUEST['game'] ."';");
}
	
$result = "";
while($row = mysqli_fetch_object($game_logs)) {
	$author = $row->author;
    $action = $row->action_id;
    $other = $row->other;
	
    $result = $result . $author . " " . strval($action);
    if ($other){
    	$result = $result . " " . $other;
    }
    $result = $result . "<br>";

}

if($_SERVER["REQUEST_METHOD"] == "POST"){
	echo $result;
}

?>