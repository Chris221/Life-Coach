<?php
	include('includes/session.php');
	if (!$_SESSION['personid']) {
		header('Location: /Login');
	}	
	$title = 'Home';
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<!-- For Mobile scaling -->
<meta name="viewport" content="width=device-width, user-scalable=no" />
<meta name="HandheldFriendly" content="true">
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="/bootstrap-3.3.7/css/bootstrap.min.css">
<!-- jQuery library -->
<script src="/js/jquery/jquery-3.2.1.min.js"></script>
<!-- Latest compiled JavaScript -->
<script src="/bootstrap-3.3.7/js/bootstrap.min.js"></script>
<!-- Our CSS -->
<link rel="stylesheet" href="/css/life-coach.css">
<title><?php echo($title); ?></title>
</head>
<body>
<div id="header">
    <table id="headRow">
        <ul> </ul>
    </table>
</div>
<?php
	echo("Hello, ".$_SESSION['first_name'].' '.$_SESSION['last_name'].'!');
?>
<br />
<br />
<br />
<br />
<a href="/NewCoach" >Add a New Coach</a>
<br />
<br />
<a href="/Logout" >Logout</a>
</body>
</html>