<?
include("saidis.php");

# Validations.
if (!gt("username") || !gt("password") || !gt("password2")) {
	errorPage("Please enter all the form data");
}
if (gt("password") !== gt("password2")) {
	errorPage("Passwords do not match");
}

$username = gt("username");
$password = gt("password");
$r = redisLink();
if ($r->hget("users", $username)) {
	errorPage("The username $username is already Taken.");
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
