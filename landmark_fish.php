<?php 
	session_start();

	include 'snippets/header.php';
	include 'snippets/main.php';

	$current_image = $_GET['image'];

	$user_email = $_GET['user'];

	$scaled_width = $_GET['swidth'];
	$scaled_height = $_GET['sheight'];

	$original_width = $_GET['owidth'];
	$original_height = $_GET['oheight'];

	$standard_length = $_GET['standardlength'];
	$front_px = $_GET['frontpx'];
	$front_py = $_GET['frontpy'];
	$mid_px = $_GET['midpx'];
	$mid_py = $_GET['midpy'];
	$back_px = $_GET['backpx'];
	$back_py = $_GET['backpy'];

	// Define Constants
	//$fish_front_end_scale = .0625;
	$fish_main_scale = .125;

	// Calculate Landmark Mosaic Square scale factors
	// Box Mapping
		// Box 1 -> gill slit (user directed)
		// Box 2 -> midpoint of SL (autocalculated)
		// Box 3 -> endpoint of SL (autocalculated)
		// Box 4 -> 
	$mosaic_box_w = $mosaic_box_h = ($standard_length*$fish_main_scale);


?>
	<script>
		function hideInstructions() {
			var instructs = document.getElementById('instructs');
			if (instructs.style.display === "none") {
			    instructs.style.display = "block";
			} else {
			   instructs.style.display = "none";
			}

			var title = document.getElementById('title-mod');
			if (title.style.display === "none") {
				title.style.display = "block";
			} else {
				title.style.display = "none";
			}
		}
	</script>

	<p id="title-mod" style="display: block;"><strong>Step 2: Landmarking Points</strong></p>
	<p class="lead small" id="instructs" style="display: block;"><i class="fas fa-exclamation-triangle"></i> In order for proper placement of landmarks, <u><strong><em>you must be scrolled and stay scrolled</em></strong> to the <strong>top of the page</strong></u> during landmark placement. Please ensure this before sampling, or points will be offset. <strong>I.e., be scrolled to the top of the page and then once you start placing points, do not scroll the page.</strong> Currently rescaling: <strong><?php echo $current_image; ?></strong><br /><span style="color: #f47742;">Click #1: Gill slit.</span> | <span style="color: #57D505;">Click #2: Top-most body depth.</span> | <span style="color: #FF00F7;">Click #3: Bottom-most body depth.</span></p>
		<!-- Buttons for triggering modals -->
		<button type="button" class="btn btn-warning" data-toggle="modal" data-target="#instructionsModal">
		  <i class="fas fa-ruler"></i> Fish Landmarking Instructions
		</button>

		<button type="button" class="btn btn-info" data-toggle="modal" data-target="#schematicModal">
		  <i class="far fa-eye"></i> Example Schematics
		</button>

		<button type="button" class="btn btn-danger" onClick="window.location.reload()">
		  <i class="fas fa-undo"></i> Try Again
		</button>
	<p></p>
	<button type="button" class="btn btn-secondary" onClick="hideInstructions()">
		Show/Hide Instructions
	</button>

	<!-- Subscaling Modal Instructions -->
	<div class="modal fade" id="instructionsModal" tabindex="-1" role="dialog">
	  <div class="modal-dialog modal-dialog-centered" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="instructionsModalTitle"><i class="fas fa-ruler"></i> Fish Landmarking Instructions</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
			<h4>Step 2: Landmarking Points</h4>
			<p><span style="color: #f47742;">Click #1: Gill slit.</span>
			<br />
			<span style="color: #57D505;">Click #2: Top-most body depth.</span>
			<br />
			<span style="color: #FF00F7;">Click #3: Bottom-most body depth.</span></p>
			<img src="assets/img/fish-landmarks-diagram.png" width="100%" /></a><br /><br />
			<p>Your goal is to have something that looks like this:</p>
			<img src="assets/img/Landmark-Example.png" width="100%" style="margin-bottom: 10px;" />
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	      </div>
	    </div>
	  </div>
	</div>

	<!-- SL Schematic Example Modal Instructions -->
	<div class="modal fade" id="schematicModal" tabindex="-1" role="dialog">
	  <div class="modal-dialog modal-dialog-centered" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="schematicModalTitle"><i class="fas fa-eye"></i> Example SL Schematics</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
			<img src="assets/img/Landmark-Example.png" width="100%" height="100%" style="padding-bottom: 10px" />
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	      </div>
	    </div>
	  </div>
	</div>

	<!-- JS Below for Modal -->
	<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"></script>

	<p></p>
	<div id="cropButton"></div>
	<p></p>

	<div class='clickable' id='clicker'>
		<span class='display'></span>
		<img src="<?php echo $current_image; ?>" id="fishSample" width="100%" height="100%" />
	</div>

	<div id="img-out"></div>

	<script>

		var clickCounter = 0;

		// output crop box 1
		var gill_slit_pos_x = 0;
		var gill_slit_pos_y = 0;

		// output crop box 2
		var sl_frontpoint_pos_x = <?php echo $front_px; ?>;
		var sl_frontpoint_pos_y = <?php echo $front_py; ?>;

		// output crop box 3
		var sl_midpoint_pos_x = <?php echo $mid_px; ?>;
		var sl_midpoint_pos_y = <?php echo $mid_py; ?>;

		// output crop box 4
		var sl_endpoint_pos_x = <?php echo $back_px; ?>;
		var sl_endpoint_pos_y = <?php echo $back_py; ?>;

		// output crop box 5
		var topmost_pos_x = 0;
		var topmost_pos_y = 0;

		// output crop box 6
		var bottommost_pos_x = 0;
		var bottommost_pos_y = 0;


		clickable = document.getElementById('clicker');
		clickable.style.backgroundSize = 'contain';
		clickable.style.backgroundRepeat = 'no-repeat';

		$('.clickable').bind('click', function (ev) {
			
			console.log("Clicks: " + clickCounter);

			if (clickCounter == 0) {
				var $div = $(ev.target);
				var $display = $div.find('.display');

				var offset = $div.offset();

				gill_slit_pos_x = ev.clientX - offset.left;
				gill_slit_pos_y = ev.clientY - offset.top;

				//$display.text('Horizontal SL Click 1: ' + 'x: ' + Hor_ClickOne_x + ', y: ' + Hor_ClickOne_y);

				scrollOffsetHeight_One = window.scrollY;
				correctedDotHeight_One = (scrollOffsetHeight_One + gill_slit_pos_y);

				var color = '#f47742';
        		var size = '11px';
        		var radius = '11px';
        		$(".clickable").append(
            		$('<div></div>')
                	.css('position', 'absolute')
                	.css('top', correctedDotHeight_One + 'px')
                	.css('left', gill_slit_pos_x + 'px')
                	.css('width', size)
                	.css('height', size)
                	.css('borderRadius', radius)
                	.css('background-color', color)
        		);
			} else if (clickCounter == 1) {
				var $div = $(ev.target);
				var $display = $div.find('.display');

				var offset = $div.offset();

				topmost_pos_x = ev.clientX - offset.left;
				topmost_pos_y = ev.clientY - offset.top;

				//$display.text('Horizontal SL Click 2: ' + 'x: ' + Hor_ClickTwo_x + ', y: ' + Hor_ClickTwo_y);

				scrollOffsetHeight_Two = window.scrollY;
				correctedDotHeight_Two = (scrollOffsetHeight_One + topmost_pos_y);

				var color = '#57D505';
        		var size = '11px';
        		var radius = '11px';
				$(".clickable").append(
            		$('<div></div>')
                	.css('position', 'absolute')
                	.css('top', correctedDotHeight_Two + 'px')
                	.css('left', topmost_pos_x + 'px')
                	.css('width', size)
                	.css('height', size)
                	.css('borderRadius', radius)
                	.css('background-color', color)
        		);
        	} else if (clickCounter == 2) {
        		var $div = $(ev.target);
				var $display = $div.find('.display');

				var offset = $div.offset();

				bottommost_pos_x = ev.clientX - offset.left;
				bottommost_pos_y = ev.clientY - offset.top;

				scrollOffsetHeight_Three = window.scrollY;
				correctedDotHeight_Three = (scrollOffsetHeight_One + bottommost_pos_y);

				var color = '#FF00F7';
        		var size = '11px';
        		var radius = '11px';
				$(".clickable").append(
            		$('<div></div>')
                	.css('position', 'absolute')
                	.css('top', correctedDotHeight_Three + 'px')
                	.css('left', bottommost_pos_x + 'px')
                	.css('width', size)
                	.css('height', size)
                	.css('borderRadius', radius)
                	.css('background-color', color)
        		);

        		var clickWidth = $('#clicker').width();
				var clickHeight = $('#clicker').height();


        	/*$(".clickable").append(
					$('<svg width="'+clickWidth+'" height="'+clickHeight+'"><line x1="'+Hor_ClickOne_x+'" y1="'+correctedDotHeight_One+'" x2="'+Hor_ClickTwo_x+'" y2="'+correctedDotHeight_Two+'" stroke="#f47742" stroke-width="6" stroke-dasharray="5,5" /></svg>')
					.css('position','absolute')
        		);*/
			} else {
				console.log("All clicks have been recorded.");
			}

			clickCounter++;
			if ((clickCounter >= 3)) {
				// calculate distance between the two clicked points
				var Hor_diffs_x = (topmost_pos_x - bottommost_pos_x);
				var Hor_diffs_y = (topmost_pos_y - bottommost_pos_y);
				var fish_height = Math.sqrt((Math.pow(Hor_diffs_y,2))+(Math.pow(Hor_diffs_x,2)));
				console.log("Height of Fish: " + fish_height);

				var originalWidth = $('#clicker').width();
				var originalHeight = $('#clicker').height();

				var height_midpoint_coord_x = ((topmost_pos_x + bottommost_pos_x)/2);

				var newScaledWidth = originalWidth;
				var newScaledHeight = originalHeight;

				console.log("Original Width: " + originalWidth);
				console.log("Original Height: " + originalHeight);

				
				html2canvas($('#clicker')[0], {
  					scale:1
				}).then(function(canvas) {
					//var ctx = canvas.getContext('2d');
  					//ctx.fillStyle = "#f47742";
  					//ctx.beginPath();
  					//var shifted_horclickone_x = (Hor_ClickOne_x - 12);
  					//console.log(shifted_horclickone_x);
    				//ctx.arc(shifted_horclickone_x, Hor_ClickOne_y, 2, 0, Math.PI * 2, true);
    				//ctx.fill();

  					$("#img-out").append(canvas);
  					clickable.style.display = 'none';
  					
				});

				var standardLength = <?php echo $standard_length; ?>;
				var temp_name_storage = "<?php echo $user_email; ?>";
				
				document.getElementById("cropButton").innerHTML+= "<a href='execute_mosaic_processing.php?image=<?php echo $current_image;?>&standardlength=" + standardLength + "&user=" + temp_name_storage + "&swidth=" + newScaledWidth + "&sheight=" + newScaledHeight + "&frontpx=" + sl_frontpoint_pos_x + "&frontpy=" + sl_frontpoint_pos_y + "&backpx=" + sl_endpoint_pos_x + "&backpy=" + sl_endpoint_pos_y + "&midpx=" + sl_midpoint_pos_x + "&midpy=" + sl_midpoint_pos_y + "&fishheight=" + fish_height + "&toppx=" + topmost_pos_x + "&toppy=" + topmost_pos_y + "&bottompx=" + bottommost_pos_x + "&bottompy=" + bottommost_pos_y + "&gillslitpx=" + gill_slit_pos_x + "&gillslitpy=" + gill_slit_pos_y + "&heightmpx=" + height_midpoint_coord_x + "&mboxw=" + <?php echo $mosaic_box_w; ?> + "&owidth=" + originalWidth + "&oheight=" + originalHeight + "'class='btn btn-success btn-lg' role='button'>Generate Fish Mosaics <i class='fas fa-upload'></i></a>";			
			}		
		});



	</script>