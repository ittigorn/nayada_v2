<!doctype html>
<html lang="en">
<head>
<?php
	require_once("../_includes/header_addon.php");
	$page->enable_ajax();

	// evaluate post
	if (isset($_POST["clear_cart"])) {
		cart::clear_cart();
		$location = $server->status->url_prefix."cart.php";
		redirect($location);
	}// end if clear cart

	$summary = cart::calculate_summary($server);
	$alert = array_merge($alert,$summary->alert);

	// calculate tip
	$cart_tip       = cart::get_tip();
    $tip            = new stdClass();
    $tip->zero      = 0;
    $tip->ten       = price($summary->subtotal * 0.1);
    $tip->fifteen   = price($summary->subtotal * 0.15);
    $tip->eighteen  = price($summary->subtotal * 0.18);
    $tip->twenty    = price($summary->subtotal * 0.2);

    // find out if cart_tip is custom amount or not
    $tip_array          = (array) $tip;
    $custom_tip_amount  = true;
    foreach ($tip_array as $value) {
        if ($value == $cart_tip) {$custom_tip_amount = false;}
    }
    unset($tip_array);
?>
<title>Cart</title>
<!-- LESS/SASS SCRIPTS GO HERE IF NECCESSARY -->
</head>
<body>
<?php include_once("../_includes/top_bar.php"); ?>
<div id="wrapper">
	<div class="container-fluid">

		<?php if ($summary->checkout === true) { ?>
		<div class="breadcrumb_container">
			<div class="breadcrumb">
				<a class="active inert">View Cart</a>
				<a href="<?php echo $server->status->url_prefix; ?>checkout.php" class="reactive">Checkout</a>
				<a class="inert">Confirm</a>
				<a class="inert">Pick-Up</a>
			</div>
		</div>
		<?php } ?> 

		<h1>Cart</h1>
		<div class="col-lg-11 col-centered">
			
				<div class="col-lg-3 summary">
					<h3>Cart Summary</h3>
					<table>
						<tr>
							<th>Total item</th>
							<td class="item_count"><?php echo $summary->item_count->total_item; ?></td>
						</tr>
						<tr>
							<th>Subtotal</th>
							<td class="subtotal"><?php echo $summary->subtotal; ?></td>
						</tr>
						<tr>
							<th>Tax (<?php echo $server->info->tax_rate; ?>%)</th>
							<td class="tax"><?php echo $summary->tax; ?></td>
						</tr>
						<tr>
							<th>Tip</th>
							<td class="tip"><?php echo $summary->tip; ?></td>
						</tr>
						<tr>
							<th colspan="2">
								
									<div class="form-group">
										<input id="tip_0" name="tip" type="radio" value="0" <?php if ($cart_tip == 0) {echo 'checked="checked"';} ?> />
										<label for="tip_0"> No Tip</label>
									</div>
									<div class="form-group">
									    <input id="tip_10" name="tip" type="radio" value="<?php echo $tip->ten; ?>" <?php if ($cart_tip == $tip->ten) {echo 'checked="checked"';} ?> />
										<label for="tip_10">10% (<?php echo $tip->ten; ?>)</label>
									</div>
									<div class="form-group">
									    <input id="tip_15" name="tip" type="radio" value="<?php echo $tip->fifteen; ?>" <?php if ($cart_tip == $tip->fifteen) {echo 'checked="checked"';} ?> />
								    	<label for="tip_15">15% (<?php echo $tip->fifteen; ?>)</label>
								    </div>
									<div class="form-group">
									    <input id="tip_18" name="tip" type="radio" value="<?php echo $tip->eighteen; ?>" <?php if ($cart_tip == $tip->eighteen) {echo 'checked="checked"';} ?> />
									    <label for="tip_18">18% (<?php echo $tip->eighteen; ?>)</label>
								    </div>
									<div class="form-group">
									    <input id="tip_20" name="tip" type="radio" value="<?php echo $tip->twenty; ?>" <?php if ($cart_tip == $tip->twenty) {echo 'checked="checked"';} ?> />
								    	<label for="tip_20">20% (<?php echo $tip->twenty; ?>)</label>
								    </div>
									<div class="form-group">
										<div class="input-group">
										<input id="tip_c" type="radio" name="tip" value="custom" <?php if ($custom_tip_amount === true) {echo 'checked="checked"';} ?> />
								        <input id="tip_c_box" type="text" name="tip_c_box" value="" placeholder="<?php echo $cart_tip; ?>" />
									</div>
								
							</th>
						</tr>
						<tr>
							<th>Grand Total</th>
							<td class="grandtotal"><?php echo $summary->grandtotal; ?></td>
						</tr>
					</table>

					<?php if ($summary->item_count->total_item > 0) { ?>
						<button type="button" class="btn btn-default danger clear_cart show_warning1">Clear Cart</button>
					<?php } ?>

					<?php if ($summary->checkout === true) { ?>
						<a href="<?php echo $server->status->url_prefix; ?>checkout.php" class="checkout"><button type="button" class="btn btn-primary">Checkout</button></a>
					<?php } ?>
				</div>
				<div class="col-lg-8 item_list">
					<h3>Item List</h3>
					<?php echo cart::generate_item_list(); ?>
				</div>
		</div>
	</div>


</div> <!-- END WRAPPER HERE -->





<script type="text/javascript">

	////////////////////////// CLEAR CART LISTENER BLOCK ///////////////////////////
	
	$(document).ready(function() {

		/////////////////////// WARNING BOX FUNCTION ///////////////////////
		// show warning when clear cart is clicked
		$(".show_warning1").click(function(){
			var $warning_message 	= "Clear Cart?";
			<?php echo 'var $url_prefix = "'.$server->status->url_prefix.'";'; ?>
			var $button				 = '<form method="post" target="_self" action="' + $url_prefix + 'cart.php">';
			$button					+= '<button class="btn btn-warning" type="submit" name="clear_cart">Clear</button></form>';
			show_warning($warning_message,$button,"50px");
		});

		// execute update_tip when customer confirm tip amount
		$(".warning").on("click","#confirm_tip",function(){
			var $tip_amount = $("#confirm_tip").val();
			update_tip($tip_amount);
			hide_warning();
		});

		/////////////////////// PAGE DATA MANIPULATION FUNCTIONS ///////////////////////
		function clear_all_data() {
			$(".summary table td").text("");
			$(".item_list ol").remove();
			$(".cart_item_count").text("");
			$("section.alert_box").remove();
		}// end function
		
		function update_page($data) {
			// clear all info on page
			clear_all_data();

			// parse response
			var $response = JSON.parse($data);

			// parse and insert new data
			$(".item_list").append($response.item_list);
			$(".cart_item_count").text($response.summary.item_count.total_item);
			$(".summary table td.item_count").text($response.summary.item_count.total_item);
			$(".summary table td.subtotal").text($response.summary.subtotal);
			$(".summary table td.tax").text($response.summary.tax);
			$(".summary table td.tip").text($response.summary.tip);
			$(".summary table td.grandtotal").text($response.summary.grandtotal);

			// display alert if there is any
			if ($response.alert !== "") {
				//var $alert = "<section class='alert_box' style='font-weight: bold; font-size: 1.2em;'>" + $response.alert.substring(8) + "</section>";
				$(".script_output").after($response.summary.combined_alert);
			}

			// hide checkout button if minimum is not reached
			if ($response.summary.checkout !== true) {
				$("a.checkout").hide();
				$(".breadcrumb_container").hide();
			}

			// hide clear cart button if no item is left
			if ($response.summary.item_count.total_item == 0) {
				$("button.clear_cart").hide();
			}

			// move tip selector to custom tip amount
			$("#tip_c").prop("checked", true);

			// update predefined tip amount
			var $subtotal = Number($response.summary.subtotal);
			$("#tip_10").siblings("label").text("10% (" + (($subtotal/100)*10).toFixed(2) + ")");
			$("#tip_15").siblings("label").text("15% (" + (($subtotal/100)*15).toFixed(2) + ")");
			$("#tip_18").siblings("label").text("18% (" + (($subtotal/100)*18).toFixed(2) + ")");
			$("#tip_20").siblings("label").text("20% (" + (($subtotal/100)*20).toFixed(2) + ")");
			$("#tip_10").prop("value",(($subtotal/100)*10).toFixed(2));
			$("#tip_15").prop("value",(($subtotal/100)*15).toFixed(2));
			$("#tip_18").prop("value",(($subtotal/100)*18).toFixed(2));
			$("#tip_20").prop("value",(($subtotal/100)*20).toFixed(2));
			
			// recalculate bottom bar
			calculate_bottom_bar_position();
		}// end function (update_page)

		function update_tip($tip_amount) {
			// clear current summary
			$(".summary table td").text("");

			// show wait while ajax is loading
			show_wait();

			// assemble request
			var $request_data = { "request_type" : "update_tip", "tip_amount" : $tip_amount };

			// make ajax call
			$.post("_ajax/cart.php",$request_data,function($data) {
				// parse response
				var $response = JSON.parse($data);

				// hide wait
				hide_wait();

				// update fields
				$(".summary table td.item_count").text($response.summary.item_count.total_item);
				$(".summary table td.subtotal").text($response.summary.subtotal);
				$(".summary table td.tax").text($response.summary.tax);
				$(".summary table td.tip").text($response.summary.tip);
				$(".summary table td.grandtotal").text($response.summary.grandtotal);
			}); // end ajax
		}// end function (update_tip)

		
		////////////////////////// REMOVING ITEM - LISTENER BLOCK ///////////////////////////
		$(".item_list").on("click",".remove_item",function(){

			// show wait while fetching ajax
			show_wait();

			// parse hash
			var $hash = $(this).attr("id");
			$hash = $hash.substring(7);

			// assemble request
			var $request_data = { "request_type" : "remove_from_cart", "hash" : $hash };

			// make ajax call
			$.post("_ajax/cart.php",$request_data,function($data) {
				
				// hide wait
				hide_wait();

				// update the whole page
				update_page($data);

			});
		});
		//////////////////////// END REMOVING ITEM - LISTENER BLOCK ///////////////////////////

		//////////////////////// UPDATING TIP - LISTENER BLOCK ///////////////////////////
		$("table input").click(function(){
			var $id = "#" + $(this).attr("id");
			var $name = $(this).prop("name");

			// if custom value is clicked
			if ($id === "#tip_c_box") {
				$("#tip_c").prop("checked",true);
			}
			if (($name === "tip") && ($id === "#tip_c")) {
				$("#tip_c_box").focus();
			}

			if (($name === "tip") && ($id !== "#tip_c")) {
				// parse tip amount
				var $tip_amount = $(this).val();
				$("#tip_c_box").prop("placeholder", $tip_amount);
				update_tip($tip_amount);
			} // if predefined tip is clicked
		});

		$("#tip_c_box").change(function(){
			// parse tip amount
			var $twenty_p = Number($("#tip_20").val());
			var $tip_amount = Number($(this).val());
			$tip_amount = $tip_amount.toFixed(2);
			$("#tip_c").prop("checked",true);
			if ($tip_amount > $twenty_p) {
				var $warning_message 	= "You have entered $ " + $tip_amount + " tip. Do you want to continue?";
				var $button				= '<button id="confirm_tip" class="btn btn-info" type="button" value="' + $tip_amount + '">Confirm</button>';
				show_warning($warning_message,$button,"40px");
			} // end if
			else {
				update_tip($tip_amount);
			}
		});
		//////////////////////// END UPDATING TIP - LISTENER BLOCK ///////////////////////////
	});

</script>







<?php
	include_once("../_includes/warning.php");
	include_once("../_includes/wait.php");
	include_once("../_includes/bottom_bar.php");
?>
</body>
</html>