<style>
  #join-game {
  	margin: 10px;
    border: 1px solid;
    border-radius: 10px;
    padding: 30px;
  }
</style>
<center>
    <div id="join-game">
    	<h3>Join game</h3>
    	<label for="game_id">Game ID:</label>
    	<br>
        <input type="text" id="game_id" class="block"  />
        <br>
        <button name="button" onClick="joingame()">Join game</button>
    </div>
</center>
Or <a href="?new_game=true">create new game</a>
<hr style="background-color: black; width: 80%; height: 1px;"/>
<h3>Your games</h3>
<?php 
$sql = "SELECT game_id FROM tanks WHERE name=?;";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "s", $param_username);
$param_username = $_SESSION['username'];
    
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

while($row = mysqli_fetch_object($result)) {
    $id = $row->game_id;
    echo '<a href="?game='.$id.'">'. $id .'</a>';
}




?>

<script type="text/javascript">
    function joingame() {
        window.location = window.location + '?game=' + encodeURIComponent(document.getElementById('game_id').value);
    }
</script>