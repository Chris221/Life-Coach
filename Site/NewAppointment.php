<?php
	//Loading Includes
	include('includes/session.php');
	include('includes/log.php');
	include('includes/api.php');
	include('includes/protection.php');
	if ($_SESSION['employeed']  ==  'f' || !$_SESSION['employeed']) {
		header('Location: /Login');
	}
	
	$back = backButton();

	if (isset($_GET['p'])) {
		$pid = decrypt($_GET['p']);
		o_log('Page Loaded','New Appointment Person ID: '.$pid);
		$new = true;
	} else if (isset($_GET['a'])) {
		$aid = $_GET['a'];
		o_log('Page Loaded','Edit Appointment ID: '.$aid);
	}
	if ($new) {
		$personResult = view('persons','personid='.$pid);

		$clientName = addStrTogether($personResult['prefix'],$personResult['first_name']);
		$clientName = addStrTogether($clientName,$personResult['middle_name']);
		$clientName = addStrTogether($clientName,$personResult['last_name']);
		$clientName = addStrTogether($clientName,$personResult['suffix']);

		$text = '<form action="#" method="post">
					<table>
					<tr><td>Start:*</td><td><input type="datetime-local" name="start" autocomplete="off" /></td></tr>
					<tr><td>End:*</td><td><input type="datetime-local" name="end" autocomplete="off" /></td></tr>
					<tr><td>Type:*</td><td><select name="type">
											  <option value="Phone">Phone</option>
											  <option value="In-Office">In-Office</option>
											  <option value="In-Home">In-Home</option>
											  <option value="Out">Out</option>
											</select></td></tr>
					<tr><td>Reason:*</td><td><textarea rows="6" cols="50" name="reason" autocomplete="off"></textarea></td></tr>
					<tr><td>Emergency:</td><td><input type="checkbox" name="emergency" /></td></tr>
					<tr><td>Address:</td><td><input type="text" name="line1" autocomplete="off" /></td></tr>
					<tr><td></td><td><input type="text" name="line2" autocomplete="off" /></td></tr>
					<tr><td>City:</td><td><input type="text" name="city" autocomplete="off" /></td></tr>
					<tr><td>State/Provence:</td><td><input type="text" name="subdivision" autocomplete="off" /></td></tr>
					<tr><td>Zip Code:</td><td><input type="number" name="zip" autocomplete="off" /></td></tr>
					<tr><td>Country Code:</td><td><input type="text" name="country" autocomplete="off" /></td></tr>
					</table><br />
					<input type="submit" value="Submit" class="button" /><br />
					<input type="reset" value="Reset" class="button" />
				 </table></form>';

		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$start = $_POST['start'];
			$end = $_POST['end'];
			$type = $_POST['type'];
			$reason = $_POST['reason'];
			$line1 = $_POST['line1'];
			$line2 = $_POST['line2'];
			$city = $_POST['city'];
			$subdivision = $_POST['subdivision'];
			$zip = $_POST['zip'];
			$country = $_POST['country'];

			if(isset($_POST['emergency'])) {
				$emergency = 'true';
			} else {
				$emergency = 'false';
			}

			if(!isset($start)) {
				$failed = 'There must be a starting time.<br />';
			} 

			$addressReturned = addAddress($line1,$line2,$city,$subdivision,$zip,$country);
			if ((strpos($addressReturned, 'blank') !== false) || $failed) {
				$text = $failed.$addressReturned.'<br />'.$text;
			} else {
				$visitReturned = addVisit($start,$type,$reason,$addressid,$emergency);
				if (strpos($visitReturned, 'blank') !== false) {
					$text = $visitReturned.'<br />'.$text;
				} else {
					$sid = addSchedule($start,$addressReturned,$pid,$visitReturned,$end);
					header('Location: /Appointments?a='.$sid);
				}
			}
		}
		$title = 'New Appointment - '.$clientName;
	} else {
		$result = view('appointments','scheduleid='.$aid);

		$clientName = addStrTogether($result['prefix'],$result['first_name']);
		$clientName = addStrTogether($clientName,$result['middle_name']);
		$clientName = addStrTogether($clientName,$result['last_name']);
		$clientName = addStrTogether($clientName,$result['suffix']);
		
		if ($result['emergency'] == 't') {
			$checked = ' checked';
		}
		$option = $result['type'];
		if ($option == 'Phone') {
			$phone = ' selected';
		} else if ($option == 'In-Office') {
			$office = ' selected';
		} else if ($option == 'In-Home') {
			$home = ' selected';
		} else if ($option == 'Out') {
			$out = ' selected';
		}
		$starta = date('Y-m-d', strtotime($result['time_start']));
		$startb = date('H:i', strtotime($result['time_start']));
		$start = ''.$starta.'T'.$startb;
		$enda = date('Y-m-d', strtotime($result['time_end']));
		$endb = date('H:i', strtotime($result['time_end']));
		$end = ''.$enda.'T'.$endb;
		$text = '<form action="#" method="post">
					<table>
					<tr><td>Start:*</td><td><input type="datetime-local" name="start" autocomplete="off" value="'.$start.'" /></td></tr>
					<tr><td>End:*</td><td><input type="datetime-local" name="end" autocomplete="off" value="'.$end.'" /></td></tr>
					<tr><td>Type:*</td><td><select name="type">
											  <option value="Phone"'.$phone.'>Phone</option>
											  <option value="In-Office"'.$office.'>In-Office</option>
											  <option value="In-Home"'.$home.'>In-Home</option>
											  <option value="Out"'.$out.'>Out</option>
											</select></td></tr>
					<tr><td>Reason:*</td><td><textarea rows="6" cols="50" name="reason" autocomplete="off">'.$result['reason'].'</textarea></td></tr>
					<tr><td>Emergency:</td><td><input type="checkbox" name="emergency" '.$checked.' /></td></tr>
					</table><br />
					<input type="submit" value="Submit" class="button" /><br />
					<input type="reset" value="Reset" class="button" />
				 </table></form>';
		
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$start = $_POST['start'];
			$end = $_POST['end'];
			$type = $_POST['type'];
			$reason = $_POST['reason'];

			if(isset($_POST['emergency'])) {
				$emergency = 'true';
			} else {
				$emergency = 'false';
			}

			if(!isset($start)) {
				$failed = 'There must be a starting time.<br />';
			}
			if(!isset($reason)) {
				$failed = 'Reason cannot be blank.<br />';
			}
			
			if ($failed){
				$text = $failed.'<br />'.$text;
			} else {
				changeSchedule($aid,$start,$type,$reason,$emergency,$end);
				header('Location: /Appointments?a='.$aid);
			}
		}
		$title = 'Edit Appointment - '.$clientName;
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
							<div class="row">
								<div class="col-sm-1 text-left">
									<a href="<?php echo($back); ?>" class="btn btn-primary">Back</a>
								</div>
								<div class="col-sm-10">
									<?php echo($title); ?>
								</div>
							</div>
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