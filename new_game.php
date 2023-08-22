<?php

$name_err  = "";
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["gamename"]))){
    	$name_err = "Entrez un nom non vide.";
    } else {
    	$sql = "SELECT * FROM games WHERE name=?;";
        
		$stmt = mysqli_prepare($link, $sql);
    	mysqli_stmt_bind_param($stmt, "s", $param_game);
		$param_game = $_POST["gamename"];
		mysqli_stmt_execute($stmt);
		mysqli_stmt_store_result($stmt);
		if(mysqli_stmt_num_rows($stmt)){
    		$name_err = "Ce nom existe déjà.";
    	} else {
    		mysqli_stmt_close($stmt);

    		$sql = "INSERT INTO games (name, owner) VALUES (?, ?);";
        
			$stmt = mysqli_prepare($link, $sql);
	    	mysqli_stmt_bind_param($stmt, "ss", $param_game, $username);
			$param_game = $_POST["gamename"];
			$username = $_SESSION["username"];
			mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);

			header("Location: index.php?game=".urlencode($_POST["gamename"]));
    	}

	}
}
?>
<center>
	<form action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>" method="post">
	    <div class="form-group">
	        <label>Id of the new game:</label>
	        <input type="text" name="gamename" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>">
	        <span class="invalid-feedback"><?php echo $name_err; ?></span>
	    </div>
	    <div class="form-group">
	        <input type="submit" class="btn btn-primary" value="Create game">
	    </div>

	    <p>Already have a game? <a href="#" onclick="reset()">Join it.</a></p>
	</form>
</center>
<script type="text/javascript">
	function reset() {
		window.location = window.location.href.split('?')[0];
	}
</script>