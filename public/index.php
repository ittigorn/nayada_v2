<!doctype html>
<html lang="en">
<head>
	<?php require_once("../_includes/header_addon.php"); ?>
	<title><?php echo $server->info->restaurant_name; ?></title>
	<!-- LESS/SASS SCRIPTS GO HERE IF NECCESSARY -->
</head>
<body>
<?php include_once("../_includes/top_bar.php"); ?>
<div id="wrapper">
	<div class="container-fluid">
		<div class="row carousel_container">
			<!-- LOAD CAROUSEL AUTOMATICALLY THROUGH AHAH IF THE SCREEN IS LARGE ENOUGH OR USE JUMBOTRON -->
			<div class="jumbotron">
				<div class="container">
					<div class="logo_container col-centered"><img src="_images/logo.png"></div>
					<h1>New &amp; Improved</h1>
					<h3>Online Ordering System</h3>
				</div>
			</div>
		</div>

		<div class="row index_feature">
			<div class="col-lg-4">
				<div class="thumbnail">
					<div class="caption">
				        <h3><span class="glyphicon glyphicon-search"> </span>Find your food fast</h3>
				        <span class="glyphicon glyphicon-search"></span>
				        <p>Not sure what your favorite dish is called? Just wanna browse around?</p>
				        <p>We got you covered!</p>
				    	<p>Try our <a href="gallery.php">Photo Gallery</a>, discover new dishes.</p>
				    	<p>Search with a fraction of names in our <a href="keyword.php">Keyword Search</a> to find the dish you're looing for.</p>
				    </div>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="thumbnail">
					<div class="caption">
				        <h3><span class="glyphicon glyphicon-time"> </span>Order a pick-up in a breeze</h3>
				        <span class="glyphicon glyphicon-time"></span>
				        <p>Once registered, ordering a pick-up is as easy as counting 1-2-3.</p>
				        <p>We aim to provide hassle-free ordering experience and serve you with the most accurate information such as estimated pick-up time.</p>
				        <p>We'll let you know if additional time is required to prepare your order .</p>
				    </div>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="thumbnail">
					<div class="caption">
				        <h3><span class="glyphicon glyphicon-lock"> </span>Security that you can trust</h3>
				        <span class="glyphicon glyphicon-lock"></span>
				        <p></p>

				        <ul>
						    <li>
						        <img src="_images/_comodo_secure_logos/comodo_secure_76x26_transp_marginless.png"> ensures your communication is professionally encrypted.
						    </li>
						    <li><a href="https://www.google.com/recaptcha/intro/index.html" title="Google's reCAPTCHA Page" target="_blank">Google's reCAPTCHA screens out any non-human attempt.</a></li>
						    <li>Authorize.net's Fraud Detection Suite for card transactions.</li>
						    <li>Requiring signature at time of picking up.</li>
						    <li>Verifying photo ID and the card used to make purchase at time of picking up.</li>
						    <li>No card information is stored in our database except name on card, expiration date and the last 4 digits card number for reference and refund.</li>
						</ul>
				    </div>
				</div>
			</div>
		</div>

		<div id="contact_area" class="row hours">
			<div class="col-lg-4 col-sm-6 col-xs-12">
				<div class="thumbnail">
					<h3>Contact Us</h3>
					<a href="https://www.google.com/maps/place/Nayada+Thai+Cuisine/@33.831682,-118.0904236,17z/data=!3m1!4b1!4m2!3m1!1s0x80dd2de9b5a1f831:0x66198da60590b5a0?hl=en-US" target="_blank">
						<blockquote>
							11401 Carson St. Suite A, Lakewood, CA 90715
						</blockquote>
					</a>
					<a href="tel:15628606108">
						<blockquote>
							1 (562) 860-6108
						</blockquote>
					</a>
					<a href="<?php echo $server->status->url_prefix; ?>comment.php">
						<blockquote>
							nayadathai@hotmail.com
						</blockquote>
					</a>
					<h3>Stay Updated</h3>
					<a target="_blank" href="https://www.facebook.com/nayadathai/">
						<blockquote>
							<i class="fa fa-facebook-official fa-lg"></i> facebook.com/nayadathai
						</blockquote>
					</a>
				</div>
			</div>
			<div class="col-lg-3 col-sm-5 col-xs-12">
				<div class="thumbnail">
					<h3>Restaurant Hours</h3>
					<div class="table-responsive">
						<table class="table">
							<tr>
								<th>Mon</th>
								<td>11.00 - 9.30</td>
							</tr>
							<tr>
								<th>Tue</th>
								<td>11.00 - 9.30</td>
							</tr>
							<tr>
								<th>Wed</th>
								<td>11.00 - 9.30</td>
							</tr>
							<tr>
								<th>Thu</th>
								<td>11.00 - 9.30</td>
							</tr>
							<tr>
								<th>Fri</th>
								<td>11.00 - 10.00</td>
							</tr>
							<tr>
								<th>Sat</th>
								<td>12.00 - 10.00</td>
							</tr>
							<tr>
								<th>Sun</th>
								<td>12.00 - 9.30</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div> <!-- END WRAPPER HERE -->
<?php include_once("../_includes/bottom_bar.php"); ?>


<script type="text/javascript">
// defining global functions for this page
function equalHeight(group) {    
    var tallest = 0;    
    group.each(function() {       
        var thisHeight = $(this).height();       
        if(thisHeight > tallest) {          
            tallest = thisHeight;       
        }    
    });    
    group.each(function() { $(this).height(tallest); });
} // end function

	$(document).ready(function() { 
		// Thumbnail equal height function
		
		if ($(window).width() >= 768) {
		    $.get("_ajax/index_carousel.html",function(data){
		    	$(".carousel_container").append(data);
		    	$('.carousel').carousel({
			    	interval: 5000
			    });
		    });
		}// end if

		if ($(window).width() >= 1183) {
			equalHeight($(".index_feature .thumbnail"));
		}// end if

		if ($(".hours").width() >= 751) {
		    equalHeight($("#contact_area .thumbnail"));
		}// end if

		$(".scroll_contact").click(function(){
			var $a_position = $("#contact_area").offset();
			$('html,body').animate({scrollTop: $a_position.top}, 800, "swing");
		});
	});

	$(window).resize(function() {
		if ($(window).width() >= 1200) {  
			$(".index_feature .thumbnail").removeAttr("style");
			$("#contact_area .thumbnail").removeAttr("style");
		    equalHeight($(".index_feature .thumbnail"));
		    equalHeight($("#contact_area .thumbnail"));
		}
		else {
			$(".index_feature .thumbnail").removeAttr("style");
			$("#contact_area .thumbnail").removeAttr("style");
		}
	});
</script>

</body>
</html>