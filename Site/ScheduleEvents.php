<?php
	include('includes/log.php');
	include('includes/session.php');
	include('includes/api.php');
	include('includes/protection.php');
	if (!$_SESSION['personid']) {
		header('Location: /Login');
	}

	if (isset($_GET['c'])) {
		$cid = decrypt($_GET['c']);
		o_log('Events Loaded','Coach ID: '.$cid);
		$schedule = viewSchedule(true);
		echo $schedule;
	} else {
		echo('*****ERROR! NO COACH ID*****');
	}

?>