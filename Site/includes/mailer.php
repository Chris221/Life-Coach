<?php
	function my_mailer($email, $subject, $body,$debug = false) {
		$from = 'system@reev.us';
		$headers = '';
		$headers .= 'MIME-Version: 1.0'."\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
		$headers .= 'From: '.$from."\r\n";
		$headers .= 'Return-Path: '.$from."\r\n";
		$headers .=	'X-Mailer: PHP/' . phpversion();
		if ($debug) {
			echo('email: "'.$email.'"<br />');
			echo('subject: "'.$subject.'"<br />');
			echo('body: "'.$body.'"<br />');
			echo('headers: "'.$headers.'"<br />');
			echo('from: "'.$from.'"<br />');
		}
		return mail($email, $subject, '<html><head><title>'.$subject.'</title></head><body>'.$body.'</body></html>', $headers, '-f'.$from);
	}
?>