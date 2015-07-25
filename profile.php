<?

include("saidis.php");
include("header.php");

$r = redisLink();
if(!gt("u") || !($userid = $r->hget("users", gt("u")))) {
	errorPage("The user " . gt("u") . " you are looking for does not exist");
}

echo "<h2 class=\"username\">" . gt('u') . "</h2>";
if (isLoggedIn() && $User['id'] != $userid) {

	$isFollowing = $r->zscore("following:" . $User['id'], $userid);
	if (!$isFollowing) {
		echo("<a href=\"follow.php?uid=$userid&f=1\" class=\"button\">Follow this user</a>");
    } else {
        echo("<a href=\"follow.php?uid=$userid&f=0\" class=\"button\">Stop following</a>");
    }

}

$start = gt("start") === false ? 0 : intval(gt("start"));
showUserPosts($userid, $start, 1000);


include("footer.php");
?>