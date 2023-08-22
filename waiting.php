<?php
if ($game["owner"] == $_SESSION["username"]){
    echo '<a href="#" onClick=redirect()>Débuter la partie</a>';
    echo '<script type="text/javascript">
            function redirect() {
            window.location = window.location + "&start=true";
            }
        </script>';
} else {
    echo "La partie commencera une fois que tout le monde sera là. Actualisez la page à ce moment-là.";
}



$sql = "SELECT * FROM tanks WHERE game_id=?;";
        
if($stmt = mysqli_prepare($link, $sql)){
    mysqli_stmt_bind_param($stmt, "s", $param_game);
    $param_game = $_REQUEST['game'];
    
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);
        if(mysqli_num_rows($result)){                  
            //$result = mysqli_stmt_get_result($stmt);
        } else {
            echo "r";
            exit('<div>Game not found<div><a href="index.php">Go back</a>');
        }
    } else {
        exit("Encountered problem");
    }
}

//$all_users = mysqli_fetch_all($result, MYSQLI_ASSOC);
echo "<br>Joueurs déjà présents:";

while($row = mysqli_fetch_object($result)) {
    $name = $row->name;
    echo "<br>".$name;
}


?>