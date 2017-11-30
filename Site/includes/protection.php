<?php
	//echo('Protection loaded.<br />');
	// Encrypt
	function encrypt($string) {
		$output = false;
		$encrypt_method = "AES-256-CBC";
		$key = crypt("LifeCoaching",LC);
		$secret_iv = 'I V Life Coaching';

		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$iv = substr(hash('sha256', $secret_iv), 0, 16);
		$output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
		return $output;
	}
	// Decrypt
	function decrypt($string) {
		$output = false;
		$encrypt_method = "AES-256-CBC";
		$key = crypt("LifeCoaching",LC);
		$secret_iv = 'I V Life Coaching';

		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$iv = substr(hash('sha256', $secret_iv), 0, 16);
		$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		return $output;
	}

?>