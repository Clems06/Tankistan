<?php
// Initialize the session
session_start();
require_once "../config.php";
require_once 'remember_me.php';
// Check if the user is already logged in, if yes then redirect him to welcome page
if(is_user_logged_in()){
    header("location: ../index.php");
    exit;
}

// Include config file


 
// Define variables and initialize with empty values
$id = $username = $password = "";
$username_err = $password_err = $login_err = "";


 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            log_user_in($username, $id);
                            if (isset($_POST["remember"])){
                                remember_me($id);
                            }

                            // Redirect user to welcome page
                            
                            if (isset($_REQUEST["redirect_uri"])){
                                header("location: ..?" . $_REQUEST["redirect_uri"]);
                            }
                            else {
                                header("location: ..");
                            };
                            

                            
                        } else{
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else{
                    // Username doesn't exist, display a generic error message
                    $login_err = "Invalid username or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ 
            width: 70%; 
            padding: 20px;
            height: 100vh;
            font-size: 2vh;
             }
        .wrapper>h2{
            font-size: 4vh;
        }
        .form-group{
            /*height: 30vh;*/
        }
        .form-group>input{
            height: 5vh;
            font-size: inherit;
        }
        #remember_block{
            display: flex;
            align-items: center;
            flex-direction: row;
            text-align: center;
            margin:0;
        }
        .form-group>input[type="checkbox"]{
            margin-left: 1vh;
            width: 2vh;
        }
        label[for="remember"]{
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Login</h2>
        <?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group" id="remember_block">
                <label for="remember">Remember me</label>
                <input id="remember" type="checkbox" name="remember">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
        </form>
    </div>
</body>
</html>