<?php
	// Hashword
	function encryptpass($pass) {
		// Encrypt password
		$pass = md5($pass);
		$pass = sha1($pass);
		$pass = md5($pass);
		$pass = crypt($pass,Ps);
		$pass = crypt($pass,aS);
		$pass = crypt($pass,LC);
		$pass = crypt($pass,wR);
		$pass = crypt($pass,Od);
		$salt = crypt("Life Coaching",TC);
		$pass = crypt($pass, '$21$' . $salt . '$');
		return $pass;
	};
	
?>