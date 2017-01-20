/////////////// HELPER FUNCTIONS //////////////
function echo($something){
	$(".script_output").append($something);
}// end function

function cl($something){
	console.log($something);
}// end function

/////////////// PAGE MANIPULATION FUNCTIONS //////////////
function reset_modal(){
	$("h4.modal-title").text("");
	$(".modal-body p.food_description").text("");
	$("h4.modal-title").removeAttr("style");
	$(".modal-body img").hide();
	$(".modal-body").css({"background-image" : "url(_images/loading.gif)", "background-size" : "auto", "height" : "200px"});
}// end function

/////////////// RESTAURANT HOURS //////////////

/////////////// MAIN NAV COLLAPSE TOGGLE //////////////
$(document).ready(function() {
	$(".main_nav .navbar-header").click(function(){
		$(".collapse").slideToggle();
	});
});

/////////////// BOTTOM BAR POSITIONING //////////////

// have to put this outside so I could call it elsewhere
function calculate_bottom_bar_position() {
	//var document_height = $(document).height();
	var body_height = $("body").height();
	var window_height	= $(window).height();
	if ((body_height - 40) <= window_height){
		$("#bottom_bar").css({
			"position": "fixed",
			"clear": "both",
			"bottom": "0",
			"margin": "60px 0 0 0"
		})
	}// end if
	else {
		$("#bottom_bar").removeAttr('style');
	}
}// end function

$(document).ready(function() {

	// call function on ready
	calculate_bottom_bar_position();
	
	// call function on resize
	$(window).resize(function() { calculate_bottom_bar_position(); });
});