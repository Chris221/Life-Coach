<?php
	function view($table,$where = '',$debug = false) {
		//gets everything from any table, if there is a where statement it will query with that where clause
		//Loading Includes
		include('includes/db.php');
		//Clean strings
		$escapedTable = pg_escape_string($conn, $table);
		//if theres a where clause add it
		if (strlen($where) > 1) {
			$fullWhere = ' WHERE '.$where;
		} else {
			$fullWhere = '';
		}
		//gets everything from a table, if theres where add it
		$sql = 'SELECT * FROM '.$escapedTable.$fullWhere.';';
		$result = pg_query($conn, $sql);
		//gets results 
		$data = pg_fetch_assoc($result);
		if ($debug) {
			//Debug information
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
		//returns data from the view
		return $data;
	}

	function delete($table,$where,$debug = false) {
		//Loading Includes
		include('includes/db.php');
		//Cleans strings
		$escapedTable = pg_escape_string($conn, $table);
		if (strlen($where) > 1) {
			$fullWhere = ' WHERE '.$where;
		} else {
			return(false);
		}
		//deletes row
		$sql = 'DELETE FROM '.$escapedTable.$fullWhere.';';
		$result = pg_query($conn, $sql);
		$error = pg_last_error($conn);
		if ($debug) {
			//Debug information
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
		//confirms it was deleted
		if ($error) {
			return(false);
		} else {
			return(true);
		}
	}

	function markAsDeleted($sid,$debug = false) {
		//Loading Includes
		include('includes/db.php');
		//marks the scheduled event as deleted
		$sql = 'UPDATE schedule SET deleted=true WHERE scheduleid='.$sid.';';
		$result = pg_query($conn, $sql);
		$error = pg_last_error($conn);
		if ($debug) {
			//Debug information
			if ($error) {
				echo('<br />Error! (markAsDeleted)<br />');
				echo('sid: '.$sid.'<br />');
				echo('SQL: '.$sql.'<br />');
				echo('Result: '.$result.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		pg_close($conn);
		//confirms it was marked as delete
		if ($error) {
			return(false);
		} else {
			return(true);
		}
	}

	function markCompanyAsDeleted($companyid,$debug = false) {
		//Loading Includes
		include('includes/db.php');
		//change company to deleted
		$sql = 'UPDATE companies SET deleted=true WHERE companyid='.$companyid.';';
		$result = pg_query($conn, $sql);
		$error = pg_last_error($conn);
		if ($debug) {
			//Debug information
			if ($error) {
				echo('<br />Error! (markCompanyAsDeleted)<br />');
				echo('companyid: '.$companyid.'<br />');
				echo('SQL: '.$sql.'<br />');
				echo('Result: '.$result.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		pg_close($conn);
	}

	function markCompanyAsNOTDeleted($companyid,$debug = false) {
		//Loading Includes
		include('includes/db.php');
		//change company to not deleted
		$sql = 'UPDATE companies SET deleted=false WHERE companyid='.$companyid.';';
		$result = pg_query($conn, $sql);
		$error = pg_last_error($conn);
		if ($debug) {
			//Debug information
			if ($error) {
				echo('<br />Error! (markCompanyAsNOTDeleted)<br />');
				echo('companyid: '.$companyid.'<br />');
				echo('SQL: '.$sql.'<br />');
				echo('Result: '.$result.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		pg_close($conn);
	}

	function viewSchedule($debug = false) {
		//gets all scheduled items
		//Loading Includes
		include('includes/db.php');
		//gets all schedule info scheduled for that coach
		$sql = 'SELECT * FROM schedule_view WHERE coachid = '.$_SESSION['coachid'].' AND deleted IS NULL;';
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
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
		//schedule open
		$schedule = '[';
		
		while ($row = pg_fetch_assoc($result)) {
			//gets the row values for each schedule
			$pid = $row['personid'];
			$sid = $row['scheduleid'];
			$middleName = '';
			//gets the correct dates
			$start = date(DATE_ISO8601, strtotime($row['time_start']));
			$end = date(DATE_ISO8601, strtotime($row['time_end']));
			//gets name who its scheduled for
			if ($row['middle_name']) {
				$middleName = ' '.$row['middle_name'];
			}
			$clientName = $row['first_name'].$middleName.' '.$row['last_name'];
			if (strlen($schedule) > 2) {
				//adds a common for the json calender load
				$schedule .= ',';
			}
			//schedule addition
			$schedule .= "{
							id: '$sid',
							title: 'Appointment with $clientName',
							start: '$start',
							end: '$end',
            				url: '/Appointments?a=$sid'
						}";
		}
		pg_close($conn);
		
		//schedule close
		$schedule .= ']';
		
		//returns schedule
		return $schedule;
	}

	function viewClients($type,$sText,$debug = false) {
		//view clients for clients page
		//Loading Includes
		include('includes/db.php');
		if ($type == 'all') {
			//veiw all clients
			$where = 'WHERE companyid='.$_SESSION['companyid'];
		} else if ($type == 'search') {
			//applies search to the query
			//Cleans strings
			$cleanSearch = pg_escape_string($sText);
			$where = "WHERE companyid='".$_SESSION['companyid']."' AND (last_name ILIKE '%$cleanSearch%' OR first_name ILIKE '%$cleanSearch%')";
		} else if ($type == 'mine') {
			//views all of our own
			$where = 'WHERE companyid='.$_SESSION['companyid'].' AND coachid='.$_SESSION['coachid'];
		} else if ($type == 'default') {
			//applies the default view
			$where = 'WHERE companyid='.$_SESSION['companyid'].' AND coachid='.$_SESSION['coachid'];
			$limit = ' LIMIT 30';
		}
		//gets person from clientid
		$sql = 'SELECT personid FROM clients_view '.$where.' ORDER BY last_name ASC, first_name ASC'.$limit.';';
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
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
			//builds client view
			$pid = $row['personid'];
			$middleName = '';
			/*$rData = view('persons','personid='.$pid);*/
			
			//gets person information
			$sql2 = "SELECT first_name, middle_name, last_name FROM persons WHERE personid='".$pid."';";
			$result2 = pg_query($conn, $sql2);
			$rData = pg_fetch_assoc($result2);
			if ($debug) {
			//Debug information
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
			//adds the client to the list
			$clientList .= '<a href="/Profile/?p='.$encryptedPID.'">'.$clientName.'</a><br />';
		}
		pg_close($conn);
		
		//returns cleint list
		return $clientList;
	}

	function viewPeople($returnLocation,$type = 'all',$sText = '',$debug = false) {
		//Adds all the people together in a list view
		//Loading Includes
		include('includes/db.php');
		if ($type == 'search') {
			//Cleans strings
			$cleanSearch = pg_escape_string($sText);
			$where = "WHERE companyid='".$_SESSION['companyid']."' AND (last_name ILIKE '%$cleanSearch%' OR first_name ILIKE '%$cleanSearch%')";
		} else {
			$where = "WHERE companyid='".$_SESSION['companyid']."'";
		} 
		$sql = 'SELECT personid FROM clients_view '.$where.' ORDER BY last_name ASC, first_name ASC;';
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
			$error = pg_last_error($conn);
			if ($error) {
				echo('<br />Error! (View People)<br />');
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
			//gets person info
			$sql2 = "SELECT first_name, middle_name, last_name FROM persons WHERE personid='".$pid."';";
			$result2 = pg_query($conn, $sql2);
			$rData = pg_fetch_assoc($result2);
			if ($debug) {
			//Debug information
				$error2 = pg_last_error($conn);
				if ($error2) {
					echo('<br />Error! (View People)<br />');
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
			$peopleName = $rData['last_name'].', '.$rData['first_name'].$middleName;
			$encryptedPID = encrypt($pid);
			
			//adds person to list
			$peopleList .= '<a href="'.$returnLocation.'&n='.$encryptedPID.'">'.$peopleName.'</a><br />';
		}
		pg_close($conn);
		
		//returns people list
		return $peopleList;
	}

	function viewCoachesForChange($returnLocation,$type = 'all',$sText = '',$debug = false) {
		//view coach for change coaches page
		//Loading Includes
		include('includes/db.php');
		if ($type == 'search') {
			//if theres a search apply that
			//Cleans strings
			$cleanSearch = pg_escape_string($sText);
			$where = "WHERE companyid='".$_SESSION['companyid']."' AND (last_name ILIKE '%$cleanSearch%' OR first_name ILIKE '%$cleanSearch%')";
		} else {
			//default view
			$where = "WHERE companyid='".$_SESSION['companyid']."'";
		} 
		//get all coach information
		$sql = 'SELECT first_name, middle_name, last_name, coachid FROM accounts '.$where.' ORDER BY last_name ASC, first_name ASC;';
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
			$error = pg_last_error($conn);
			if ($error) {
				echo('<br />Error! (View Coaches for change)<br />');
				echo('Type: '.$type.'<br />');
				echo('Where: '.$where.'<br />');
				echo('SQL: '.$sql.'<br />');
				echo('Result: '.$result.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		
		while ($row = pg_fetch_assoc($result)) {
			//gets row values
			$coachid = $row['coachid'];
			$middleName = '';
			if ($row['middle_name']) {
				$middleName = ' '.$row['middle_name'];
			}
			$coachName = $row['last_name'].', '.$row['first_name'].$middleName;
			$encryptedPID = encrypt($coachid);
			//adds coach to list
			$coachList .= '<a href="'.$returnLocation.'&n='.$encryptedPID.'">'.$coachName.'</a><br />';
		}
		pg_close($conn);
		
		//returns coach list
		return $coachList;
	}

	function viewCoaches($type = 'all',$sText = '',$debug = false) {
		//view coach for coaches page
		//Loading Includes
		include('includes/db.php');
		if ($type == 'search') {
			//if theres a search apply that
			//Cleans strings
			$cleanSearch = pg_escape_string($sText);
			$where = "WHERE companyid='".$_SESSION['companyid']."' AND (last_name ILIKE '%$cleanSearch%' OR first_name ILIKE '%$cleanSearch%')";
		} else {
			//default view
			$where = "WHERE companyid='".$_SESSION['companyid']."'";
		} 
		//get all coach information
		$sql = 'SELECT first_name, middle_name, last_name, personid FROM accounts '.$where.' ORDER BY last_name ASC, first_name ASC;';
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
			$error = pg_last_error($conn);
			if ($error) {
				echo('<br />Error! (View Coaches)<br />');
				echo('Type: '.$type.'<br />');
				echo('Where: '.$where.'<br />');
				echo('SQL: '.$sql.'<br />');
				echo('Result: '.$result.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		
		while ($row = pg_fetch_assoc($result)) {
			//gets row values
			$pid = $row['personid'];
			$middleName = '';
			if ($row['middle_name']) {
				$middleName = ' '.$row['middle_name'];
			}
			$coachName = $row['last_name'].', '.$row['first_name'].$middleName;
			$encryptedPID = encrypt($pid);
			//adds coach to list
			$coachList .= '<a href="/Profile/?p='.$encryptedPID.'">'.$coachName.'</a><br />';
		}
		pg_close($conn);
		
		//returns coach list
		return $coachList;
	}

	function viewCompanies($type = 'all',$sText = '',$debug = false) {
		//Loading Includes
		include('includes/db.php');
		if ($type == 'search') {
			//if theres a search apply that
			//Cleans strings
			$cleanSearch = pg_escape_string($sText);
			$where = "WHERE name ILIKE '%$cleanSearch%'";
		}
		//gets all companies info
		$sql = 'SELECT name, companyid, deleted FROM companies '.$where.' ORDER BY name ASC;';
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
			$error = pg_last_error($conn);
			if ($error) {
				echo('<br />Error! (View Companies)<br />');
				echo('Type: '.$type.'<br />');
				echo('Where: '.$where.'<br />');
				echo('SQL: '.$sql.'<br />');
				echo('Result: '.$result.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		
		while ($row = pg_fetch_assoc($result)) {
			//gets row values
			$companyid = encrypt($row['companyid']);
			$name = $row['name'];
			//if marked as deleted mute it
			if ($row['deleted'] == 't') {
				$deleted = ' class="text-muted"';
			} else {
				$deleted = '';
			}
			
			//adds to the list
			$companyList .= '<a href="/Company/?c='.$companyid.'"'.$deleted.'>'.$name.'</a><br />';
		}
		pg_close($conn);
		
		//returns companylist
		return $companyList;
	}

	function addPerson($firstName, $lastName, $email, $cell, $gender, $companyid, $photoid = null, $prefix = null, $suffix = null, $home = null, $work = null, $extension = null, $dob = null, $address = null, $middleName = null, $debug = false) {
		//Loading Includes
		include('includes/db.php');
		//Cleans strings
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
		
		//Converts empty strings to null for postgres
		$eHome = convertEmptyToNull($eHome);
		$eWork = convertEmptyToNull($eWork);
		$eExtension = convertEmptyToNull($eExtension);
		$address = convertEmptyToNull($address);
		$photoid = convertEmptyToNull($photoid);
		
		//adds new person
		$sql = "INSERT INTO persons (photoid, prefix, first_name, last_name, suffix, email, cell, home, work, extension, date_of_birth, addressid, middle_name, companyid, gender, deceased, deleted) VALUES ($photoid, '$ePrefix', '$eFirstName', '$eLastName', '$eSuffix', '$eEmail', '$eCell', $eHome, $eWork, $eExtension, '$dob', $address, '$eMiddleName','$companyid','$eGender',false,false);";
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
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
		//returns last insert id
		return $last_insert_id;
	}

	function addCoach($personid,$surperviser,$pass,$debug = false) {
		//Loading Includes
		include('includes/db.php');
		//Adds new coach
		$sql = "INSERT INTO coaches(personid,supervisor,password) VALUES ('$personid',$surperviser::boolean,'$pass');";
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
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
		//returns last insert id
		return $last_insert_id;
	}

	function addCompany($companyname,$companylocation,$companysite,$pid,$debug = false) {
		//Loading Includes
		include('includes/db.php');
		//Adds a new company
		$sql = "INSERT INTO companies (admin_personid,name,location,domain,deleted) VALUES ('$pid','$companyname','$companylocation','$companysite',false);";
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('result: '.$result.'<br />');
				echo('pid: '.$pid.'<br />');
				echo('companyname: '.$companyname.'<br />');
				echo('companylocation: '.$companylocation.'<br />');
				echo('companysite: '.$companysite.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		
		//Get row
		$insert_query = pg_query($conn,"SELECT lastval();");
		$insert_row = pg_fetch_row($insert_query);
		$last_insert_id = $insert_row[0];
		
		//Cleans strings
		$companyid = pg_escape_string($conn, $last_insert_id);
		
		//Changes the persons
		$sql = "UPDATE persons SET companyid='$companyid' WHERE personid='$pid';";
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('result: '.$result.'<br />');
				echo('pid: '.$pid.'<br />');
				echo('companyname: '.$companyname.'<br />');
				echo('companylocation: '.$companylocation.'<br />');
				echo('companysite: '.$companysite.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		
		pg_close($conn);
		//returns companyid
		return $companyid;
	}

	function addClient($personid,$workaddress,$workcompany = null,$worktitle = null,$workfield = null,$favoritebook = null,$favoritefood = null,$visitpreferencestart = null, $visitpreferenceend = null,$callpreferencestart = null,$callpreferenceend = null,$goals = null,$needs = null,$selfawareness = null,$coachid = null,$debug = false) {
		//Loading Includes
		include('includes/db.php');
		
		//Cleans strings
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
		
		//Converts empty strings to null for postgres
		$workaddress = convertEmptyToNull($workaddress);
		
		//Adds a new client
		$sql = "INSERT INTO clients(personid, work_company, work_address, work_title, work_field, favorite_book, favorite_food, visit_time_preference_start, visit_time_preference_end, call_time_preference_start, call_time_preference_end, goals, needs, selfawareness, coachid) VALUES ('$personid','$workcompany',$workaddress,'$worktitle','$workfield','$favoritebook','$favoritefood',$visitpreferencestart,$visitpreferenceend,$callpreferencestart,$callpreferenceend,'$goals','$needs','$selfawareness','$coachid');";
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
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
		//returns last insert id
		return $last_insert_id;
	}

	function addNote($pid,$postedNote,$clientid,$coachid,$photoid = null,$visitID = null,$debug = false) {
		//Loading Includes
		include('includes/db.php');
		//Cleans strings
		$cleanedNote = pg_escape_string($conn,$postedNote);
		$photoid = "'".$photoid."'";
		$visitID = "'".$visitID."'";
		
		//Converts empty strings to null for postgres
		$photoid = convertEmptyToNull($photoid);
		$visitID = convertEmptyToNull($visitID);
		
		//curent date
		$date = date("Y-m-d H:i:s");
		
		//Adds an notes
		$sql = "INSERT INTO notes(clientid, coachid, visitid, photoid, description, date_added) VALUES ('$clientid','$coachid',$visitID,$photoid,'$cleanedNote','$date');";
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
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
		
		//adds the pid to the return address
		if ($pid) {
			$return = '?p='.$pid;
		}
		
		pg_close($conn);
		//changes location to profile
		header('Location: /Profile'.$return);
		//returns last insert id
		return $last_insert_id;
	}

	function addEvent($pid,$name,$date,$description,$clientid,$coachid,$photoid = null,$debug = false) {
		//Loading Includes
		include('includes/db.php');
		//Cleans strings
		$name = pg_escape_string($conn,$name);
		$date = pg_escape_string($conn,$date);
		$description = pg_escape_string($conn,$description);
		$photoid = "'".$photoid."'";
		
		//Converts empty strings to null for postgres
		$photoid = convertEmptyToNull($photoid);
		
		//curent date
		$dateNow = date("Y-m-d H:i:s");
		
		//Adds an event
		$sql = "INSERT INTO events(clientid, photoid, coachid, name, description, date, date_added) VALUES ('$clientid', $photoid, '$coachid', '$name', '$description', '$date', '$dateNow');";
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('result: '.$result.'<br />');
				echo('clientid: '.$clientid.'<br />');
				echo('photoid: '.$photoid.'<br />');
				echo('coachid: '.$coachid.'<br />');
				echo('name: '.$name.'<br />');
				echo('description: '.$description.'<br />');
				echo('date: '.$date.'<br />');
				echo('date_added: '.$dateNow.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		
		//Get row
		$insert_query = pg_query($conn,"SELECT lastval();");
		$insert_row = pg_fetch_row($insert_query);
		$last_insert_id = $insert_row[0];
		
		//adds the pid to the return address
		if ($pid) {
			$return = '?p='.$pid;
		}
		
		pg_close($conn);
		//changes location to profile
		header('Location: /Profile'.$return);
		//returns last insert id
		return $last_insert_id;
	}

	function addAddress($adressline1,$adressline2,$city,$subdivision,$zip,$country,$debug = false) {
		//Loading Includes
		include('includes/db.php');
		//Cleans strings
		$adressline1 = "'".pg_escape_string($conn,$adressline1)."'";
		$adressline2 = "'".pg_escape_string($conn,$adressline2)."'";
		$city = "'".pg_escape_string($conn,$city)."'";
		$subdivision = "'".pg_escape_string($conn,$subdivision)."'";
		$notCleanZip = pg_escape_string($conn,$zip);
		$country = "'".pg_escape_string($conn,$country)."'";
		
		//Cleans numbers
		$zip = "'".cleanNumber($notCleanZip)."'";
		
		//Converts empty strings to null for postgres
		$adressline1 = convertEmptyToNull($adressline1);
		$adressline2 = convertEmptyToNull($adressline2);
		$city = convertEmptyToNull($city);
		$subdivision = convertEmptyToNull($subdivision);
		$zip = convertEmptyToNull($zip);
		$country = convertEmptyToNull($country);
		
		pg_close($conn);
		//Loading Includes
		include('includes/db.php');
		//Adds address
		$sql = "INSERT INTO addresses(adressline1, adressline2, city, subdivision, zip, country) VALUES ($adressline1,$adressline2,$city,$subdivision,$zip,$country);";
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
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
		//returns last insert id
		return $last_insert_id;
	}

	function changeAddress($addressid,$adressline1,$adressline2,$city,$subdivision,$zip,$country,$debug = false) {
		//Loading Includes
		include('includes/db.php');
		//Cleans strings
		$adressline1 = "'".pg_escape_string($conn,$adressline1)."'";
		$adressline2 = "'".pg_escape_string($conn,$adressline2)."'";
		$city = "'".pg_escape_string($conn,$city)."'";
		$subdivision = "'".pg_escape_string($conn,$subdivision)."'";
		$notCleanZip = pg_escape_string($conn,$zip);
		$country = "'".pg_escape_string($conn,$country)."'";
		
		//Cleans numbers
		$zip = "'".cleanNumber($notCleanZip)."'";
		
		//Converts empty strings to null for postgres
		$adressline1 = convertEmptyToNull($adressline1);
		$adressline2 = convertEmptyToNull($adressline2);
		$city = convertEmptyToNull($city);
		$subdivision = convertEmptyToNull($subdivision);
		$zip = convertEmptyToNull($zip);
		$country = convertEmptyToNull($country);
		
		pg_close($conn);
		//Loading Includes
		include('includes/db.php');
		//Changes the address
		$sql = "UPDATE addresses SET adressline1=$adressline1, adressline2=$adressline2, city=$city, subdivision=$subdivision, zip=$zip, country=$country WHERE addressid='$addressid';";
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
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

	function changeCompany($companyid,$name,$location,$site,$debug = false) {
		//Loading Includes
		include('includes/db.php');
		//Cleans strings
		$name = pg_escape_string($conn,$name);
		$location = pg_escape_string($conn,$location);
		$site = pg_escape_string($conn,$site);
		
		//changes company values
		$sql = "UPDATE companies SET name='$name', location='$location', domain='$site' WHERE companyid='$companyid';";
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
			$error = pg_last_error($conn);
			if ($error) {
				echo('name: '.$name.'<br />');
				echo('adressline1: '.$adressline1.'<br />');
				echo('location: '.$location.'<br />');
				echo('site: '.$site.'<br />');
				echo('result: '.$result.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		pg_close($conn);
	}

	function addVisit($start,$type,$reason,$addressid,$emergency,$debug = false) {
		//Loading Includes
		include('includes/db.php');
		//Cleans strings
		$start = pg_escape_string($conn,$start);
		$addressid = "'".pg_escape_string($conn,$addressid)."'";
		$type = pg_escape_string($conn,$type);
		$reason = pg_escape_string($conn,$reason);
		
		//checks theres a reason
		if (!(strlen($reason) >= 1)) {
			$failed = 'Reason cannont be blank.<br />';
		} 
		//checks theres aa type
		if (!(strlen($type) >= 1)) {
			$failed .= 'Type cannont be blank.<br />';
		} 
		//returns if failed
		if ($failed) {
			return $failed;
		}
		
		//Converts empty strings to null for postgres
		$addressid = convertEmptyToNull($addressid);
		
		//insert visit into table
		$sql = "INSERT INTO visits(date, type, reason, addressid, emergency) VALUES ('$start','$type','$reason',$addressid,'$emergency');";
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
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
		//returns last insert id
		return $last_insert_id;
	}

	function addSchedule($start,$addressid,$personid,$visitid,$end,$debug = false) {
		//Loading Includes
		include('includes/db.php');
		//Cleans strings
		$addressid = "'".pg_escape_string($conn,$addressid)."'";
		$visitid = "'".pg_escape_string($conn,$visitid)."'";
		$end = "'".$end."'";
		
		//Converts empty strings to null for postgres
		$addressid = convertEmptyToNull($addressid);
		$visitid = convertEmptyToNull($visitid);
		$end = convertEmptyToNull($end);
		
		//gets current user ids
		$scheduledby = $_SESSION['coachid'];
		$coachid = $_SESSION['coachid'];
		
		//current date time
		$date = date("Y-m-d H:i:s");
		
		//adds schedule event
		$sql = "INSERT INTO schedule(time_start, addressid, personid, coachid, scheduledby, visitid, time_end, date_added) VALUES ('$start',$addressid,'$personid','$coachid','$scheduledby',$visitid,$end,'$date');";
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
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
		//returns last insert id
		return $last_insert_id;
	}

	function changeSchedule($aid,$start,$type,$reason,$emergency,$end,$debug = false) {
		//Loading Includes
		include('includes/db.php');
		$end = "'".$end."'";
		//Converts empty strings to null for postgres
		$end = convertEmptyToNull($end);
		//updates the scheduled event
		$sql = "UPDATE schedule SET time_start='$start', time_end=$end WHERE scheduleid=$aid;";
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('result: '.$result.'<br />');
				echo('start: '.$start.'<br />');
				echo('end: '.$end.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		//gets the visit id
		$sql = "SELECT visitid FROM schedule WHERE scheduleid=$aid;";
		$result = pg_query($conn, $sql);
		$result = pg_fetch_row($result);
		$vid = $result[0];
		if ($debug) {
			//Debug information
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('result: '.$result.'<br />');
				echo('vid: '.$vid.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		//updates visits table for the scheduled event
		$sql = "UPDATE visits SET date='$start', type='$type', reason='$reason', emergency=$emergency WHERE visitid=$vid;";
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
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
		//Loading Includes
		include('includes/db.php');
		//gets the notes for a client where theyre not deleted
		$sql = 'SELECT * FROM notes WHERE clientid='.$clientid.' AND deleted IS NULL ORDER BY Date_Added ASC;';
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
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
			//goes row by row adding the display together
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
		if (!$notes) {
			//No current notes
			$notes = "Currently No Notes";
		}
		//returns notes for client
		return $notes;
	}

	function viewEvent($clientid,$debug = false) {
		//Loading Includes
		include('includes/db.php');
		//gets the events for a client where theyre not deleted
		$sql = 'SELECT * FROM events WHERE clientid='.$clientid.' AND deleted IS NULL ORDER BY Date_Added ASC;';
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
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
			//goes row by row adding the display together
			if (strlen($events) > 0) {
				$events .= '<br /><br />';
			}
			$eid = $row['eventid'];
			$name = $row['name'];
			$description = $row['description'];
			$date = date('m/d/Y g:i A', strtotime($row['date']));
			$dateAdded = readableDate($row['date_added']);
			
			$events .= '<div class="inline-block" id="'.$eid.'">Name: '.$name.'<br />'
															.'Date: '.$date.'<br />'
						.$description.'</div><br />';
			$events .= '<div class="inline-block"><span class="notesDate">'.$dateAdded.'</span>
                            <a href="/Events?e='.$eid.'" class="btn btn-primary">Delete</a></div>';
		}
		if (!$events) {
			//No current events
			$events = "Currently No Events";
		}
		//returns events for client
		return $events;
	}

	function markNoteAsDeleted($nid,$debug = false) {
		//Loading Includes
		include('includes/db.php');
		//sets the note as deleted
		$sql = 'UPDATE notes SET deleted=true WHERE noteid='.$nid.';';
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('Result: '.$result.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		pg_close($conn);
	}

	function markEventAsDeleted($eid,$debug = false) {
		//Loading Includes
		include('includes/db.php');
		//sets the event as deleted
		$sql = 'UPDATE events SET deleted=true WHERE eventid='.$eid.';';
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
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
		//Loading Includes
		include('includes/db.php');
		//sets the person as deleted
		$sql = 'UPDATE persons SET deleted=true WHERE personid='.$pid.';';
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('Result: '.$result.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		pg_close($conn);
	}
	
	function cleanNumber($number) {
		//cleans the number of extras
		$number = str_replace("e","",$number);
		$number = str_replace(".","",$number);
		$number = str_replace("-","",$number);
		$number = str_replace("+","",$number);
		include('includes/db.php');
		//Cleans strings
		$number = pg_escape_string($conn, $number);
		pg_close($conn);
		//returns the number
		return $number;
	}

	function convertEmptyToNull($string) {
		//return the null value for postgress from our empty strings
		if ($string == "''") {
			//sets to null
			$string = "NULL";
		}
		//returns the correct string
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
		//makes sure the time is a full time value
		if ($t == ':00') {
			//sets a default
			$t = '00:00:00';
		}
		//returns the time
		return $t;
	}

	function addStrTogether($s1,$s2) {
		//Adds two string together
		if (strlen($s1) > 0) {
			//adds the first string
			$r = $s1;
		}
		if (strlen($r) > 0 && strlen($s2) > 0) {
			//Adds the second string to the first string
			$r = $r.' '.$s2;
		} else if (strlen($s2) > 0) {
			//Adds the second string
			$r = $s2;
		}
		//returns the string
		return $r;
	}

	function addExtToNumber($n,$e) {
		//adds ext to the number for calling, only if there is a number
		if (strlen($s1) > 0) {
			//adds the number
			$r = $s1;
		}
		if (strlen($r) > 0 && strlen($s2) > 0) {
			//adds ext with p
			$r = $r.'p'.$s2;
		}
		//returns the number
		return $r;
	}

	function addExtToNumberWithEXT($n,$e) {
		//adds ext to the number, only if there is a number
		if (strlen($s1) > 0) {
			//adds the number
			$r = $s1;
		}
		if (strlen($r) > 0 && strlen($s2) > 0) {
			//adds ext with ext
			$r = $r.' ext. '.$s2;
		}
		//returns the number
		return $r;
	}

	function getAddress($aid) {
		//Returns a formated address from the database
		//gets data from the table
		$address = view('addresses','addressid='.$aid);
		//if the work exists build display
		if ($address['addressid']) {
			$returnAddress = $address['adressline1'].'<br />';
			if (strlen($address['adressline2']) > 0) {
				//if adressline2 then breakline after
				$returnAddress .= $address['adressline2'].'<br />';
			}
			//makes the display
			$returnAddress .= $address['city'].', '.$address['subdivision'].' '.$address['zip'].' '.$address['country'].'<br />'.'<a href="/Address?a='.encrypt($aid).'" class="btn btn-primary">Edit Address</a>';
			if (!isset($address['adressline1']) || !isset($address['city']) || !isset($address['subdivision']) || !isset($address['zip']) || !isset($address['country'])) {
				//if not complete display that
				$returnAddress = 'This address is not complete. Please correct it.<br />'.'<a href="/Address?a='.encrypt($aid).'" class="btn btn-primary">Edit Address</a>';
			}
		}
		//returns the formated address from the database
		return $returnAddress;
	}

	function backButton() {
		//simple back button address
		return $_SERVER['HTTP_REFERER'];
	}

	function readableDate($date) {
		//A readable Date in EST
		$UTC = date_create($date, timezone_open('UTC'));
		$EST = date_timezone_set($UTC, timezone_open('America/New_York'));
		$date = date_format($EST, 'm/d/Y g:i A');
		//returns the date in readable EST format
		return $date;
	}

	function readableDateNoTZ($date) {
		//A readable Date in UTC
		$UTC = date_create($date, timezone_open('UTC'));
		$date = date_format($UTC, 'm/d/Y g:i A');
		//returns the date in readable UTC format
		return $date;
	}

	function formatAddress($aid,$line1,$line2,$city,$subdivision,$zip,$country) {
		if (strlen($line2) > 1) {
			//if there is a line2 add a break after
			$cLine2 = "$line2<br />";
		}
		if (strlen($line1) > 1) {
			//if is a line1 then build the display
			$address = "$line1<br />$cLine2
						$city, $subdivision $zip $country<br />".'<a href="/Address?a='.encrypt($aid).'" class="btn btn-primary">Edit Address</a>';
			if (!isset($address['adressline1']) || !isset($address['city']) || !isset($address['subdivision']) || !isset($address['zip']) || !isset($address['country'])) {
				$returnAddress = 'This address is not complete. Please correct it.<br />'.'<a href="/Address?a='.encrypt($aid).'" class="btn btn-primary">Edit Address</a>';
			}
		}
		//returns the address in readable format
		return $address;
	}

	function changeProfile($pid, $firstname, $lastname, $email, $cell, $gender, $prefix, $suffix, $home, $worknumber, $extension, $correctDOB, $middlename, $workcompany, $worktitle, $workfield, $favoritebook, $favoritefood, $visitpreferencestart, $visitpreferenceend, $callpreferencestart, $callpreferenceend, $goals, $needs, $selfawareness, $supervisor, $employeed, $deceased, $debug = false) {
		//Loading Includes
		include('includes/db.php');
		//Cleans strings
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
		
		//Converts empty strings to null for postgres
		$eHome = convertEmptyToNull($eHome);
		$eWork = convertEmptyToNull($eWork);
		$eExtension = convertEmptyToNull($eExtension);
		
		//Editing a person from there id
		$sql = "UPDATE persons SET prefix='$ePrefix', first_name='$eFirstName', last_name='$eLastName', suffix='$eSuffix', email='$eEmail', cell='$eCell', home=$eHome, work=$eWork, extension=$eExtension, date_of_birth='$correctDOB', middle_name='$eMiddleName', gender='$eGender', deceased=$deceased::boolean WHERE personid='$pid';";
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
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
		
		//Cleans strings
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
		
		
		//Editing a client from there id
		$sql = "UPDATE clients SET work_company='$workcompany', work_title='$worktitle', work_field='$workfield', favorite_book='$favoritebook', favorite_food='$favoritefood', visit_time_preference_start=$visitpreferencestart, visit_time_preference_end=$visitpreferenceend, call_time_preference_start=$callpreferencestart, call_time_preference_end=$callpreferenceend, goals='$goals', needs='$needs', selfawareness='$selfawareness' WHERE personid='$pid';";
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
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
		
		//figures out if they are a coach to attempt the change
		$coachResult  = view('coaches','personid='.$pid);
		if ($coachResult['coachid']) {
		//Loading Includes
		include('includes/db.php');
			//Editing a coach from there id
			$sql = "UPDATE coaches SET supervisor=$supervisor::boolean, employeed=$employeed::boolean WHERE personid='$pid';";
			$result = pg_query($conn, $sql);
			if ($debug) {
			//Debug information
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
			//Not a coach no change
			echo('They are not a Coach, no attempt to change.<br />');
		}
	}

	function mostRecentTimeContacted($clientid,$debug = false) {
		//returns time a client was contacted 
		include('includes/db.php');
		//Gets the last datetime a client was contacted at
		$sql = "select date_added as date from events where clientid='$clientid' AND date_added <= now()
				union
				select date_added as date from notes where clientid='$clientid' AND date_added <= now()
				union
				select date_added as date from schedule_client where clientid='$clientid' AND date_added <= now()
				order by date desc
				limit 1;";
		$result = pg_query($conn, $sql);
		$data = pg_fetch_assoc($result);
		//gets last date in raw form
		$dateRaw = $data['date'];
		if (!isset($dateRaw)) {
			//They were never contacted
			$date = 'Never Contacted';
		} else {
			//gets a readable date
			$date = readableDate($dateRaw);
		}
		if ($debug) {
			//Debug information
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('result: '.$result.'<br />');
				echo('date raw: '.$dateRaw.'<br />');
				echo('date: '.$date.'<br />');
				echo('clientid: '.$clientid.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		pg_close($conn);
		//returns last contacted date
		return $date;
	}

	function mostRecentContact($coachid,$debug = false) {
		//returns most recently contacted clientid 
		include('includes/db.php');
		include('../protection.php');
		//Gets the most recent contacted client
		$sql = "select date_added as date, clientid from events where coachid='$coachid' AND date_added <= now()
				union
				select date_added as date, clientid from notes where coachid='$coachid' AND date_added <= now()
				union
				select date_added as date, clientid from schedule_client where coachid='$coachid' AND date_added <= now()
				order by date desc
				limit 1;";
		$result = pg_query($conn, $sql);
		$data = pg_fetch_assoc($result);
		//Gets the client id
		$clientid = $data['clientid'];
		//gets the readable date
		$date = readableDate($data['date']);
		if (!isset($clientid)) {
			//No last contact
			$r = 'None';
		} else {
			$clientResult = view('clients','clientid='.$clientid);
			$pid = $clientResult['personid'];
			$personResult = view('persons','personid='.$pid);

			//Adds the name together
			$name = addStrTogether($personResult['prefix'],$personResult['first_name']);
			$name = addStrTogether($name,$personResult['middle_name']);
			$name = addStrTogether($name,$personResult['last_name']);
			$name = addStrTogether($name,$personResult['suffix']);
			//the last contacted client display
			$r = '<a href="/Profile?p='.encrypt($pid).'">'.$name.'</a><br />'.$date;
		}
		if ($debug) {
			//Debug information
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('result: '.$result.'<br />');
				echo('coachid: '.$coachid.'<br />');
				echo('clientid: '.$clientid.'<br />');
				echo('date: '.$date.'<br />');
				echo('return: '.$r.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		pg_close($conn);
		return $r;
	}

	function getRelationship($pid,$familyRelation,$pos = '-1',$debug=false) {
		//Loading Includes
		include('includes/db.php');
		include('../protection.php');
		//Defines which pulling 
		if ($familyRelation == 'parent') {
			$relationship = '1';
		} else if ($familyRelation == 'child') {
			$relationship = '2';
		} else if ($familyRelation == 'spouse') {
			$relationship = '3';
		}
		//Gets the id and other person based on the type of relation and who person 1 is
		$sql = "SELECT relationshipid,personid2 FROM relationships WHERE personid1='$pid' AND relationship='$relationship';";
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('pid: '.$pid.'<br />');
				echo('familyRelation: '.$familyRelation.'<br />');
				echo('pos: '.$pos.'<br />');
				echo('relationship: '.$relationship.'<br />');
				echo('result: '.$result.'<br />');
				echo('data: '.$data.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		//Defines how to build the family list
		if ($relationship == '2') {
			if ($pos == '-1') {
				//pulls all rows
				while ($row = pg_fetch_assoc($result)) {
					$familypid = $row['personid2'];
					$relationshipid = $row['relationshipid'];
					if (isset($familypid)) {
						//Returns the table rows of all people
						$personResult = view('persons','personid='.$familypid);

						//Adds the name together
						$name = addStrTogether($personResult['first_name'],$personResult['middle_name']);
						$name = addStrTogether($name,$personResult['last_name']);

						$r .= '<tr><td>&thinsp;</td><td><a href="/Profile?p='.encrypt($familypid).'">'.$name.'</a></td><td><a href="/EditRelationship?p='.encrypt($pid).'&d='.$relationshipid.'" class="btn btn-primary">Delete</a></td></tr>';
					}
				}
			} else {
				//Returns the table row of a postion row
				$data = pg_fetch_assoc($result,$pos);
				$familypid = $data['personid2'];
				$relationshipid = $data['relationshipid'];
				if (isset($familypid)) {
					$personResult = view('persons','personid='.$familypid);

					//Adds the name together
					$name = addStrTogether($personResult['first_name'],$personResult['middle_name']);
					$name = addStrTogether($name,$personResult['last_name']);

					$r = '<tr><td>&thinsp;</td><td><a href="/Profile?p='.encrypt($familypid).'">'.$name.'</a></td><td><a href="/EditRelationship?p='.encrypt($pid).'&d='.$relationshipid.'" class="btn btn-primary">Delete</a></td></tr>';
				}
			}
		} else {
			//Returns the table row of of the requested person
			$data = pg_fetch_assoc($result,$pos);
			$familypid = $data['personid2'];
			$relationshipid = $data['relationshipid'];
			if (isset($familypid)) {
				$personResult = view('persons','personid='.$familypid);

				//Adds the name together
				$name = addStrTogether($personResult['first_name'],$personResult['middle_name']);
				$name = addStrTogether($name,$personResult['last_name']);

				$r = '<td><a href="/Profile?p='.encrypt($familypid).'">'.$name.'</a></td><td><a href="/EditRelationship?p='.encrypt($pid).'&d='.$relationshipid.'" class="btn btn-primary">Delete</a></td>';
			} else {
				$r = '<td>None</td><td><a href="/EditRelationship?r='.$relationship.'&p='.encrypt($pid).'" class="btn btn-primary">Add</a></td>';
			}
		}
		pg_close($conn);
		return($r);
	}

	function addRelationship($pid,$relationshipType,$newPersionID,$debug=false) {
		//Loading Includes
		include('includes/db.php');
		//Clean strings
		$pid = pg_escape_string($conn, $pid);
		$relationshipType = pg_escape_string($conn, $relationshipType);
		$newPersionID = pg_escape_string($conn, $newPersionID);
		
		//Adds the first relation
		$sql = "INSERT INTO relationships(personid1, relationship, personid2) VALUES ('$pid','$relationshipType','$newPersionID');";
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
			$error = pg_last_error($conn);
			if ($error) {
				echo('First<br />');
				echo('SQL: '.$sql.'<br />');
				echo('result: '.$result.'<br />');
				echo('personid1: '.$pid.'<br />');
				echo('relationship: '.$relationshipType.'<br />');
				echo('personid2: '.$newPersionID.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		
		//Flips relationship for adding the other way
		if ($relationshipType == '1') {
			$otherRelationship = '2';
		} else if ($relationshipType == '2') {
			$otherRelationship = '1';
		} else {
			$otherRelationship = $relationshipType;
		}
		
		//Adds the second relation
		$sql = "INSERT INTO relationships(personid1, relationship, personid2) VALUES ('$newPersionID','$otherRelationship','$pid');";
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
			$error = pg_last_error($conn);
			if ($error) {
				echo('Second<br />');
				echo('SQL: '.$sql.'<br />');
				echo('result: '.$result.'<br />');
				echo('personid1: '.$newPersionID.'<br />');
				echo('relationship: '.$otherRelationship.'<br />');
				echo('personid2: '.$pid.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		pg_close($conn);
	}

	function removeRelationship($returnLink,$relationshipID,$debug=false) {
		//Loading Includes
		include('includes/db.php');
		//Clean strings
		$relationshipID = pg_escape_string($conn, $relationshipID);
		
		//Gets the personids for second relation removal
		$sql = "SELECT relationship,personid1,personid2 FROM relationships WHERE relationshipid='$relationshipID';";
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('pid: '.$pid.'<br />');
				echo('familyRelation: '.$familyRelation.'<br />');
				echo('pos: '.$pos.'<br />');
				echo('relationship: '.$relationship.'<br />');
				echo('result: '.$result.'<br />');
				echo('data: '.$data.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		//Sets data for removal
		$data = pg_fetch_assoc($result);
		$relationship = $data['relationship'];
		$personid2 = $data['personid2'];
		$personid1 = $data['personid1'];
		
		//removes the first relation
		$sql = "DELETE FROM relationships WHERE relationshipid='$relationshipID';";
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('result: '.$result.'<br />');
				echo('relationshipID: '.$relationshipID.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		
		//Flips relationship for removing the other way
		if ($relationship == '1') {
			$otherRelationship = '2';
		} else if ($relationship == '2') {
			$otherRelationship = '1';
		} else {
			$otherRelationship = $relationship;
		}
		
		//Removes the second relation
		$sql = "DELETE FROM relationships WHERE personid1='$personid2' AND relationship='$otherRelationship' AND personid2='$personid1';";
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('result: '.$result.'<br />');
				echo('relationshipID: '.$relationshipID.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		pg_close($conn);
		//Returns you to where you were
		header('Location: '.$returnLink);
	}

	function changeCoach($pid,$newCoachID,$debug = false) {
		//Changes the coach from one to another
		//Loading Includes
		include('includes/db.php');
		//Clean strings
		$pid = pg_escape_string($conn,$pid);
		$newCoachID = pg_escape_string($conn,$newCoachID);
		
		$sql = "UPDATE clients SET coachid='$newCoachID' WHERE personid='$pid';";
		$result = pg_query($conn, $sql);
		if ($debug) {
			//Debug information
			$error = pg_last_error($conn);
			if ($error) {
				echo('SQL: '.$sql.'<br />');
				echo('newCoachID: '.$newCoachID.'<br />');
				echo('pid: '.$pid.'<br />');
				echo('result: '.$result.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		pg_close($conn);
	}

	function getCompanyLink() {
		//Returns the encrypted company link
		//Loading Includes
		include('../protection.php');
		return '/Company?c='.encrypt($_SESSION['companyid']);
	}

	function getPersonName($pid) {
		//Returns persons Name and adds it together
		$result = view('persons','personid='.$pid);
		//Adds the name together
		$name = addStrTogether($result['prefix'],$result['first_name']);
		$name = addStrTogether($name,$result['middle_name']);
		$name = addStrTogether($name,$result['last_name']);
		$name = addStrTogether($name,$result['suffix']);
		return $name;
	}
?>