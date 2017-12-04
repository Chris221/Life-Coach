<?php
	include('includes/log.php');
	include('includes/session.php');
	include('includes/api.php');
	include('includes/protection.php');
	if ($_SESSION['employeed']  ==  'f' || !$_SESSION['employeed']) {
		header('Location: /Login');
	}
	
	if (isset($_GET['p'])) {
		$pid = decrypt($_GET['p']);
		o_log('Page Loaded','Changing Coach Person ID: '.$pid.', Type: '.$relationshipType);
		$back = '/Profile/?p='.$_GET['p'];
		$currentLink = '/ChangeCoach/?p='.$_GET['p'];
		if (isset($_GET['n'])) {
			//ADDS THE RELATIONSHIP
			$newCoachID = decrypt($_GET['n']);
			$newRelationshipID = changeCoach($pid,$newCoachID);
			header('Location: '.$back);
		}
		$topBar = '<div class = "row">
					<div class="col-sm-12">
						<div class="card text-center page-margin0 left">
							<div class="card-header title">
								<div class = "row">
									<div class = "col-sm-3 text-left">
										<a href="'.$back.'" class="btn btn-primary">Back</a>
										<a href="'.$currentLink.'" class="btn btn-primary">All Coaches</a>
									</div>
									<div class = "col-sm-7">
										<form class="form-inline my-2 my-lg-0" method="get" action="#">
											<input type="hidden" value="'.$_GET['p'].'" name="p">
											<input class="form-control mr-sm-2" type="search" placeholder="Search all coaches" name="s">
											<button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
										</form>
									</div>';
		if ($_SESSION['supervisor'] == 't') {
			$topBar .= '<div class = "col-sm-2 text-right">
				<a href="/NewCoach" class="btn btn-primary">Add a New Coach</a>
			</div>';
		}
        $topBar .= '</div>
                        </div>
                    </div>
                </div>
            </div>';
		if (isset($_GET['s'])) {
			$search = $_GET['s'];
			$peopleList = viewCoachesForChange($currentLink,'search',$search);
		} else {
			$peopleList = viewCoachesForChange($currentLink);
		}
	} else {
		header('Location: /Profile');
	}

	$personResult = view('persons','personid='.$pid);

	$name = addStrTogether($personResult['prefix'],$personResult['first_name']);
	$name = addStrTogether($name,$personResult['middle_name']);
	$name = addStrTogether($name,$personResult['last_name']);
	$name = addStrTogether($name,$personResult['suffix']);

	$title = 'Change Coach - '.$name;
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
            <?php echo($topBar); ?>
            <div class ="row">
                <div class="col-sm-12">
                    <div class="card text-center page-margin0 left right">
                        <div class="card-header title">
                            <?php echo($title); ?>
                        </div>
                        <div class="card-body scrollBox">
                        	<?php echo($peopleList); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>