<?php
	function view($table,$where = '',$debug = false){
		include('includes/db.php');
		$escapedTable = pg_escape_string($conn, $table);
		if (strlen($where) > 1) {
			//$escapedWhere = pg_escape_string($conn, $where);
			$fullWhere = ' WHERE '.$where;
		} else {
			$fullWhere = '';
		}
		$sql = 'SELECT * FROM '.$escapedTable.$fullWhere.';';
		$result = pg_query($conn, $sql);
		$data = pg_fetch_assoc($result);
		if ($debug) {
			$error = pg_last_error($conn);
			if ($error) {
				echo('<br />Error! (View)<br />');
				echo('Table trying to be viewed: '.$table.'<br />');
				echo('Escaped Table: '.$escapedTable.'<br />');
				echo('Where statement: '.$where.'<br />');
				echo('Escaped Where: '.$escapedWhere.'<br />');
				echo('Full Where: '.$fullWhere.'<br />');
				echo('SQL: '.$sql.'<br />');
				echo('Result: '.$result.'<br />');
				echo('Data: '.$data.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		pg_close($conn);
		return $data;
	}

	function viewClients($type,$debug = false){
		include('includes/db.php');
		if ($type == 'all') {
			$where = 'WHERE companyid='.$_SESSION['companyid'];
		} else if ($type == 'mine') {
			$where = 'WHERE companyid='.$_SESSION['companyid'].' AND coachid='.$_SESSION['coachid'];
		} else if ($type == 'default') {
			$where = 'WHERE companyid='.$_SESSION['companyid'].' AND coachid='.$_SESSION['coachid'];
		}
		$sql = 'SELECT * FROM clients_view '.$where.';';
		$result = pg_query($conn, $sql);
		if ($debug) {
			$error = pg_last_error($conn);
			if ($error) {
				echo('<br />Error! (View Clients)<br />');
				echo('Type: '.$type.'<br />');
				echo('Where: '.$where.'<br />');
				echo('SQL: '.$sql.'<br />');
				echo('Result: '.$result.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		pg_close($conn);
		
		while ($row = pg_fetch_assoc($result)) {
			$pid = $row['personid'];
			$rData = view('persons','personid='.$pid);
			if ($rData['middle_name']) {
				$middleName = ' '.$rData['middle_name'];
			}
			$clientName = $rData['last_name'].', '.$rData['first_name'].$middleName;
			$encryptedPID = base64url_encode($pid);
			$clientList .= '<a href="/Profile/?p='.$encryptedPID.'">'.$clientName.'</a><br />';
		}
		
		return $clientList;
	}

	function addPerson($firstName, $lastName, $email, $cell, $companyid, $photoid = null, $prefix = null, $suffix = null, $home = null, $work = null, $extension = null, $dob = null, $address = null, $middleName = null, $debug = false){
		include('includes/db.php');
		$eFirstName = pg_escape_string($conn, $firstName);
		$eLastName = pg_escape_string($conn, $lastName);
		$eEmail = pg_escape_string($conn, $email);
		$eCell = pg_escape_string($conn, $cell);
		$ePrefix = pg_escape_string($conn, $prefix);
		$eSuffix = pg_escape_string($conn, $suffix);
		$eHome = "'".pg_escape_string($conn, $home)."'";
		$eWork = "'".pg_escape_string($conn, $work)."'";
		$eExtension = "'".pg_escape_string($conn, $extension)."'";
		$eMiddleName = pg_escape_string($conn, $middleName);
		$address = "'".$address."'";
		$photoid = "'".$photoid."'";
		
		$eHome = convertEmptyToNull($eHome);
		$eWork = convertEmptyToNull($eWork);
		$eExtension = convertEmptyToNull($eExtension);
		$address = convertEmptyToNull($address);
		$photoid = convertEmptyToNull($photoid);
		
		$sql = "INSERT INTO persons (photoid, prefix, first_name, last_name, suffix, email, cell, home, work, extension, date_of_birth, address, middle_name, companyid) VALUES ($photoid, '$ePrefix', '$eFirstName', '$eLastName', '$eSuffix', '$eEmail', '$eCell', $eHome, $eWork, $eExtension, '$dob', $address, '$eMiddleName','$companyid');";
		$result = pg_query($conn, $sql);
		if ($debug) {
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		
		//Get row
		$insert_query = pg_query($conn,"SELECT lastval();");
		$insert_row = pg_fetch_row($insert_query);
		$last_insert_id = $insert_row[0];
		
		pg_close($conn);
		return $last_insert_id;
	}

	function addCoach($personid,$surperviser,$pass,$debug = false){
		include('includes/db.php');
		$sql = "INSERT INTO coaches(personid,supervisor,password) VALUES ('$personid',$surperviser::boolean,'$pass');";
		$result = pg_query($conn, $sql);
		if ($debug) {
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		
		//Get row
		$insert_query = pg_query($conn,"SELECT lastval();");
		$insert_row = pg_fetch_row($insert_query);
		$last_insert_id = $insert_row[0];
		
		pg_close($conn);
		return $last_insert_id;
	}

	function addClient($personid,$workaddress,$workcompany = null,$worktitle = null,$workfield = null,$favoritebook = null,$favoritefood = null,$visitpreferencestart = null, $visitpreferenceend = null,$callpreferencestart = null,$callpreferenceend = null,$goals = null,$needs = null,$coachid = null,$debug = false) {
		include('includes/db.php');
		
		$workaddress = "'".$workaddress."'";
		$workcompany = pg_escape_string($conn, $workcompany);
		$worktitle = pg_escape_string($conn, $worktitle);
		$workfield = pg_escape_string($conn, $workfield);
		$favoritebook = pg_escape_string($conn, $favoritebook);
		$favoritefood = pg_escape_string($conn, $favoritefood);
		$visitpreferencestart = "'".pg_escape_string($conn, $visitpreferencestart)."'";
		$visitpreferenceend = "'".pg_escape_string($conn, $visitpreferenceend)."'";
		$callpreferencestart = "'".pg_escape_string($conn, $callpreferencestart)."'";
		$callpreferenceend = "'".pg_escape_string($conn, $callpreferenceend)."'";
		$goals = pg_escape_string($conn, $goals);
		$needs = pg_escape_string($conn, $needs);
		
		$workaddress = convertEmptyToNull($workaddress);
		
		$sql = "INSERT INTO clients(personid, work_company, work_address, work_title, work_field, favorite_book, favorite_food, visit_time_preference_start, visit_time_preference_end, call_time_preference_start, call_time_preference_end, goals, needs, coachid) VALUES ('$personid','$workcompany',$workaddress,'$worktitle','$workfield','$favoritebook','$favoritefood',$visitpreferencestart,$visitpreferenceend,$callpreferencestart,$callpreferenceend,'$goals','$needs','$coachid');";
		$result = pg_query($conn, $sql);
		if ($debug) {
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		
		//Get row
		$insert_query = pg_query($conn,"SELECT lastval();");
		$insert_row = pg_fetch_row($insert_query);
		$last_insert_id = $insert_row[0];
		
		pg_close($conn);
		return $last_insert_id;
	}

	function addNote($postedNotes,$clientid,$coachid,$photoid,$visitID = null,$debug = false) {
		include('includes/db.php');
		$goals = pg_escape_string($conn,$postedNotes);
		
		$date = date("Y-m-d H:i:s");
		
		$sql = "INSERT INTO notes(clientid, coachid, visitid, photoid, description, date_added) VALUES ($clientid,$coachid,$visitID,$photoid,$postedNotes,$date);";
		$result = pg_query($conn, $sql);
		if ($debug) {
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		
		//Get row
		$insert_query = pg_query($conn,"SELECT lastval();");
		$insert_row = pg_fetch_row($insert_query);
		$last_insert_id = $insert_row[0];
		
		pg_close($conn);
		return $last_insert_id;
	}

	function getNote($clientid,$debug = false) {
		include('includes/db.php');
		$goals = pg_escape_string($conn,$postedNotes);
		
		$date = date("Y-m-d H:i:s");
		
		$sql = "INSERT INTO notes(clientid, coachid, visitid, photoid, description, date_added) VALUES ($clientid,$postedNotes,$date);";
		$result = pg_query($conn, $sql);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		
		pg_close($conn);
		return $last_insert_id;
	}
	
	function cleanPhoneNumber($number) {
		//cleans the phone number of extras
		$number = str_replace("e","",$number);
		$number = str_replace(".","",$number);
		$number = str_replace("-","",$number);
		$number = str_replace("+","",$number);
		include('includes/db.php');
		pg_escape_string($conn, $number);
		pg_close($conn);
		return $number;
	}

	function convertEmptyToNull($string) {
		//makes the null value for postgress
		if ($string == "''") {
			$string = "NULL";
		}
		return $string;
	}
	
	function changeTime($time, $f = '24') {
		//f is what we are converting to
		if ($f == '24') {
			$r = date("H:i:s", strtotime($time));
		} else if ($f == '12') {
			$r = date("g:i a", strtotime($time));
		} else {
			$r = 'ERROR IN THE CHANGE TIME FUNCTION CALL';
		}
		return $r;
	}

	function checkTime($t) {
		if ($t == ':00') {
			$t = '00:00:00';
		}
		return $t;
	}

	function addStrTogether($s1,$s2) {
		if (strlen($s1) > 0) {
			$r = $s1;
		}
		if (strlen($r) > 0 && strlen($s2) > 0) {
			$r = $r.' '.$s2;
		} else if (strlen($s2) > 0) {
			$r = $s2;
		}
		return $r;
	}

	function addExtToNumber($n,$e) {
		if (strlen($s1) > 0) {
			$r = $s1;
		}
		if (strlen($r) > 0 && strlen($s2) > 0) {
			$r = $r.'p'.$s2;
		}
		return $r;
	}

	function addExtToNumberWithEXT($n,$e) {
		if (strlen($s1) > 0) {
			$r = $s1;
		}
		if (strlen($r) > 0 && strlen($s2) > 0) {
			$r = $r.' ext. '.$s2;
		}
		return $r;
	}

	function getAddress($aid) {
		$address = view('addresses','addressid='.$aid);
		if ($address['addressid']) {
			$returnAddress = $address['adressline1'].'<br />';
			if (strlen($address['adressline2']) > 0) {
				$returnAddress .= $address['adressline2'].'<br />';
			}
			if (strlen($address['subdivision']) > 0) {
				$city = $address['subdivision'].' ';
			}
			$returnAddress .= $address['city'].', '.$city.$address['zip'].', '.$address['country'];
		}
		
		return $returnAddress;
	}

?>