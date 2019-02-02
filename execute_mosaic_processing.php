<?php 
	session_start();

	$user_email = $_GET['user'];

	header("refresh: 0.1; url=fish_list.php?user=" . $user_email . "&" . SID);

	include 'snippets/header.php';
	include 'snippets/main.php';

	$current_image = $_GET['image'];

	

	$scaled_width = $_GET['swidth'];
	$scaled_height = $_GET['sheight'];
	$original_width = $_GET['owidth'];
	$original_height = $_GET['oheight'];

	// fish measurement vars
	$sl = $_GET['standardlength'];
	$sl_frontpoint_pos_x = $_GET['frontpx'];
	$sl_frontpoint_pos_y = $_GET['frontpy'];
	$sl_midpoint_pos_x = $_GET['midpx'];
	$sl_midpoint_pos_y = $_GET['midpy'];
	$sl_endpoint_pos_x = $_GET['backpx'];
	$sl_endpoint_pos_y = $_GET['backpy'];
	$topmost_pos_x = $_GET['toppx'];
	$topmost_pos_y = $_GET['toppy'];
	$bottommost_pos_x = $_GET['bottompx'];
	$bottommost_pos_y = $_GET['bottompy'];
	$gill_slit_pos_x = $_GET['gillslitpx'];
	$gill_slit_pos_y = $_GET['gillslitpy'];

	$posterior_eye_pos_x  = $_GET['posterioreyepx'];
	$posterior_eye_pos_y = $_GET['posterioreyepy'];

	$ventral_eye_pos_x = $_GET['ventraleyepx'];
	$ventral_eye_pos_y = $_GET['ventraleyepy'];

	$mosaic_box_w = $_GET['mboxw'];
	$fish_height = $_GET['fishheight'];
	$fish_height_midpoint_pos_x = $_GET['heightmpx'];

	$edge_shift_factor_const = 0.1;
	$sl_edge_shift_factor = $sl * $edge_shift_factor_const;
	$hight_shift_factor_const = 0.1;
	$height_edge_shift_factor = $fish_height * $hight_shift_factor_const;

	// Choose standard y-coord from SL for allignment
	$std_sl_y_loc = $sl_midpoint_pos_y;

	$midpoint_square = ($mosaic_box_w / 2);

	$file_output_counter = 0;

	// Adjacent to gill slit of fish along SL line (1)
	execute_crop_fish($_GET['image'], $scaled_width, $scaled_height, ($gill_slit_pos_x-$mosaic_box_w), $gill_slit_pos_y, $mosaic_box_w, 1, $user_email);
	$file_output_counter++;
	// Midpoint of fish along fish height line; i.e., the intersection point between height line and SL line (2)
	// execute_crop_fish($_GET['image'], $scaled_width, $scaled_height, $fish_height_midpoint_pos_x, $std_sl_y_loc, $mosaic_box_w, 2, $user_email);
	execute_crop_fish($_GET['image'], $scaled_width, $scaled_height, $gill_slit_pos_x, $gill_slit_pos_y, $mosaic_box_w, 2, $user_email);
	$file_output_counter++;
	// Midpoint of fish along SL line (3)
	execute_crop_fish($_GET['image'], $scaled_width, $scaled_height, ($sl_midpoint_pos_x+$midpoint_square), $std_sl_y_loc, $mosaic_box_w, 3, $user_email);
	$file_output_counter++;
	// Tailend of fish along SL line (shifted inwards 30% of fish SL) (4)
	execute_crop_fish($_GET['image'], $scaled_width, $scaled_height, ($sl_endpoint_pos_x-$sl_edge_shift_factor)-$mosaic_box_w, ($sl_endpoint_pos_y-$midpoint_square), $mosaic_box_w, 4, $user_email);
	$file_output_counter++;
	// Topmost portion of fish along fish height (shifted downwards 30% of fish height) (5)
	execute_crop_fish($_GET['image'], $scaled_width, $scaled_height, $topmost_pos_x, ($topmost_pos_y+abs($height_edge_shift_factor)), $mosaic_box_w, 5, $user_email);
	$file_output_counter++;
	// Bottommost portion of fish along fish height (shifted upwards 30% of fish height) (6)
	execute_crop_fish($_GET['image'], $scaled_width, $scaled_height, $bottommost_pos_x, ($bottommost_pos_y-abs($height_edge_shift_factor))-$mosaic_box_w, $mosaic_box_w, 6, $user_email);
	$file_output_counter++;

	// Posterior Eye 
		execute_crop_fish($_GET['image'], $scaled_width, $scaled_height, $posterior_eye_pos_x, $posterior_eye_pos_y-$midpoint_square, $mosaic_box_w / 2, 7, $user_email);
	$file_output_counter++;

	// Ventral Eye
	execute_crop_fish($_GET['image'], $scaled_width, $scaled_height, $ventral_eye_pos_x-$midpoint_square, $ventral_eye_pos_y, $mosaic_box_w / 2, 8, $user_email);
	$file_output_counter++;

	echo '<script type="text/javascript">alert("' . $file_output_counter . ' Fish mosaic samples have been successfully saved! Serving next fish...Press OK to continue!");</script>';


	exit;





	function output_image($image_file) {
		header('Content-Length: ' . filesize($image_file));
		// imagejpeg($final,null,$jpeg_quality); //display image to browser window (viewport)
		ob_clean();
		flush();
		readfile($image_file);
	}

	/**
		function execute_crop_fish():
		@param $fish: image path for image to subsample mosaic out of
		@param $fish_ws: scaled width of fish image
		@param $fish_hs: scaled height of fish image
		@param $pos_x: x coordinate position for crop box
		@param $pos_y: y coordinate position for crop box
		@param $fish_mosaic_w: width of fish mosaic output, and therefore height since a square
		@param $boxnum: number for region of fish for uniform output
		@param $useraddress: stores username or email of the current user for output log purposes
	*/
	function execute_crop_fish($fish, $fish_ws, $fish_hs, $pos_x, $pos_y, $fish_mosaic_w, $boxnum, $useraddress) {
		$seshID = session_id();

		$date   = new DateTime();
		$readableDate = $date->format('m-d-Y,h:i:sa');

		$targ_w = $targ_h = $fish_mosaic_w;
		$jpeg_quality = 100;

		$img_r = imagecreatefromjpeg($fish);
		$dst_r = ImageCreateTrueColor($fish_ws, $fish_hs);

		// Resize on basis of scale factor
		list($width, $height) = getimagesize($fish);
		imagecopyresized($dst_r, $img_r, 0, 0, 0, 0, $fish_ws, $fish_hs, $width, $height);

		$final = imagecreatetruecolor($targ_w, $targ_h);

		imagecopyresampled($final, $dst_r, 0, 0, $pos_x, $pos_y, $targ_w, $targ_h, $targ_w, $targ_h);

		// header('Content-type: image/jpeg');	

		

		// make filename equivalent to prior naming scheme of input file
		$file = $fish;
		$nospace = explode(" ", $file);
		$noslash = explode("/", $nospace[0]);
		$sepgenus = explode("_", $noslash[2]);
		$exportPath = "fish_output/" . $boxnum . "_" . $noslash[1] . "/";
		// $name = md5($file) . ".jpg";
		$name = $noslash[1] . "_" . $sepgenus[1] . "_" . $nospace[1];

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
    		$txt=$file.','. $exportPath . $name . ',' . $seshID . ',' . round($fish_ws) . 'x' . round($fish_hs) . ',(' . round($pos_x) . ',' . round($pos_y) . '),'  . $readableDate . ',' . $seshID . ',' . $useraddress . '<hr />'; 
    		fwrite($fh,$txt); // Write information to the file
    		fclose($fh); // Close the file

   			imagedestroy($final);
		}

		output_image($image_file);

		//exit;
	}
	
		

?>


