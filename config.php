<?php
if (!defined('ROOTPATH')){
	define('ROOTPATH', __DIR__);
}
require_once('libraries/dotenv.php');



if (!defined('DB_SERVER')){
	(new DotEnv(ROOTPATH.'/database.env'))->load();
	define('DB_SERVER', getenv('DB_SERVER'));
	define('DB_USERNAME', getenv('DB_USERNAME'));
	define('DB_PASSWORD', getenv('DB_PASSWORD'));
	define('DB_NAME', getenv('DB_NAME'));
}

 
/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, 3306);

// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>