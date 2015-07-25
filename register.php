<?
include("saidis.php");

# Validations.
if (!gt("username") || !gt("password") || !gt("password2")) {
	//error -- invalid entry
	echo "Invalid Entry";
}
if (gt("password") !== gt("password2")) {
	//error -- passwords don't match
	echo "Mismatched Passwords";
}

$username = gt("username");
$password = gt("password");
$r = redisLink();
if ($r->hget("users", $username)) {
	//error -- username in use
	echo "Username is taken";
}

$uid = $r->incr("next_user_id");
$authToken = generateRandomAuthToken();
$r->hset("users", $username, $uid);
$r->hmset("user:$uid",
		"username", $username,
		"password", $password,
		"auth", $authToken);
$r->hset("auths", $authToken, $uid);
$r->zadd("users_by_time", time(), $username);

setcookie($auth, $authToken, time()+3600*24*365);

include("header.php");
?>

<h2>Welcome Aboard!</h2>
Hey <?= $username ?>, you now have an account. <a href='index.php'>Here's a good place to start saying!</a>

<? include("footer.php") ?>
