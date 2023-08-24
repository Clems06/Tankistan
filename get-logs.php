<?php
require 'config.php';

$sql = "SELECT * FROM logs WHERE game_id=?;";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "s", $param_game);

if($_SERVER["REQUEST_METHOD"] == "POST"){
	$param_game = $_POST['game'];
} else {
	$param_game = $_REQUEST['game'];
}

    
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

	
$string_logs = "";
while($row = mysqli_fetch_object($result)) {
	$author = $row->author;
    $action = $row->action_id;
    $other = $row->other;
	
    $string_logs = $string_logs . $author . " " . strval($action);
    if ($other){
    	$string_logs = $string_logs . " " . $other;
    }
    $string_logs = $string_logs . "<br>";

}

if($_SERVER["REQUEST_METHOD"] == "POST"){
	echo $string_logs;
}

?>