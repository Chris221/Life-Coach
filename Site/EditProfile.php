<?php
	include('includes/log.php');
	include('includes/session.php');
	include('includes/protection.php');
	include('includes/api.php');
	if ($_SESSION['employeed']  ==  'f') {
		header('Location: /Login');
	}
	if (isset($_GET['p'])) {
		$pid = decrypt($_GET['p']);
		o_log('Page Loaded', 'Edit Profile ID: '.$pid);
		$title = 'Edit Profile';
		$back = '/Profile?p='.$_GET['p'];
	} else {
		header('Location: /Profile');
	}

	$personResult = view('persons','personid='.$pid);
	$clientResult = view('clients','personid='.$pid);
	$coachResult  = view('coaches','personid='.$pid);

	$prefix = $personResult['prefix'];
	$firstname = $personResult['first_name'];
	$middlename = $personResult['middle_name'];
	$lastname = $personResult['last_name'];
	$suffix = $personResult['suffix'];

	$email = $personResult['email'];
	$cell = $personResult['cell'];
	$home = $personResult['home'];
	$worknumber = $personResult['work'];
	$extension = $personResult['extension'];
	$dob = date('Y-m-d', strtotime($personResult['date_of_birth']));

	$workcompany = $clientResult['work_company'];
	$clientid = $clientResult['clientid'];
	$cid = $clientResult['coachid'];
	$workaddress = getAddress($clientResult['work_address']);
	$worktitle = $clientResult['work_title'];
	$workfield = $clientResult['work_field'];
	$favoritebook = $clientResult['favorite_book'];
	$favoritefood = $clientResult['favorite_food'];
	$selfawareness = $clientResult['selfawareness'];
	$visit_time_preference_start = date('H:i', strtotime($clientResult['visit_time_preference_start']));
	$visit_time_preference_end = date('H:i', strtotime($clientResult['visit_time_preference_end']));
	$call_time_preference_start = date('H:i', strtotime($clientResult['call_time_preference_start']));
	$call_time_preference_end = date('H:i', strtotime($clientResult['call_time_preference_end']));
	$goals = $clientResult['goals'];
	$needs = $clientResult['needs'];
	$gender = $personResult['gender'];

	if ($gender == 'male'){
		$male = 'selected';
	} else if ($gender == 'female'){
		$female = 'selected';
	} else if ($gender == 'other'){
		$other = 'selected';
	}

	if ($coachResult['supervisor'] != 'f') {
		$supervisor = 'checked';
	}
	if ($coachResult['employeed'] != 'f') {
		$employeed = 'checked';
	}
	
	if ($personResult['deceased'] != 'f') {
		$deceased = 'checked';
	}
	
	if ($_SESSION['supervisor'] != 'f') {
		$supervisorFull = '<tr><td>Supervisor:</td><td><input type="checkbox" name="supervisor" autocomplete="off" '.$supervisor.' /></td></tr>';
	}

	if ($coachResult['coachid']) {
		$coachInfo = '<tr><td><h3>Coach Information</h3></td><td>&thinsp;</td></tr>
                    '.$supervisorFull.'
                    <tr><td>Employeed:</td><td><input type="checkbox" name="employeed" autocomplete="off" '.$employeed.' /></td></tr>
                    <tr><td>&thinsp;</td><td>&thinsp;</td></tr>';
	}


	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		include('includes/db.php');
		$prefix = pg_escape_string($conn, $_POST['prefix']);
		$firstname = pg_escape_string($conn, $_POST['firstname']);
		$middlename = pg_escape_string($conn, $_POST['middlename']);
		$lastname = pg_escape_string($conn, $_POST['lastname']);
		$suffix = pg_escape_string($conn, $_POST['suffix']);
		$email = pg_escape_string($conn, strtolower($_POST['email']));
		$cell = cleanPhoneNumber($_POST['cell']);
		$home = cleanPhoneNumber($_POST['home']);
		$worknumber = cleanPhoneNumber($_POST['work']);
		$extension = cleanPhoneNumber($_POST['extension']);
		$dob = $_POST['dob'];
		$gender = $_POST['gender'];

		$workcompany = $_POST['workcompany'];
		$worktitle = $_POST['worktitle'];
		$workfield = $_POST['workfield'];
		$visit_time_preference_start = checkTime($_POST['visittimepreferencestart'] . ':00');
		$visit_time_preference_end = checkTime($_POST['visittimepreferenceend'] . ':00');
		$call_time_preference_start = checkTime($_POST['calltimepreferencestart'] . ':00');
		$call_time_preference_end = checkTime($_POST['calltimepreferenceend'] . ':00');
		$favoritebook = $_POST['favoritebook'];
		$favoritefood = $_POST['favoritefood'];
		$goals = $_POST['goals'];
		$needs = $_POST['needs'];
		$selfawareness = $_POST['selfawareness'];
		if(isset($_POST['supervisor'])) {
			$supervisor = 'true';
		} else {
			$supervisor = 'false';
		}
		if(isset($_POST['employeed'])) {
			$employeed = 'true';
		} else {
			$employeed = 'false';
		}
		if(isset($_POST['deceased'])) {
			$deceased = 'true';
		} else {
			$deceased = 'false';
		}

		pg_close($conn);

		$work = true;
		if (strlen($email) < 1) {
			$text = "The Email Adress cannot be empty.<br />";
			$work = false;
		}
		if (strlen($firstname) < 1) {
			$text .= "First name cannot be blank.<br />";
			$work = false;
		}
		if (strlen($lastname) < 1) {
			$text .= "Last name cannot be blank.<br />";
			$work = false;
		}
		if (strlen($cell) < 1) {
			$text .= "Cell number cannot be blank.<br />";
			$work = false;
		}

		if (strlen($email) > 100) {
			$text .= "The Email Adress is to long.<br />";
			$work = false;
		}
		if (strlen($firstname) > 50) {
			$text .= "The First name is to long.<br />";
			$work = false;
		}
		if (strlen($lastname) > 50) {
			$text .= "The Last name cannot is to long.<br />";
			$work = false;
		}
		if (strlen($cell) < 9) {
			$text .= "Cell number cannot be less then 9 digets.<br />";
			$work = false;
		}
		if (strlen($cell) > 15) {
			$text .= "Cell number cannot be longer then 15 digets.<br />";
			$work = false;
		}

		// Check for valid email
		if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
			//echo("$email is a valid email address.");
		} else if ($work) {
			$text = "\"$email\" is not a valid email address.<br />";
			$work = false;
		}
		if ($work) {
			//$photoid = uploadImage();
			$email = strtolower($email);
			$correctDOB = date("Y-m-d", strtotime($dob));
			
			changeProfile($pid, $firstname, $lastname, $email, $cell, $gender, $prefix, $suffix, $home, $worknumber, $extension, $correctDOB, $middlename, $workcompany, $worktitle, $workfield, $favoritebook, $favoritefood, $visit_time_preference_start, $visit_time_preference_end, $call_time_preference_start, $call_time_preference_end, $goals, $needs, $selfawareness, $supervisor, $employeed, $deceased);
			
			header('Location: '.$back);
		}
	}
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <!-- For Mobile scaling -->
    <meta name="viewport" content="width=device-width, user-scalable=no"/>
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
    <a class="navbar-brand" href="/"><img src="/logo.png" width="50" height="50" alt="Logo"/></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02"
            aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
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
						 if ($_SESSION['admin'] == 'true') {
							echo('<li class="nav-item">
								<a class="nav-link" href="'.getCompanyLink().'">Manage Company</a>
							</li>');
						}
						if ($_SESSION['supervisor'] == 't') {
							echo('<li class="nav-item right-marigin50p">
								<a class="nav-link" href="/NewCoach">Add New Coach</a>
							</li>');
						}
					?>
            <li class="nav-item active">
                <a class="nav-link" href="/Profile">Profile<span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/Logout">Logout</a>
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
<br/>
<div class="container">
        <div class="card text-center page-margin0">
            <div class="card-header title">
                <div class="row">
                    <div class="col-sm-1">
                        <a href="<?php echo($back); ?>" class="btn btn-primary">Back</a>
                    </div>
                    <div class="col-sm-10">
                		<?php echo($title); ?>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="newclient_page page">
                            <div class="newclient">
                                <?php
                                echo($text);
                                echo('
                        <form action="#" method="post"><table>
                            
                            <tr><td><h3>Personal Information</h3></td><td>&thinsp;</td></tr>
                            <tr><td>Prefix:</td><td><input type="text" name="prefix" autocomplete="off" value="'.$prefix.'" /></td></tr>
                            <tr><td>First Name:*</td><td><input type="text" name="firstname" autocomplete="off" value="'.$firstname.'" /></td></tr>
                            <tr><td>Middle Name:</td><td><input type="text" name="middlename" autocomplete="off" value="'.$middlename.'" /></td></tr>
                            <tr><td>Last Name:*</td><td><input type="text" name="lastname" autocomplete="off" value="'.$lastname.'" /></td></tr>
                            <tr><td>Suffix:</td><td><input type="text" name="suffix" autocomplete="off" value="'.$suffix.'" /></td></tr>
							<tr><td>Gender:*</td><td><select name="gender">
								<option value="male" '.$male.'>Male</option>
								<option value="female" '.$female.'>Female</option>
								<option value="other" '.$other.'>Other</option>
								</select></td></tr>
                            <tr><td>Email:*</td><td><input type="email" name="email" autocomplete="off" value="'.$email.'" /></td></tr>
                            <tr><td>Cell Number:*</td><td><input type="number" name="cell" autocomplete="off" value="'.$cell.'" /></td></tr>
                            <tr><td>Home Number:</td><td><input type="number" name="home" autocomplete="off" value="'.$home.'" /></td></tr>
                            <tr><td>Date of Birth:</td><td><input type="date" name="dob" autocomplete="off" value="'.$dob.'" /></td></tr>
                    		<tr><td>Deceased:</td><td><input type="checkbox" name="deceased" autocomplete="off" '.$deceased.' /></td></tr>
							
                            <tr><td>&thinsp;</td><td>&thinsp;</td></tr>
							'.$coachInfo.'
                            
                            <tr><td><h3>Work Information</h3></td><td>&thinsp;</td></tr>
                            <tr><td>Work Number:</td><td><input type="number" name="work" autocomplete="off" value="'.$worknumber.'" /></td></tr>
                            <tr><td>Work extension:</td><td><input type="number" name="extension" autocomplete="off" value="'.$extension.'" /></td></tr>
                            <tr><td>Place of work:</td><td><input type="text" name="workcompany" autocomplete="off" value="'.$workcompany.'" /></td></tr>
                            <tr><td>Job title:</td><td><input type="text" name="worktitle" autocomplete="off"  value="'.$worktitle.'" /></td></tr>
                            <tr><td>Field of employment:</td><td><input type="text" name="workfield" autocomplete="off" value="'.$workfield.'" /></td></tr>
                            
                            <tr><td>&thinsp;</td><td>&thinsp;</td></tr>
                            
                            <tr><td><h3>Time Preferences</h3></td><td>&thinsp;</td></tr>
                            <tr><td>Visit start:</td><td><input type="time" name="visittimepreferencestart" autocomplete="off" value="'.$visit_time_preference_start.'" /></td></tr>
                            <tr><td>Visit end:</td><td><input type="time" name="visittimepreferenceend" autocomplete="off"  value="'.$visit_time_preference_end.'" /></td></tr>
                            <tr><td>Call start:</td><td><input type="time" name="calltimepreferencestart" autocomplete="off"  value="'.$call_time_preference_start.'" /></td></tr>
                            <tr><td>Call end:</td><td><input type="time" name="calltimepreferenceend" autocomplete="off"  value="'.$call_time_preference_end.'" /></td></tr>
                            
                            <tr><td>&thinsp;</td><td>&thinsp;</td></tr>
                            
                            <tr><td><h3>About</h3></td><td>&thinsp;</td></tr>
                            <tr><td>Favorite book:</td><td><input type="text" name="favoritebook" autocomplete="off"  value="'.$favoritebook.'" /></td></tr>
                            <tr><td>Favorite food:</td><td><input type="text" name="favoritefood" autocomplete="off"  value="'.$favoritefood.'" /></td></tr>
                            <tr><td>Prefered Self-Awareness Method:</td><td><input type="text" name="selfawareness" autocomplete="off"  value="'.$selfawareness.'" /></td></tr>
                            </table>
                            <table>
                            <tr><td class="client_about">Goals:</td></tr>
                            <tr><td class="client_about"><textarea rows="4" cols="50" name="goals" autocomplete="off">'.$goals.'</textarea></td></tr>
                            <tr><td class="client_about">Needs:</td></tr>
                            <tr><td class="client_about"><textarea rows="4" cols="50" name="needs" autocomplete="off">'.$needs.'</textarea></td></tr>
                            
                            <tr><td class="client_about">&thinsp;</td></tr>
                            
                            <tr><td class="client_about"><input type="submit" value="Submit" class="button" /></td></tr>
                        </form>
                        </table>
                    ');
                                ?>
                            </div>
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
</body>
</html>