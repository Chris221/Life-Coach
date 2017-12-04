<?php
	//Loading Includes
	include('includes/log.php');
	include('includes/session.php');
	include('includes/api.php');
	include('includes/protection.php');
	if ($_SESSION['admin'] == 'false') {
		header('Location: /');
	}
	
	$back = backButton();

	if (isset($_GET['c'])) {
		$companyid = decrypt($_GET['c']);
		o_log('Page Loaded','Company ID: '.$companyid);
	} else {
		header('Location: /');
	}

	$result = view('companies','companyid='.$companyid);

	$name = $result['name'];
	$admin_personid = $result['admin_personid'];
	$admin_name = getPersonName($admin_personid);
	$location = $result['location'];
	$domain = $result['domain'];
	$disabled = $result['deleted'];

	if ($disabled == 'f') {
		$disabled = 'No';
	} else {
		$disabled = 'Yes';
	}

	$title = 'Manage Company '.$name;

	$text = '<table>
				<tr><td>Company Name:</td><td>&thinsp;</td><td>'.$name.'</td></tr>
				<tr><td>Admin:</td><td>&thinsp;</td><td><a href="/Profile?p='.encrypt($admin_personid).'">'.$admin_name.'</a></td></tr>
				<tr><td>Location:</td><td>&thinsp;</td><td>'.$location.'</td></tr>
				<tr><td>Website:</td><td>&thinsp;</td><td><a href="http://'.$domain.'">'.$domain.'</a></td></tr>
				<tr><td>Disabled:</td><td>&thinsp;</td><td>'.$disabled.'</td></tr>
			</table>';
	$edit = '/EditCompany?c='.$_GET['c'];
	$editDelete = '<a href="'.$edit.'" class="btn btn-primary">Edit</a>';

	if ($_SESSION['super_admin'] == 'true') {
		$delete = '/EditCompany?c='.$_GET['c'].'&d=yes';
		$restore = '/EditCompany?c='.$_GET['c'].'&r=yes';
		
		if ($companyid == $_SESSION['companyid']) {
			$disabledDelete = ' disabled';
		}
		if ($disabled == 'Yes') {
			$editDelete .= '&thinsp;<a href="'.$restore.'" class="btn btn-primary">Restore</a>';
		} else {
			$editDelete .= '&thinsp;<a href="'.$delete.'" class="btn btn-primary'.$disabledDelete.'">Disable</a>';
		}
		
		if ($_GET['s']) {
			$search = $_GET['s'];
			$companyList = viewCompanies('search',$search);
		} else {
			$companyList = viewCompanies();
		}
		$fullPage = '<div class = "row">
					<div class="col-sm-12">
						<div class="card text-center page-margin0 left">
							<div class="card-header title">
								<div class = "row">
									<div class = "col-sm-2 text-left">
										<a href="/Company?c='.$_GET['c'].'" class="btn btn-primary">All Companies</a>
									</div>
									<div class = "col-sm-7">
										<form class="form-inline my-2 my-lg-0" method="get" action="#">
											<input type="hidden" value="'.$_GET['c'].'" name="c">
											<input class="form-control mr-sm-2" type="search" placeholder="Search all companies" name="s">
											<button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
										</form>
									</div>
									<div class = "col-sm-3 text-right">
										<a href="/NewCompany" class="btn btn-primary">Add a New Company</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="card text-center page-margin0 left">
							<div class="card-header title"> Company List </div>
							<div class="card-body scrollBox">'.$companyList.'</div>
						</div>
					</div>
				</div>';
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
						 if ($_SESSION['admin'] == 'true') {
							echo('<li class="nav-item active">
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
                        	<div class="row">
								<div class="col-sm-3 text-left">
									<!--<a href="<?php echo($back); ?>" class="btn btn-primary">Back</a>-->
								</div>
								<div class="col-sm-6">
									<?php echo($title); ?>
								</div>
								<div class="col-sm-3 text-right">
									<?php echo($editDelete); ?>
								</div>
                            </div>
                        </div>
                        <div class="card-body">
                        	<span class="marginAuto inline-block"><?php echo($text); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo($fullPage); ?>
        </div>
    </body>
</html>