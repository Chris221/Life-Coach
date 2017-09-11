<?php
	$connPage = $_SERVER['REQUEST_URI'];
	$connString = "host=localhost port=PORT dbname=DB_NAME user=USERNAME pass=PASSWORD options='--application_name=$connPage'";

	$conn = pg_connect($connString);



	//CODE FOR LATER USE, Love past Chris
	//$result = pg_query($conn, "SQL GOES HERE");
	//while ($row = pg_fetch_row($result)) 
	//pg_fetch_all($result);
?>