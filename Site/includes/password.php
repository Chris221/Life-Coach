<?php
	// Hashword
	function encryptpass($pass) {
		// Encrypt password
		$pass = md5($pass);
		$pass = sha1($pass);
		$pass = md5($pass);
		$pass = crypt($pass,Ps);
		$pass = crypt($pass,Sa);
		$pass = crypt($pass,LC);
		$pass = crypt($pass,wR);
		$pass = crypt($pass,Od);
		$salt = crypt("Life Coaching",TC);
		$pass = crypt($pass, '$1$' . $salt . '$');
		return $pass;
	};
	
?>