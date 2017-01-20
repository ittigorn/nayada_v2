<?php

//////////////////// DEBUG FUNCTIONS ///////////////////
function dump($var){
	echo '<pre>';
	print_r($var);
	echo '</pre>';
}


//////////////////// GLOBAL FUNCTIONS ///////////////////
function redirect($location) {
	header("location: ".$location);
	exit;
}// end function

function price($price) {
	return number_format($price,2,".","");
}// end function

function generate_random_hash() {
	return hash('ripemd160', rand().time());
}// end function


////////////////// LOAD CLASSES //////////////////
spl_autoload_register("load_classes");
function load_classes() {
	require_once(SITE_ROOT."_includes/_classes/cart.php");
	require_once(SITE_ROOT."_includes/_classes/cart.cart_item.php");
	require_once(SITE_ROOT."_includes/_classes/cart.cart_item.item_info.php");

	require_once(SITE_ROOT."_includes/_classes/cust.php");
	require_once(SITE_ROOT."_includes/_classes/cust.validated_cust.php");

	require_once(SITE_ROOT."_includes/_classes/food.php");
	require_once(SITE_ROOT."_includes/_classes/mysql_db.php");
	require_once(SITE_ROOT."_includes/_classes/page.php");
	require_once(SITE_ROOT."_includes/_classes/server.php");

	require_once(SITE_ROOT."_includes/_classes/bill.php");

	require_once(SITE_ROOT."_includes/_classes/transaction_paypal.php");
} // end loader
?>