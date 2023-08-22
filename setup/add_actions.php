<?php
require_once "../config.php";

$all_users = mysqli_query($link, "UPDATE tanks SET actions = actions + 1 WHERE health > 0 AND x>0;");
?>