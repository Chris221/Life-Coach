<?php
	function log($Action,$Description = '') {
		include('includes/db.php');
		
		$personid = $_SESSION['personid'];
		$Page = $_SERVER['REQUEST_URI'];
		$date = date("Y-m-d H:i:s");
		$IP = $_SERVER['REMOTE_ADDR'];
		$SID = session_id();
		
		pg_query($conn, "INSERT INTO Logs (personid,page,date,action,description,ip,session_id) VALUES ('$personid','$Page','$date','$Action','$Description','$IP','$SID')");
		if (strlen(pg_last_error($conn))>0) {
			include('includes/mailer.php');
			my_mailer('Chris@ChrisSiena.com', 'Log Creation ERROR', "Log Creation Failed: " . pg_last_error($conn) . '<br />' . 'Page: ' . $_SERVER['REQUEST_URI']);
		}
		pg_close($conn);
	}
	
?>