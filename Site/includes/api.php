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

	function delete($table,$where,$debug = false){
		include('includes/db.php');
		$escapedTable = pg_escape_string($conn, $table);
		if (strlen($where) > 1) {
			//$escapedWhere = pg_escape_string($conn, $where);
			$fullWhere = ' WHERE '.$where;
		} else {
			return(false);
		}
		$sql = 'DELETE FROM '.$escapedTable.$fullWhere.';';
		$result = pg_query($conn, $sql);
		$error = pg_last_error($conn);
		if ($debug) {
			if ($error) {
				echo('<br />Error! (Delete)<br />');
				echo('Table: '.$table.'<br />');
				echo('Escaped Table: '.$escapedTable.'<br />');
				echo('Where statement: '.$where.'<br />');
				echo('Full Where: '.$fullWhere.'<br />');
				echo('SQL: '.$sql.'<br />');
				echo('Result: '.$result.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		pg_close($conn);
		if ($error) {
			return(false);
		} else {
			return(true);
		}
	}

	function markAsDeleted($sid,$debug = false){
		include('includes/db.php');
		$sql = 'UPDATE schedule SET deleted=true WHERE scheduleid='.$sid.';';
		$result = pg_query($conn, $sql);
		if ($debug) {
			if ($error) {
				echo('<br />Error! (markAsDeleted)<br />');
				echo('sid: '.$sid.'<br />');
				echo('SQL: '.$sql.'<br />');
				echo('Result: '.$result.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		pg_close($conn);
		if ($error) {
			return(false);
		} else {
			return(true);
		}
	}

	function viewSchedule($debug = false){
		include('includes/db.php');
		$sql = 'SELECT * FROM schedule_view WHERE coachid = '.$_SESSION['coachid'].' AND deleted IS NULL;';
		$result = pg_query($conn, $sql);
		if ($debug) {
			$error = pg_last_error($conn);
			if ($error) {
				echo('<br />Error! (View Schedule)<br />');
				echo('Type: '.$type.'<br />');
				echo('Where: '.$where.'<br />');
				echo('SQL: '.$sql.'<br />');
				echo('Result: '.$result.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		$schedule = '[';
		
		while ($row = pg_fetch_assoc($result)) {
			$pid = $row['personid'];
			$sid = $row['scheduleid'];
			$middleName = '';
			$start = date(DATE_ISO8601, strtotime($row['time_start']));
			$end = date(DATE_ISO8601, strtotime($row['time_end']));
			if ($row['middle_name']) {
				$middleName = ' '.$row['middle_name'];
			}
			$clientName = $row['first_name'].$middleName.' '.$row['last_name'];
			if (strlen($schedule) > 2) {
				$schedule .= ',';
			}
			$schedule .= "{
							id: '$sid',
							title: 'Appointment with $clientName',
							start: '$start',
							end: '$end',
            				url: '/Appointments?a=$sid'
						}";
		}
		pg_close($conn);
		
		$schedule .= ']';
		
		return $schedule;
	}

	function viewClients($type,$sText,$debug = false){
		include('includes/db.php');
		if ($type == 'all') {
			$where = 'WHERE companyid='.$_SESSION['companyid'];
		} else if ($type == 'search') {
			$cleanSearch = pg_escape_string($sText);
			$where = "WHERE companyid='".$_SESSION['companyid']."' AND (last_name ILIKE '%$cleanSearch%' OR first_name ILIKE '%$cleanSearch%')";
		} else if ($type == 'mine') {
			$where = 'WHERE companyid='.$_SESSION['companyid'].' AND coachid='.$_SESSION['coachid'];
		} else if ($type == 'default') {
			$where = 'WHERE companyid='.$_SESSION['companyid'].' AND coachid='.$_SESSION['coachid'];
			$limit = ' LIMIT 30';
		}
		$sql = 'SELECT personid FROM clients_view '.$where.' ORDER BY last_name ASC, first_name ASC'.$limit.';';
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
		
		while ($row = pg_fetch_assoc($result)) {
			$pid = $row['personid'];
			$middleName = '';
			/*$rData = view('persons','personid='.$pid);*/
			
			$sql2 = "SELECT first_name, middle_name, last_name FROM persons WHERE personid='".$pid."';";
			$result2 = pg_query($conn, $sql2);
			$rData = pg_fetch_assoc($result2);
			if ($debug) {
				$error2 = pg_last_error($conn);
				if ($error2) {
					echo('<br />Error! (View Clients)<br />');
					echo('Person ID: '.$pid.'<br />');
					echo('SQL: '.$sql2.'<br />');
					echo('Result: '.$result2.'<br />');
					echo('Data: '.$rData.'<br />');
					echo('Error: '.$error2.'<br />');
				}
			}
			if ($rData['middle_name']) {
				$middleName = ' '.$rData['middle_name'];
			}
			$clientName = $rData['last_name'].', '.$rData['first_name'].$middleName;
			$encryptedPID = encrypt($pid);
			$clientList .= '<a href="/Profile/?p='.$encryptedPID.'">'.$clientName.'</a><br />';
		}
		pg_close($conn);
		
		return $clientList;
	}

	function addPerson($firstName, $lastName, $email, $cell, $gender, $companyid, $photoid = null, $prefix = null, $suffix = null, $home = null, $work = null, $extension = null, $dob = null, $address = null, $middleName = null, $debug = false){
		include('includes/db.php');
		$eFirstName = pg_escape_string($conn, $firstName);
		$eLastName = pg_escape_string($conn, $lastName);
		$eEmail = pg_escape_string($conn, $email);
		$eCell = pg_escape_string($conn, $cell);
		$ePrefix = pg_escape_string($conn, $prefix);
		$eSuffix = pg_escape_string($conn, $suffix);
		$eGender = pg_escape_string($conn, strtolower($gender));
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
		
		$sql = "INSERT INTO persons (photoid, prefix, first_name, last_name, suffix, email, cell, home, work, extension, date_of_birth, addressid, middle_name, companyid, gender, deceased, deleted) VALUES ($photoid, '$ePrefix', '$eFirstName', '$eLastName', '$eSuffix', '$eEmail', '$eCell', $eHome, $eWork, $eExtension, '$dob', $address, '$eMiddleName','$companyid','$eGender',false,false);";
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

	function addClient($personid,$workaddress,$workcompany = null,$worktitle = null,$workfield = null,$favoritebook = null,$favoritefood = null,$visitpreferencestart = null, $visitpreferenceend = null,$callpreferencestart = null,$callpreferenceend = null,$goals = null,$needs = null,$selfawareness = null,$coachid = null,$debug = false) {
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
		$selfawareness = pg_escape_string($conn, $selfawareness);
		
		$workaddress = convertEmptyToNull($workaddress);
		
		$sql = "INSERT INTO clients(personid, work_company, work_address, work_title, work_field, favorite_book, favorite_food, visit_time_preference_start, visit_time_preference_end, call_time_preference_start, call_time_preference_end, goals, needs, selfawareness, coachid) VALUES ('$personid','$workcompany',$workaddress,'$worktitle','$workfield','$favoritebook','$favoritefood',$visitpreferencestart,$visitpreferenceend,$callpreferencestart,$callpreferenceend,'$goals','$needs','$selfawareness','$coachid');";
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

	function addNote($pid,$postedNote,$clientid,$coachid,$photoid = null,$visitID = null,$debug = false) {
		include('includes/db.php');
		$cleanedNote = pg_escape_string($conn,$postedNote);
		$photoid = "'".$photoid."'";
		$visitID = "'".$visitID."'";
		
		$photoid = convertEmptyToNull($photoid);
		$visitID = convertEmptyToNull($visitID);
		
		$date = date("Y-m-d H:i:s");
		
		$sql = "INSERT INTO notes(clientid, coachid, visitid, photoid, description, date_added) VALUES ('$clientid','$coachid',$visitID,$photoid,'$cleanedNote','$date');";
		$result = pg_query($conn, $sql);
		if ($debug) {
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('Posted Note: '.$postedNote.'<br />');
				echo('Cleaned Note: '.$cleanedNote.'<br />');
				echo('Client ID: '.$clientid.'<br />');
				echo('Coach ID: '.$coachid.'<br />');
				echo('Photo ID: '.$photoid.'<br />');
				echo('Visit ID: '.$visitID.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		
		//Get row
		$insert_query = pg_query($conn,"SELECT lastval();");
		$insert_row = pg_fetch_row($insert_query);
		$last_insert_id = $insert_row[0];
		
		if ($pid) {
			$return = '?p='.$pid;
		}
		
		pg_close($conn);
		header('Location: /Profile'.$return);
		return $last_insert_id;
	}

	function addAddress($adressline1,$adressline2,$city,$subdivision,$zip,$country,$debug = false) {
		include('includes/db.php');
		$adressline1 = pg_escape_string($conn,$adressline1)."'";
		$adressline2 = "'".pg_escape_string($conn,$adressline2)."'";
		$city = "'".pg_escape_string($conn,$city)."'";
		$subdivision = "'".pg_escape_string($conn,$subdivision)."'";
		$notCleanZip = pg_escape_string($conn,$zip);
		$country = "'".pg_escape_string($conn,$country)."'";
		
		$zip = "'".cleanPhoneNumber($notCleanZip)."'";
		
		$adressline1 = convertEmptyToNull($adressline1);
		$adressline2 = convertEmptyToNull($adressline2);
		$city = convertEmptyToNull($city);
		$subdivision = convertEmptyToNull($subdivision);
		$zip = convertEmptyToNull($zip);
		$country = convertEmptyToNull($country);
		
		pg_close($conn);
		include('includes/db.php');
		$sql = "INSERT INTO addresses(adressline1, adressline2, city, subdivision, zip, country) VALUES ($adressline1,$adressline2,$city,$subdivision,$zip,$country);";
		$result = pg_query($conn, $sql);
		if ($debug) {
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('adressline1: '.$adressline1.'<br />');
				echo('adressline2: '.$adressline2.'<br />');
				echo('city: '.$city.'<br />');
				echo('subdivision: '.$subdivision.'<br />');
				echo('zip: '.$zip.'<br />');
				echo('country: '.$country.'<br />');
				echo('failed?: '.$failed.'<br />');
				echo('result: '.$result.'<br />');
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

	function changeAddress($addressid,$adressline1,$adressline2,$city,$subdivision,$zip,$country,$debug = false) {
		include('includes/db.php');
		$adressline1 = "'".pg_escape_string($conn,$adressline1)."'";
		$adressline2 = "'".pg_escape_string($conn,$adressline2)."'";
		$city = "'".pg_escape_string($conn,$city)."'";
		$subdivision = "'".pg_escape_string($conn,$subdivision)."'";
		$notCleanZip = pg_escape_string($conn,$zip);
		$country = "'".pg_escape_string($conn,$country)."'";
		
		$zip = "'".cleanPhoneNumber($notCleanZip)."'";
		
		$adressline1 = convertEmptyToNull($adressline1);
		$adressline2 = convertEmptyToNull($adressline2);
		$city = convertEmptyToNull($city);
		$subdivision = convertEmptyToNull($subdivision);
		$zip = convertEmptyToNull($zip);
		$country = convertEmptyToNull($country);
		
		pg_close($conn);
		include('includes/db.php');
		$sql = "UPDATE addresses SET adressline1=$adressline1, adressline2=$adressline2, city=$city, subdivision=$subdivision, zip=$zip, country=$country WHERE addressid='$addressid';";
		$result = pg_query($conn, $sql);
		if ($debug) {
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('adressline1: '.$adressline1.'<br />');
				echo('adressline2: '.$adressline2.'<br />');
				echo('city: '.$city.'<br />');
				echo('subdivision: '.$subdivision.'<br />');
				echo('zip: '.$zip.'<br />');
				echo('country: '.$country.'<br />');
				echo('failed?: '.$failed.'<br />');
				echo('result: '.$result.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		pg_close($conn);
	}

	function addVisit($start,$type,$reason,$addressid,$emergency,$debug = false) {
		include('includes/db.php');
		$start = pg_escape_string($conn,$start);
		$addressid = "'".pg_escape_string($conn,$addressid)."'";
		$type = pg_escape_string($conn,$type);
		$reason = pg_escape_string($conn,$reason);
		
		if (!(strlen($reason) >= 1)) {
			$failed = 'Reason cannont be blank.<br />';
		} 
		if (!(strlen($type) >= 1)) {
			$failed .= 'Type cannont be blank.<br />';
		} 
		if ($failed) {
			return $failed;
		}
		
		$addressid = convertEmptyToNull($addressid);
		
		$sql = "INSERT INTO visits(date, type, reason, addressid, emergency) VALUES ('$start','$type','$reason',$addressid,'$emergency');";
		$result = pg_query($conn, $sql);
		if ($debug) {
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('start: '.$start.'<br />');
				echo('type: '.$type.'<br />');
				echo('reason: '.$reason.'<br />');
				echo('addressid: '.$addressid.'<br />');
				echo('emergency: '.$emergency.'<br />');
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

	function addSchedule($start,$addressid,$personid,$visitid,$end,$debug = false) {
		include('includes/db.php');
		$addressid = "'".pg_escape_string($conn,$addressid)."'";
		$visitid = "'".pg_escape_string($conn,$visitid)."'";
		$end = "'".$end."'";
		
		$addressid = convertEmptyToNull($addressid);
		$visitid = convertEmptyToNull($visitid);
		$end = convertEmptyToNull($end);
		
		$scheduledby = $_SESSION['coachid'];
		$coachid = $_SESSION['coachid'];
		
		$sql = "INSERT INTO schedule(time_start, addressid, personid, coachid, scheduledby, visitid, time_end) VALUES ('$start',$addressid,'$personid','$coachid','$scheduledby',$visitid,$end);";
		$result = pg_query($conn, $sql);
		if ($debug) {
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('start: '.$start.'<br />');
				echo('addressid: '.$addressid.'<br />');
				echo('personid: '.$personid.'<br />');
				echo('coachid: '.$coachid.'<br />');
				echo('scheduledby: '.$scheduledby.'<br />');
				echo('end: '.$end.'<br />');
				echo('failed?: '.$failed.'<br />');
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

	function changeSchedule($aid,$start,$type,$reason,$emergency,$end,$debug = false) {
		include('includes/db.php');
		$end = "'".$end."'";
		$end = convertEmptyToNull($end);
		
		$sql = "UPDATE schedule SET time_start='$start', time_end=$end WHERE scheduleid=$aid;";
		$result = pg_query($conn, $sql);
		if ($debug) {
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('result: '.$result.'<br />');
				echo('start: '.$start.'<br />');
				echo('end: '.$end.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		$sql = "SELECT visitid FROM schedule WHERE scheduleid=$aid;";
		$result = pg_query($conn, $sql);
		$result = pg_fetch_row($result);
		$vid = $result[0];
		if ($debug) {
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('result: '.$result.'<br />');
				echo('vid: '.$vid.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		
		$sql = "UPDATE visits SET date='$start', type='$type', reason='$reason', emergency='$emergency' WHERE visitid=$vid;";
		$result = pg_query($conn, $sql);
		if ($debug) {
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('result: '.$result.'<br />');
				echo('start: '.$start.'<br />');
				echo('type: '.$type.'<br />');
				echo('reason: '.$reason.'<br />');
				echo('emergency: '.$emergency.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		pg_close($conn);
	}

	function viewNote($clientid,$debug = false) {
		include('includes/db.php');
		$sql = 'SELECT * FROM notes WHERE clientid='.$clientid.' AND deleted IS NULL ORDER BY Date_Added ASC;';
		$result = pg_query($conn, $sql);
		if ($debug) {
			$error = pg_last_error($conn);
			if ($error) {
				echo('<br />Error! (View Notes)<br />');
				echo('Client ID: '.$clientid.'<br />');
				echo('SQL: '.$sql.'<br />');
				echo('Result: '.$result.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		pg_close($conn);
		
		while ($row = pg_fetch_assoc($result)) {
			if (strlen($notes) > 0) {
				$notes .= '<br /><br />';
			}
			$nid = $row['noteid'];
			$description = $row['description'];
			$vid = $row['visitid'];
			$date = readableDate($row['date_added']);
			
			$notes .= '<div class="inline-block" id="'.$nid.'">'.$description.'</div><br />';
			$notes .= '<div class="inline-block"><span class="notesDate">'.$date.'</span>
                            <a href="/Notes?n='.$nid.'" class="btn btn-primary">Delete</a></div>';
		}
		
		return $notes;
	}

	function markNoteAsDeleted($nid,$debug = false) {
		include('includes/db.php');
		$sql = 'UPDATE notes SET deleted=true WHERE noteid='.$nid.';';
		$result = pg_query($conn, $sql);
		if ($debug) {
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('Result: '.$result.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		pg_close($conn);
	}

	function markPersonAsDeleted($pid,$debug = false) {
		include('includes/db.php');
		$sql = 'UPDATE persons SET deleted=true WHERE personid='.$pid.';';
		$result = pg_query($conn, $sql);
		if ($debug) {
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('Result: '.$result.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		pg_close($conn);
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
			$returnAddress .= $address['city'].', '.$address['subdivision'].' '.$address['zip'].' '.$address['country'].'<br />'.'<a href="/Address?a='.encrypt($aid).'" class="btn btn-primary">Edit Address</a>';
			if (!isset($address['adressline1']) || !isset($address['city']) || !isset($address['subdivision']) || !isset($address['zip']) || !isset($address['country'])) {
				$returnAddress = 'This address is not complete. Please correct it.<br />'.'<a href="/Address?a='.encrypt($aid).'" class="btn btn-primary">Edit Address</a>';
			}
		}
		
		return $returnAddress;
	}

	function backButton() {
		return $_SERVER['HTTP_REFERER'];
	}

	function readableDate($date) {
		$cleanDate = date('m/d/Y g:i A', strtotime($date));
		return $cleanDate;
	}

	function formatAddress($aid,$line1,$line2,$city,$subdivision,$zip,$country) {
		if (strlen($line2) > 1) {
			$cLine2 = "$line2<br />";
		}
		if (strlen($line1) > 1) {
			$address = "$line1<br />$cLine2
						$city, $subdivision $zip $country<br />".'<a href="/Address?a='.encrypt($aid).'" class="btn btn-primary">Edit Address</a>';
			if (!isset($address['adressline1']) || !isset($address['city']) || !isset($address['subdivision']) || !isset($address['zip']) || !isset($address['country'])) {
				$returnAddress = 'This address is not complete. Please correct it.<br />'.'<a href="/Address?a='.encrypt($aid).'" class="btn btn-primary">Edit Address</a>';
			}
		}
		return $address;
	}

	function changeProfile($pid, $firstname, $lastname, $email, $cell, $gender, $prefix, $suffix, $home, $worknumber, $extension, $correctDOB, $middlename, $workcompany, $worktitle, $workfield, $favoritebook, $favoritefood, $visitpreferencestart, $visitpreferenceend, $callpreferencestart, $callpreferenceend, $goals, $needs, $selfawareness, $supervisor, $employeed, $deceased, $debug = false) {
		include('includes/db.php');
		$eFirstName = pg_escape_string($conn, $firstname);
		$eLastName = pg_escape_string($conn, $lastname);
		$eEmail = pg_escape_string($conn, $email);
		$eCell = pg_escape_string($conn, $cell);
		$ePrefix = pg_escape_string($conn, $prefix);
		$eSuffix = pg_escape_string($conn, $suffix);
		$eGender = pg_escape_string($conn, strtolower($gender));
		$eHome = "'".pg_escape_string($conn, $home)."'";
		$eWork = "'".pg_escape_string($conn, $worknumber)."'";
		$eExtension = "'".pg_escape_string($conn, $extension)."'";
		$eMiddleName = pg_escape_string($conn, $middlename);
		
		$eHome = convertEmptyToNull($eHome);
		$eWork = convertEmptyToNull($eWork);
		$eExtension = convertEmptyToNull($eExtension);
		
		$sql = "UPDATE persons SET prefix='$ePrefix', first_name='$eFirstName', last_name='$eLastName', suffix='$eSuffix', email='$eEmail', cell='$eCell', home=$eHome, work=$eWork, extension=$eExtension, date_of_birth='$correctDOB', middle_name='$eMiddleName', gender='$eGender', deceased=$deceased::boolean WHERE personid='$pid';";
		$result = pg_query($conn, $sql);
		if ($debug) {
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('pid: '.$pid.'<br />');
				echo('result: '.$result.'<br />');
				echo('ePrefix: '.$ePrefix.'<br />');
				echo('eFirstName: '.$eFirstName.'<br />');
				echo('eLastName: '.$eLastName.'<br />');
				echo('eSuffix: '.$eSuffix.'<br />');
				echo('eEmail: '.$eEmail.'<br />');
				echo('eCell: '.$eCell.'<br />');
				echo('eHome: '.$eHome.'<br />');
				echo('eWork: '.$eWork.'<br />');
				echo('eExtension: '.$eExtension.'<br />');
				echo('correctDOB: '.$correctDOB.'<br />');
				echo('eMiddleName: '.$eMiddleName.'<br />');
				echo('eGender: '.$eGender.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		
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
		
		
		$sql = "UPDATE clients SET work_company='$workcompany', work_title='$worktitle', work_field='$workfield', favorite_book='$favoritebook', favorite_food='$favoritefood', visit_time_preference_start=$visitpreferencestart, visit_time_preference_end=$visitpreferenceend, call_time_preference_start=$callpreferencestart, call_time_preference_end=$callpreferenceend, goals='$goals', needs='$needs', selfawareness='$selfawareness' WHERE personid='$pid';";
		$result = pg_query($conn, $sql);
		if ($debug) {
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('pid: '.$pid.'<br />');
				echo('result: '.$result.'<br />');
				echo('workcompany: '.$workcompany.'<br />');
				echo('worktitle: '.$worktitle.'<br />');
				echo('workfield: '.$workfield.'<br />');
				echo('favorite_book: '.$favorite_book.'<br />');
				echo('favorite_food: '.$favorite_food.'<br />');
				echo('visitpreferencestart: '.$visitpreferencestart.'<br />');
				echo('visitpreferenceend: '.$visitpreferenceend.'<br />');
				echo('callpreferencestart: '.$callpreferencestart.'<br />');
				echo('callpreferenceend: '.$callpreferenceend.'<br />');
				echo('goals: '.$goals.'<br />');
				echo('needs: '.$needs.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		pg_close($conn);
		
		$coachResult  = view('coaches','personid='.$pid);
		if ($coachResult['coachid']) {
			include('includes/db.php');
			$sql = "UPDATE coaches SET supervisor=$supervisor::boolean, employeed=$employeed::boolean WHERE personid='$pid';";
			$result = pg_query($conn, $sql);
			if ($debug) {
				$error = pg_last_error($conn);
				if ($error) {
					echo('SQL: '.$sql.'<br />');
					echo('result: '.$result.'<br />');
					echo('supervisor: '.$supervisor.'<br />');
					echo('Error: '.$error.'<br />');
				}
			}
			pg_close($conn);
		} else {
			echo('They are not a Coach, no attempt to change.<br />');
		}
	}

?>