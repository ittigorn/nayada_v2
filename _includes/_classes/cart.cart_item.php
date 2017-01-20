<?php
class cart_item extends food {

	public $item_info;
	public $all_ok;
	public $error_array;

	function __construct($db,$server,$food_id,$option_array,$quantity,$request = array("food_id","food_name_en","food_name_th","price_base","available","category","need_time_to_prepare","option_main_set")) {
		
		$this->item_info = new item_info();
		parent::__construct($db,$server,$food_id,$request);


		$this->error_array 	= array();
		$this->all_ok 		= true;

		// check cust logged in
		if (isset($_SESSION["cust"])) {
			$cust_logged_in = $_SESSION["cust"]->security_check($db,$server);
		}
		else {$cust_logged_in = false;}
		if ($cust_logged_in !== true) {$this->all_ok = false; array_push($this->error_array, "You're not logged in");}
		
		// check ordering ok
		if ($this->status->ordering_ok !== true) {$this->all_ok = false; array_push($this->error_array, "Food is not available");}

		// assign quantity
		if (empty($quantity)) {$quantity = 1;}
		$this->item_info->quantity = $quantity;

		// assign options to item_info and check options available
		if ($this->assign_options_to_cart_item($db,$option_array) === false) {$this->all_ok = false; array_push($this->error_array, "Selected option is not available");};

		// check if max item is reached
		if ($this->check_add_this_item_to_cart_possible($server) !== true) {$this->all_ok = false; array_push($this->error_array, "(header)Your cart is full <span style='font-weight: normal;'>( Maximum {$server->info->cart_max_counted_item} on-demand items per order )</span>");}

		$this->item_info->set_total_price($this->calculate_total_price());
	}// end constructor

	private function assign_options_to_cart_item($db,$option_array){
		$all_ok = true;

		if (isset($option_array->option_main)) {
			if ($this->food_info->option_main_set !== "0") {
				$option_set_name = "option_main_".$this->food_info->option_main_set;
				$this->item_info->option_main = $this->get_option_info($db,$option_set_name,$option_array->option_main);
				if ($this->item_info->option_main->option_available !== "1") {$all_ok = false;}
			}
		}
		if (isset($option_array->option_spice)) {
			$this->item_info->option_spice = $this->get_option_info($db,"option_spice",$option_array->option_spice);
			if ($this->item_info->option_spice->option_available !== "1") {$all_ok = false;}
		}
		if (isset($option_array->option_lun)) {
			$this->item_info->option_lun = $this->get_option_info($db,"option_lun",$option_array->option_lun);
			if ($this->item_info->option_lun->option_available !== "1") {$all_ok = false;}
		}

		return $all_ok;
	}// end function

	private function calculate_total_price() {
		
		$total_price = $this->food_info->price_base;
		
		// sum all prices
		if (isset($this->item_info->option_main->option_price)) {
			$total_price += $this->item_info->option_main->option_price;
		}
		if (isset($this->item_info->option_spice->option_price)) {
			$total_price += $this->item_info->option_spice->option_price;
		}
		if (isset($this->item_info->option_lun->option_price)) {
			$total_price += $this->item_info->option_lun->option_price;
		}

		// times with quantity
		$total_price = $total_price * $this->item_info->quantity;

		return $total_price;
	}// end function

	public function check_add_this_item_to_cart_possible($server) {
		
		// count item in cart
		$item_count = $item_in_cart = cart::count_item();
		if ($this->food_info->need_time_to_prepare === "1") {
			if (($item_count->counted_item + $this->item_info->quantity) <= $server->info->cart_max_counted_item) {
				return true;
			}
			else {
				return false;
			}
		}
		else {
			if (($item_count->total_item + $this->item_info->quantity) <= $server->info->cart_max_total_item) {
				return true;
			}
			else {
				return false;
			}
		}

	}// end function

	public function insert_new_record($db,$bill) {
		//echo "inserted {$this->food_info->food_name_en}<br>";

		// setup option variables in case there's no option for the item
		$option_main 		= NULL;
		$option_main_price 	= 0;
		$option_spice 		= NULL;
		$option_spice_price = 0;
		$option_lun 		= NULL;
		$option_lun_price 	= 0;
		if (isset($this->item_info->option_main->option_code)) {
			$option_main 		= $this->item_info->option_main->option_code;
			$option_main_price 	= $this->item_info->option_main->option_price;
		}
		if (isset($this->item_info->option_spice->option_code)) {
			$option_spice 		= $this->item_info->option_spice->option_code;
			$option_spice_price 	= $this->item_info->option_spice->option_price;
		}
		if (isset($this->item_info->option_lun->option_code)) {
			$option_lun 		= $this->item_info->option_lun->option_code;
			$option_lun_price 	= $this->item_info->option_lun->option_price;
		}

		$query  = "INSERT INTO `history_order` (";
		$query .= "`invoice_id`, ";
		$query .= "`food_id`, ";
		$query .= "`price_base`, ";
		$query .= "`option_main`, ";
		$query .= "`option_main_price`, ";
		$query .= "`option_spice`, ";
		$query .= "`option_spice_price`, ";
		$query .= "`option_lun`, ";
		$query .= "`option_lun_price`, ";
		$query .= "`quantity`, ";
		$query .= "`time_placed`";
		$query .= ") VALUES (";
		$query .= "'{$bill->invoice_id}', ";
		$query .= "'{$this->food_info->food_id}', ";
		$query .= "'{$this->food_info->price_base}', ";
		$query .= "'{$option_main}', ";
		$query .= "'{$option_main_price}', ";
		$query .= "'{$option_spice}', ";
		$query .= "'{$option_spice_price}', ";
		$query .= "'{$option_lun}', ";
		$query .= "'{$option_lun_price}', ";
		$query .= "'{$this->item_info->quantity}', ";
		$query .= "'{$bill->time_placed}'";
		$query .= ");";
		$db->query_and_check($query);
	}// end function

} // end class cart_item
?>