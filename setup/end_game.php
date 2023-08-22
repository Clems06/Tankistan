<?php

file_put_contents("../game-data.txt", json_encode(["started" => false, "size" => 0]));

require_once "../config.php";



mysqli_multi_query($link, "TRUNCATE TABLE users; TRUNCATE TABLE tanks;");

echo 'Game succesfully ended';


?>