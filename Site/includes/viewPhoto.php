<?php
	include('includes/db.php');
	include('includes/protection.php');
	include('includes/view.php');
	$id = base64url_decode($_REQUEST['a']);
	$data = view('photos','photoid='.$id);
	$picture = $data['file'];
	$MIMEtype = $data['mimetype'];
	header("Content-type: $MIMEtype");
	echo $picture;
?>