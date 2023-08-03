<?php
$servername = "localhost";
$username = "local_user";
$password = "pass";

// Creating connection
$conn = mysqli_connect($servername, $username, $password, "tank_data");


// Checking connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully";
$result = mysqli_query($conn, "SELECT * FROM tanks");
$all = mysqli_fetch_all($result);

foreach ($all as $row) {
    foreach ($row as $element) {
        echo '<br>' . $element;
    }
}
echo $all;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tankistan</title>
</head>
<body>
Hallooooo
</body>

</html>
