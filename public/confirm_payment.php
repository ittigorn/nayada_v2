<!doctype html>
<html lang="en">
<head>
<?php
	require_once("../_includes/header_addon.php");

	// redirect if no payment type is set
	if (!isset($_GET["payment"])) {
		$location = $server->status->url_prefix."checkout.php";
		redirect($location);
	}// end if

	if ($server->info->ordering === "1") {

		// evaluate get
		$payment = "undefined";
		if ($_GET["payment"] === "paypal") {
			$payment = "paypal";
		}
		elseif ($_GET["payment"] === "card") {
			$payment = "card";
		}// end elseif

		// calculate summary
		$summary = cart::calculate_summary($server);
		$alert = array_merge($alert,$summary->alert);

		// PAYPAL - GET INFO
		if ($payment === "paypal") {
			// PayPal Express Checkout Call
			// Check to see if the Request object contains a variable named 'token'
			$token = "";
			if (isset($_REQUEST['token'])) {
				$token = $_REQUEST['token'];
			}
			
			// If the Request object contains the variable 'token' then it means that the user is coming from PayPal site.	
			if ( $token != "" ) {

				require_once("../_includes/paypalfunctions.php");

				// Calls the GetExpressCheckoutDetails API call
				$paypal_response_array = GetShippingDetails( $token );
				$paypal_ack = strtoupper($paypal_response_array["ACK"]);
				if($paypal_ack == "SUCCESS" || $paypal_ack == "SUCESSWITHWARNING") {

					// The information that is returned by the GetExpressCheckoutDetails call should be integrated by the partner into his Order Review page
					$paypal_email 			= $paypal_response_array["EMAIL"]; // ' Email address of payer.
					$paypal_id 				= $paypal_response_array["PAYERID"]; // ' Unique PayPal customer account identification number.
					$paypal_status			= $paypal_response_array["PAYERSTATUS"]; // ' Status of payer. Character length and limitations: 10 single-byte alphabetic characters.
			//		$salutation				= $paypal_response_array["SALUTATION"]; // ' Payer's salutation.
					$paypal_name_first		= $paypal_response_array["FIRSTNAME"]; // ' Payer's first name.
			//		$middleName				= $paypal_response_array["MIDDLENAME"]; // ' Payer's middle name.
					$paypal_name_last		= $paypal_response_array["LASTNAME"]; // ' Payer's last name.
			//		$suffix					= $paypal_response_array["SUFFIX"]; // ' Payer's suffix.
					$paypal_country_code 	= $paypal_response_array["COUNTRYCODE"]; // ' Payer's country of residence in the form of ISO standard 3166 two-character country codes.
				
					$_SESSION["cart"]->payment_info			= new stdClass();
					$_SESSION["cart"]->payment_info->paypal	= new stdClass();
					$_SESSION["cart"]->payment_info->paypal = (object) array(
																				"payer_id"				=> $paypal_id,
																				"payer_email"			=> $paypal_email,
																				"payer_status"			=> $paypal_status,
																				"payer_name_first"		=> $paypal_name_first,
																				"payer_name_last"		=> $paypal_name_last,
																				"payer_country_code"	=> $paypal_country_code
																			);
				} 
				else {

					//Display a user friendly Error on the page using any of the following error information returned by PayPal
					$ErrorCode = urldecode($paypal_response_array["L_ERRORCODE0"]);
					$ErrorShortMsg = urldecode($paypal_response_array["L_SHORTMESSAGE0"]);
					$ErrorLongMsg = urldecode($paypal_response_array["L_LONGMESSAGE0"]);
					$ErrorSeverityCode = urldecode($paypal_response_array["L_SEVERITYCODE0"]);
					
					//echo "GetExpressCheckoutDetails API call failed. ";
					//echo "Detailed Error Message: " . $ErrorLongMsg;
					//echo "Short Error Message: " . $ErrorShortMsg;
					//echo "Error Code: " . $ErrorCode;
					//echo "Error Severity Code: " . $ErrorSeverityCode;
				} // end else ( if payment error occurs )
			} //end paypal GetShippingDetail block
		}// end if paypal is submitted
		elseif ($payment === "card") {
			// do something
		}// end if card is submitted
		
	} // end if ordering is allowed

?>
<title>Confirm Payment</title>
<!-- LESS/SASS SCRIPTS GO HERE IF NECCESSARY -->
</head>
<body>
<?php
	include_once("../_includes/top_bar.php");
	if (($server->info->ordering === "1") && ($summary->checkout === true) && (($payment === "paypal") || ($payment === "card"))) {
 ?>
<div id="wrapper">
	<div class="container-fluid">
		
		<div class="breadcrumb_container">
			<div class="breadcrumb">
				<a href="<?php echo $server->status->url_prefix; ?>cart.php" class="reactive">View Cart</a>
				<a href="<?php echo $server->status->url_prefix; ?>checkout.php" class="reactive">Checkout</a>
				<a class="inert active">Confirm</a>
				<a class="inert">Pick-Up</a>
			</div>
		</div>

		<h1 class="title">Confirm Payment</h1>
			<div class="container-fluid">
				<div class="col-sm-8 col-md-6 col-lg-5 col-centered summary">
					<h3>Cart Summary</h3>
					<table>
						<tr>
							<th>Total item</th>
							<td class="item_count"><?php echo $summary->item_count->total_item; ?></td>
						</tr>
						<tr>
							<th>Subtotal</th>
							<td class="subtotal">$ <?php echo $summary->subtotal; ?></td>
						</tr>
						<tr>
							<th>Tax (<?php echo $server->info->tax_rate; ?>%)</th>
							<td class="tax">$ <?php echo $summary->tax; ?></td>
						</tr>
						<tr>
							<th>Tip</th>
							<td class="tip">$ <?php echo $summary->tip; ?></td>
						</tr>
						<tr>
							<th>Grand Total</th>
							<td class="grandtotal">$ <?php echo $summary->grandtotal; ?></td>
						</tr>
					</table>
					<?php if (($payment === "paypal") && ($paypal_ack == "SUCCESS" || $paypal_ack == "SUCESSWITHWARNING")) { ?>
					<h3>PayPal Payer's Summary</h3>
					<table>
						<tr>
							<th>Payer ID</th>
							<td><?php echo $paypal_id; ?></td>
						</tr>
						<tr>
							<th>Payer Status</th>
							<td><?php echo $paypal_status; ?></td>
						</tr>
						<tr>
							<th>Payer Email</th>
							<td><?php echo $paypal_email; ?></td>
						</tr>
						<tr>
							<th>Payer Name</th>
							<td><?php echo $paypal_name_first." ".$paypal_name_last; ?></td>
						</tr>
						<tr>
							<th>Payment Type</th>
							<td class="payment_type">PayPal</td>
						</tr>
					</table>
					<p class="binding">By clicking 'Confirm Payment', you agree to pay <?php echo $server->info->restaurant_name; ?> the 'Grand Total' amount stated above.</p>
					<a href="<?php echo $server->status->url_prefix; ?>cart.php"><button type="button" class="btn btn-default">Edit Cart</button></a>
					<form action="<?php echo $server->status->url_prefix; ?>order_placed.php" target="_self" method="post">
						<button type="submit" name="confirm_paypal" class="btn btn-primary show_wait">Confirm Payment</button>
					</form>
					<?php
						} // end if paypal
						elseif (($payment === "paypal") && ($paypal_ack != "SUCCESS" && $paypal_ack != "SUCESSWITHWARNING")) {
					?>
					<h3>Warning!</h3>
					<p>PayPal has failed to return payer information.</p>
					<?php if ($server->info->paypal_checkout === "1") { ?>
						<a href="paypal_expresscheckout.php"><button type="button" class="btn btn-default">Try Again</button></a>
					<?php } // end if paypal checkout is allowed ?>
					<?php } // end if paypal call fails ?>
				</div>
			</div>
		</div>
	</div>
</div> <!-- END WRAPPER HERE -->





<script type="text/javascript">

	$(document).ready(function() {

		

	}); // end document ready

</script>







<?php
	include_once("../_includes/wait.php");
	} // end if ordering is allowed
	include_once("../_includes/bottom_bar.php");
?>
</body>
</html>