<?
include("saidis.php");

if (!isLoggedIn()) {
	header("Location : index.php");
	exit;
}

$r = redisLink();
$newToken = generateRandomAuthToken();
$userid = $User["id"];
$oldToken = $r->hget("user:$userid", "auth");

$r->hset("user:$userid", "auth", $newToken);
$r->hset("auths", $newToken, $userid);
$r->hdel("auths", $oldToken);

header("Location: index.php");
?>