<?php
	$upload_image_text;
	function resizeImage($w,$h, $conn) {
		$tmpName  = $_FILES['image']['tmp_name'];
		$fileType = $_FILES['image']['type'];
		switch(strtolower($fileType)) {
			case 'image/jpeg':
				$image = imagecreatefromjpeg($tmpName);
				$t = 'jpg';
				break;
			case 'image/png':
				$image = imagecreatefrompng($tmpName);
				$t = 'png';
				break;
			case 'image/gif':
				$image = imagecreatefromgif($tmpName);
				$t = 'gif';
				break;
			default:
		}
		// Target dimensions
		$max_width = $w;
		$max_height = $h;

		// Get current dimensions
		$old_width  = imagesx($image);
		$old_height = imagesy($image);

		// Calculate the scaling we need to do to fit the image inside our frame
		$scale      = min($max_width/$old_width, $max_height/$old_height);

		// Get the new dimensions
		$new_width  = ceil($scale*$old_width);
		$new_height = ceil($scale*$old_height);
		// Create new empty image
		$new = imagecreatetruecolor($new_width, $new_height);

		// Resize old image into new
		imagecopyresampled($new, $image, 
			0, 0, 0, 0, 
			$new_width, $new_height, $old_width, $old_height);
		
		// Catch the imagedata
		ob_start();
		if ($t == 'jpg') {
			imagejpeg($new, NULL, 90);
		} else if ($t == 'png') {
			imagepng($new, NULL, 9, PNG_NO_FILTER);
		} else if ($t == 'gif') {
			imagegif($new, NULL);
		}
		
		$data = ob_get_clean();
		
		// Destroy resources
		imagedestroy($image);
		imagedestroy($new);
		return (pg_escape_bytea($conn, $data));
	}

	function uploadImage($debug = true) {
		include('includes/db.php');
		include('includes/protection.php');
		include('includes/mailer.php');
		include('includes/log.php');
		
		global $upload_image_text;
		
		if($_FILES['image']['size'] > 0) {
			$date = date("Y-m-d H:i:s");
			$personid = $_SESSION['personid'];
			$email = $_SESSION['email'];
			$fileName = $_FILES['image']['name'];
			$tmpName  = $_FILES['image']['tmp_name'];
			$fileSize = $_FILES['image']['size'];
			$fileType = $_FILES['image']['type'];
			$fileErrorMsg = $_FILES['image']['error'];

			$fp      = fopen($tmpName, 'r');
			$content = fread($fp, filesize($tmpName));
			$content = pg_escape_bytea($conn, $content);
			fclose($fp);

			if(!get_magic_quotes_gpc()) {
				$fileName = pg_escape_string($fileName);
			}
			
			$uploadOk = 1;
			
			if ($fileErrorMsg) {
				$upload_image_text .= "An error occured while processing the file. Try again.<br />";
				$uploadOk = 0;
			}
			if ($uploadOk && $fileSize > 5120000) {
				$upload_image_text .= "Sorry, your file is too large.<br />";
				$uploadOk = 0;
			}
			if ($uploadOk) {
				$check = getimagesize($tmpName);
				if($check !== false) {
					if ($debug) {
						$upload_image_text .= "File is an image - " . $check["mime"] . ".<br />";
					}
				} else {
					$upload_image_text .= "File is not an image.<br />";
					$uploadOk = 0;
				}
				$target_file = basename($fileName);
				$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
				if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $uploadOk) {
					$upload_image_text .=  "Sorry, only JPG, PNG, & GIF files are allowed.<br />";
					$uploadOk = 0;
				}
			}
			if ($uploadOk) {
				$image160 = resizeImage(160,160,$conn);
				$image240 = resizeImage(240,240,$conn);
				//$image160 = 0;
				//$image240 = 0;
				$sql = "INSERT INTO photos (uploader_personid, uploaddate, mimetype, file, thumbnail160, thumbnail240) VALUES ('$personid', '$date', '$fileType', '$content'::bytea, '$image160'::bytea, '$image240'::bytea)";
				if (!pg_query($conn,$sql)) {
					$readdate = date("Y-m-d H:i:s A");
					$b = '<br />';
					$e .= "Image upload failed".$b.$b;
					$e .= "When: ".$readdate.$b.$b;

					$e .= "Uploader ID: ".$id.$b;
					$e .= "MIME Type: ".$fileType.$b.$b.$b.$b;

					$e .= "Image upload failed: " . pg_last_error($conn).$b;
					if ($debug) {
						$upload_image_text .= $e;
					} else {
						$upload_image_text .= "Image upload failed: " . pg_last_error($conn).$b;
					}
					
					my_mailer('Chris@ChrisSiena.com', 'File Upload ERROR', $e);
					} else {
					
					$insert_query = pg_query($conn,"SELECT lastval();");
					$insert_row = pg_fetch_row($insert_query);
					$imageID = $insert_row[0];
					
					$upload_image_text = "Image uploaded!<br />";
					o_log('Image Uploaded','Image ID: '.$imageID);
					};
			}
			$upload_image_text .= "<br />";
		}
		pg_close($conn);
		return $imageID;
	}

	function buildFullImageForm() {
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
		return('Image: '.$o.' &thinsp;
				<input type="hidden" name="MAX_FILE_SIZE" value="5120000">
				<input name="image" type="file"><br />
				The photo can not be any larger then 5MB.<br />
				The photo types supported are JPG, PNG, & GIF.<br />
				May take up to 5 minutes as the server processes the image.<br /><br />');
	}
?>