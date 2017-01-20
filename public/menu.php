<!doctype html>
<html lang="en">
<head>
<?php
	require_once("../_includes/header_addon.php");
	$page->enable_ajax();

	// evaluate GET variables
	if (isset($_GET["category"]) && is_numeric($_GET["category"])) {
		$category = ($_GET["category"]);
	}
	else {
		$category = 2;
	}
	$cat 	= new stdClass();
	// setup category
	switch ($category) {
		case 1:
		$cat->code = "lun";
		$cat->title = "Lunch Special";
		break;

		case 2:
		$cat->code = "app";
		$cat->title = "Appetizers";
		break;
		
		case 3:
		$cat->code = "sou";
		$cat->title = "Soup";
		break;
		
		case 4:
		$cat->code = "sal";
		$cat->title = "Salad";
		break;

		case 5:
		$cat->code = "cur";
		$cat->title = "Curry";
		break;

		case 6:
		$cat->code = "noo";
		$cat->title = "Noodle";
		break;

		case 7:
		$cat->code = "ric";
		$cat->title = "Rice";
		break;

		case 8:
		$cat->code = "ent";
		$cat->title = "Entree";
		break;

		case 9:
		$cat->code = "spe";
		$cat->title = "Chef's Special";
		break;

		case 10:
		$cat->code = "des";
		$cat->title = "Dessert";
		break;

		case 11:
		$cat->code = "dri";
		$cat->title = "Beverage";
		break;

		case 12:
		$cat->code = "sid";
		$cat->title = "Side";
		break;

		default:
		$category = 2;
		$cat->code = "app";
		$cat->title = "Appetizer";
		break;
	}// end switch

	// search for food in the immediate category
	$food_list = food::search_food($db,$cat->code,"category",true,"id");

	// setup alert if ordering is not allowed
	if ($server->info->ordering !== "1") {array_push($alert,"(header)We're sorry, online ordering is temporarily seized by management team.");}
?>
<title><?php echo $cat->title; ?></title>
<!-- LESS/SASS SCRIPTS GO HERE IF NECCESSARY -->
</head>
<body>
<?php include_once("../_includes/top_bar.php"); ?>
<div id="wrapper">
	<h1><?php echo $cat->title; ?></h1>
	<div class="container-fluid menu_area">
		<ul>
			<?php 
				$request = array("food_id","food_name_en","description","price_base","available","category");
				foreach ($food_list as $current_food) {
					$food = new food($db,$server,$current_food->food_id,$request);
					echo $food->generate_menu_item($server);
				}// end while
			?>
		</ul>
	</div>

	<!-- Modal -->
	<div id="food_preview" class="modal fade" role="dialog">
		<div class="modal-dialog">
		    <!-- Modal content-->
		    <div class="modal-content">
		    	<div class="modal-header">
		        	<button type="button" class="close" data-dismiss="modal">&times;</button>
		        	<h4 class="modal-title">Food Name</h4>
		    	</div>
		      	<div class="modal-body">
		      		<p class="food_description">Food Description</p>
		      		<form>

		      		</form>
		      	</div>
		      	<div class="modal-footer">
		      		<?php if ($cust_logged_in === true) { ?>
		      		<button type="button" class="btn btn-default add_to_cart" data-dismiss="modal">Add to Cart</button>
		      		<?php } // end if cust is logged in ?>
		        	<button type="button" class="btn btn-default modal_close" data-dismiss="modal">Close</button>
		      	</div>
		    </div>
		</div>
	</div>
<div class="output"></div>
</div> <!-- END WRAPPER HERE -->






<script type="text/javascript">

	$(document).ready(function(){

		function check_hours() {
			console.log("test");
		}
		//var $hours_checker = setInterval(function(){check_hours()}, 60000);

		function mark_added_menu_item($food_id){
			var $list_id = "#food_list_" + $food_id;
			$($list_id).css({"box-shadow" : "0 0 5px green", "border-color" : "green"});
		}// end function

		$(".menu_item").click(function(){
			// reset old values
			$(".modal-footer button.add_to_cart").show();
			$(".modal-content form").show();
			reset_modal();

			// reassign new values
			var $food_id 	= $(this).attr("id"); 
			$food_id 		= $food_id.substring(10);
			var $food_name_en = $(this).children(".food_name_en").text();
			var $description = $(this).children(".description").text();
			$("h4.modal-title").text($food_name_en);
			$(".modal-body p.food_description").text($description);
			$(".modal-content").attr("id","modal_"+$food_id);

			// fetch form data
			$.post("_ajax/html_generator.php",{
				"request_type" 	: "menu_modal",
				"food_id"		: $food_id
				},function(data){
				$(".modal-body form").empty();
				$(".modal-body form").append(data);
			});

			// if food is unavailable
			if ($(this).hasClass("unavailable") === true) {
				$(".modal-footer button.add_to_cart").hide();
				$(".modal-content form").hide();
				$("h4.modal-title").append(" (unavailable)");
				$("h4.modal-title").css("color","#c00");
			}

			// See if food picture exists, if not, use default picture
			var $food_url = "_images/menu/large/" + $food_id + ".jpg";
			$.ajax({
			    url: $food_url,
			    type:'HEAD',
			    error: function() {
			        $(".modal-body").removeAttr("style");
			        $(".modal-body").css({
						"background-image" : "none"
					});
					
					/*
					$(".modal-body").css({
						"background-image" : "url(_images/menu/large/default.jpg)", 
						"background-size" : "cover", 
						"background-position" : "center",
						"background-repeat" : "no-repeat"
					}); */

					$(".modal-body img").show();

			    },
			    success: function() {
			    	$(".modal-body").removeAttr("style");
					$(".modal-body").css({
						"background-image" : "url(" + $food_url + ")", 
						"background-size" : "cover", 
						"background-position" : "center",
						"background-repeat" : "no-repeat"
					});
					$(".modal-body img").show();
			    }
			});
		}); // end menu_item .click

		$(".modal-footer button.add_to_cart").click(function(){
			// check if quantity is set and complies with the rule
			if (($("#quantity").val() > 0) && ($("#quantity").val() <= 20)) {
				var $food_id 	= $(".modal-content").attr("id"); 
				$food_id 		= $food_id.substring(6);
				var $form_values = $(".modal-body").children("form").serialize();
				var $request_data = "request_type=add_to_cart&food_id=" + $food_id + "&" + $form_values;
				$.post("_ajax/cart.php",$request_data,function(data) { //echo(data);
					var $response = JSON.parse(data);
					if ($response.success === "yes") {
						mark_added_menu_item($food_id);
						$(".cart_item_count").text($response.item_count);
					}
					else {
						$(".script_output").siblings(".alert_box").remove();
						$(".script_output").after($response.combined_alert);

						// scroll to alert_box
						var $error_box_pos = $(".script_output").offset();
						$('html,body').animate({scrollTop: $error_box_pos.top}, 400, "swing");
					}
				});
			}// end if quantity is set
		}); // ennd add_to_cart button .click

	});
</script>

<?php include_once("../_includes/bottom_bar.php"); ?>
</body>
</html>