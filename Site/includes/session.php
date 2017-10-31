<?php
	session_start();
	if (isset($_COOKIE['Login']) && !isset($_SESSION["personid"])) {
		include('includes/db.php');
		include('includes/protection.php');
		$personid = (int) decrypt($_COOKIE['Login']);
		$sql = "SELECT * FROM accounts WHERE personid='$personid'";
		$data = pg_query($conn, $sql);
		$data = pg_fetch_assoc($data);
		
		$_SESSION['personid'] = $data['personid'];
		$_SESSION['coachid'] = $data['coachid'];
		$_SESSION['email'] = $data['email'];
		$_SESSION['prefix'] = $data['prefix'];
		$_SESSION['first_name'] = $data['first_name'];
		$_SESSION['last_name'] = $data['last_name'];
		$_SESSION['suffix'] = $data['suffix'];
		$_SESSION['companyid'] = $data['companyid'];
		$_SESSION['supervisor'] = $data['supervisor'];
		$_SESSION['clientid'] = $data['clientid'];
		$_SESSION['employeed'] = $data['employeed'];
		pg_close($conn);
	}
	if ($_SESSION['employeed']) {
		include('includes/db.php');
		$date = date("Y-m-d H:i:s");
		pg_query($conn, "UPDATE coaches SET last_active='$date' WHERE personid='$personid'");
		pg_close($conn);
	} else {
		//NOT ALLOWED IN
	}
?>