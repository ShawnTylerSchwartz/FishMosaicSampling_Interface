<?php
    include '../snippets/header.php';
    include '../snippets/main.php';

    if(isset($_POST['submit'])){
        $username = $_POST['emailaddress'];
    }

    function arraycount($array, $value){
    $counter = 0;
    foreach($array as $thisvalue) /*go through every value in the array*/
     {
           if($thisvalue === $value){ /*if this one value of the array is equal to the value we are checking*/
           $counter++; /*increase the count by 1*/
           }
     }
     return $counter;
     }


?>

<p class="lead"><i class="fas fa-check"></i> Check your progress. <br />
Please enter your <strong>email</strong> or a <strong>unique identifier</strong> below to view your progress.</p>

<form class="form-signin text-center" name="login" action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
      <label for="inputEmail" class="sr-only">Username</label>
      <input type="text" id="inputUsername" name="emailaddress" class="form-control" placeholder="Email Address or Unique Identifier" required autofocus>
      <p></p>
      <button class="btn btn-success text-center" id="view-fullscreen" type="submit" name="submit" value="Submit">View Your Progress <i class="fas fa-check"></i></button>
</form>

<p></p>

<?php 

if(isset($_POST['submit'])){
    echo "<h3>Digitizing Progress for " . $username . "</h3>";

    $block = 1024*1024; //1MB for file read in
        $tmpstorage = array();
        if ($fh = fopen("../_outputData.html", "r")) { 
            $left='';
            while (!feof($fh)) { // read in file
                $temp = fread($fh, $block);  
                $fgetslines = explode("<hr />",$temp);
                $fgetslines[0]=$left.$fgetslines[0];
                if(!feof($fh) )$left = array_pop($lines);           
                foreach ($fgetslines as $k => $line) {
                    $completedComponents = explode(",", $line);
                    array_push($tmpstorage, $completedComponents[5]);
                }
            }
            //print_r($tmpstorage);   
        }

        fclose($fh); // close file stream
}

$total_input_fish = new FilesystemIterator('fish_input/Labridae', FilesystemIterator::SKIP_DOTS);
$total_output_fish = new FilesystemIterator('../fish_output/Labridae', FilesystemIterator::SKIP_DOTS);

$user_percentage_completed = 100 * ((arraycount($tmpstorage, $username)) / (iterator_count($total_input_fish)));
$overall_percentage_completed = 100 * ((iterator_count($total_output_fish))/(iterator_count($total_input_fish)));

if(isset($_POST['submit'])){

echo "<h5>You have digitized <u>" . arraycount($tmpstorage, $username) . "</u> fish out of a total <u>" . iterator_count($total_input_fish) . "</u> images.</h5>";

echo "<h6>Your percentage contribution: " . $user_percentage_completed . "%.</h6>";
echo "<div class='progress'>";
  echo "<div class='progress-bar bg-info' role='progressbar' style='width: $user_percentage_completed%'' aria-valuenow='50' aria-valuemin='0' aria-valuemax='100'></div>";
echo "</div>";
echo "<br />";
echo "<h6>Overall percentage of Labridae completed: " . $overall_percentage_completed . "%.</h6>";
echo "<div class='progress'>";
  echo "<div class='progress-bar bg-success' role='progressbar' style='width: $overall_percentage_completed%'' aria-valuenow='50' aria-valuemin='0' aria-valuemax='100'></div>";
echo "</div>";
echo "<br />";
}
?>


<?php 
    include '../snippets/footer.php';
?>