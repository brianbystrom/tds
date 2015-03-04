<?php
// All login and verification functions

function login_user($conn,$user,$password) {

	try {
        
	        $PDO = $conn->prepare('SELECT * FROM heroku_635bec65ba648c2.login WHERE username = :user');
	        $PDO->bindParam(':user', $user, PDO::PARAM_STR);
	        $PDO->execute();
	        
	        $PDO->setFetchMode(PDO::FETCH_OBJ);

	        $x = 0;

	        

	    } catch(PDOException $e) {
	        echo 'ERROR: '.$e->getMessage();    
	    }

	if($login = $PDO->fetch()) {
		$confirm = true;
		$pass = $login->pass;
		$name = $login->name;
	} else {
		$confirm = false;
	}
	
    if($confirm === false) {
    	header('Location: home.php?error=1');
    	exit;
    } else {
	
		if (password_verify($password,$pass))
		{	
			$_SESSION['name'] = $name;
			$_SESSION['auth'] = md5(time());
			header('Location: home.php?error=2');
			exit;	
		} else if ($pass === $password) {
			//valid
			//$auth_code = md5(time());
			//$time = time() + (60*60);
			//$ip = $_SERVER['REMOTE_ADDR'];
			//$_SESSION['auth'] = $auth_code;
			//$_SESSION['status'] = 'active';
			//$cid = $login[0]['cid'];
			//$_SESSION['cid'] = $cid;
			
			
			header('Location: home.php');
			exit;
		} else {
			header('Location: home.php?error=3');
			exit;
		}
	}
	
	
}

function check_login() {
	// Function to evaluate login status
	$id = $_SESSION['id'];
	$auth_code = $_SESSION['auth'];
	$status = $_SESSION['status'];
	if (check_credentials($id, $auth_code, $status) != 'TRUE') {
		header('Location: logout.php');
		exit;
	}
}

function check_credentials($id, $auth_code, $status) {
	// Function to check if user is correctly logged in
	if ($status == 'active') {
		$sql = "SELECT * FROM `members` WHERE (`email` = '$id' OR `username` = '$id') AND `auth_code` = '$auth_code'";
		$query = mysql_query($sql);
		$num = mysql_num_rows($query);
		if ($num =='1' ) {
			return "TRUE";
		} else {
			return "FALSE"; } 
	} else {
		return "FALSE"; }
}


function check_raffle(){
	$time_end = get_raffle_time_end(1);
	$time = time();
	if($time > $time_end)
	{
		$current_raffle = get_current_raffle();
		$sql = "UPDATE `raffle` SET `current` = 'n' WHERE `id` = '$current_raffle'";
		mysql_query($sql);
		
		$current_raffle++;
		$sql = "UPDATE `raffle` SET `current` = 'y' WHERE `id` = '$current_raffle'";
		mysql_query($sql);
	}
	
}