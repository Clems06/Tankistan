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

    		$sql = "INSERT INTO games (name, owner, public) VALUES (?, ?, ?);";
        
			$stmt = mysqli_prepare($link, $sql);
	    	mysqli_stmt_bind_param($stmt, "sss", $param_game, $username, $is_public);
			$param_game = $_POST["gamename"];
			$username = $_SESSION["username"];
			if (isset($_POST['public'])){
				$is_public = true;
			} else {
				$is_public = false;
			}
			mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);

			header("Location: index.php?game=".urlencode($_POST["gamename"]));
    	}

	}
}
?>
<style type="text/css">
	.switch {
  position: relative;
  display: inline-block;
  width: 48px;
  height: 27.2px;
}

/* Hide default HTML checkbox */
.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

/* The slider */
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
	position: absolute;
	content: "";
	height: 20.8px;
	width: 20.8px;
	left: 3px;
	bottom: 3.5px;
	background-color: white;
	-webkit-transition: .4s;
	transition: .4s;
}

input:checked + .slider {
  	background-color: #2196F3;
}

input:focus + .slider {
  	box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  	-webkit-transform: translateX(20.8px);
  	-ms-transform: translateX(20.8px);
  	transform: translateX(20.8px);
}

/* Rounded sliders */
.slider.round {
  	border-radius: 27.2px;
}

.slider.round:before {
  	border-radius: 50%;
}

.form-group{
	margin: 10px;
}

</style>
<center>
	<form action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>" method="post">
	    <div class="form-group">
	        <label>Id of the new game:</label>
	        <input type="text" name="gamename" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>">
	        <span class="invalid-feedback"><?php echo $name_err; ?></span>
	    </div>
	    <div class="form-group">
	    	<label style="vertical-align: middle;">Public game: </label>
		    <label class="switch">
  				<input name="public" type="checkbox" id="public">
  				<span class="slider round"></span>
			</label>
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