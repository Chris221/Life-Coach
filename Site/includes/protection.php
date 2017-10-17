<?php
	// Encrypt
	function encrypt($string) {
		$key = crypt("Life Coach",TC);
		$string = rtrim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $string, MCRYPT_MODE_ECB)));
		return $string;
	};
	// Decrypt
	function decrypt($string) {
		$key = crypt("Life Coach",TC);
		$string = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($string), MCRYPT_MODE_ECB));
		return $string;
	};
	// Url Dncode
	function base64url_encode($data) {
		$key = crypt("Life Coach",TC);
		$data = rtrim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_MODE_ECB)));
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
	}; 
	// Url Decode
	function base64url_decode($data) {
		$data = base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
		$key = crypt("Life Coach",TC);
		return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($data), MCRYPT_MODE_ECB));
	};
?>