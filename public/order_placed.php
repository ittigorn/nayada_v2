<!doctype html>
<html lang="en">
<head>
<?php
	require_once("../_includes/header_addon.php");

	// redirect if no payment type is set
	if (!isset($_GET["invoice_id"])) {
		$location = $server->status->url_prefix."checkout.php";
		redirect($location);
	}// end if

	$view_bill_ok = true;
	$error = "undefined";
	if ($server->info->ordering === "1") {

		$invoice_id = $db->clean_input($_GET["invoice_id"]);

		// query for bill info
		$bill = new bill();

		// check if the invoice_id exists
		if ($bill->get_bill_info($db,$server,$invoice_id,false) === false) {
			$view_bill_ok = false;
			$error = "This bill does not exist.";
		}
		else {
			// check if customer has the privilege to view the invoice
			if (isset($_SESSION["cust"]->info->id)) {
				if ($bill->cust_info->id != $_SESSION["cust"]->info->id) {
					$view_bill_ok = false;
					$error = "You do not have permission to view this bill.";
				}
			}
			else {
				$view_bill_ok = false;
				$error = "You do not have permission to view this bill. Please login.";
			}
		}// end else

	} // end if ordering is allowed

?>
<title>Order Placed</title>
<!-- LESS/SASS SCRIPTS GO HERE IF NECCESSARY -->
</head>
<body>
<?php
	include_once("../_includes/top_bar.php");
 ?>
<div id="wrapper">
	<div class="container-fluid">
		<?php
		if ($view_bill_ok === true) {
		?>
		<div class="breadcrumb_container">
			<div class="breadcrumb">
				<a class="inert">View Cart</a>
				<a class="inert">Checkout</a>
				<a class="inert">Confirm</a>
				<a class="inert active">Pick-Up</a>
			</div>
		</div>
		<h1 class="title">Your Order Has Been Placed</h1>
		<div class="container-fluid">
			<div class="col-sm-8 col-md-6 col-lg-5 col-centered summary">
				<h3>Cart Summary</h3>
				<table>
					<tr>
						<th>Subtotal</th>
						<td class="subtotal">$ <?php echo $bill->subtotal; ?></td>
					</tr>
					<tr>
						<th>Tax (<?php echo $bill->tax_rate; ?>%)</th>
						<td class="tax">$ <?php echo $bill->tax; ?></td>
					</tr>
					<tr>
						<th>Tip</th>
						<td class="tip">$ <?php echo $bill->tip; ?></td>
					</tr>
					<tr>
						<th>Grand Total</th>
						<td class="grandtotal">$ <?php echo $bill->grandtotal; ?></td>
					</tr>
				</table>
				<?php if ($bill->payment_method === "paypal") { ?>
				<h3>PayPal Payer's Summary</h3>
				<table>
				</table>
				<?php
					} // end if paypal
					elseif ($bill->payment_method === "card") {
				?>
				<table>
				</table>
				<?php } // end elseif ?>
			</div>
		</div>
		<?php
		} // end if cust can view the bill
		else {
		?>
		<h1 class="title">Error!</h1>
		<div class="container-fluid">
			<div class="col-sm-8 col-md-6 col-lg-5 col-centered summary">
				<h3><?php echo $error; ?></h3>
			</div>
		</div>
		<?php } // end else (if view_bill_ok === false) ?>
	</div>
</div> <!-- END WRAPPER HERE -->





<script type="text/javascript">

	$(document).ready(function() {

		

	}); // end document ready

</script>







<?php
	include_once("../_includes/bottom_bar.php");
?>
</body>
</html>