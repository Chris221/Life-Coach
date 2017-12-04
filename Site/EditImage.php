<?php
	include('includes/log.php');
	include('includes/session.php');
	include('includes/uploadPhoto.php');
	include('includes/protection.php');
	include('includes/api.php');
	if ($_SESSION['employeed']  ==  'f') {
		header('Location: /Login');
	}
	
	//$back = backButton();

	if (isset($_GET['p'])) {
		$pid = decrypt($_GET['p']);
		o_log('Page Loaded','Edit Image person ID: '.$pid);
		$title = 'Edit Image';
		$back = '/Profile?p='.$_GET['p'];
	} else {
		header('Location: /Profile');
	}
	
	$text = '<form action="#" method="post" enctype="multipart/form-data">
				<table>
					<input type="hidden" name="MAX_FILE_SIZE" value="5120000">
					<tr><td><input name="image" type="file" accept="image/*"></td><td>&thinsp;</td></tr>
					<tr><td>The photo can not be any larger then 5MB.</td><td>&thinsp;</td></tr>
					<tr><td>The photo types supported are JPG, PNG, & GIF.</td><td>&thinsp;</td></tr>
					<tr><td>May take up to 5 minutes as the server processes the image.</td><td>&thinsp;</td></tr>
					<tr><td><input type="submit" value="Submit" class="button" /></td></tr>
				</table>
			</form>';
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$photoid = uploadImage();
		if (strpos($photoid, '<br />') !== false) {
			echo('FAILED!');
			$text = $photoid.'<br />'.$text;
		} else {
			$photoid = "'".$photoid."'";
			$photoid = convertEmptyToNull($photoid);
			include('includes/db.php');
			$sql = "UPDATE persons SET photoid=$photoid WHERE personid='$pid';";
			$result = pg_query($conn, $sql);
			pg_close($conn);
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('result: '.$result.'<br />');
				echo('photoid: '.$photoid.'<br />');
				echo('pid: '.$pid.'<br />');
				echo('Error: '.$error.'<br />');
			} else {
				header('Location: '.$back);
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
								<div class="col-sm-2 text-left">
									<a href="<?php echo($back); ?>" class="btn btn-primary">Back</a>
								</div>
								<div class="col-sm-8">
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