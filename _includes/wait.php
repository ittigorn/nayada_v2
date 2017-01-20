<div class="fader fader_wait"></div>
<div class="wait">
	<p><img src="_images/loading.gif"></p>
	<h4>Please wait while we process your request</h4>
</div>

<script type="text/javascript">

	function show_wait() {
		calculate_wait();
		$(".fader_wait").fadeIn(100);
		$(".wait").fadeIn(100);
	}// end function

	function hide_wait() {
		$(".fader_wait").removeAttr("style");
		$(".fader_wait").hide();
		$(".wait").removeAttr("style");
		$(".wait").hide();
	}// end function

	function calculate_wait() {
		var $win = { "height" : $(window).height(), "width" : $(window).width(),
					"outerHeight" : $(window).outerHeight(), "outerWidth" : $(window).outerWidth() }

		$(".fader_wait").css({"width" : $win.outerWidth + "px", "height" : $win.outerHeight + "px" });
		$(".wait").css({"top" : (($win.outerHeight / 2)-150) + "px", "left" : (($win.outerWidth / 2)-150) + "px" });
	}// end function

	$(document).ready(function(){
		$(".show_wait").click(function(){
			show_wait();
		});

		$(window).resize(function(){
			calculate_wait();
		});
	});

</script>