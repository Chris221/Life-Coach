<?php
	$connString = "host=localhost dbname=life_coach user=webuser password=Cappingteampablo2";

	$conn = pg_connect($connString);

	if (false) {
		$stat = pg_connection_status($conn);
		if ($stat === PGSQL_CONNECTION_OK) {
			echo 'Connection status ok';
		} else {
			echo 'Connection status bad';
		}
	}



	//CODE FOR LATER USE, Love past Chris
	//$result = pg_query($conn, "SQL GOES HERE");
	//while ($row = pg_fetch_row($result))
	//pg_fetch_all($result); pg_fetch_assoc
	//pg_escape_string($conn, "STRING GOES HERE");
	//pg_last_error($conn);
	//pg_close($conn);

	//Get last insert id
    //$insert_query = pg_query($conn,"SELECT lastval();");
	//$insert_row = pg_fetch_row($insert_query);
	//$last_insert_id = $insert_row[0];
?>