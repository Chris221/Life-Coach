<?php
	include('includes/log.php');
	include('includes/session.php');
	include('includes/api.php');
	include('includes/protection.php');
	if (!$_SESSION['personid']) {
		header('Location: /Login');
	}
	
	$back = backButton();

	if (isset($_GET['a'])) {
		$addressid = decrypt($_GET['a']);
		o_log('Page Loaded','Edit Address ID: '.$addressid);
	} else {
		header('Location: /');
	}

	$result = view('addresses','addressid='.$addressid);

	$line1 = $result['adressline1'];
	$line2 = $result['adressline2'];
	$city = $result['city'];
	$subdivision = $result['subdivision'];
	$zip = $result['zip'];
	$country = $result['country'];
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$line1 = $_POST['line1'];
		$line2 = $_POST['line2'];
		$city = $_POST['city'];
		$subdivision = $_POST['subdivision'];
		$zip = $_POST['zip'];
		$country = $_POST['country'];
		$back = $_POST['back'];

		if (!(strlen($line1) > 1)) {
			$failed = 'Address Line 1 cannont be blank.<br />';
		}
		if (!(strlen($city) >= 1)) {
			$failed = 'City cannont be blank.<br />';
		} 
		if (!(strlen($subdivision) >= 1)) {
			$failed .= 'State/Provence cannont be blank.<br />';
		} 
		if (!(strlen($zip) >= 1)) {
			$failed .= 'Zip Code cannont be blank.<br />';
		} 
		if (!(strlen($country) >= 1)) {
			$failed .= 'Country Code cannont be blank.<br />';
		} 

		if ($failed){
			$text = $failed.'<br />'.$text;
		} else {
			changeAddress($addressid,$line1,$line2,$city,$subdivision,$zip,$country,true);
			header('Location: '.$back);
		}
	}

	$text = '<form action="#" method="post"><table>
				<input type="hidden" name="back" value="'.$back.'" hidden />
                <tr><td>Address:</td><td><input type="text" name="line1" autocomplete="off" value="'.$line1.'" /></td></tr>
				<tr><td></td><td><input type="text" name="line2" autocomplete="off" value="'.$line2.'" /></td></tr>
				<tr><td>City:</td><td><input type="text" name="city" autocomplete="off" value="'.$city.'" /></td></tr>
				<tr><td>State/Provence:</td><td><input type="text" name="subdivision" autocomplete="off" value="'.$subdivision.'" /></td></tr>
				<tr><td>Zip Code:</td><td><input type="number" name="zip" autocomplete="off" value="'.$zip.'" /></td></tr>
				<tr><td>Country Code:</td><td><input type="text" name="country" autocomplete="off" value="'.$country.'" /></td></tr>
				</table><br />
				<input type="submit" value="Submit" class="button" /><br />
				<input type="reset" value="Reset" class="button" />
			 </table></form>';


	$title = 'Edit Address';
?>
<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <!-- For Mobile scaling -->
        <meta name="viewport" content="width=device-width, user-scalable=no" />
        <meta name="HandheldFriendly" content="true">
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
