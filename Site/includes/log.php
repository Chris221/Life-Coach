<?php
	function o_log($Action,$Description = '',$debug = false) {
		include('includes/db.php');
		
		$personid = $_SESSION['personid'];
		$Page = $_SERVER['REQUEST_URI'];
		$date = date("Y-m-d H:i:s");
		$IP = $_SERVER['REMOTE_ADDR'];
		$SID = session_id();
		$sql = "INSERT INTO logs(personid,page,date,action,description,ip,session_id) VALUES ('$personid','$Page','$date','$Action','$Description','$IP','$SID');";
		
		pg_query($conn, $sql);
		$error = pg_last_error($conn);
		if ($debug) {
			echo ('Action: '.$Action.'<br />');
			echo ('Description: '.$Description.'<br />');
			echo ('Person ID: '.$personid.'<br />');
			echo ('Page: '.$Page.'<br />');
			echo ('date: '.$date.'<br />');
			echo ('IP: '.$IP.'<br />');
			echo ('SID: '.$SID.'<br />');
			echo ('SQL: '.$sql.'<br />');
			echo ('Error: '.$error.'<br />');
		}
		if (strlen(pg_last_error($conn))>0) {
			include('includes/mailer.php');
			my_mailer('Chris@ChrisSiena.com', 'Log Creation ERROR', "Log Creation Failed: " . pg_last_error($conn) . '<br />' . 'Page: ' . $_SERVER['REQUEST_URI']);
		}
		pg_close($conn);
	}
	
?>