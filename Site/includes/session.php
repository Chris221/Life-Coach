<?php
	//date_default_timezone_set('America/New_York');
	session_start();
	if (isset($_COOKIE['Login']) && !isset($_SESSION["personid"])) {
		include('includes/db.php');
		include('includes/protection.php');
		$personid = (int) decrypt($_COOKIE['Login']);
		$sql = "SELECT * FROM accounts WHERE personid='$personid';";
		$result = pg_query($conn, $sql);
		$data = pg_fetch_assoc($result);
		
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
		
		$companyid = $_SESSION['companyid'];
		
		$sql = "SELECT * FROM companies WHERE companyid='$companyid';";
		$result = pg_query($conn, $sql);
		$data = pg_fetch_assoc($result);
		
		if (($data['admin_personid'] == $_SESSION['personid']) && ($_SESSION['personid'] >= '1')) {
			$_SESSION['admin'] = 'true';
		} else {
			$_SESSION['admin'] = 'false';
		}
				
		if ($_SESSION['personid'] == '1') {
			$_SESSION['super_admin'] = 'true';
		} else {
			$_SESSION['super_admin'] = 'false';
		}
		
		if ($data['deleted'] != 'f') {
			session_unset();
			session_destroy();
			if (isset($_COOKIE['Login'])) {
				unset($_COOKIE['Login']);
				setcookie('Login', null, -1, '/');
			}
			header('Location: /Login');
		}
		
		pg_close($conn);
		include('includes/log.php');
		o_log('Logged in', 'From the cookie');
	}
	if ($_SESSION['employeed']) {
		include('includes/db.php');
		$date = date("Y-m-d H:i:s");
		$personid = $_SESSION['personid'];
		pg_query($conn, "UPDATE coaches SET last_active='$date' WHERE personid='$personid'");
		pg_close($conn);
	} else {
		//NOT ALLOWED IN
	}
?>