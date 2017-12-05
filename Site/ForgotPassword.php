<?php
	//Loading Includes
	include('includes/session.php');
	include('includes/log.php');
	include('includes/api.php');
	include('includes/protection.php');
	include('includes/mailer.php');
	include('includes/password.php');
	include('includes/db.php');
	if (isset($_GET['uid'])) {
		if ($_SESSION['employeed'] == 't') {
			header('Location: /');
		}
		$UID = $_GET['uid'];
		$coachResult = view('coaches','reset_code='.$UID);
		$pid = $coachResult['personid'];
		if (!isset($pid)) {
			header('Location: /Login');
		}
		o_log('Page Loaded','Reset password person ID: '.$pid);
		$title = 'Forgot Password?';
		$form = '<form action="#" method="post">
					<table style="margin: auto;">
						<tr><td>Password:* <span class="password_info">(minimum 8 characters)</span></td><td><input type="password" name="pass1"  autocomplete="off" /></td></tr>
                    	<tr><td>Confirm Password:*</td><td><input type="password" name="pass2"  autocomplete="off" /></td></tr>
					</table>
					<br />
                 	<input type="submit" value="Reset Password" class="button login_button" />
                 </form>';
		
	} else if (isset($_GET['p'])) {
		$pid = decrypt($_GET['p']);
		if ($_SESSION['supervisor'] == 't' || ($pid == $_SESSION['personid'])) {
		} else {
			header('Location: /Profile?p='.$pid);
		}
		o_log('Page Loaded','Reset password person ID: '.$pid);
		$title = 'Password Reset';
		$form = '<form action="#" method="post">
					<table style="margin: auto;">
						<tr><td>Password:* <span class="password_info">(minimum 8 characters)</span></td><td><input type="password" name="pass1"  autocomplete="off" /></td></tr>
                    	<tr><td>Confirm Password:*</td><td><input type="password" name="pass2"  autocomplete="off" /></td></tr>
					</table>
					<br />
                 	<input type="submit" value="Reset Password" class="button login_button" />
                 </form>';
	} else {
		if ($_SESSION['employeed'] == 't') {
			header('Location: /');
		}
		o_log('Page Loaded');
		$title = 'Forgot Password?';
		$form = '<form action="#" method="post">
					<table style="margin: auto;">
                 		<tr><td>Email Address:*</td><td><input type="text" name="email" class="login_input" /></td></tr>
					</table>
					<br />
                 	<input type="submit" value="Reset Password" class="button login_button" />
                 </form>';
	}
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if (isset($_GET['uid'])) {
			$pass1 = $_POST['pass1'];
			$pass2 = $_POST['pass2'];
			$work = true;
			if (strlen($pass1)<1) {
				$text .= "The Password cannot be empty.<br />";
				$work = false;
			}
			if (strlen($pass1)>50) {
				$text .= "The Password is to long.<br />";
				$work = false;
			}
			// Check password length
			if (strlen($pass1)<8 && $work) {
				$text = "The password is not long enough.<br />";
				$work = false;
			}
			// Check password equality
			if ($pass1 === $pass2) {
				//echo("The passwords do match.");
			} else if ($work) {
				$text = "The passwords do not match.<br />";
				$work = false;
			}
			$pass = encryptpass($pass1);
			if ($work) {
				$sql = "UPDATE coaches SET reset_code='', password='$pass' where personid='$pid';";
				$result = pg_query($conn, $sql);
				$error = pg_last_error($conn);
				if (!$error) {
					o_log('Page Loaded','Password was reset successfully, person ID: '.$pid);
					header('Location: /Login');
				} else {
					o_log('Page Loaded','Password failed to reset, person ID: '.$pid);
					$text .= 'Password failed to reset.<br />';
					$text .= 'Error: '.$error.'<br />';
				}
			}
		} else if (isset($_GET['p'])) {
			$pass1 = $_POST['pass1'];
			$pass2 = $_POST['pass2'];
			$work = true;
			if (strlen($pass1)<1) {
				$text .= "The Password cannot be empty.<br />";
				$work = false;
			}
			if (strlen($pass1)>50) {
				$text .= "The Password is to long.<br />";
				$work = false;
			}
			// Check password length
			if (strlen($pass1)<8 && $work) {
				$text = "The password is not long enough.<br />";
				$work = false;
			}
			// Check password equality
			if ($pass1 === $pass2) {
				//echo("The passwords do match.");
			} else if ($work) {
				$text = "The passwords do not match.<br />";
				$work = false;
			}
			$pass = encryptpass($pass1);
			if ($work) {
				$sql = "UPDATE coaches SET reset_code='', password='$pass' where personid='$pid';";
				//resets $conn
				pg_close($conn);
				include('includes/db.php');
				$result = pg_query($conn, $sql);
				$error = pg_last_error($conn);
				if (!$error) {
					o_log('Page Loaded','Password was reset successfully, person ID: '.$pid);
					header('Location: /Profile?p='.encrypt($pid));
				} else {
					o_log('Page Loaded','Password failed to reset, person ID: '.$pid);
					$text .= 'Password failed to reset.<br />';
					$text .= 'Error: '.$error.'<br />';
				}
			}
		} else {
			$email = strtolower($_POST['email']);
			$emailislong = true;

			if (strlen($email)<1) {
				$text = "The Email cannot be empty. <br />";
				$emailislong = false;
			}
			// Check for valid email
			if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
			  //echo("$email is a valid email address.");
			} else if (!strlen($email)<1 && $emailislong) {
			  $text .= "\"$email\" is not a valid email address.<br />";
			  $email = '';
			}
			//reset $conn
			pg_close($conn);
			include('includes/db.php');
			$email = pg_escape_string($conn,$email);

			function generateRandomString($length = 10) {
				$characters = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				$charactersLength = strlen($characters);
				$randomString = '';
				for ($i = 0; $i < $length; $i++) {
					$randomString .= $characters[rand(0, $charactersLength - 1)];
				}
				return $randomString;
			}
			//used to reset the pass
			$UID = generateRandomString();
			
			$accountsResult = view('accounts','email='.$email);
			$pid = $accountsResult['personid'];
			$sql = "UPDATE coaches SET reset_code='$UID' WHERE personid='$pid';";

			$result = pg_query($conn, $sql);
			$error = pg_last_error($conn);
			if (!$error) {
				$subject = "ABS Life Coach";
				$body = '<p>Password reset for '.$email.'.</p>
						  <p>Click here to reset your password <a href="https://abslifecoach.reev.us/ForgotPassword?uid='.$UID.'">https://abslifecoach.reev.us/ForgotPassword?uid='.$UID.'</a> or copy and paste this into your browser.</p><br />
						  <p>Do not reply to this email. It will not be checked.</p>';
				$mail = my_mailer($email, $subject, $body);
				if ($mail) {
					$text = 'Mail successfully sent.<br />';
					$form = 'Please check your email to reset your password.';
				} else {
					$form = 'Please check your email to reset your password.';
				}
			} else {
				echo('Error: '.$error.'<br />');
				echo('email: '.$email.'<br />');
				echo('sql: '.$sql.'<br />');
				echo('UID: '.$UID.'<br />');
				echo('result: '.$result.'<br />');
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
       <?php
			if ($_SESSION['supervisor'] == 't' || ($pid == $_SESSION['personid'] && isset($_SESSION['personid']))) {
				echo('<nav class="navbar navbar-expand-lg navbar-dark bg-blue">
						<a class="navbar-brand" href="/"><img src="/logo.png" width="50" height="50" alt="Logo" /></a>
						<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
							<span class="navbar-toggler-icon"></span>
						</button>

						<div class="collapse navbar-collapse" id="navbarTogglerDemo02">
							<ul class="navbar-nav mr-auto mt-2 mt-lg-0">
								<li class="nav-item">
									<a class="nav-link" href="/">Home</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" href="/Schedule">Schedule</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" href="/Clients">Clients</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" href="/Coaches">Coaches</a>
								</li>
							</ul>
							<!--        I changed this to align the logout to the right-->
							<ul class="nav navbar-nav navbar-right">');
							 if ($_SESSION['admin'] == 'true') {
							echo('<li class="nav-item">
									<a class="nav-link" href="'.getCompanyLink().'">Manage Company</a>
								</li>');
							}
							if ($_SESSION['supervisor'] == 't') {
							echo('<li class="nav-item right-marigin40p">
									<a class="nav-link" href="/NewCoach">Add New Coach</a>
								</li>');
							}
					 		echo('<li class="nav-item active">
									<a class="nav-link" href="/Profile">Profile<span class="sr-only">(current)</span></a>
								</li>
								<li class="nav-item">
									<a class="nav-link" href="/Logout" >Logout</a>
								</li>
								<!--            <li class="nav-item">
												<a class="nav-link disabled" href="#">Disabled</a>
											</li>-->
							</ul>
							<!--        <form class="form-inline my-2 my-lg-0">
										<input class="form-control mr-sm-2" type="search" placeholder="Search">
										<button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
									</form>-->
						</div>
					</nav>');
			} else {
				echo('<nav class="navbar navbar-expand-lg navbar-dark bg-blue">
						<a class="navbar-brand" href="/index"><img src="/logo.png" width="50" height="50" alt="Logo" /></a>
						<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
							<span class="navbar-toggler-icon"></span>
						</button>

						<div class="collapse navbar-collapse" id="navbarTogglerDemo02">
							<ul class="navbar-nav mr-auto mt-2 mt-lg-0">
							</ul>
						</div>
					</nav>');
			}
		
		?>
        <br />
        <div class="container">
			<div class = "row">
				<div class="col-sm-12">
					<div class="card text-center page-margin0 left right">
						<div class="card-header title"><?php echo($title); ?></div>
						<div class="card-body">
							<?php
								echo($text);
								echo($form);
							?>
						</div>
					</div>
				</div>
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