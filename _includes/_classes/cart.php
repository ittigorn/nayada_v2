<?php
class cart {

	public static function check_cart_exists() {
		if (!isset($_SESSION["cart"])) {
			self::create_empty_cart();
		}// end if
	} // end function

	public static function count_item() {
		// count item in cart
		if (!isset($_SESSION["cart"]->items)) {
			return 	(object)	array(
									"counted_item" => 0,
									"total_item" => 0
								);
		}
		else {
			$counted_item 	= 0;
			$total_item		= 0;
			foreach ($_SESSION["cart"]->items as $cart_item) {
				$total_item += $cart_item->item_info->quantity;
				if ($cart_item->food_info->need_time_to_prepare === "1") {
					$counted_item += $cart_item->item_info->quantity;
				}
			}
			return 	(object) 	array(
									"counted_item" => $counted_item,
									"total_item" => $total_item
								);
		}
	}// end function

	public static function get_tip() {
		if (isset($_SESSION["cart"]->tip)) {
			if (is_numeric($_SESSION["cart"]->tip) && ($_SESSION["cart"]->tip != 0)) {return price($_SESSION["cart"]->tip);}
			else {return 0;}
		}// end if tip is set
		else {
			$_SESSION["cart"]->tip = 0;
			return 0;
		}
	}// end function

	public static function clear_cart() {

		// clear cart
		self::create_empty_cart();

		// clear paypal's messy variables
		if (isset($_SESSION["nvpReqArray"])) {unset($_SESSION["nvpReqArray"]);}
		if (isset($_SESSION["payer_id"])) {unset($_SESSION["payer_id"]);}
		if (isset($_SESSION["currencyCodeType"])) {unset($_SESSION["currencyCodeType"]);}
		if (isset($_SESSION["PaymentType"])) {unset($_SESSION["PaymentType"]);}
		if (isset($_SESSION["TOKEN"])) {unset($_SESSION["TOKEN"]);}
	} // end function

	public static function set_tip($tip) {
		if ($tip != 0) {$_SESSION["cart"]->tip = price($tip);}
		else {$_SESSION["cart"]->tip = $tip;}
	}// end function

	public static function calculate_summary($server, $combined_alert = false) {
		$tip 		= self::get_tip();
		$subtotal 	= price(self::calculate_subtotal());
		$tax 		= price(self::calculate_tax($server,$subtotal));
		$grandtotal = price($subtotal + $tax + $tip);
		$item_count = self::count_item();

		// calculate additional wait time
		if ($item_count->counted_item > $server->info->additional_wait_time_treshold) {
			$item_over_threshold = $item_count->counted_item - $server->info->additional_wait_time_treshold;
		}
		else {
			$item_over_threshold = 0;
		}
		$additional_wait_time = $item_over_threshold * $server->info->additional_wait_time_increment;

		// generate alert
		$alert 		= array();
		if ($subtotal < $server->info->min_order_amount) {array_push($alert,'(header)A minimum subtotal of $'.$server->info->min_order_amount.' is required per order');}
		if ($item_over_threshold !== 0) {array_push($alert,'(header)You have '.$item_count->counted_item.' on-demand items in your cart.<br />This might put about '.$additional_wait_time.' minutes delay to your order.');}

		// generate combined alert in html (optional)
		if ($combined_alert === true) {
			$combined_alert = array_merge($alert, $server->alert);
			$combined_alert = page::generate_alert($combined_alert);
		}// end if

		// check if allowed to checkout or not with the current status
		$checkout = true;
		if ($subtotal < $server->info->min_order_amount) {$checkout = false;}
		if ($server->status->restaurant_open !== true) {$checkout = false;}
		if ($server->info->ordering !== "1") {$checkout = false;}

		return (object) array(
							"subtotal"				=> $subtotal,
							"tax"					=> $tax,
							"tip"					=> $tip,
							"grandtotal"			=> $grandtotal,
							"item_count"			=> $item_count,
							"item_over_threshold" 	=> $item_over_threshold,
							"additional_wait_time" 	=> $additional_wait_time,
							"alert"					=> $alert,
							"combined_alert" 		=> $combined_alert,
							"checkout"				=> $checkout
						);
	}// end function

	private static function calculate_subtotal() {
		// count item in cart
		if (!isset($_SESSION["cart"]->items)) {
			return 0;
		}
		else {
			$subtotal = 0;
			foreach ($_SESSION["cart"]->items as $cart_item) {
				$subtotal += $cart_item->item_info->get_total_price();
			}
			return $subtotal;
		}
	}// end function

	private static function calculate_tax($server,$subtotal) {
		$tax_rate = $server->info->tax_rate;
		return ($subtotal/100)*$tax_rate;
	}// end function

	public static function check_food_exists($food_id) {
		if (isset($_SESSION["cart"]->items)) {
			foreach ($_SESSION["cart"]->items as $cart_item) {
				if (isset($cart_item->food_info->food_id)) {
					if ($cart_item->food_info->food_id === $food_id) {return true;}
				}
			}
			return false;
		}
		else {
			return false;
		}
	}// end function 

	public static function generate_item_list() {
		// count item in cart
		if (!isset($_SESSION["cart"]->items)) {
			return "<h4>(No Item)</h4>";
		}
		else {
			$html  = "<ol>";
			foreach ($_SESSION["cart"]->items as $cart_item) {

				$price = price($cart_item->item_info->get_total_price());

				$html .= "<li>";
					$html .= "<h4><span class='food_name'>{$cart_item->food_info->food_name_en}</span>";

					// display option(s)
					if (!empty($cart_item->item_info->option_main)) {
						$html .= ", ".$cart_item->item_info->option_main->option_name_en;
					}
					if (!empty($cart_item->item_info->option_spice)) {
						$html .= ", ".$cart_item->item_info->option_spice->option_name_en;
					}
					if (!empty($cart_item->item_info->option_lun)) {
						$html .= ", ".$cart_item->item_info->option_lun->option_name_en;
					}

					if ($cart_item->item_info->quantity > 1) {$html .= ", x".$cart_item->item_info->quantity;}
					$html .= "</h4>";
					$html .= "<span class='price'>$ {$price}</span>";
				$html .= "<button id='remove_{$cart_item->item_info->hash}' type='button' class='btn btn-sm btn-default remove_item'>Remove</button>";
				$html .= "</li>";
			}
			$html .= "</ol>";
			return $html;
		}
	}// end function

	public static function add_item($item) {
		if (isset($_SESSION["cart"])) {
			if (isset($_SESSION["cart"]->items)) {
				if (is_array($_SESSION["cart"]->items)) {
					array_push($_SESSION["cart"]->items, $item);
				}
				else {
					unset($_SESSION["cart"]->items);
					$_SESSION["cart"]->items = array($item);
				}
			}
			else {
				$_SESSION["cart"]->items = array($item);
			}
		}
		else { // if cart is not set then instantiate one with standard class
			self::create_empty_cart();
			$_SESSION["cart"]->items = array($item);
		}
	}// end function

	public static function create_empty_cart() {
		$_SESSION["cart"] = new stdClass();
		$_SESSION["cart"]->items = array();
		$_SESSION["cart"]->tip = 0;
	}// end function

	public static function remove_item($hash) {
		if (isset($_SESSION["cart"]->items)) {
			foreach ($_SESSION["cart"]->items as $key => $value) {
				if ($value->item_info->hash === $hash) {
					unset($_SESSION["cart"]->items[$key]);
					$_SESSION["cart"]->items = array_values($_SESSION["cart"]->items); // resets array keys
					return true;
				}
			} // end foreach
			return false;
		}
		else {
			return false;
		}
	}// end function

}// end class cart
?>