<?
include("saidis.php");
if (!isLoggedIn()) {
    include("header.php");
    include("signup.php");
    include("footer.php");
} else {
    header("Location:home.php");
    exit;
}
?>