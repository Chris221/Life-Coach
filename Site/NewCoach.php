<?php
	include('includes/session.php');
	if (!$_SESSION['superviser']) {
		header('Location: /');
	}
	$title = 'Add New Coach';
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		include('includes/api.php');
		
		$prefix = $_POST['prefix'];
		$firstname = $_POST['firstname'];
		$middlename = $_POST['middlename'];
		$lastname = $_POST['lastname'];
		$suffix = $_POST['suffix'];
		$email1 = $_POST['email1'];
		$email2 = $_POST['email2'];
		$cell = cleanPhoneNumber($_POST['cell']);
		$home = cleanPhoneNumber($_POST['home']);
		$worknumber = cleanPhoneNumber($_POST['work']);
		$extension = cleanPhoneNumber($_POST['extension']);
		$dob = $_POST['dob'];
		$pass1 = $_POST['pass1'];
		$pass2 = $_POST['pass2'];
		if(isset($_POST['superviser'])) {
			$superviser = true;
		} else {
			$superviser = false;
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
			view('accounts',"email='$email1'");
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
			$pid = addPerson($firstname,$lastname,$email1,$cell,$photoid,$prefix,$suffix,$home,$worknumber,$extension,$correctDOB,$address,$middlename);
			$companyid = $_SESSION['companyid'];
			$output = true;
			if ($pid && $output) {
				echo("Person was added succesfully!<br />");
				echo("Person ID:".$pid."<br />");
			} else if ($output) {
				echo("ERROR PERSON WAS NOT ADDED!<br />");
			}
			$cid = addCoach($pid,$clientid,$companyid,$superviser,$pass);
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
<title><?php echo($title); ?></title>
</head>
<body>
<div class="login_page page">
	<div class="login">
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
					superviser: <br />
					<input type="checkbox" name="superviser" /><br />
					
					Password:* (minimum 8 characters)<br />
					<input type="password" name="pass1" /><br />
					Confirm Password:* <br />
					<input type="password" name="pass2" /><br />
					<input type="reset" value="Reset" />&thinsp;
					<input type="submit" value="Submit" /><br /><br />
				</form>
			');
		?>
	</div>
</div>
</body>
</html>