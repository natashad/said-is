<? 
include("saidis.php");

if(!isLoggedIn()) {
	header("Location: index.php");
	exit;
}

include("header.php");


$r = redisLink();
?>
<form method="POST" action="post.php">
	<p>Hey <?= $User['username'] ?>, What are you up to?</p>

	<div>
		<textarea cols="70" rows="3" name="status"></textarea>
		<input type="submit" name="doit" value="SayIt" />
	</div>
</form>

<div id="info-box">
	<?= $r->zcard("followers:" . $User['id']) ?> followers <br />
	<?= $r->zcard("following:" . $User['id']) ?> following <br />
</div>

<? 
$start = gt("start") === false ? 0 : intval(gt("start"));
showUserPosts($User['id'], $start, 100);

include("footer.php");

?>