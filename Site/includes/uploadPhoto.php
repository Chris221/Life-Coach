<?php
	function uploadImage($debug = false) {
		//uploads the image to the database
		//Loading Includes
		include('includes/db.php');
		include('db.php');
		
		if($_FILES['image']['size'] > 0) {
			//if the size exists then upload
			//gets current date
			$date = date("Y-m-d H:i:s");
			//gets the file
			$personid = $_SESSION['personid'];
			$fileName = $_FILES['image']['name'];
			$tmpName  = $_FILES['image']['tmp_name'];
			$fileSize = $_FILES['image']['size'];
			$fileType = $_FILES['image']['type'];
			$fileErrorMsg = $_FILES['image']['error'];
			//gets the data from the file
			$data = file_get_contents($tmpName);
			//cleans the data for entry into the database
			$escaped = pg_escape_bytea($data);
			//default for uploading
			$uploadOk = 1;
			
			if ($fileErrorMsg) {
				//confirms there is no error with the file
				$upload_image_text .= "An error occured while processing the file. Try again.<br />";
				$uploadOk = 0;
			}
			if ($uploadOk && $fileSize > 5120000) {
				//checks file size
				$upload_image_text .= "Sorry, your file is too large.<br />";
				$uploadOk = 0;
			}
			if ($uploadOk) {
				//checks image size
				$check = getimagesize($tmpName);
				if($check !== false) {
					if ($debug) {
						//Debug information
						$upload_image_text .= "File is an image - " . $check["mime"] . ".<br />";
					}
				} else {
					$upload_image_text .= "File is not an image.<br />";
					return $upload_image_text;
				}
			}
			if ($uploadOk) {
				//adds new image
				$sql = "INSERT INTO photos (uploader_personid, uploaddate, mimetype, file) VALUES ('$personid', '$date', '$fileType', '$escaped')";
				$result = pg_query($conn, $sql);
				if ($debug) {
					//Debug information
					$error = pg_last_error($conn);
					if ($error) {
						echo('SQL: '.$sql.'<br />');
						echo('result: '.$result.'<br />');
						echo('personid: '.$personid.'<br />');
						echo('date: '.$date.'<br />');
						echo('fileType: '.$fileType.'<br />');
						echo('Error: '.$error.'<br />');
					}
				}
				//Get row
				$insert_query = pg_query($conn,"SELECT lastval();");
				$insert_row = pg_fetch_row($insert_query);
				$imageID = $insert_row[0];
			}
		}
		
		pg_close($conn);
		//returns image id
		return $imageID;
	}

	function buildFullImageForm() {
		//builds image form
		echo('
			<form action="#" method="post" enctype="multipart/form-data">
				Image:&thinsp;
				<input type="hidden" name="MAX_FILE_SIZE" value="5120000">
				<input name="image" type="file"> 
				<input name="upload" type="submit" value=" Upload "><br />
			</form><br />
			The photo can not be any larger then 5MB.<br />
			The photo types supported are JPG, PNG, & GIF.<br />
			May take up to 5 minutes as the server processes the image.<br />
			
		');
	}

	function buildImageForm($o = '') {
		//builds image form with image
		return('Image: '.$o.' &thinsp;
				<input type="hidden" name="MAX_FILE_SIZE" value="5120000">
				<input name="image" type="file"><br />
				The photo can not be any larger then 5MB.<br />
				The photo types supported are JPG, PNG, & GIF.<br />
				May take up to 5 minutes as the server processes the image.<br /><br />');
	}
?>