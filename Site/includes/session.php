<?php
	//date_default_timezone_set('America/New_York');
	session_start();
	if (isset($_COOKIE['Login']) && !isset($_SESSION['personid'])) {
		//sets all session values from cookie
		//Loading Includes
		include('includes/db.php');
		include('includes/protection.php');
		//gets personid from cookie
		$personid = (int) decrypt($_COOKIE['Login']);
		$sql = "SELECT * FROM accounts WHERE personid='$personid';";
		$result = pg_query($conn, $sql);
		$data = pg_fetch_assoc($result);
		
		//sets session values
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
		
		//gets companyid
		$companyid = $_SESSION['companyid'];
		
		//gets all company information
		$sql = "SELECT * FROM companies WHERE companyid='$companyid';";
		$result = pg_query($conn, $sql);
		$data = pg_fetch_assoc($result);
		
		//sets if an admin
		if (($data['admin_personid'] == $_SESSION['personid']) && ($_SESSION['personid'] >= '1')) {
			$_SESSION['admin'] = 'true';
		} else {
			$_SESSION['admin'] = 'false';
		}
		
		//sets of a super admin
		if ($_SESSION['personid'] == '1') {
			$_SESSION['super_admin'] = 'true';
		} else {
			$_SESSION['super_admin'] = 'false';
		}
		
		//if deleted remove session
		if ($data['deleted'] == 't') {
			session_unset();
			session_destroy();
			if (isset($_COOKIE['Login'])) {
				unset($_COOKIE['Login']);
				setcookie('Login', null, -1, '/');
			}
			//redirect to log in
			header('Location: /Login');
		}
		
		pg_close($conn);
		include('includes/log.php');
		//logs the log in
		o_log('Logged in', 'From the cookie');
	} else if (isset($_SESSION['personid'])) {
		//sets all session values
		//Loading Includes
		include('includes/db.php');
		//gets personid
		$personid = $_SESSION['personid'];
		//gets all account information
		$sql = "SELECT * FROM accounts WHERE personid='$personid';";
		$result = pg_query($conn, $sql);
		$data = pg_fetch_assoc($result);
		
		//sets session values
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
		
		//gets companyid
		$companyid = $_SESSION['companyid'];
		
		//gets all company information
		$sql = "SELECT * FROM companies WHERE companyid='$companyid';";
		$result = pg_query($conn, $sql);
		$data = pg_fetch_assoc($result);
		
		//sets if an admin
		if (($data['admin_personid'] == $_SESSION['personid']) && ($_SESSION['personid'] >= '1')) {
			$_SESSION['admin'] = 'true';
		} else {
			$_SESSION['admin'] = 'false';
		}
		
		//sets of a super admin
		if ($_SESSION['personid'] == '1') {
			$_SESSION['super_admin'] = 'true';
		} else {
			$_SESSION['super_admin'] = 'false';
		}
		
		//if deleted remove session
		if ($data['deleted'] == 't') {
			session_unset();
			session_destroy();
			if (isset($_COOKIE['Login'])) {
				unset($_COOKIE['Login']);
				setcookie('Login', null, -1, '/');
			}
			//redirect to log in
			header('Location: /Login');
		}
		pg_close($conn);
	}
	if ($_SESSION['employeed'] == 't') {
		//Loading Includes
		include('includes/db.php');
		//gets current date
		$date = date("Y-m-d H:i:s");
		//gets current personid
		$personid = $_SESSION['personid'];
		//updates current active
		pg_query($conn, "UPDATE coaches SET last_active='$date' WHERE personid='$personid'");
		pg_close($conn);
	} else {
		//NOT ALLOWED IN
	}
	if ($_SERVER['REQUEST_URI'] != '/Login/' && !$_SESSION['employeed']) {
		//if not logged in and not on the login redirect to login
		header('Location: /Login/');
	}
?>