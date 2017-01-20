<div class="fader fader_warning"></div>
<div class="warning">
	<p><img src="_images/warning.png"></p>
	<h4 class="warning_message"></h4>
	<span class="warning_button_contnainer">
		
	</span>
	<button type="button" class="btn btn-default hide_warning">Cancel</button>
</div>

<script type="text/javascript">

	$(document).ready(function(){
		$(window).resize(function(){
			calculate_warning();
		});
		$(".warning button.hide_warning").click(function(){ hide_warning() });
	}); // end document ready

	function show_warning($warning_message,$continue_button,$padding_top) {

		// set up padding-top
		if ($padding_top === undefined) {$padding_top = 50;}
		if ($padding_top != 0) {
			$(".warning").css({"padding-top" : $padding_top});
		}

		// set up warning message and button
		$(".warning h4").text($warning_message);
		$(".warning .warning_button_contnainer").append($continue_button);

		// calculate and display warning box
		calculate_warning();
		$(".fader_warning").fadeIn(100);
		$(".warning").fadeIn(100);

	}// end function

	function hide_warning() {
		$(".warning .warning_button_contnainer").empty();
		$(".fader_warning").removeAttr("style");
		$(".fader_warning").hide();
		$(".warning").removeAttr("style");
		$(".warning").hide();
	}// end function

	function calculate_warning() {
		var $win = { "height" : $(window).height(), "width" : $(window).width(),
					"outerHeight" : $(window).outerHeight(), "outerWidth" : $(window).outerWidth() }

		$(".fader_warning").css({"width" : $win.outerWidth + "px", "height" : $win.outerHeight + "px" });
		$(".warning").css({"top" : (($win.outerHeight / 2)-150) + "px", "left" : (($win.outerWidth / 2)-150) + "px" });
	}// end function

	//////////////////////  Show warning trigger example /////////////////////////
	// $(".show_warning1").click(function(){
	// 	var $warning_message 	= "Clear Cart?";
	// 	<?php echo 'var $url_prefix = "'.$server->status->url_prefix.'";'; ?>
	// 	var $button				 = 	'<form method="post" target="_self" action="' + $url_prefix + 'cart.php">';
	// 	$button					+= '<button class="btn btn-warning" type="submit" name="clear_cart">Clear</button></form>';
	// 	show_warning($warning_message,$button,"50px");
	// });
</script>
