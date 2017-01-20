<?php
require_once("../../../_nayada_connections/config.php");
require_once(SITE_ROOT."_includes/functions.php");
session_start();
$db 	= new mysql_db(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
$server = new server($db);
if (isset($_SESSION["cust"])) {
	$cust_logged_in = $_SESSION["cust"]->security_check($db,$server);
}
else {$cust_logged_in = false;}
$summary = cart::calculate_summary($server);

if ($summary->checkout !== true) {
	$location = $server->status->url_prefix."cart.php";
	redirect($location);
}// end if not qualified to checkout
else {
	// setting up urls
	$returnURL = $server->status->url_prefix."confirm_payment.php?payment=paypal";
	$cancelURL = $server->status->url_prefix."checkout.php";

	// require paypal functions after all variables are set up
	require_once(SITE_ROOT."_includes/paypalfunctions.php");

	// ==================================
	// PayPal Express Checkout Module
	// ==================================

	//'------------------------------------
	//' The paymentAmount is the total value of 
	//' the shopping cart, that was set 
	//' earlier in a session variable 
	//' by the shopping cart page
	//'------------------------------------
	$paymentAmount = $summary->grandtotal;

	//'------------------------------------
	//' The currencyCodeType and paymentType 
	//' are set to the selections made on the Integration Assistant 
	//'------------------------------------
	$currencyCodeType = "USD";
	$paymentType = "Sale";

	//'------------------------------------
	//' Calls the SetExpressCheckout API call
	//'
	//' The CallShortcutExpressCheckout function is defined in the file PayPalFunctions.php,
	//' it is included at the top of this file.
	//'-------------------------------------------------
	$resArray = CallShortcutExpressCheckout ($paymentAmount, $currencyCodeType, $paymentType, $returnURL, $cancelURL);
	$ack = strtoupper($resArray["ACK"]);
	if($ack=="SUCCESS" || $ack=="SUCCESSWITHWARNING") {
		RedirectToPayPal ( $resArray["TOKEN"] );
	} 
	else {
		//Display a user friendly Error on the page using any of the following error information returned by PayPal
		$ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
		$ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
		$ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
		$ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);
		
		//echo "SetExpressCheckout API call failed. <br />";
		//echo "Detailed Error Message: " . $ErrorLongMsg."<br />";
		echo "PayPal has returned an error : " . $ErrorLongMsg."<br />";
		echo "Please go back and try again.<br />";
		echo "We're sorry for your inconvenience";
		//echo "Short Error Message: " . $ErrorShortMsg;
		//echo "Error Code: " . $ErrorCode;
		//echo "Error Severity Code: " . $ErrorSeverityCode;
	}
}// end if qualified to checkout
?>