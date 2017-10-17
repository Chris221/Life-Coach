<?php
	include('includes/session.php');
	if ($_SESSION['personid']) {
		header('Location: /');
	}	
	$title = 'Login';

	include('includes/db.php');
	include('includes/protection.php');
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$email = $_POST['email'];
		$pass = $_POST['pass'];
		$emailislong = true;
		
		if (strlen($email)<1) {
			$text = "The Email cannot be empty. <br />";
			$emailislong = false;
		}
		if (strlen($pass)<1) {
			$text .= "The Password cannot be empty. <br />";
		}
		// Check for valid email
		if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
		  //echo("$email is a valid email address.");
		} else if (!strlen(email)<1 && $emailislong) {
		  $text .= "\"$email\" is not a valid email address.<br />";
		  $email = '';
		}
		
		// Encrypt password
		include('includes/password.php');
		$email = pg_escape_string($conn,$email);
		$pass = encryptpass($pass);
		
		$sql = "SELECT * FROM accounts WHERE email = '$email' AND password = '$pass';";
		
		$data = pg_query($conn, $sql);
		$data = pg_fetch_all($data);
		
		if ($data['RowID']) {
			$work = true;
			// checks if the users is allowed to connect
			if (!$data['employeed']) {
				$text = "This account has been disabled. If you believe this is a mistake please contact your system administrator.<br />";
				$work = false;
			}
			if ($work) {
				//sets sessions veriables 
				$_SESSION['personid'] = $data['personid'];
				$_SESSION['coachid'] = $data['coachid'];
				$_SESSION['email'] = $data['email'];
				$_SESSION['prefix'] = $data['prefix'];
				$_SESSION['first_name'] = $data['first_name'];
				$_SESSION['last_name'] = $data['last_name'];
				$_SESSION['suffix'] = $data['suffix'];
				$_SESSION['companyid'] = $data['companyid'];
				$_SESSION['superviser'] = $data['superviser'];
				$_SESSION['clientid'] = $data['clientid'];
				$_SESSION['employeed'] = $data['employeed'];
				
				$personid = $_SESSION['personid'];
				//sets cookie if it was checked
				if ($_POST['remember']) {
					setcookie("Login", encrypt($_SESSION['personid']), time() + (86400 * 30), "/"); // 86400 = 1 day
				}
				
				//LOGS
				//include('includes/log.php');
				//c_Log('Log in','User: '.$email);
				$date = date("Y-m-d H:i:s");
				pg_query($conn, "UPDATE coaches SET last_active='$date' WHERE personid='$personid'");
				header('Location: /');
			}
		} else {
			if (strlen($text) == 0){
				$text = "Email and password do not match.<br />";
			}
		}
		if(strlen($text) > 0) {
			$text .= "<br />";
		}
		pg_close($conn);
	}
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<!-- For Mobile scaling -->
<meta name="viewport" content="width=device-width, user-scalable=no" />
<meta name="HandheldFriendly" content="true">
<!-- Computer -->
<link type="text/css" rel="stylesheet" href="/CSS/browser.css" />
<title><?php echo($title); ?></title>
</head>
<body>
<div class="login_page page">
	<div class="login">
		<?php
			echo($title);
			echo($text);
            echo('
				<form action="#" method="post">
					Email Address: <br />
					<input type="text" name="email" value="'.$email.'" /><br />
					Password: <br />
					<input type="password" name="pass" /><br />
					<input type="checkbox" name="remember" />Keep me logged in for 30 days<br />
					<input type="submit" value="Submit" /><br /><br />
				</form>
				<a href="/ForgotPassword" class="register_link">Forgot Password?</a>
			');
		?>
	</div>
</div>
</body>
</html>