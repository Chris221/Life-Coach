<?php
	include('includes/log.php');
	include('includes/session.php');
	include('includes/api.php');
	include('includes/protection.php');
	if (!$_SESSION['employeed']) {
		header('Location: /Login');
	}
	$title = 'Profile';

	if (isset($_GET['p'])) {
		$pid = decrypt($_GET['p']);
		o_log('Page Loaded','Profile ID: '.$pid);
		$pTitle =  'Client Photo';
		$iTitle = 'Client Info';
		$proTitle = 'Client Profile';
		$nTitle = "Client Notes";
		$notelink = '/Notes/?p='.encrypt($pid);
		$eTitle = "Client's Life Events";
		$eventlink = '/Events/?p='.encrypt($pid);
	} else {
		$own = true;
		$pid = $_SESSION['personid'];
		o_log('Page Loaded','Own Profile');
		$pTitle =  'Your Photo';
		$iTitle = 'Your Info';
		$proTitle = 'Your Profile';
		$nTitle = "Your Notes";
		$notelink = '/Notes';
		$eTitle = "Your Life Events";
		$eventlink = '/Events';
	}
	$changephoto = '/EditImage/?p='.encrypt($pid);
	$edit = '/EditProfile/?p='.encrypt($pid);
	$deleteProfile = '/DeleteProfile/?p='.encrypt($pid);
	$relationshipslink = '/Relationships/?p='.encrypt($pid);

	$newAppointment = '/NewAppointment?p='.encrypt($pid);

	$personResult = view('persons','personid='.$pid);
	$clientResult = view('clients','personid='.$pid);
	$coachResult  = view('coaches','personid='.$pid);

	$name = addStrTogether($personResult['prefix'],$personResult['first_name']);
	$name = addStrTogether($name,$personResult['middle_name']);
	$name = addStrTogether($name,$personResult['last_name']);
	$name = addStrTogether($name,$personResult['suffix']);

	$email = $personResult['email'];
	$cell = $personResult['cell'];
	$home = $personResult['home'];
	$work = $personResult['work'];
	$extension = $personResult['extension'];
	$workNumber = addExtToNumber($work,$extension);
	$workNumberToDisplay = addExtToNumberWithEXT($work,$extension);
	$homeAddress = getAddress($personResult['addressid']);
	$dob = date('m/d/Y', strtotime($personResult['date_of_birth']));
	$photoID = $personResult['photoid'];
	$companyID = $personResult['companyid'];

	$workCompany = $clientResult['work_company'];
	$clientid = $clientResult['clientid'];
	$lastContacted = mostRecentTimeContacted($clientid);
	$cid = $clientResult['coachid'];
	$workAddress = getAddress($clientResult['work_address']);
	$workTitle = $clientResult['work_title'];
	$workField = $clientResult['work_field'];
	$favoriteBook = $clientResult['favorite_book'];
	$favoriteFood = $clientResult['favorite_food'];
	$selfawareness = $clientResult['selfawareness'];
	$visit_time_preference_start = date('g:i A', strtotime($clientResult['visit_time_preference_start']));
	$visit_time_preference_end = date('g:i A', strtotime($clientResult['visit_time_preference_end']));
	$call_time_preference_start = date('g:i A', strtotime($clientResult['call_time_preference_start']));
	$call_time_preference_end = date('g:i A', strtotime($clientResult['call_time_preference_end']));
	$goals = $clientResult['goals'];
	$needs = $clientResult['needs'];
	$gender = $personResult['gender'];
	if ($personResult['deceased'] != 'f') {
		$deceased = 'Yes';
	} else  {
		$deceased = 'No';
	}
	//echo('pid: '.$pid.'<br />');
	//echo('cid: '.$clientid.'<br />');
	if ($photoID) {
		$imagelink = '\includes\viewPhoto?a='.encrypt($photoID);
		$ptext = '<a href="'.$imagelink.'"><img src="'.$imagelink.'" width="250" height="250" alt="Profile Photo" /></a>';
	} else {
		$ptext = 'No Image Currently';
	}

	if ($coachResult['coachid']) {
		if ($coachResult['supervisor'] != 'f') {
			$supervisor = 'Yes';
		} else  {
			$supervisor = 'No';
		}
		if ($coachResult['employeed'] != 'f') {
			$employeed = 'Yes';
		} else  {
			$employeed = 'No';
		}
		if (isset($coachResult['last_active'])) {
			$lastActive = readableDate($coachResult['last_active']);
		} else {
			$lastActive = 'Never';
		}
		
		if ($_SESSION['supervisor']) {
			$resetpass = '<br /><a href="/ForgotPassword?p='.encrypt($pid).'" class="btn btn-primary">Reset</a>';
		}
		if ($own) {
			$resetpass = '<br /><a href="/ForgotPassword?p='.encrypt($pid).'" class="btn btn-primary">Change</a>';
		}
		
		$coachText = '
		<tr><td><h3>Coach Information</h3></td></tr>
		<tr><td>Supervisor:</td><td>'.$supervisor.'</td></tr>
		<tr><td>Employed:</td><td>'.$employeed.'</td></tr>
		<tr><td>Last Active on:</td><td>'.$lastActive.'</td></tr>
		<tr><td>Password:</td><td>Encrypted'.$resetpass.'</td></tr>
		<tr><td>&thinsp;</td><td>&thinsp;</td></tr>
		';
	} else {
		$myCoachResult  = view('accounts','coachid='.$cid);
		$cName = addStrTogether($myCoachResult['prefix'],$myCoachResult['first_name']);
		$cName = addStrTogether($cName,$myCoachResult['middle_name']);
		$cName = addStrTogether($cName,$myCoachResult['last_name']);
		$cName = addStrTogether($cName,$myCoachResult['suffix']);
		
		$eCoachPID = encrypt($myCoachResult['personid']);
		
		$cName = '<a href="/Profile/?p='.$eCoachPID.'">'.$cName.'</a>';
		$cChange = '<a href="/ChangeCoach/?p='.encrypt($pid).'" class="btn btn-primary">Change Coach</a>';
		
		$coachText = '
		<tr><td><h3>Coach Information</h3></td></tr>
		<tr><td>Coach:</td><td>'.$cName.'</td></tr>
		<tr><td>&thinsp;</td><td>'.$cChange.'</td></tr>
		<tr><td>Last Contacted:</td><td>'.$lastContacted.'</td></tr>
		<tr><td>&thinsp;</td><td>&thinsp;</td></tr>
		';
	}


	$itext = '
		<table>
		<tr><td><h3>Personal Information</h3></td></tr>
		<tr><td>Name:</td><td>'.$name.'</td></tr>
		<tr><td>Email:</td><td><a href="mailto:'.$email.'" target="_blank">'.$email.'</a></td></tr>
		<tr><td>Cell Phone:</td><td><a href="tel:+:'.$cell.'" target="_blank">'.$cell.'</a></td></tr>
		<tr><td>Home Phone:</td><td><a href="tel:+:'.$home.'" target="_blank">'.$home.'</a></td></tr>
		<tr><td>Date of Birth:</td><td>'.$dob.'</td></tr>
		<tr><td>Gender:</td><td>'.$gender.'</td></tr>
		<tr><td>Deceased:</td><td>'.$deceased.'</td></tr>
		<tr><td>Home Address:</td><td>'.$homeAddress.'</td></tr>
		<tr><td>&thinsp;</td><td>&thinsp;</td></tr>
		
		'.$coachText.'
		
		<tr><td><h3>Work Information</h3></td></tr>
		<tr><td>Place of Employment:</td><td>'.$workCompany.'</td></tr>
		<tr><td>Title of Job:</td><td>'.$workTitle.'</td></tr>
		<tr><td>Field of Employment:</td><td>'.$workField.'</td></tr>
		<tr><td>Work Phone:</td><td><a href="tel:+:'.$workNumber.'" target="_blank">'.$workNumberToDisplay.'</a></td></tr>
		<tr><td>Work Address:</td><td>'.$workAddress.'</td></tr>
		<tr><td>&thinsp;</td><td>&thinsp;</td></tr>
		
		<tr><td><h3>Preferences</h3></td></tr>
		<tr><td>Visit Preference:</td><td>'.$visit_time_preference_start.' - '.$visit_time_preference_end.'</td></tr>
		<tr><td>Call Preference:</td><td>'.$call_time_preference_start.' - '.$call_time_preference_end.'</td></tr>
		<tr><td>&thinsp;</td><td>&thinsp;</td></tr>
		
		<tr><td><h3>About</h3></td></tr>
		<tr><td>Favorite Book:</td><td>'.$favoriteBook.'</td></tr>
		<tr><td>Favorite Food:</td><td>'.$favoriteFood.'</td></tr>
		<tr><td>Preffered Self-Awareness Method:</td><td>'.$selfawareness.'</td></tr>
		<tr><td>Goals:</td><td>'.$goals.'</td></tr>
		<tr><td>Needs:</td><td>'.$needs.'</td></tr>
		</table>
		';

	$notes = viewNote($clientid);
	$events = viewEvent($clientid);

	if ($companyID <> $_SESSION['companyid']) {
		//echo('CompanyID: '.$companyID.'<br />');
		//echo('Session CompanyID: '.$_SESSION['companyid'].'<br />');
		$itext = 'This client is not apart of your company';
		$ptext = $itext;
		$notes = $itext;
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
						if ($_SESSION['supervisor']) {
							echo('<li class="nav-item right-marigin50p">
								<a class="nav-link" href="/NewCoach">Add New Coach</a>
							</li>');
						}
					?>
                    <li class="nav-item active">
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
        </nav>

        <br />

        <div class="container">
            <div class ="row">
                <div class="col-sm-12">
                    <div class="card text-center page-margin0 left right">
                        <div class="card-header title">
                            <?php echo($proTitle); ?>
							<a href="<?php echo($newAppointment); ?>" class="btn btn-primary">New Appointment</a>
							<a href="<?php echo($deleteProfile); ?>" class="btn btn-primary">Delete Person</a>
                        </div>
                    </div>
                </div>
            </div><!--
            <div class = "row">
                <div class="col-sm-4 text-center">
                    <a href="/NewClient" class="btn btn-primary">Add New Coach</a>
                </div>
            </div>-->
            <br />
            <div class = "row">
                <div class="col-sm-4">
                    <div class="card text-center page-margin5 left">
                        <div class="card-header title"><?php echo($pTitle); ?>
							<a href="<?php echo($changephoto); ?>" class="btn btn-primary">Change</a></div>
                             <div class="card-body">
                                 <?php echo($ptext); ?>
                             </div>
                    </div>
                    <div class="card text-center page-margin5 left">
                        <div class="card-header title">Family Tree
							<a href="<?php echo($relationshipslink); ?>" class="btn btn-primary">Details</a>
                       	</div>
                        <div class="card-body">
                            <?php include('includes/treeBuilder.php'); buildTree($pid); ?>
                        </div>
                    </div>
                </div>

                <div class="col-sm-8">
                    <div class="card text-center page-margin5 right">
                        <div class="card-header title"><?php echo($iTitle); ?>
							<a href="<?php echo($edit); ?>" class="btn btn-primary">Edit</a></div>
                        <div class="card-body">
                        	<span class="marginAuto inline-block"><?php echo($itext); ?></span>
                        </div>
                    </div>
                </div>
            </div>
			<div class="row">
				<div class="col-sm-6">
				<div class="card text-center page-margin5 left">
					<div class="card-header title">
						<?php echo($eTitle); ?>
						<a href="<?php echo($eventlink); ?>" class="btn btn-primary">New Life Events</a>
					</div>
					<div class="card-body">
						<span class="marginAuto inline-block"><?php echo($events); ?></span>
					</div>
				</div>
				</div>
				<div class="col-sm-6">
				<div class="card text-center page-margin5 right">
					<div class="card-header title">
						<?php echo($nTitle); ?>
						<a href="<?php echo($notelink); ?>" class="btn btn-primary">Add a Note</a>
					</div>
					<div class="card-body">
						<span class="marginAuto inline-block"><?php echo($notes); ?></span>
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