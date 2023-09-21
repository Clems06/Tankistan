<?php

function log_user_in($username, $id){
	// prevent session fixation attack
    if (session_regenerate_id()) {
        $_SESSION["loggedin"] = true;
    	$_SESSION["username"] = $username;
    	$_SESSION['user_id'] = $id;
        return true;
    }

    return false;
}

function logout(): void
{
    if (is_user_logged_in()) {

        // delete the user token
        delete_user_token($_SESSION['user_id']);

        // delete session
        $_SESSION = array();

        // remove the remember_me cookie
        if (isset($_COOKIE['remember_me'])) {
            unset($_COOKIE['remember_me']);
            setcookie('remember_user', null, -1);
        }
        
        session_destroy();
    }
}

function generate_tokens(): array
{
    $selector = bin2hex(random_bytes(16));
    $validator = bin2hex(random_bytes(32));

    return [$selector, $validator, $selector . ':' . $validator];
}

function parse_token(string $token): ?array
{
    $parts = explode(':', $token);

    if ($parts && count($parts) == 2) {
        return [$parts[0], $parts[1]];
    }
    return null;
}

function insert_user_token(int $user_id, string $selector, string $hashed_validator, string $expiry): bool
{
	global $link;
    $sql = 'INSERT INTO user_tokens(user_id, selector, hashed_validator, expiry) VALUES (?, ?, ?, ?)';


    $stmt = mysqli_prepare($link, $sql);
	mysqli_stmt_bind_param($stmt, "ssss", $user_id, $selector, $hashed_validator, $expiry);

    return mysqli_stmt_execute($stmt);
}

function find_user_token_by_selector(string $selector)
{
	global $link;
    $sql = 'SELECT id, selector, hashed_validator, user_id, expiry
                FROM user_tokens
                WHERE selector = ? AND
                    expiry >= now()
                LIMIT 1';

    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $selector);

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result);
}

function delete_user_token(int $user_id): bool
{
	global $link;

    $sql = 'DELETE FROM user_tokens WHERE user_id = ?';
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $user_id);

    return mysqli_stmt_execute($stmt);
}

function find_user_by_token(string $token)
{	
	global $link;

    $tokens = parse_token($token);

    if (!$tokens) {
        return null;
    }

    $sql = 'SELECT users.id, username
            FROM users
            INNER JOIN user_tokens ON user_id = users.id
            WHERE selector = ? AND
                expiry > now()
            LIMIT 1';

    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $tokens[0]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_row($result);
}

function token_is_valid(string $token): bool { 
	// parse the token to get the selector and validator 
	[$selector, $validator] = parse_token($token);

	$tokens = find_user_token_by_selector($selector);
	if (!$tokens) {
	    return false;
	}

	return password_verify($validator, $tokens['hashed_validator']);

}

function remember_me(int $user_id, int $day = 30)
{
    [$selector, $validator, $token] = generate_tokens();

    // remove all existing token associated with the user id
    delete_user_token($user_id);

    // set expiration date
    $expired_seconds = time() + 60 * 60 * 24 * $day;

    // insert a token to the database
    $hash_validator = password_hash($validator, PASSWORD_DEFAULT);
    $expiry = date('Y-m-d H:i:s', $expired_seconds);

    if (insert_user_token($user_id, $selector, $hash_validator, $expiry)) {
        setcookie('remember_me', $token, $expired_seconds);
    }
}

function is_user_logged_in(): bool
{
    // check the session
    if (isset($_SESSION['username'])) {
        return true;
    }

    // check the remember_me in cookie
    $token = filter_input(INPUT_COOKIE, 'remember_me', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if ($token && token_is_valid($token)) {

        $user = find_user_by_token($token);

        if ($user) {
            return log_user_in($user[1], $user[0]);
        }
    }
    return false;
}


?>
