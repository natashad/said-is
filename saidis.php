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

?>

