<?php
	include('includes/log.php');
	include('includes/session.php');
	if ($_SESSION['employeed']) {
		header('Location: /');
	}
	o_log('Page Loaded');
	$title = 'Login';

	include('includes/db.php');
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$email = strtolower($_POST['email']);
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
		//$data = pg_fetch_array($data, 0, PGSQL_ASSOC);
		$data = pg_fetch_assoc($data);
		$error = pg_last_error($conn);
		
		if ($data['personid']) {
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
				$_SESSION['supervisor'] = $data['supervisor'];
				$_SESSION['clientid'] = $data['clientid'];
				$_SESSION['employeed'] = $data['employeed'];
				
				$companyid = $_SESSION['companyid'];
				
				$sql = "SELECT * FROM companies WHERE companyid='$companyid';";
				$result = pg_query($conn, $sql);
				$data = pg_fetch_assoc($result);

				if (($data['admin_personid'] == $_SESSION['personid']) && ($_SESSION['personid'] >= '1')) {
					$_SESSION['admin'] = 'true';
				} else {
					$_SESSION['admin'] = 'false';
				}
				
				//sets cookie if it was checked
				if ($_POST['remember']) {
					include('includes/protection.php');
					//BROKEN fix with Try catches
					$pid = $_SESSION['personid'];
					$epid = encrypt($pid);
					o_log('Logged in', 'From the log in page');
					setcookie("Login", $epid, time() + (86400 * 30), "/"); // 86400 = 1 day
				}
				
				//LOGS
				//include('includes/log.php');
				//c_Log('Log in','User: '.$email);
				$date = date("Y-m-d H:i:s");
				//Sets $personid
				$personid = $_SESSION['personid'];
				//update last active time
				pg_query($conn, "UPDATE coaches SET last_active='$date' WHERE personid='$personid'");
				//redirects to home
				
				if ($data['deleted'] == 'f') {
					header('Location: /');
				} else {
					session_unset();
					session_destroy();
					if (isset($_COOKIE['Login'])) {
						unset($_COOKIE['Login']);
						setcookie('Login', null, -1, '/');
					}
					$text = 'The company you are trying to connect to does not exisit.<br />';
				}
			}
		} else {
			if (strlen($text) == 0){
				$text = "Email and password do not match.<br />";
				o_log('Logged in failed', 'Email: '.$email.' Encrypted pass: '.$pass);
			}
		}
		if(strlen($text) > 0) {
			$text .= "<br />";
		}
	}
	pg_close($conn);
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<!-- For Mobile scaling -->
<meta name="viewport" content="width=device-width, user-scalable=no" />
<meta name="HandheldFriendly" content="true">
        <!-- BrowserIcon -->
        <link rel="icon" type="image/ico" href="/logo.png">
<!-- Latest compiled and minified CSS -->
<link type="text/css" rel="stylesheet" href="/bootstrap/4.0.0/css/bootstrap.min.css">
<!-- jQuery library -->
<script type="text/javascript" src="/js/jquery/jquery-3.2.1.min.js"></script>
<!-- bootstrap bundle -->
<script type="text/javascript" src="/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
<!-- popper -->
<script type="text/javascript" src="https://unpkg.com/popper.js@1.12.9/dist/umd/popper.js"></script>
<!-- Latest compiled JavaScript -->
<script type="text/javascript" src="/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<!-- Our CSS -->
<link type="text/css" rel="stylesheet" href="/css/life-coach.css">
<title><?php echo($title); ?></title>
</head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-blue">
            <a class="navbar-brand" href="/index"><img src="/logo.png" width="50" height="50" alt="Logo" /></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
                <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                </ul>
            </div>
        </nav>
        <div class="login_page">
            <div class="login">
                <?php
                    echo($text);
                    echo('
                        <form action="#" method="post">
                            Email Address: <br />
                            <input type="text" name="email" value="'.$email.'" class="login_input" /><br />
                            Password: <br />
                            <input type="password" name="pass" class="login_input" /><br />
                            <input type="checkbox" name="remember" class="login_checkbox" />Keep me logged in for 30 days<br />
                            <input type="submit" value="Submit" class="button login_button" />
                        </form>
                        <a href="/ForgotPassword">Forgot Password?</a>
                    ');
                ?>
            </div>
        </div>

        <br/>
        <p class="footerText">
            Copyright &copy; 2017 No Rights Reserved.
            <br>
            Abroad Squad + Chris
        </p>
    </body>
</html>