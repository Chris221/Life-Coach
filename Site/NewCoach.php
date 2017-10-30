<?php
	include('includes/session.php');
	if (!$_SESSION['supervisor']) {
		header('Location: /');
	}
	$title = 'Add New Coach';
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		include('includes/api.php');
		include('includes/db.php');
		
		$prefix = pg_escape_string($conn, $_POST['prefix']);
		$firstname = pg_escape_string($conn, $_POST['firstname']);
		$middlename = pg_escape_string($conn, $_POST['middlename']);
		$lastname = pg_escape_string($conn, $_POST['lastname']);
		$suffix = pg_escape_string($conn, $_POST['suffix']);
		$email1 = pg_escape_string($conn, strtolower($_POST['email1']));
		$email2 = pg_escape_string($conn, strtolower($_POST['email2']));
		$cell = cleanPhoneNumber($_POST['cell']);
		$home = cleanPhoneNumber($_POST['home']);
		$worknumber = cleanPhoneNumber($_POST['work']);
		$extension = cleanPhoneNumber($_POST['extension']);
		$dob = $_POST['dob'];
		$pass1 = $_POST['pass1'];
		$pass2 = $_POST['pass2'];
		
		pg_close($conn);
		
		if(isset($_POST['supervisor'])) {
			$supervisor = 'true';
		} else {
			$supervisor = 'false';
		}
		
		$work = true;
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
		if (strlen($cell)<9) {
			$text .= "Cell number cannot be less then 9 digets.<br />";
			$work = false;
		}
		if (strlen($cell)>15) {
			$text .= "Cell number cannot be longer then 15 digets.<br />";
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
			$data = view('accounts',"email='$email1'",true);
			if($data['personid']) {
				$text = "The email address \"$email1\" already exists.<br />";
				$work = false;
			}
		}
		if ($work) {
			$email1 = strtolower($email1);
			$correctDOB = date("Y-m-d", strtotime($dob));
			include('includes/password.php');
			$pass = encryptpass($pass1);
			$pid = addPerson($firstname,$lastname,$email1,$cell,$photoid,$prefix,$suffix,$home,$worknumber,$extension,$correctDOB,$address,$middlename,true);
			$companyid = $_SESSION['companyid'];
			$output = true;
			if ($pid && $output) {
				echo("Person was added succesfully!<br />");
				echo("Person ID:".$pid."<br />");
			} else if ($output) {
				echo("ERROR PERSON WAS NOT ADDED!<br />");
			}
			$cid = addCoach($pid,$clientid,$companyid,$supervisor,$pass,true);
			if ($cid && $output) {
				echo("Coach was added succesfully!<br />");
				echo("Coach ID:".$cid."<br />");
				header('Location: /');
			} else if ($output) {
				echo("ERROR COACH WAS NOT ADDED!<br />");
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
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="/bootstrap/4.0.0/css/bootstrap.min.css">
<!-- jQuery library -->
<script src="/js/jquery/jquery-3.2.1.min.js"></script>
<!-- Latest compiled JavaScript -->
<script src="/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<!-- Our CSS -->
<link rel="stylesheet" href="/css/life-coach.css">
<title><?php echo($title); ?></title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-blue">
    <a class="navbar-brand" href="/index">Logo</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
            <li class="nav-item active">
                <a class="nav-link" href="/index">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Schedule</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Clients</a>
            </li>
        </ul>
        <!--        I changed this to align the logout to the right-->
        <ul class="nav navbar-nav navbar-right">
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
<div>
    <div class="card text-center">
        <div class="card-header title">
            Add a New Coach
        </div>
        <div class="card-body">
            <div class="newcoach_page page">
                <div class="newcoach">
                    <?php
                    echo($title.'<br />');
                    echo($text);
                    echo('
				<form action="#" method="post">
					Prefix: <br />
					<input type="text" name="prefix" /><br />
					First Name:* <br />
					<input type="text" name="firstname" /><br />
					Middle Name: <br />
					<input type="text" name="middlename" /><br />
					Last Name:* <br />
					<input type="text" name="lastname" /><br />
					Suffix: <br />
					<input type="text" name="suffix" /><br />
					Email:* <br />
					<input type="email" name="email1" /><br />
					Confirm Email:* <br />
					<input type="email" name="email2" /><br />
					Cell Number:* <br />
					<input type="number" name="cell" /><br />
					Home Number: <br />
					<input type="number" name="home" /><br />
					Work Number: <br />
					<input type="number" name="work" /><br />
					Work extension: <br />
					<input type="number" name="extension" /><br />
					Date of Birth: <br />
					<input type="date" name="dob" /><br />
					supervisor:
					<input type="checkbox" name="supervisor" /><br />
					
					Password:* (minimum 8 characters)<br />
					<input type="password" name="pass1" /><br />
					Confirm Password:* <br />
					<input type="password" name="pass2" /><br />
					<input type="submit" value="Submit" class="button" /><br />
					<input type="reset" value="Reset" class="button" />
				</form>
			');
                    ?>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            Footer Text
        </div>
    </div>
</div>

</body>
</html>
