<?php 
	session_start();

	include 'snippets/header.php';
	include 'snippets/main.php';

	$current_image = $_GET['image'];
	$scaled_width = $_GET['swidth'];
	$scaled_height = $_GET['sheight'];

	$original_width = $_GET['owidth'];
	$original_height = $_GET['oheight'];

?>

	<div class="alert alert-warning" role="alert">
		To the best of your ability, place the square at mid body posterior to the pectoral fin while avoiding the pectoral fin.
		<br />Currently subsampling <strong><?php echo $current_image; ?></strong>
	</div>
	<p>
		<em>New Scaled Dimensions: <mark><?php echo round($scaled_width); ?> x <?php echo round($scaled_height); ?></mark></em><br />Please view <a href="instructions.php" target="_blank">instructions</a> for guidance.
		<div style="margin-bottom: 30px"></div>
	</p>

	<script>

		$(function($){
		var jcrop_api; // Holder for the API
		initJcrop();

		function initJcrop(){
		  $('.requiresjcrop').hide();

		  $('#cropbox').Jcrop({
		  	trueSize: [<?php echo $scaled_width; ?>, <?php echo $scaled_height; ?>],
		  	bgColor: '',
		  	bgOpacity: .4
		  },function(){

		    $('.requiresjcrop').show();

		    jcrop_api = this;
		    jcrop_api.animateTo([100,100,400,300]);

		  });

		};

		var desired_sampling_size = 175;

		jcrop_api.setOptions({allowResize: false});
		jcrop_api.setOptions({minSize: [desired_sampling_size, desired_sampling_size], maxSize: [desired_sampling_size, desired_sampling_size]});
		// jcrop_api.setOptions({bgColor: ''});
		jcrop_api.focus();

		});


	    $(function(){
			$('#cropbox').Jcrop({
				aspectRatio: 1,
				onSelect: updateCoords
			});
		});

	  	function updateCoords(c){
	    	$('#x').val(c.x);
	    	$('#y').val(c.y);

	    	$('#w').val(c.w);
	    	$('#h').val(c.h);

	    	console.log(c.x);
	    	console.log(c.y);

	    	console.log(c.w);
	    	console.log(c.h);
	  	};

		  function checkCoords(){
		  	if (parseInt($('#w').val())) return true;
		    alert('Please select a crop region then press submit.');
		    return false;
		  };

	</script>
	<img src="<?php echo $current_image; ?>" id="cropbox" width="<?php echo $original_width; ?>" height="<?php echo $original_height; ?>" />
	<br />
	<form action="execute_crop.php?image=<?php echo $current_image; ?>&swidth=<?php echo $scaled_width; ?>&sheight=<?php echo $scaled_height; ?>" method="post" onsubmit="return checkCoords();">
		<input type="hidden" id="x" name="x" />
		<input type="hidden" id="y" name="y" />
		<input type="hidden" id="w" name="w" />
		<input type="hidden" id="h" name="h" />
		<button type="submit" class="btn btn-primary btn-lg">
    		Subsample Fish <i class='fas fa-upload'></i>
		</button>
	</form>