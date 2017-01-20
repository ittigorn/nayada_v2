<!doctype html>
<html lang="en">
<head>
<?php
	require_once("../_includes/header_addon.php");

	if ($server->info->ordering === "1") {

		// Setting up default values
		$cc_number		= "";
		$cc_mo			= "";
		$cc_yr			= "";
		$cc_cvv			= "";
		$cc_name_first	= "";
		$cc_name_last	= "";
		$cc_address1	= "";
		$cc_address2	= "";
		$cc_city		= "";
		$cc_state		= "";
		$cc_country		= "";
		$cc_zip			= "";
		$alt_phone		= "";

		function generate_month_selector($cc_mo) {
			$month 	= 1;
			$html = '<label for="cc_mo">Month</label><select id="cc_mo" name="cc_mo">';
			while ($month <= 12){
				$two_digit_month = $month;
				if (strlen($two_digit_month) < 2){$two_digit_month = "0".$two_digit_month;}
				$html .= "<option value='{$two_digit_month}'";
				// keep the selected value
				if ($two_digit_month == $cc_mo){$html .= ' selected="selected"';}
				$html .= ">{$two_digit_month}</option>";
				$month++;
			}// end while
			$html .= '</select>';
			return $html;
		}// end function

		function generate_year_selector($cc_yr) {
			$current_year 		= date("Y");
			$year_range 		= 30;
			$year_range 		= $current_year+$year_range;
			$html = '<label for="cc_yr">Year</label><select id="cc_yr" name="cc_yr">';
			while ($current_year <= $year_range){
				$html .= "<option value='{$current_year}'";
				// keep the selected value
				if ($current_year == $cc_yr){$html .= ' selected="selected"';}
			$html .= ">{$current_year}</option>";
			$current_year++;
			}
			$html .= '</select>';
			return $html;
		}// end function

		// evaluate post

		// calculate summary
		$summary = cart::calculate_summary($server);
		$alert = array_merge($alert,$summary->alert);
		
	} // end if ordering is allowed

?>
<title>Checkout</title>
<!-- LESS/SASS SCRIPTS GO HERE IF NECCESSARY -->
</head>
<body>
<?php
	include_once("../_includes/top_bar.php");
	if (($server->info->ordering === "1") && ($summary->checkout === true)) {
 ?>
<div id="wrapper">
	<div class="container-fluid">
		
		<div class="breadcrumb_container">
			<div class="breadcrumb">
				<a href="<?php echo $server->status->url_prefix; ?>cart.php" class="reactive">View Cart</a>
				<a class="inert active">Checkout</a>
				<a class="inert">Confirm</a>
				<a class="inert">Pick-Up</a>
			</div>
		</div>

		<h1>Checkout</h1>

		<div class="col-lg-11 col-centered">

				<div class="col-lg-8 payment_info">
					<h3>Choose Payment Type</h3>

					<!-- Centered Tabs -->
					<ul class="nav nav-tabs nav-justified payment_tabs">
						<?php echo ($server->info->card_checkout === "1") ? '<li class="active"><a href="#payment1">Card</a></li>' : '<li><a class="disabled">Card</a></li>'; ?>
						<?php 
							if ($server->info->paypal_checkout === "1") {
								if ($server->info->card_checkout === "1") {
									echo '<li><a href="paypal_expresscheckout.php" class="show_wait">PayPal</a></li>';
								}
								else {
									echo '<li class="active"><a href="paypal_expresscheckout.php" class="show_wait">PayPal</a></li>';
								}
							}
							else {
								echo '<li><a class="disabled">PayPal</a></li>';
							}
						?>
					</ul>

					<div class="tab-content">
						<div class="tab-pane fade in active">
					    	<h3>Card</h3>
					    	
					    	<form action="<?php echo $server->status->url_prefix; ?>checkout.php" method="post" target="_self" id="cc_form" class="<?php echo ($server->info->card_checkout !== "1") ? 'disabled' : ''; ?>">
						    	<div class="form-group">
									<label for="cc_number">Card Number</label>
										<input id="cc_number" name="cc_number" type="text" class="form-control" placeholder="0000-0000-0000-0000" maxlength="25" value="<?php echo (isset($_POST["name_first"])) ? $_POST["name_first"] : "" ; ?>" />
										<span class="help-block">* Can be entered with or wiithout dashes.</span>
								</div>
								<div class="form-group">
									<label>Exp Date</label>
									<div class="form-inline">
										<div class="input-group">
											<?php echo generate_month_selector($cc_mo); ?>
										</div>
										<div class="input-group">
											<?php echo generate_year_selector($cc_yr); ?>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="cc_name_first">First Name on Card</label>
										<input id="cc_name_first" name="cc_name_first" type="text" class="form-control" placeholder="Iam" maxlength="50" value="<?php echo (isset($_POST["name_first"])) ? $_POST["name_first"] : "" ; ?>" />
										<span class="help-block">* First and last name are required for security purposes.</span>
								</div>
								<div class="form-group">
									<label for="cc_name_last">Last Name on Card</label>
										<input id="cc_name_last" name="cc_name_last" type="text" class="form-control" placeholder="Hungry" maxlength="50" value="<?php echo (isset($_POST["name_last"])) ? $_POST["name_last"] : "" ; ?>" />
										<span class="help-block">* First and last name are required for security purposes.</span>
								</div>
								<div class="form-group">
									<label for="cc_address1">Address</label>
										<input id="cc_address1" name="cc_address1" type="text" class="form-control" placeholder="11401 Carson St." maxlength="100" value="<?php echo (isset($_POST["cc_address1"])) ? $_POST["cc_address1"] : "" ; ?>" />
										<span class="help-block">* Your billing address.</span>
								</div>
								<div class="form-group">
									<label for="cc_address2">Address 2 (optional)</label>
										<input id="cc_address2" name="cc_address2" type="text" class="form-control" placeholder="Suite A" maxlength="100" value="<?php echo (isset($_POST["cc_address2"])) ? $_POST["cc_address2"] : "" ; ?>" />
										<span class="help-block">* Your additional billing address such as apartment number.</span>
								</div>
								<div class="form-group">
									<label for="cc_city">City</label>
										<input id="cc_city" name="cc_city" type="text" class="form-control" placeholder="Lakewood" maxlength="50" value="<?php echo (isset($_POST["cc_city"])) ? $_POST["cc_city"] : "" ; ?>" />
								</div>
								<div class="form-group">
									<label for="cc_state">State</label>
									<select id="cc_state" name="cc_state">
						                <option value="AK" <?php if ($cc_state === "AK"){echo 'selected="selected"';} ?>>AK</option>
						                <option value="AL" <?php if ($cc_state === "AL"){echo 'selected="selected"';} ?>>AL</option>
						                <option value="AR" <?php if ($cc_state === "AR"){echo 'selected="selected"';} ?>>AR</option>
						                <option value="AZ" <?php if ($cc_state === "AZ"){echo 'selected="selected"';} ?>>AZ</option>
						                <option value="CA" <?php if ($cc_state === "CA"){echo 'selected="selected"';} ?>>CA</option>
						                <option value="CO" <?php if ($cc_state === "CO"){echo 'selected="selected"';} ?>>CO</option>
						                <option value="CT" <?php if ($cc_state === "CT"){echo 'selected="selected"';} ?>>CT</option>
						                <option value="DC" <?php if ($cc_state === "DC"){echo 'selected="selected"';} ?>>DC</option>
						                <option value="DE" <?php if ($cc_state === "DE"){echo 'selected="selected"';} ?>>DE</option>
						                <option value="FL" <?php if ($cc_state === "FL"){echo 'selected="selected"';} ?>>FL</option>
						                <option value="GA" <?php if ($cc_state === "GA"){echo 'selected="selected"';} ?>>GA</option>
						                <option value="HI" <?php if ($cc_state === "HI"){echo 'selected="selected"';} ?>>HI</option>
						                <option value="IA" <?php if ($cc_state === "IA"){echo 'selected="selected"';} ?>>IA</option>
						                <option value="ID" <?php if ($cc_state === "ID"){echo 'selected="selected"';} ?>>ID</option>
						                <option value="IL" <?php if ($cc_state === "IL"){echo 'selected="selected"';} ?>>IL</option>
						                <option value="IN" <?php if ($cc_state === "IN"){echo 'selected="selected"';} ?>>IN</option>
						                <option value="KS" <?php if ($cc_state === "KS"){echo 'selected="selected"';} ?>>KS</option>
						                <option value="KY" <?php if ($cc_state === "KY"){echo 'selected="selected"';} ?>>KY</option>
						                <option value="LA" <?php if ($cc_state === "LA"){echo 'selected="selected"';} ?>>LA</option>
						                <option value="MA" <?php if ($cc_state === "MA"){echo 'selected="selected"';} ?>>MA</option>
						                <option value="MD" <?php if ($cc_state === "MD"){echo 'selected="selected"';} ?>>MD</option>
						                <option value="ME" <?php if ($cc_state === "ME"){echo 'selected="selected"';} ?>>ME</option>
						                <option value="MI" <?php if ($cc_state === "MI"){echo 'selected="selected"';} ?>>MI</option>
						                <option value="MN" <?php if ($cc_state === "MN"){echo 'selected="selected"';} ?>>MN</option>
						                <option value="MO" <?php if ($cc_state === "MO"){echo 'selected="selected"';} ?>>MO</option>
						                <option value="MS" <?php if ($cc_state === "MS"){echo 'selected="selected"';} ?>>MS</option>
						                <option value="MT" <?php if ($cc_state === "MT"){echo 'selected="selected"';} ?>>MT</option>
						                <option value="NC" <?php if ($cc_state === "NC"){echo 'selected="selected"';} ?>>NC</option>
						                <option value="ND" <?php if ($cc_state === "ND"){echo 'selected="selected"';} ?>>ND</option>
						                <option value="NE" <?php if ($cc_state === "NE"){echo 'selected="selected"';} ?>>NE</option>
						                <option value="NH" <?php if ($cc_state === "NH"){echo 'selected="selected"';} ?>>NH</option>
						                <option value="NJ" <?php if ($cc_state === "NJ"){echo 'selected="selected"';} ?>>NJ</option>
						                <option value="NM" <?php if ($cc_state === "NM"){echo 'selected="selected"';} ?>>NM</option>
						                <option value="NV" <?php if ($cc_state === "NV"){echo 'selected="selected"';} ?>>NV</option>
						                <option value="NY" <?php if ($cc_state === "NY"){echo 'selected="selected"';} ?>>NY</option>
						                <option value="OH" <?php if ($cc_state === "OH"){echo 'selected="selected"';} ?>>OH</option>
						                <option value="OK" <?php if ($cc_state === "OK"){echo 'selected="selected"';} ?>>OK</option>
						                <option value="OR" <?php if ($cc_state === "OR"){echo 'selected="selected"';} ?>>OR</option>
						                <option value="PA" <?php if ($cc_state === "PA"){echo 'selected="selected"';} ?>>PA</option>
						                <option value="RI" <?php if ($cc_state === "RI"){echo 'selected="selected"';} ?>>RI</option>
						                <option value="SC" <?php if ($cc_state === "SC"){echo 'selected="selected"';} ?>>SC</option>
						                <option value="SD" <?php if ($cc_state === "SD"){echo 'selected="selected"';} ?>>SD</option>
						                <option value="TN" <?php if ($cc_state === "TN"){echo 'selected="selected"';} ?>>TN</option>
						                <option value="TX" <?php if ($cc_state === "TX"){echo 'selected="selected"';} ?>>TX</option>
						                <option value="UT" <?php if ($cc_state === "UT"){echo 'selected="selected"';} ?>>UT</option>
						                <option value="VA" <?php if ($cc_state === "VA"){echo 'selected="selected"';} ?>>VA</option>
						                <option value="VT" <?php if ($cc_state === "VT"){echo 'selected="selected"';} ?>>VT</option>
						                <option value="WA" <?php if ($cc_state === "WA"){echo 'selected="selected"';} ?>>WA</option>
						                <option value="WI" <?php if ($cc_state === "WI"){echo 'selected="selected"';} ?>>WI</option>
						                <option value="WV" <?php if ($cc_state === "WV"){echo 'selected="selected"';} ?>>WV</option>
						                <option value="WY" <?php if ($cc_state === "WY"){echo 'selected="selected"';} ?>>WY</option>
						            </select>
						        </div>
						        <div class="form-group">
									<label for="cc_zip">Zip Code</label>
										<input id="cc_zip" name="cc_zip" type="text" class="form-control" placeholder="90715" maxlength="20" value="<?php echo (isset($_POST["cc_zip"])) ? $_POST["cc_zip"] : "" ; ?>" />
								</div>
								<div class="form-group">
									<label for="phone2">Alternate Phone Number</label>
										<input id="phone2" name="phone2" type="text" class="form-control" placeholder="562-860-6108" maxlength="20" value="<?php echo (isset($_POST["alt_phone"])) ? $_POST["alt_phone"] : "" ; ?>" />
										<span class="help-block">* If you prefer us to call this number other than what we have on file.</span>
								</div>

								<?php if ($server->info->card_checkout === "1") { ?>
								<button type="button" class="btn btn-default" id="clear_form">Clear Form</button>
								<button type="submit" name="submit" class="btn btn-default" id="submit">Submit</button>
								<span class="help-block">* Plase correct all error(s) before submitting</span>
								<?php } // end if ?>

							</form>
						</div>

					</div>
				</div>
			
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
							<th>Grand Total</th>
							<td class="grandtotal"><?php echo $summary->grandtotal; ?></td>
						</tr>
					</table>
					<a href="<?php echo $server->status->url_prefix; ?>cart.php"><button type="button" class="btn btn-default">Edit Cart</button></a>
				</div>
		</div>
	</div>
</div> <!-- END WRAPPER HERE -->





<script type="text/javascript">

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

		// Functions for showing and hiding helper texts
		$("form .help-block").hide();
		$("input").focusin(function() {
			$(this).siblings("form .help-block").slideDown();
		});
		$("input").focusout(function() {
			$(this).siblings("form .help-block").slideUp();
		});

		// disable the whole form if card checkout is not allowed
		if ($("#cc_form").hasClass("disabled") === true) {
			$("#cc_form input").prop("disabled", true);
			$("#cc_form select").prop("disabled", true);
		}// end if

	}); // end document ready

</script>







<?php
	include_once("../_includes/warning.php");
	include_once("../_includes/wait.php");
	} // end if ordering is allowed
	include_once("../_includes/bottom_bar.php");
?>
</body>
</html>