<?php
	function view($table,$where = '',$debug = false){
		include('includes/db.php');
		$escapedTable = pg_escape_string($conn, $table);
		if (strlen($where) > 1) {
			$escapedWhere = pg_escape_string($conn, $where);
			$fullWhere = ' WHERE '.$escapedWhere;
		} else {
			$fullWhere = '';
		}
		$sql = 'SELET * FROM '.$escapedTable.$fullWhere.';';
		$result = pg_query($conn, $sql);
		$data = pg_fetch_assoc($data);
		if ($debug) {
			$error = pg_last_error($conn);
			if ($error) {
				echo('<br />Error!<br />');
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

	function addPerson($firstName, $lastName, $email, $cell, $photoid = null, $prefix = null, $suffix = null, $home = null, $work = null, $extension = null, $dob = null, $address = null, $middleName = null, $debug = false){
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
		
		$sql = "INSERT INTO persons (photoid, prefix, first_name, last_name, suffix, email, cell, home, work, extension, date_of_birth, address, middle_name) VALUES ($photoid, '$ePrefix', '$eFirstName', '$eLastName', '$eSuffix', '$eEmail', '$eCell', $eHome, $eWork, $eExtension, '$dob', $address, '$eMiddleName');";
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

	function addCoach($personid,$clientid,$companyid,$surperviser,$pass,$debug = false){
		include('includes/db.php');
		$clientid = "'".$clientid."'";
		$clientid = convertEmptyToNull($clientid);
		$sql = "INSERT INTO coaches(personid,clientid,companyid,superviser,password) VALUES ('$personid',$clientid,'$companyid',$surperviser::boolean,'$pass');";
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
	
	function cleanPhoneNumber($number) {
		$number = str_replace(".","",$number);
		$number = str_replace("-","",$number);
		$number = str_replace("+","",$number);
		return $number;
	}

	function convertEmptyToNull($string) {
			if ($string == "''") {
				$string = "NULL";
			}
			return $string;
		}
	
?>