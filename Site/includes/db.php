<?php
	$connString = "host=localhost port=5432 dbname=Life_Coach user=webuser pass=Cappingteampablo2";

	$conn = pg_connect($connString);



	//CODE FOR LATER USE, Love past Chris
	//$result = pg_query($conn, "SQL GOES HERE");
	//while ($row = pg_fetch_row($result))
	//pg_fetch_all($result);
	//pg_escape_string($conn, "STRING GOES HERE");
	//pg_last_error($conn);
	//pg_close($conn);

	//Get last insert id
    //$insert_query = pg_query($conn,"SELECT lastval();");
	//$insert_row = pg_fetch_row($insert_query);
	//$last_insert_id = $insert_row[0];
?>