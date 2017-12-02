<?php
	include('includes/log.php');
	include('includes/session.php');
	include('includes/api.php');
	include('includes/protection.php');
	if (!$_SESSION['employeed']) {
		header('Location: /Login');
	}
	
	$back = '/Schedule';

	if (isset($_GET['a'])) {
		$appointmentID = $_GET['a'];
		o_log('Page Loaded','Appointment ID: '.$appointmentID);
		$delete = '/Appointments?a='.$appointmentID.'&d=yes';
		$edit = '/NewAppointment?a='.$appointmentID;
		if ($_GET['d'] == "yes") {
			if (markAsDeleted($appointmentID)) {
				header('Location: '.$back);
			} else {
				header('Location: /Appointments?a='.$appointmentID);
			}
		}
	} else {
		header('Location: '.$back);
	}

	$result = view('appointments','scheduleid='.$appointmentID);

	$clientName = addStrTogether($result['prefix'],$result['first_name']);
	$clientName = addStrTogether($clientName,$result['middle_name']);
	$clientName = addStrTogether($clientName,$result['last_name']);
	$clientName = addStrTogether($clientName,$result['suffix']);

	$start = readableDate($result['time_start']);
	$end = readableDate($result['time_end']);
	$scheduledby = $result['scheduledby'];
	$type = $result['type'];
	$reason = $result['reason'];
	$addressid = $result['addressid'];
	$personid = $result['personid'];
	$coachid = $result['coachid'];
	$emergency = $result['emergency'];
	$scheduleid = $result['scheduleid'];
	
	$coachResult = view('accounts','coachid='.$coachid);
	$coachName = addStrTogether($coachResult['prefix'],$coachResult['first_name']);
	$coachName = addStrTogether($coachName,$coachResult['middle_name']);
	$coachName = addStrTogether($coachName,$coachResult['last_name']);
	$coachName = addStrTogether($coachName,$coachResult['suffix']);

	$address = getAddress($addressid);
	$addressOutput = '<tr><td>Address:</td><td>'.$address.'</td></tr>';

	if ($emergency == 't') {
		$emergency = 'Yes';
	} else {
		$emergency = 'No';
	}

	$text = '<table>
                <tr><td>Name:</td><td><a href="/Profile?p='.encrypt($personid).'">'.$clientName.'</a></td></tr>
                <tr><td>Starts at:</td><td>'.$start.'</td></tr>
                <tr><td>Ends at:</td><td>'.$end.'</td></tr>
				
                <tr><td>Emergency:</td><td>'.$emergency.'</td></tr>
                <tr><td>Type:</td><td>'.$type.'</td></tr>
                <tr><td>Reason:</td><td>'.$reason.'</td></tr>
                <tr><td>Assigned to:</td><td><a href="/Profile?p='.encrypt($coachid).'">'.$coachName.'</a></td></tr>
				'.$addressOutput.'
             </table>';


	$title = 'Appointment';
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
                    <li class="nav-item active">
                        <a class="nav-link" href="/Schedule">Schedule<span class="sr-only">(current)</span></a>
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
							echo('<li class="nav-item">
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
                <div class="row">
                    <div class="col-md-2">
                        <a href="<?php echo($back); ?>" class="btn btn-primary">Back</a>
                        <br />
                        <br />
                    </div>
                </div>
            <div class ="row">
                <div class="col-sm-12">
                    <div class="card text-center page-margin0 left right">
                        <div class="card-header title">
                            <?php echo($title); ?>
                            <a href="<?php echo($edit); ?>" class="btn btn-primary">Edit</a>
                            <a href="<?php echo($delete); ?>" class="btn btn-primary">Delete</a>
                        </div>
                        <div class="card-body">
                        	<span class="marginAuto inline-block"><?php echo($text); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>