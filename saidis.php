<?
require 'Predis/Autoloader.php';
Predis\Autoloader::register();

function generateRandomAuthToken() {
	$rand_int = rand();
	return md5($rand_int);
}

function isLoggedIn() {
	global $User, $_COOKIE;

	if (isset($User)) return true;

	if (isset($_COOKIE['auth'])) {
		$r = redisLink();
		$authCookie = $_COOKIE['auth'];
		if ($uid = $r->hget("auths", $authCookie)) {
			if ($r->hget("user:$uid", "auth") != $authCookie) return false;
			loadUser($uid);
			return true;
		}
	}

	return false;
}

function loadUser($uid) {
	global $User;

	$r = redisLink();
	$User['id'] = $uid;
	$User['username'] = $r->hget("user:$uid", "username");
	return true;
}

function redisLink() {
    static $r = false;
    if ($r) return $r;
    $r = new Predis\Client();
    return $r;
}

function g($param) {
	global $_GET, $_POST, $_COOKIE;

	if (isset($_COOKIE[$param])) return $_COOKIE[$param];
	if (isset($_POST[$param])) return $_POST[$param];
	if (isset($_GET[$param])) return $_GET[$param];
	return false;
}

function gt($param) {
	$val = g($param);
	if ($val === false) return false;
	return  trim($val);
}


function errorpage($msg) {
    include("header.php");
    echo('<div id ="error">'. $msg .'<br>');
    echo('<a href="javascript:history.back()">Please return back and try again</a></div>');
    include("footer.php");
    exit;
}

function strElapsed($t) {
    $d = time()-$t;
    if ($d < 60) return "$d seconds";
    if ($d < 3600) {
        $m = (int)($d/60);
        return "$m minute".($m > 1 ? "s" : "");
    }
    if ($d < 3600*24) {
        $h = (int)($d/3600);
        return "$h hour".($h > 1 ? "s" : "");
    }
    $d = (int)($d/(3600*24));
    return "$d day".($d > 1 ? "s" : "");
}

function showPost($id) {
	$r = redisLink();
	$post = $r->hgetall("post:$id");
	if (empty($post)) return false;

	$userid = $post['user_id'];
	$username = $r->hget("user:$userid", "username");
	$elapsed = strElapsed($post['time']);
	$userlink = "<a class=\"username\" href=\"profile.php?u=" . urlencode($username) . "\">".$username."</a>";

	echo('<div class="post">' . $userlink . ' ' . $post["body"] . '<br />');
	echo('<i>posted ' . $elapsed . ' ago via web</i></div>');
	return true;
}

function showUserPosts($userid, $start, $count) {
	$r = redisLink();
	$key = ($userid == -1) ? "timeline" : "posts:$userid";
	$posts = $r->lrange($key, $start, $start+$count);
	$c = 0;
	foreach ($posts as $post) {
		if (showPost($post)) $c++;
		if ($c == $count) break;
	}
	return count($posts) === $count + 1;
}

function showLastUsers() {
    $r = redisLink();
    $users = $r->zrevrange("users_by_time",0,9);
    echo("<div>");
    foreach($users as $u) {
        echo("<a class=\"username\" href=\"profile.php?u=".urlencode($u)."\">". $u ."</a> ");
    }
    echo("</div><br>");
}

?>