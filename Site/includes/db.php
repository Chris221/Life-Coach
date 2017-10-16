<?php
	$connPage = $_SERVER['REQUEST_URI'];
	$connString = "host=localhost port=5432 dbname=Life_Coach user=webuser pass=Cappingteampablo2 options='--application_name=$connPage'";

	$conn = pg_connect($connString);



	//CODE FOR LATER USE, Love past Chris
	//$result = pg_query($conn, "SQL GOES HERE");
	//while ($row = pg_fetch_row($result)) 
	//pg_fetch_all($result);
?>