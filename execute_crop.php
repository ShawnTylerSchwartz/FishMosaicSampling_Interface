<?php 
	session_start();

	header("refresh: 0.1; url=fish_list.php?" . SID);
	echo '<script type="text/javascript">alert("Fish subsample has been successfully saved! Resetting interface...");</script>';
	
	include 'snippets/header.php';
	include 'snippets/main.php';

	$current_image = $_GET['image'];
	$scaled_width = $_GET['swidth'];
	$scaled_height = $_GET['sheight'];

	$seshID = session_id();

	$date   = new DateTime();
	$readableDate = $date->format('m-d-Y,h:i:sa');


	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$desired_sampling_size = 175;

		$targ_w = $targ_h = $desired_sampling_size;
		$jpeg_quality = 100;

		$img_r = imagecreatefromjpeg($_GET['image']);
		$dst_r = ImageCreateTrueColor($scaled_width, $scaled_height);

		// Resize on basis of scale factor
		list($width, $height) = getimagesize($_GET['image']);
		imagecopyresized($dst_r, $img_r, 0, 0, 0, 0, $scaled_width, $scaled_height, $width, $height);

		$final = imagecreatetruecolor($targ_w, $targ_h);

		imagecopyresampled($final, $dst_r, 0, 0, $_POST['x'], $_POST['y'], $targ_w, $targ_h, $_POST['w'], $_POST['h']);

		// header('Content-type: image/jpeg');	

		function output_image($image_file) {
    		header('Content-Length: ' . filesize($image_file));
			// imagejpeg($final,null,$jpeg_quality); //display image to browser window (viewport)
    		ob_clean();
    		flush();
    		readfile($image_file);
		}

		// make filename equivalent to prior naming scheme of input file
		$file = $_GET['image'];
		$nospace = explode(" ", $file);
		$noslash = explode("/", $nospace[0]);
		$exportPath = "fish_output/" . $noslash[1] . "/";
		// $name = md5($file) . ".jpg";
		$name = $noslash[2] . "-" . $nospace[1];

		// make directory for family output if doesn't already exist
		if(!is_dir($exportPath)) {
			mkdir($exportPath);
		}

		// $image_file = "fish_output/" . $name;
		$image_file = $exportPath . $name;

		$txt = "_outputData.html";

		if(!file_exists($image_file)) {
   			imagejpeg($final, $image_file);

   			$fh = fopen($txt, 'a'); 
    		$txt=$file.','.$name . ',' . $seshID . ',' . round($scaled_width) . 'x' . round($scaled_height) . ',' . $readableDate . '<hr />'; 
    		fwrite($fh,$txt); // Write information to the file
    		fclose($fh); // Close the file

   			imagedestroy($final);
		}

		output_image($image_file);

		exit;
	}
?>