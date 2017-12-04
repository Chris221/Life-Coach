<?php
	function o_log($Action,$Description = '',$debug = false) {
		//logging script
		//echo ('o_log loaded<br />');
		include('includes/db.php');
		
		//gets personid
		$personid = $_SESSION['personid'];
		if ($personid == '') {
			$personid = '-1';
		}
		//gets page
		$Page = $_SERVER['REQUEST_URI'];
		//gets current date
		$date = date("Y-m-d H:i:s");
		//gets ip
		$IP = $_SERVER['REMOTE_ADDR'];
		//gets session id
		$SID = session_id();
		//adds log
		$sql = "INSERT INTO logs(personid,page,date,action,description,ip,session_id) VALUES ('$personid','$Page','$date','$Action','$Description','$IP','$SID');";
		
		pg_query($conn, $sql);
		$error = pg_last_error($conn);
		if ($debug) {
			//Debug information
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
			//Loading Includes
			include('includes/mailer.php');
			//emails developer after log creation error
			my_mailer('Chris@ChrisSiena.com', 'Log Creation ERROR', "Log Creation Failed: " . pg_last_error($conn) . '<br />' . 'Page: ' . $_SERVER['REQUEST_URI']);
		}
		pg_close($conn);
	}
	
?>