<?php
	function my_mailer($email, $subject, $body) {
		$_SESSION['companyid'];
		include('includes/db.php');
		$sql = "SELECT domain FROM companies WHERE companyid='$companyid'";
		$data = pg_query($conn, $sql);
		$data = pg_fetch_assoc($data);
		pg_close($conn);
		
		$from = 'system@'.$data['domain'];
		$headers = '';
		$headers .= 'MIME-Version: 1.0'."\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
		$headers .= 'From: '.$from."\r\n";
		$headers .= 'Return-Path: '.$from."\r\n";
		$headers .=	'X-Mailer: PHP/' . phpversion();
		return mail($email, $subject, '<html><head><title>'.$subject.'</title></head><body>'.$body.'</body></html>', $headers, '-f'.$from);
	}
?>