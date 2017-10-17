<?php
	include('includes/session.php');
	session_unset();
	session_destroy();
	if (isset($_COOKIE['Login'])) {
		unset($_COOKIE['Login']);
		setcookie('Login', null, -1, '/');
	}
	header('Location: /Login');
?>