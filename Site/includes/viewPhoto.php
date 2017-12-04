<?php
	//loads the image from the database
	//Loading Includes
	include('includes/db.php');
	include('includes/protection.php');
	include('db.php');
	include('protection.php');
	//decrypts the image id
	$id = decrypt($_REQUEST['a']);
	//gets the photo info
	$sql = "SELECT file, mimetype FROM photos WHERE photoid='$id';";
	$result = pg_query($conn, $sql);
	$data = pg_fetch_assoc($result);
	//gets the image read for the page
	$picture = pg_unescape_bytea($data['file']);
	$MIMEtype = $data['mimetype'];
	//sets headers
	header("Content-type: $MIMEtype");
	//displays the image
	echo $picture;
	pg_close($conn);
?>