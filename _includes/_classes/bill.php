<?php
class bill {

	public $invoice_id;
	public $subtotal;
	public $tax_rate;
	public $tax;
	public $tip;
	public $grandtotal;
	public $time_placed;
	public $date_placed;
	public $wait_time;
	public $additional_wait_time;
	public $estimated_pickup_time;
	public $payment_method;
	public $adjusted;
	public $adjustment_array;
	public $order_array;
	public $cust_id;
	public $cust_info;
	public $transaction_info;

	public function get_bill_info($db,$server,$invoice_id,$get_order_info_too = false) {
		
		$query  = "SELECT * FROM `history_bill` WHERE `invoice_id` = '{$invoice_id}' LIMIT 1;";
		$result = $db->query_and_check($query);
		$row = mysqli_fetch_object($result);
		if (empty($row)) {return false;}
		// try this >> $this = mysqli_fetch_object($result);
			$this->invoice_id 		= $invoice_id;
			$this->subtotal			= $row->subtotal;
			$this->tax_rate			= $row->tax_rate;
			$this->tax 				= $row->tax;
			$this->tip 				= $row->tip;
			$this->grandtotal 		= $row->grandtotal;
			$this->time_placed 		= $row->time_placed;
			$this->wait_time 		= $row->wait_time;
			$this->additional_wait_time = $row->additional_wait_time;
			$this->cust_id 			= $row->cust_id;
			$this->payment_method 	= $row->payment_method;
			$this->adjusted 		= $row->adjusted;

		// calculate time
		$this->estimated_pickup_time 	= $this->time_placed + ($this->wait_time*60) + ($this->additional_wait_time*60);
		$this->estimated_pickup_time 	= date("g:i a", $this->estimated_pickup_time);
		$this->date_placed				= date("D. M j, Y", $this->time_placed);
		$this->time_placed				= date("g:i a", $this->time_placed);

		// if there's any adjustment, query for it
		$this->adjustment_array 	= array();
		if ($this->adjusted === "1") {
			$query  					= "SELECT * FROM `history_adjustments` WHERE `invoice_id` = '{$invoice_id}';";
			$result 					= $db->query_and_check($query);
			while ($row = mysqli_fetch_object($result)) {
				array_push($this->adjustment_array, $row);
			}// end while
		}// end if

		////////////// GET TRANSACTION INFO /////////////
		if ($this->payment_method === "paypal") {
			$query  = "SELECT * FROM `transaction_paypal` WHERE `invoice_id` = '{$this->invoice_id}' LIMIT 1;";
			$result = $db->query_and_check($query);
			$this->transaction_info = mysqli_fetch_object($result);
		}// end if paypal

		////////////// IF get_order_info_too /////////////
		if ($get_order_info_too === true) {

			// query for order(s) from this bill
			$this->order_array 	= array();
			$query  		= "SELECT * FROM `history_order` WHERE `invoice_id` = '{$invoice_id}';";
			$result = $db->query_and_check($query);
			while ($row = mysqli_fetch_object($result)) {

				// instantiate $order
				$order = new stdClass();

				// pass on raw info
				$order->food_id 			= $row->food_id;
				$order->price_base 			= $row->price_base;
				$order->option_main			= "";
				$order->option_spice		= "";
				$order->option_lun			= "";
				$order->quantity 			= $row->quantity;

				// query for food info
				$food 		= new food($db,$server,$row->food_id,$request = array("food_name_en","food_name_th","print_to_kitchen","option_main_set","available","category"));
				$food_info 	= $food->food_info;

				$order->food_name_en 		= $food_info->food_name_en;
				$order->food_name_th 		= $food_info->food_name_th;
				$order->print_to_kitchen 	= $food_info->print_to_kitchen;
				$order->option_main_set 	= $food_info->option_main_set;

				// replace option price with the option price from history_order to prevent mistakes in case price has been changed
				// query for main option info
				if ($order->option_main_set !== "0") {
					$order->option_main 	= $food->get_option_info($db,"option_main_".$order->option_main_set,$row->option_main);
					$order->option_main->option_price 	= $row->option_main_price;
				}// end if

				// query for spice option info
				if (!empty($row->option_spice)) {
					$order->option_spice 	= $food->get_option_info($db,"option_spice",$row->option_spice);
					$order->option_spice->option_price 	= $row->option_spice_price;
				}// end if

				// query for lunch option info
				if (!empty($row->option_lun)) {
					$order->option_lun 		= $food->get_option_info($db,"option_lun",$row->option_lun);
					$order->option_lun->option_price 	= $row->option_lun_price;
				}// end if

				array_push($this->order_array, $order);
			}// end while

		}// end if get_order_info_too

		// query for customer's info
		$cust 				= new cust($db,$this->cust_id,"id");
		$this->cust_info 	= $cust->info;

	}// end function

	private function generate_invoice_id($db) {
		$invoice_id  = $db->get_auto_increment_value("history_bill");
		$invoice_id .= "-";
		$invoice_id .= $_SESSION["cust"]->info->id;
		$invoice_id .= "-";
		$invoice_id .= time();
		return $invoice_id;
	}// end function

	public function insert_new_record($db,$server,$summary,$payment_method) {
		$this->invoice_id 		= $this->generate_invoice_id($db);
		$this->subtotal			= $summary->subtotal;
		$this->tax_rate			= $server->info->tax_rate;
		$this->tax 				= $summary->tax;
		$this->tip 				= $summary->tip;
		$this->grandtotal		= $summary->grandtotal;
		$this->time_placed 		= time();
		$this->wait_time 		= $server->info->wait_time;
		$this->additional_wait_time = $summary->additional_wait_time;
		$this->cust_id			= $_SESSION["cust"]->info->id;
		$this->payment_method	= $payment_method;
		$this->adjusted			= 0;

		$query  = "INSERT INTO `history_bill` (";
			$query .= "`invoice_id`, ";
			$query .= "`subtotal`, ";
			$query .= "`tax_rate`, ";
			$query .= "`tax`, ";
			$query .= "`tip`, ";
			$query .= "`grandtotal`, ";
			$query .= "`time_placed`, ";
			$query .= "`wait_time`, ";
			$query .= "`additional_wait_time`, ";
			$query .= "`cust_id`, ";
			$query .= "`payment_method`, ";
			$query .= "`adjusted`";
		$query .= ") VALUES (";
			$query .= "'{$this->invoice_id}', ";
			$query .= "'{$this->subtotal}', ";
			$query .= "'{$this->tax_rate}', ";
			$query .= "'{$this->tax}', ";
			$query .= "'{$this->tip}', ";
			$query .= "'{$this->grandtotal}', ";
			$query .= "'{$this->time_placed}', ";
			$query .= "'{$this->wait_time}', ";
			$query .= "'{$this->additional_wait_time}', ";
			$query .= "'{$this->cust_id}', ";
			$query .= "'{$this->payment_method}', ";
			$query .= "'{$this->adjusted}'";
		$query .= ");";

		$db->query_and_check($query);
	}// end function

}// end class bill
?>