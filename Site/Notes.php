<?php
	include('includes/log.php');
	include('includes/session.php');
	include('includes/api.php');
	include('includes/protection.php');
	if ($_SESSION['employeed']  ==  'f') {
		header('Location: /Login');
	}
	
	//$back = backButton();
	if (isset($_GET['n'])) {
		$nid = $_GET['n'];
		o_log('Page Loaded','Delete Notes ID: '.$nid);
		markNoteAsDeleted($nid);
		$back = backButton();
		header('Location: '.$back);
	}

	if (isset($_GET['p'])) {
		$pid = decrypt($_GET['p']);
		o_log('Page Loaded','Notes Person ID: '.$pid);
		$tTitle = "Client's Notes";
		$back = '/Profile?p='.$_GET['p'];
	} else {
		$pid = $_SESSION['personid'];
		o_log('Page Loaded','Own Notes');
		$tTitle = 'Your Notes';
		$back = '/Profile';
	}

	$personResult = view('persons','personid='.$pid);
	$clientResult = view('clients','personid='.$pid);

	$clientName = addStrTogether($personResult['prefix'],$personResult['first_name']);
	$clientName = addStrTogether($clientName,$personResult['middle_name']);
	$clientName = addStrTogether($clientName,$personResult['last_name']);
	$clientName = addStrTogether($clientName,$personResult['suffix']);

	$companyID = $personResult['companyid'];

	$clientid = $clientResult['clientid'];
	$coachid = $clientResult['coachid'];

	$text = '<form action="#" method="post">
				<textarea rows="6" cols="50" name="note" autocomplete="off"></textarea><br /><br />
				<input type="submit" value="Submit" class="button" /><br />
				<input type="reset" value="Reset" class="button" />
             </form></table>';

	if (($companyID <> $_SESSION['companyid']) && ($_SESSION['super_admin'] == 'false')) {
		echo('CompanyID: '.$companyID.'<br />');
		echo('Session CompanyID: '.$_SESSION['companyid'].'<br />');
		$itext = 'This client is not apart of your company';
		$ptext = $itext;
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$postedNote = $_POST['note'];
		$photoid;
		if ($postedNote) {
			addNote($_GET['p'],$postedNote,$clientid,$coachid,$photoid,$visitID);
		} else {
			$error = 'The note cannot be blank.<br /><br />';
			$text = $error.$text;
		}
	}

	$notes = viewNote($clientid);

	$title = 'Notes - '.$clientName;
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
                <div class="col-sm-6">
                    <div class="card text-center page-margin0 left right">
                        <div class="card-header title">
							<div class="row">
								<div class="col-sm-2">
									<a href="<?php echo($back); ?>" class="btn btn-primary">Back</a>
								</div>
								<div class="col-sm-8">
									<?php echo($title); ?>
								</div>
							</div>
                        </div>
                        <div class="card-body">
                        	<span class="marginAuto inline-block"><?php echo($notes); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card text-center page-margin0 left right">
                    	<div class="card-header title">
                            Add Note
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