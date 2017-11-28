<?php
	include('includes/db.php');
	include('includes/protection.php');
	include('db.php');
	include('protection.php');
	$id = decrypt($_REQUEST['a']);
	$sql = "SELECT file, mimetype FROM photos WHERE photoid='$id';";
	$result = pg_query($conn, $sql);
	$data = pg_fetch_assoc($result);
	$picture = pg_unescape_bytea($data['file']);
	$MIMEtype = $data['mimetype'];
	header("Content-type: $MIMEtype");
	echo $picture;
	pg_close($conn);
?>