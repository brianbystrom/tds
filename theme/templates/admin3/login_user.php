<?

include('./include/include.inc');

session_start();
//die("TEST");

$user = $_POST['username'];
$password = $_POST['password'];

//$salt = "bb1120";
//die(password_hash($password,PASSWORD_DEFAULT));

login_user($conn, $user, $password);
