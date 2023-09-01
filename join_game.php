<style>
  #join-game {
  	margin: 10px;
    border: 1px solid;
    border-radius: 10px;
    padding: 30px;
  }

  .line{
    background-color: black; 
    width: 80%; 
    height: 1px;
  }
</style>
<center>
    <div id="join-game">
    	<h3 style="margin: 0;">Join game</h3>
    	<label for="game_id">Game ID:</label>
    	<br>
        <input type="text" id="game_id" class="block"  />
        <br>
        <button name="button" onClick="joingame()">Join game</button>
    </div>
</center>
Or <a href="?new_game=true">create new game</a>
<hr class="line"/>
<h3>Your games</h3>
<?php 
$sql = "SELECT game_id FROM tanks WHERE name=?;";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "s", $param_username);
$param_username = $_SESSION['username'];
    
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$user_games = array();

while($row = mysqli_fetch_object($result)) {
    $id = $row->game_id;
    array_push($user_games, $id);
    if (!$id){
        continue;
    }
    echo '<a href="?game='.urlencode($id).'">'. $id .'</a>';
}

?>
<hr class="line"/>
<h3>Public games</h3>
<?php 
    $public_games = mysqli_query($link, "SELECT name, owner FROM games WHERE public=TRUE;");
    

    while($row = mysqli_fetch_object($public_games)) {
        $id = $row->name;
        $owner = $row->owner;
        if (!$id Or in_array($id, $user_games)){
            continue;
        };
        echo '<a href="?game='.urlencode($id).'">'. $id . ' - ' . $owner . '</a>';
    }

?>
<script type="text/javascript">
    function joingame() {
        window.location = window.location + '?game=' + encodeURIComponent(document.getElementById('game_id').value);
    }
</script>