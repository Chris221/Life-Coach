<?php
	include('includes/log.php');
	include('includes/session.php');
	include('includes/uploadPhoto.php');
	include('includes/api.php');
	include('includes/protection.php');
	if (!$_SESSION['super_admin']) {
		header('Location: /Company?c='.encrypt($_SESSION['companyid']));
	}
	o_log('Page Loaded');
	$title = 'Add New Company';

	$back = backButton();


	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		include('includes/db.php');
		
		$companyname = pg_escape_string($conn, $_POST['companyname']);
		$companylocation = pg_escape_string($conn, $_POST['companylocation']);
		$companysite = pg_escape_string($conn, $_POST['companysite']);
		$firstname = pg_escape_string($conn, $_POST['firstname']);
		$lastname = pg_escape_string($conn, $_POST['lastname']);
		$email1 = pg_escape_string($conn, strtolower($_POST['email1']));
		$email2 = pg_escape_string($conn, strtolower($_POST['email2']));
		$cell = cleanPhoneNumber($_POST['cell']);
		$pass1 = $_POST['pass1'];
		$pass2 = $_POST['pass2'];
		$gender = $_POST['gender'];
		
		pg_close($conn);
		
		$supervisor = 'true';
		
		$work = true;
		if (strlen($companyname)<1) {
			$text = "The Company Name cannot be empty.<br />";
			$work = false;
		}
		if (strlen($companylocation)<1) {
			$text .= "The Company Location cannot be empty.<br />";
			$work = false;
		}
		if (strlen($companysite)<1) {
			$text .= "The Company Website cannot be blank.<br />";
			$work = false;
		}
		if (strlen($email1)<1) {
			$text = "The Email Adress cannot be empty.<br />";
			$work = false;
		}
		if (strlen($pass1)<1) {
			$text .= "The Password cannot be empty.<br />";
			$work = false;
		}
		if (strlen($firstname)<1) {
			$text .= "First name cannot be blank.<br />";
			$work = false;
		}
		if (strlen($lastname)<1) {
			$text .= "Last name cannot be blank.<br />";
			$work = false;
		}
		if (strlen($cell)<1) {
			$text .= "Cell number cannot be blank.<br />";
			$work = false;
		}
		
		if (strlen($companyname)>100) {
			$text = "The Company Name is to long.<br />";
			$work = false;
		}
		if (strlen($companylocation)>100) {
			$text .= "The Company Location is to long.<br />";
			$work = false;
		}
		if (strlen($companysite)>100) {
			$text .= "The Company Website is to long.<br />";
			$work = false;
		}
		if (strlen($email1)>100) {
			$text .= "The Email Adress is to long.<br />";
			$work = false;
		}
		if (strlen($pass1)>50) {
			$text .= "The Password is to long.<br />";
			$work = false;
		}
		if (strlen($firstname)>50) {
			$text .= "The First name is to long.<br />";
			$work = false;
		}
		if (strlen($lastname)>50) {
			$text .= "The Last name cannot is to long.<br />";
			$work = false;
		}
		if (strlen($cell)<8) {
			$text .= "Cell number cannot be less then 8 digits.<br />";
			$work = false;
		}
		if (strlen($cell)>15) {
			$text .= "Cell number cannot be longer then 15 digits.<br />";
			$work = false;
		}
		
		// Check for valid email
		if (!filter_var($email1, FILTER_VALIDATE_EMAIL) === false) {
			//echo("$email is a valid email address.");
		} else if ($work) {
			$text = "\"$email1\" is not a valid email address.<br />";
			$work = false;
		}
		// Check email equality
		if ($email1 === $email2) {
			//echo("The emails match.");
		} else if ($work) {
			$text = "The emails do not match.<br />";
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
		
		if ($work) {
			// Check for Email duplicates
			$data = view('accounts',"email='$email1'");
			if($data['personid']) {
				$text = "The email address \"$email1\" already exists.<br />";
				$work = false;
			}
		}
		if ($work) {
			$photoid;
			$email1 = strtolower($email1);
			$correctDOB = date("Y-m-d", strtotime($dob));
			include('includes/password.php');
			$pass = encryptpass($pass1);
			$companyid = $_SESSION['companyid'];
			$address = addAddress($line1,$line2,$city,$subdivision,$zip,$country);
			$pid = addPerson($firstname,$lastname,$email1,$cell,$gender,$companyid,$photoid,$prefix,$suffix,$home,$worknumber,$extension,$correctDOB,$address,$middlename);
			$output = true;
			if ($pid && $output) {
				echo("Person was added succesfully!<br />");
				echo("Person ID:".$pid."<br />");
				o_log('Person Add Successful', 'ID: '.$pid);
			} else if ($output) {
				echo("ERROR PERSON WAS NOT ADDED!<br />");
				o_log('Person Add Failed');
			}
			$cid = addCoach($pid,$supervisor,$pass);
			if ($cid && $output) {
				echo("Coach was added succesfully!<br />");
				echo("Coach ID:".$cid."<br />");
				o_log('Coach Add Successful', 'ID: '.$cid);
			} else if ($output) {
				echo("ERROR COACH WAS NOT ADDED!<br />");
				o_log('Coach Add Failed');
			}
			$time = date("H:i:s", strtotime('12:00:00'));
			$workaddress = addAddress($line1,$line2,$city,$subdivision,$zip,$country);
			$clientID = addClient($pid,$workaddress,$workcompany,$worktitle,$workfield,$favoritebook,$favoritefood,$time,$time,$time,$time,$goals,$needs,$selfawareness,$cid);
			if ($clientID && $output) {
				echo("Client was added succesfully!<br />");
				echo("Client ID:".$clientID."<br />");
				o_log('Client Add Successful', 'ID: '.$clientID);
			} else if ($output) {
				echo("ERROR CLIENT WAS NOT ADDED!<br />");
				o_log('Client Add Failed');
			}
			
			$companyid = addCompany($companyname,$companylocation,$companysite,$pid);
			if ($companyid) {
				o_log('Company Add Successful', 'ID: '.$companyid);
				header('Location: /Company?c='.encrypt($companyid));
			} else {
				o_log('Company Add Failed');
			}
		}
	}
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
        <ul class="nav navbar-nav navbar-right">
                	<?php
						if ($_SESSION['admin']) {
							echo('<li class="nav-item active">
								<a class="nav-link" href="'.getCompanyLink().'">Manage Company</a>
							</li>');
						}
						if ($_SESSION['supervisor']) {
							echo('<li class="nav-item right-marigin50p">
								<a class="nav-link" href="/NewCoach">Add New Coach</a>
							</li>');
						}
					?>
            <li class="nav-item">
                <a class="nav-link" href="/Profile">Profile</a>
            </li>
            <li class="van-item">
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
</nav>
<br />
<div class="container">
    <div class="card text-center page-margin">
        <div class="card-header title">
            <?php echo($title.'<br />'); ?>
        </div>
        <div class="card-body">
                   <div class="row">
                    <div class="col-md-2">
                        <a href="<?php echo($back); ?>" class="btn btn-primary">Back</a>
                    </div>
                </div>
            <div class="row">
            <div class="col-md-12">
            <div class="newcoach_page page">
                <div class="newcoach">
                    <?php
                    echo($upload_image_text);
                    echo($text);
                    echo('
                <form action="#" method="post">
                    <table>
                    
                    <tr><td><h3>Company Information</h3></td><td>&thinsp;</td></tr>
                    <tr><td>Company Name:*</td><td><input type="text" name="companyname" autocomplete="off" /></td></tr>
                    <tr><td>Company Location:*</td><td><input type="text" name="companylocation" autocomplete="off" /></td></tr>
                    <tr><td>Company Website:*</td><td><input type="text" name="companysite" autocomplete="off" /></td></tr>
                    
                    <tr><td>&thinsp;</td><td>&thinsp;</td></tr>
					
					<tr><td><h3>Admin Information</h3></td><td>&thinsp;</td></tr>
                    <tr><td>First Name:*</td><td><input type="text" name="firstname" autocomplete="off" /></td></tr>
                    <tr><td>Last Name:*</td><td><input type="text" name="lastname" autocomplete="off" /></td></tr>
					<tr><td>Gender:*</td><td><select name="gender">
								<option value="male">Male</option>
								<option value="female">Female</option>
								<option value="other">Other</option>
								</select></td></tr>
                    <tr><td>Email:*</td><td><input type="email" name="email1" autocomplete="off" /></td></tr>
                    <tr><td>Confirm Email:*</td><td><input type="email" name="email2" autocomplete="off" /></td></tr>
                    <tr><td>Cell Number:*</td><td><input type="number" name="cell" autocomplete="off" /></td></tr>
                    <tr><td>Password:* <span class="password_info">(minimum 8 characters)</span></td><td><input type="password" name="pass1"  autocomplete="off" /></td></tr>
                    <tr><td>Confirm Password:*</td><td><input type="password" name="pass2"  autocomplete="off" /></td></tr>
                    <tr><td>&thinsp;</td><td>&thinsp;</td></tr>
                    </table>
                    <input type="submit" value="Submit" class="button" /><br />
                    <input type="reset" value="Reset" class="button" />
                </form>
            ');
                    ?>
                </div>
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
</div>

</body>
</html>