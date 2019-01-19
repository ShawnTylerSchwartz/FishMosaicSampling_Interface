<?php
    session_start();

    $seshID = session_id();

    $get_user = $_GET['user'];

    $date   = new DateTime();
    $readableDate = $date->format('m-d-Y,h:i:sa');

if($_POST) {
	
    // define('UPLOAD_DIR', 'uploads-test/');
    $img = $_POST['image'];
    $img = str_replace('data:image/png;base64,', '', $img);
   
    $img = str_replace(' ', '+', $img);

    $cur_img = $_GET['image'];

    $nospace = explode(" ", $cur_img);
    $noext = explode(".", $nospace[1]);
    $noslash = explode("/", $nospace[0]);
    $sepgenus = explode("_", $noslash[2]);
    $exportPath = "../fish_output/" . $noslash[1] . "/";  
    // $name = md5($file) . ".jpg";
    $name = $noslash[1] . "_" . $sepgenus[1] . "_" . $noext[0];

    if(!is_dir($exportPath)) {
        mkdir($exportPath);
    }
   
    $dataimg = base64_decode($img);
    
    // $nameimg= uniqid() ;
    $nameimg = $exportPath . $name;
    // $fileimg = UPLOAD_DIR . $nameimg . '.png';
    $fileimg = $nameimg . '.png';
    $successimg = file_put_contents($fileimg, $dataimg);
    echo $nameimg;

    $txt = "../_outputData.html";

    if(!file_exists($nameimg)) {
        $fh = fopen($txt, 'a'); 
        $txt = $cur_img . ',' . $name . ',' . $readableDate . ',' . $seshID . ',' . $get_user . '<hr />'; 
        fwrite($fh,$txt); // Write information to the file
        fclose($fh); // Close the file
    }

}
?>