<?php
	include('includes/session.php');
	if (!$_SESSION['employeed']) {
		header('Location: /');
	}
	$title = 'Add New Client';
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
		
		/*$workcompany = pg_escape_string($conn, $POST['workcompany']);
		$worktitle = pg_escape_string($conn, $POST['worktitle']);
		$workfield = pg_escape_string($conn, $POST['workfield']);
		$visitpreferencestart = $POST['visittimepreferencestart'].':00';
		$visitpreferenceend = $POST['visittimepreferenceend'].':00';
		$callpreferencestart = $POST['calltimepreferencestart'].':00';
		$callpreferenceend = $POST['calltimepreferenceend'].':00';
		$favoritebook = pg_escape_string($conn, $POST['favoritebook']);
		$favoritefood = pg_escape_string($conn, $POST['favoritefood']);
		$goals = pg_escape_string($conn, $POST['goals']);
		$needs = pg_escape_string($conn, $POST['needs']);*/
		
		$workcompany = $_POST['workcompany'];
		$worktitle = $_POST['worktitle'];
		$workfield = $_POST['workfield'];
		$visitpreferencestart = checkTime($_POST['visittimepreferencestart'].':00');
		$visitpreferenceend = checkTime($_POST['visittimepreferenceend'].':00');
		$callpreferencestart = checkTime($_POST['calltimepreferencestart'].':00');
		$callpreferenceend = checkTime($_POST['calltimepreferenceend'].':00');
		$favoritebook = $_POST['favoritebook'];
		$favoritefood = $_POST['favoritefood'];
		$goals = $_POST['goals'];
		$needs = $_POST['needs'];
		
		
		echo ('prefix: '.$prefix.'. from post: '.$_POST['prefix'].'<br />');
		echo ('firstname: '.$firstname.'. from post: '.$_POST['firstname'].'<br />');
		echo ('middlename: '.$middlename.'. from post: '.$_POST['middlename'].'<br />');
		echo ('lastname: '.$lastname.'. from post: '.$_POST['lastname'].'<br />');
		echo ('suffix: '.$suffix.'. from post: '.$_POST['suffix'].'<br />');
		echo ('email1: '.$email1.'. from post: '.$_POST['email1'].'<br />');
		echo ('email2: '.$email2.'. from post: '.$_POST['email2'].'<br />');
		echo ('cell: '.$cell.'. from post: '.$_POST['cell'].'<br />');
		echo ('home: '.$home.'. from post: '.$_POST['home'].'<br />');
		echo ('work: '.$worknumber.'. from post: '.$_POST['work'].'<br />');
		echo ('extension: '.$extension.'. from post: '.$_POST['extension'].'<br />');
		echo ('dob: '.$dob.'. from post: '.$_POST['dob'].'<br />');
		
		echo ('workcompany: '.$workcompany.'. from post: '.$_POST['workcompany'].'<br />');
		echo ('worktitle: '.$worktitle.'. from post: '.$_POST['worktitle'].'<br />');
		echo ('workfield: '.$workfield.'. from post: '.$_POST['workfield'].'<br />');
		echo ('visittimepreferencestart: '.$visitpreferencestart.'. from post: '.$_POST['visittimepreferencestart'].'<br />');
		echo ('visittimepreferenceend: '.$visitpreferenceend.'. from post: '.$_POST['visittimepreferenceend'].'<br />');
		echo ('calltimepreferencestart: '.$callpreferencestart.'. from post: '.$_POST['calltimepreferencestart'].'<br />');
		echo ('calltimepreferenceend: '.$callpreferenceend.'. from post: '.$_POST['calltimepreferenceend'].'<br />');
		echo ('favoritebook: '.$favoritebook.'. from post: '.$_POST['favoritebook'].'<br />');
		echo ('favoritefood: '.$favoritefood.'. from post: '.$_POST['favoritefood'].'<br />');
		echo ('goals: '.$goals.'. from post: '.$_POST['goals'].'<br />');
		echo ('needs: '.$needs.'. from post: '.$_POST['needs'].'<br />');
		
		pg_close($conn);
		
		$work = true;
		if (strlen($email1)<1) {
			$text = "The Email Adress cannot be empty.<br />";
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
			$pid = addPerson($firstname,$lastname,$email1,$cell,$photoid,$prefix,$suffix,$home,$worknumber,$extension,$correctDOB,$address,$middlename,true);
			$companyid = $_SESSION['companyid'];
			$output = true;
			if ($pid && $output) {
				echo("Person was added succesfully!<br />");
				echo("Person ID:".$pid."<br />");
			} else if ($output) {
				echo("ERROR PERSON WAS NOT ADDED!<br />");
			}
			
			$cid = addClient($pid,$companyid,$workaddress,$workcompany,$worktitle,$workfield,$favoritebook,$favoritefood,$visitpreferencestart,$visitpreferenceend,$callpreferencestart,$callpreferenceend,$goals,$needs,true);
			if ($cid && $output) {
				echo("Client was added succesfully!<br />");
				echo("Client ID:".$cid."<br />");
				//header('Location: /');
			} else if ($output) {
				echo("ERROR CLIENT WAS NOT ADDED!<br />");
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
<link type="text/css" rel="stylesheet" href="/bootstrap/4.0.0/css/bootstrap.min.css">
<!-- jQuery library -->
<script type="text/javascript" src="/js/jquery/jquery-3.2.1.min.js"></script>
<!-- Latest compiled JavaScript -->
<script type="text/javascript" src="/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<!-- Our CSS -->
<link type="text/css" rel="stylesheet" href="/css/life-coach.css">
<title><?php echo($title); ?></title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="/">Logo</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
            <li class="nav-item active">
                <a class="nav-link" href="/">Home <span class="sr-only">(current)</span></a>
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
<div class="card text-center page-margin">
	<div class="card-header title">
		<?php echo($title); ?>
	</div>
	<div class="card-body">
		<div class="newclient_page page">
			<div class="newclient">
				<?php
				echo($text);
				echo('
			<form action="#" method="post">
				<h3>Personal Information</h3>
				Prefix:<input type="text" name="prefix" autocomplete="off" /><br />
				First Name:*<input type="text" name="firstname" autocomplete="off" /><br />
				Middle Name:<input type="text" name="middlename" autocomplete="off" /><br />
				Last Name:*<input type="text" name="lastname" autocomplete="off" /><br />
				Suffix:<input type="text" name="suffix" autocomplete="off" /><br />
				Email:*<input type="email" name="email1" autocomplete="off" /><br />
				Confirm Email:*<input type="email" name="email2" autocomplete="off" /><br />
				Cell Number:*<input type="number" name="cell" autocomplete="off" /><br />
				Home Number:<input type="number" name="home" autocomplete="off" /><br />
				Date of Birth:<input type="date" name="dob" autocomplete="off" /><br />
				Home address: *NOT IMPLEMENTED YET*<br /><br />
				
				<h3>Work Information</h3>
				Work Number:<input type="number" name="work" autocomplete="off" /><br />
				Work extension:<input type="number" name="extension" autocomplete="off" /><br />
				Place of work:<input type="text" name="workcompany" autocomplete="off" /><br />
				Job title:<input type="text" name="worktitle" autocomplete="off" /><br />
				Field of employment:<input type="text" name="workfield" autocomplete="off" /><br />
				Work address: *NOT IMPLEMENTED YET*<br /><br />
				
				<h3>Time Preferences</h3>
				Visit start:
				<input type="time" name="visittimepreferencestart" autocomplete="off" /><br />
				Visit end:
				<input type="time" name="visittimepreferenceend" autocomplete="off" /><br />
				Call start:
				<input type="time" name="calltimepreferencestart" autocomplete="off" /><br />
				Call end:
				<input type="time" name="calltimepreferenceend" autocomplete="off" /><br /><br />
				
				<h3>About</h3>
				Favorite book:
				<input type="text" name="favoritebook" autocomplete="off" /><br />
				Favorite food:
				<input type="text" name="favoritefood" autocomplete="off" /><br />
				Goals:<br />
				<textarea rows="4" cols="50" name="goals" autocomplete="off"></textarea><br />
				Needs:<br />
				<textarea rows="4" cols="50" name="needs" autocomplete="off"></textarea><br />
				
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

</body>
</html>