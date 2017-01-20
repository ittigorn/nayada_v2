<!doctype html>
<html lang="en">
<head>
<?php
	require_once("../_includes/header_addon.php");

	// redirect if no payment type is set
	if ((!isset($_POST["confirm_paypal"])) && (!isset($_POST["confirm_card"]))) {
		$location = $server->status->url_prefix."checkout.php";
		redirect($location);
	}// end if

	if ($server->info->ordering === "1") {

		// enable mailer
		$page->enable_mailer($db,$server);

		// calculate summary
		$summary = cart::calculate_summary($server);
		$alert = array_merge($alert,$summary->alert);

		if ($summary->checkout === true) {

			// setup variables
			$payment_ok = false;
			$payment_method = "undefined";

			if  (isset($_POST["confirm_card"]) &&
				(isset($_SESSION["cart"]->payment_info->card->cc_number)) &&
				($server->info->card_checkout === "1")) {
				// Authorize.net setup
				// require('vendor/autoload.php');
				// use net\authorize\api\contract\v1 as AnetAPI;
				// use net\authorize\api\controller as AnetController;
				// define("AUTHORIZENET_LOG_FILE", "phplog");
			} // end if card checkout is allowed

			// evaluate post
			if  (isset($_POST["confirm_paypal"]) &&
				(isset($_SESSION["cart"]->payment_info->paypal->payer_id)) &&
				($server->info->paypal_checkout === "1")) {

				// require paypalfunction
				require_once("../_includes/paypalfunctions.php");

				// execute paypal final confirmation
				$paypal_response_array = ConfirmPayment ($summary->grandtotal);

				// parse response array
				$paypal_ack = strtoupper($paypal_response_array["ACK"]);
				if (($paypal_ack == "SUCCESS") || ($paypal_ack == "SUCCESSWITHWARNING")) {
					
					// setup variables for result display
					$payment_ok 				=	true;
					$payment_method				=	"paypal";
					$paypal_transaction_info	= 	new stdClass();
				
					/////////////////// ASSIGNING PAYPAL RESPONSE INTO A TEMPORARY OBJECT ////////////////////
					$paypal_transaction_info->transaction_id		= $paypal_response_array["PAYMENTINFO_0_TRANSACTIONID"]; // ' Unique transaction ID of the payment. Note:  If the PaymentAction of the request was Authorization or Order, this value is your AuthorizationID for use with the Authorization & Capture APIs. 
					$paypal_transaction_info->transaction_type		= $paypal_response_array["PAYMENTINFO_0_TRANSACTIONTYPE"]; //' The type of transaction Possible values: l  cart l  express-checkout 
					$paypal_transaction_info->payment_type			= $paypal_response_array["PAYMENTINFO_0_PAYMENTTYPE"];  //' Indicates whether the payment is instant or delayed. Possible values: l  none l  echeck l  instant 
					//$paypal_transaction_info->order_time 			= $paypal_response_array["PAYMENTINFO_0_ORDERTIME"];  //' Time/date stamp of payment
					$paypal_transaction_info->amount_charged		= $paypal_response_array["PAYMENTINFO_0_AMT"];  //' The final amount charged, including any shipping and taxes from your Merchant Profile.
					$paypal_transaction_info->currency_code			= $paypal_response_array["PAYMENTINFO_0_CURRENCYCODE"];  //' A three-character currency code for one of the currencies listed in PayPay-Supported Transactional Currencies. Default: USD. 
					//$feeAmt			= $paypal_response_array["PAYMENTINFO_0_FEEAMT"];  //' PayPal fee amount charged for the transaction
					$paypal_transaction_info->settle_amount 		=  (isset($paypal_response_array["PAYMENTINFO_0_SETTLEAMT"])) ? $paypal_response_array["PAYMENTINFO_0_SETTLEAMT"] : "" ;  //' Amount deposited in your PayPal account after a currency conversion.
					//$taxAmt			= $paypal_response_array["PAYMENTINFO_0_TAXAMT"];  //' Tax charged on the transaction.
					$paypal_transaction_info->exchange_rate 		=  (isset($paypal_response_array["PAYMENTINFO_0_EXCHANGERATE"])) ? $paypal_response_array["PAYMENTINFO_0_EXCHANGERATE"] : "";  //' Exchange rate if a currency conversion occurred. Relevant only if your are billing in their non-primary currency. If the customer chooses to pay with a currency other than the non-primary currency, the conversion occurs in the customer's account.
					
					
					/*' Status of the payment: 
							'Completed: The payment has been completed, and the funds have been added successfully to your account balance.
							'Pending: The payment is pending. See the PendingReason element for more information. */
					$paypal_transaction_info->payment_status 		= (isset($paypal_response_array["PAYMENTINFO_0_PAYMENTSTATUS"])) ? $paypal_response_array["PAYMENTINFO_0_PAYMENTSTATUS"] : ""; 
				
					/*
					'The reason the payment is pending:
					'  none: No pending reason 
					'  address: The payment is pending because your customer did not include a confirmed shipping address and your Payment Receiving Preferences is set such that you want to manually accept or deny each of these payments. To change your preference, go to the Preferences section of your Profile. 
					'  echeck: The payment is pending because it was made by an eCheck that has not yet cleared. 
					'  intl: The payment is pending because you hold a non-U.S. account and do not have a withdrawal mechanism. You must manually accept or deny this payment from your Account Overview. 		
					'  multi-currency: You do not have a balance in the currency sent, and you do not have your Payment Receiving Preferences set to automatically convert and accept this payment. You must manually accept or deny this payment. 
					'  verify: The payment is pending because you are not yet verified. You must verify your account before you can accept this payment. 
					'  other: The payment is pending for a reason other than those listed above. For more information, contact PayPal customer service. 
					*/
					
					$paypal_transaction_info->pending_reason		= (isset($paypal_response_array["PAYMENTINFO_0_PENDINGREASON"])) ? $paypal_response_array["PAYMENTINFO_0_PENDINGREASON"] : "";  
				
					/*
					'The reason for a reversal if TransactionType is reversal:
					'  none: No reason code 
					'  chargeback: A reversal has occurred on this transaction due to a chargeback by your customer. 
					'  guarantee: A reversal has occurred on this transaction due to your customer triggering a money-back guarantee. 
					'  buyer-complaint: A reversal has occurred on this transaction due to a complaint about the transaction from your customer. 
					'  refund: A reversal has occurred on this transaction because you have given the customer a refund. 
					'  other: A reversal has occurred on this transaction due to a reason not listed above. 
					*/
					
					$paypal_transaction_info->reason_code 			= (isset($paypal_response_array["PAYMENTINFO_0_REASONCODE"])) ? $paypal_response_array["PAYMENTINFO_0_REASONCODE"] : "";   
					/////////////////// END ASSIGNING PAYPAL RESPONSE INTO A TEMPORARY OBJECT ////////////////////
					
					
		
					//////////////////////////////////////////////////////////////////////
					///////////////////////// CREATE RECORDS BELOW ///////////////////////
					//////////////////////////////////////////////////////////////////////

					// Insert new record in history_bill table
					$bill = new bill;
					$bill->insert_new_record($db,$server,$summary,$payment_method);

					// Insert new record in transaction_paypal table
					$paypal_transaction = new paypal_transaction;
					$paypal_transaction->insert_new_record(
																$db,
																$bill->invoice_id,
																$bill->time_placed,
																$paypal_transaction_info
															);



					//////////// CREATE RECORD IN THE history_order TABLE TOO ////////////
					foreach ($_SESSION["cart"]->items as $cart_item) {
						$cart_item->insert_new_record($db,$bill);
					}// end foreach

					//////////// ONCE DONE INSERTING NEW RECORDS, UNSET CART ////////////
					cart::clear_cart();

					///////////// SEND THE RESTAURANT KITCHEN RECEIPT EMAIL //////////////
					$mailer->send_kitchen_slip($db,$server,$bill->invoice_id);

					///////////// SEND THE RESTAURANT PAYPAL PICK-UP SLIP EMAIL //////////////
					$mailer->send_paypal_pickup_slip($db,$server,$bill->invoice_id);
					
					////////////////// SEND THE RESTAURANT CHECKER SLIP //////////////////
					$mailer->send_checker_slip($db,$server,$bill->invoice_id);
					
					///////////////// SEND CUSTOMER A CONFIRMATION EMAIL /////////////////
					$mailer->send_order_confirmation($db,$server,$bill->invoice_id);

					///////////////// REDIRECT AND SHOW REPORT ////////////////
					$location = $server->status->url_prefix."order_placed.php?invoide_id=".$bill->invoide_id;
					redirect($location);
					
				}// end if paypal payment is a success
				else { // THIS IS TRIGGERED WHEN TRANSACTION ERROR OCCURS
				
					$paypal_error_code				= urldecode($paypal_response_array["L_ERRORCODE0"]);
					$paypal_error_short_message		= urldecode($paypal_response_array["L_SHORTMESSAGE0"]);
					$paypal_error_long_message		= urldecode($paypal_response_array["L_LONGMESSAGE0"]);
					$paypal_error_severity_code		= urldecode($paypal_response_array["L_SEVERITYCODE0"]);
					
					////////////////// CREATE NEW TRANSACTION ERROR LOG //////////////////
					
					////////////////// setting up message for error report email ///////////////////
					
				} // end if paypal error occurs
			}// finalize paypal
		}// if qualified to checkout
		
	} // end if ordering is allowed

?>
<title>Cannot place order</title>
<!-- LESS/SASS SCRIPTS GO HERE IF NECCESSARY -->
</head>
<body>
<?php
	include_once("../_includes/top_bar.php");
	if (($server->info->ordering === "1") && ($summary->checkout === true) && (($payment_method === "paypal") || ($payment_method === "card"))) {
 ?>





<div id="wrapper">
	<div class="container-fluid">
		
		
	</div>
</div> <!-- END WRAPPER HERE -->





<script type="text/javascript">

	$(document).ready(function() {

		

	}); // end document ready

</script>







<?php
	} // end if ordering is allowed
	include_once("../_includes/bottom_bar.php");
?>
</body>
</html>