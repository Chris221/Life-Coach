<?php
	//Loading Includes
	include('includes/session.php');
	include('includes/log.php');
	include('includes/uploadPhoto.php');
	include('includes/api.php');
	include('includes/protection.php');
	if ($_SESSION['employeed']  ==  'f' || !$_SESSION['employeed']) {
		header('Location: /Login');
	}
	o_log('Page Loaded');
	$title = 'Add New Client';


	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		//Loading Includes
		include('includes/db.php');
		$prefix = pg_escape_string($conn, $_POST['prefix']);
		$firstname = pg_escape_string($conn, $_POST['firstname']);
		$middlename = pg_escape_string($conn, $_POST['middlename']);
		$lastname = pg_escape_string($conn, $_POST['lastname']);
		$suffix = pg_escape_string($conn, $_POST['suffix']);
		$email1 = pg_escape_string($conn, strtolower($_POST['email1']));
		$email2 = pg_escape_string($conn, strtolower($_POST['email2']));
		$cell = cleanNumber($_POST['cell']);
		$home = cleanNumber($_POST['home']);
		$worknumber = cleanNumber($_POST['work']);
		$extension = cleanNumber($_POST['extension']);
		$dob = $_POST['dob'];
		$gender = $_POST['gender'];

		$workcompany = $_POST['workcompany'];
		$worktitle = $_POST['worktitle'];
		$workfield = $_POST['workfield'];
		$visitpreferencestart = checkTime($_POST['visittimepreferencestart'] . ':00');
		$visitpreferenceend = checkTime($_POST['visittimepreferenceend'] . ':00');
		$callpreferencestart = checkTime($_POST['calltimepreferencestart'] . ':00');
		$callpreferenceend = checkTime($_POST['calltimepreferenceend'] . ':00');
		$favoritebook = $_POST['favoritebook'];
		$favoritefood = $_POST['favoritefood'];
		$selfawareness = $_POST['selfawareness'];
		$goals = $_POST['goals'];
		$needs = $_POST['needs'];

		pg_close($conn);

		$work = true;
		if (strlen($email1) < 1) {
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

		if (strlen($email1) > 100) {
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
			$data = view('persons', "email='$email1'");
			if ($data['personid']) {
				$text = "The email address \"$email1\" already exists.<br />";
				$work = false;
			}
		}
		if ($work) {
			$hline1 = $_POST['hline1'];
			$hline2 = $_POST['hline2'];
			$hcity = $_POST['hcity'];
			$hsubdivision = $_POST['hsubdivision'];
			$hzip = $_POST['hzip'];
			$hcountry = $_POST['hcountry'];
			
			$address = addAddress($hline1,$hline2,$hcity,$hsubdivision,$hzip,$hcountry);
			
			if ((strpos($address, 'blank') !== false)) {
				$text = $address.'<br />';
			} else {
				$wline1 = $_POST['wline1'];
				$wline2 = $_POST['wline2'];
				$wcity = $_POST['wcity'];
				$wsubdivision = $_POST['wsubdivision'];
				$wzip = $_POST['wzip'];
				$wcountry = $_POST['wcountry'];
			
				$workaddress = addAddress($wline1,$wline2,$hcity,$wsubdivision,$wzip,$wcountry);
				if ((strpos($workaddress, 'blank') !== false)) {
					$text = $workaddress.'<br />';
				} else {
					$photoid = uploadImage();
					if (strpos($photoid, '<br />') !== false) {
						$text = $photoid.'<br />';
					} else {
						$email1 = strtolower($email1);
						$correctDOB = date("Y-m-d", strtotime($dob));
						$companyid = $_SESSION['companyid'];
						$pid = addPerson($firstname, $lastname, $email1, $cell, $gender, $companyid, $photoid, $prefix, $suffix, $home, $worknumber, $extension, $correctDOB, $address, $middlename);
						$output = true;
						if ($pid && $output) {
							//echo("Person was added succesfully!<br />");
							//echo("Person ID:" . $pid . "<br />");
							o_log('Person Add Successful', 'ID: ' . $pid);
						} else if ($output) {
							//echo("ERROR PERSON WAS NOT ADDED!<br />");
							o_log('Person Add failed');
						}

						$coachid = $_SESSION['coachid'];

						$cid = addClient($pid, $workaddress, $workcompany, $worktitle, $workfield, $favoritebook, $favoritefood, $visitpreferencestart, $visitpreferenceend, $callpreferencestart, $callpreferenceend, $goals, $needs, $selfawareness, $coachid);
						if ($cid && $output) {
							//echo("Client was added succesfully!<br />");
							//echo("Client ID:" . $cid . "<br />");
							o_log('Client Add Successful', 'ID: ' . $cid);
							header('Location: /Profile?p='.encrypt($pid));
						} else if ($output) {
							//echo("ERROR CLIENT WAS NOT ADDED!<br />");
							o_log('Client Add failed');
						}
					}
				}
			}
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
            <li class="nav-item active">
                <a class="nav-link" href="/Clients">Clients <span class="sr-only">(current)</span></a>
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
							echo('<li class="nav-item right-marigin40p">
								<a class="nav-link" href="/NewCoach">Add New Coach</a>
							</li>');
						}
					?>
            <li class="nav-item">
                <a class="nav-link" href="/Profile">Profile</a>
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
        <div class="card text-center page-margin">
            <div class="card-header title">
                <div class="row">
                    <div class="col-sm-1 text-left">
                        <a href="/Clients" class="btn btn-primary">Back</a>
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
                                echo($upload_image_text);
                                echo($text);
                                echo('<table>
                        <form action="#" method="post" enctype="multipart/form-data">
                            <tr><td><h3 class="image_header">Photo</h3></td><td>&thinsp;</td></tr>
                            <input type="hidden" name="MAX_FILE_SIZE" value="5120000">
                            <tr><td><input name="image" type="file" accept="image/*"></td><td>&thinsp;</td></tr>
                            <tr><td>The photo can not be any larger then 5MB.</td><td>&thinsp;</td></tr>
                            <tr><td>The photo types supported are JPG, PNG, & GIF.</td><td>&thinsp;</td></tr>
                            <tr><td>May take up to a minute as the server processes the image.</td><td>&thinsp;</td></tr>
                            
                            </table><table>
                            
                            <tr><td><h3>Personal Information</h3></td><td>&thinsp;</td></tr>
                            <tr><td>Prefix:</td><td><input type="text" name="prefix" autocomplete="off" /></td></tr>
                            <tr><td>First Name:*</td><td><input type="text" name="firstname" autocomplete="off" /></td></tr>
                            <tr><td>Middle Name:</td><td><input type="text" name="middlename" autocomplete="off" /></td></tr>
                            <tr><td>Last Name:*</td><td><input type="text" name="lastname" autocomplete="off" /></td></tr>
                            <tr><td>Suffix:</td><td><input type="text" name="suffix" autocomplete="off" /></td></tr>
							<tr><td>Gender:*</td><td><select name="gender">
								<option value="male">Male</option>
								<option value="female">Female</option>
								<option value="other">Other</option>
								</select></td></tr>
                            <tr><td>Email:*</td><td><input type="email" name="email1" autocomplete="off" /></td></tr>
                            <tr><td>Confirm Email:*</td><td><input type="email" name="email2" autocomplete="off" /></td></tr>
                            <tr><td>Cell Number:*</td><td><input type="number" name="cell" autocomplete="off" /></td></tr>
                            <tr><td>Home Number:</td><td><input type="number" name="home" autocomplete="off" /></td></tr>
                            <tr><td>Date of Birth:</td><td><input type="date" name="dob" autocomplete="off" /></td></tr>
							
                            <tr><td>&thinsp;</td><td>&thinsp;</td></tr>
							
                            <tr><td><h3>Home Address</h3></td><td>&thinsp;</td></tr>
							<tr><td>Address:</td><td><input type="text" name="hline1" autocomplete="off" /></td></tr>
							<tr><td></td><td><input type="text" name="hline2" autocomplete="off" /></td></tr>
							<tr><td>City:</td><td><input type="text" name="hcity" autocomplete="off" /></td></tr>
							<tr><td>State/Provence:</td><td><input type="text" name="hsubdivision" autocomplete="off" /></td></tr>
							<tr><td>Zip Code:</td><td><input type="number" name="hzip" autocomplete="off" /></td></tr>
							<tr><td>Country Code:</td><td><input type="text" name="hcountry" autocomplete="off" /></td></tr>
							
                            <tr><td>&thinsp;</td><td>&thinsp;</td></tr>
                            
                            <tr><td><h3>Work Information</h3></td><td>&thinsp;</td></tr>
                            <tr><td>Work Number:</td><td><input type="number" name="work" autocomplete="off" /></td></tr>
                            <tr><td>Work extension:</td><td><input type="number" name="extension" autocomplete="off" /></td></tr>
                            <tr><td>Place of work:</td><td><input type="text" name="workcompany" autocomplete="off" /></td></tr>
                            <tr><td>Job title:</td><td><input type="text" name="worktitle" autocomplete="off" /></td></tr>
                            <tr><td>Field of employment:</td><td><input type="text" name="workfield" autocomplete="off" /></td></tr>
                            
                            <tr><td>&thinsp;</td><td>&thinsp;</td></tr>
							
                            <tr><td><h3>Work Address</h3></td><td>&thinsp;</td></tr>
							<tr><td>Address:</td><td><input type="text" name="wline1" autocomplete="off" /></td></tr>
							<tr><td></td><td><input type="text" name="wline2" autocomplete="off" /></td></tr>
							<tr><td>City:</td><td><input type="text" name="wcity" autocomplete="off" /></td></tr>
							<tr><td>State/Provence:</td><td><input type="text" name="wsubdivision" autocomplete="off" /></td></tr>
							<tr><td>Zip Code:</td><td><input type="number" name="wzip" autocomplete="off" /></td></tr>
							<tr><td>Country Code:</td><td><input type="text" name="wcountry" autocomplete="off" /></td></tr>
                            
                            <tr><td>&thinsp;</td><td>&thinsp;</td></tr>
                            
                            <tr><td><h3>Time Preferences</h3></td><td>&thinsp;</td></tr>
                            <tr><td>Visit start:</td><td><input type="time" name="visittimepreferencestart" autocomplete="off" /></td></tr>
                            <tr><td>Visit end:</td><td><input type="time" name="visittimepreferenceend" autocomplete="off" /></td></tr>
                            <tr><td>Call start:</td><td><input type="time" name="calltimepreferencestart" autocomplete="off" /></td></tr>
                            <tr><td>Call end:</td><td><input type="time" name="calltimepreferenceend" autocomplete="off" /></td></tr>
                            
                            <tr><td>&thinsp;</td><td>&thinsp;</td></tr>
                            
                            <tr><td><h3>About</h3></td><td>&thinsp;</td></tr>
                            <tr><td>Favorite book:</td><td><input type="text" name="favoritebook" autocomplete="off" /></td></tr>
                            <tr><td>Favorite food:</td><td><input type="text" name="favoritefood" autocomplete="off" /></td></tr>
                            <tr><td>Prefered Self-Awareness Method:</td><td><input type="text" name="selfawareness" autocomplete="off" /></td></tr>
                            </table>
                            <table>
                            <tr><td class="client_about">Goals:</td></tr>
                            <tr><td class="client_about"><textarea rows="4" cols="50" name="goals" autocomplete="off"></textarea></td></tr>
                            <tr><td class="client_about">Needs:</td></tr>
                            <tr><td class="client_about"><textarea rows="4" cols="50" name="needs" autocomplete="off"></textarea></td></tr>
                            
                            <tr><td class="client_about">&thinsp;</td></tr>
                            
                            <tr><td class="client_about"><input type="submit" value="Submit" class="button" /></td></tr>
                            <tr><td class="client_about"><input type="reset" value="Reset" class="button" /></td></tr>
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