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
<title><?php echo($title); ?></title>
</head>
<body>
<?php
	echo("Hello, ".$_SESSION['first_name'].' '.$_SESSION['last_name'].'!');
?>
</body>
</html>