<?php
	include('includes/log.php');
	include('includes/session.php');
	include('includes/api.php');
	include('includes/protection.php');
	if ($_SESSION['super_admin'] == 'false') {
		header('Location: /Company');
	}
	
	$back = backButton();

	if (isset($_GET['c']) && ($_GET['d'] == 'yes2')) {
		$companyid = decrypt($_GET['c']);
		o_log('Page Loaded','Disabled Company ID: '.$companyid);
		markCompanyAsDeleted($companyid);
		header('Location: /Company?c='.$_GET['c']);
	} else if (isset($_GET['c']) && ($_GET['r'] == 'yes')) {
		$companyid = decrypt($_GET['c']);
		o_log('Page Loaded','Restore Company ID: '.$companyid);
		markCompanyAsNOTDeleted($companyid);
		header('Location: /Company?c='.$_GET['c']);
	} else if (isset($_GET['c']) && ($_GET['d'] == 'yes')) {
		$companyid = decrypt($_GET['c']);
		o_log('Page Loaded','Delete confirm Company ID: '.$companyid);
	} else if (isset($_GET['c'])) {
		$companyid = decrypt($_GET['c']);
		o_log('Page Loaded','Edit Company ID: '.$companyid);
	} else {
		header('Location: '.$back);
	}

	$result = view('companies','companyid='.$companyid);

	$name = $result['name'];
	$location = $result['location'];
	$site = $result['domain'];
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$name = $_POST['name'];
		$location = $_POST['location'];
		$site = $_POST['site'];
		$back = $_POST['back'];

		changeCompany($companyid,$name,$location,$site);
		header('Location: '.$back);
	}

	$text = '<form action="#" method="post"><table>
				<input type="hidden" name="back" value="'.$back.'" hidden />
                <tr><td>Company Name:</td><td>&thinsp;</td><td><input type="text" name="name" autocomplete="off" value="'.$name.'" /></td></tr>
				<tr><td>Company Location:</td><td>&thinsp;</td><td><input type="text" name="location" autocomplete="off" value="'.$location.'" /></td></tr>
				<tr><td>Company Website:</td><td>&thinsp;</td><td><input type="text" name="site" autocomplete="off" value="'.$site.'" /></td></tr></table>
				<input type="submit" value="Submit" class="button" /><br />
				<input type="reset" value="Reset" class="button" />
			 </form>';

	$title = 'Edit Company '.$name;
	if ($_GET['d'] == 'yes') {
		$yes = '/EditCompany/?c='.encrypt($companyid).'&d=yes2';
		$text = 'Are you sure you want to disable the company "'.$name.'"?<br /><br />
			<a href="'.$back.'" class="btn btn-primary">No</a>&emsp;&emsp;&emsp;
			<a href="'.$yes.'" class="btn btn-primary">Yes</a>';
		
		$title = 'Disable Company '.$name;
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
								<div class="col-sm-3 text-left">
									<a href="<?php echo($back); ?>" class="btn btn-primary">Back</a>
								</div>
								<div class="col-sm-6">
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