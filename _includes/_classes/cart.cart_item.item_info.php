<?php
class item_info { // to be used as a sub-object of "cart_item" class
	
	public $hash;
	public $option_main;
	public $option_spice;
	public $option_lun;
	public $quantity;
	private $total_price;

	function __construct() {
		$this->hash = generate_random_hash();
	}// end constructor

	public function set_total_price($total_price) {
        if (is_numeric($total_price)) {
            $this->total_price = $total_price;
            return true;
        }
        else {
        	return false;
        }
    }// end setter for total_price

    public function get_total_price() {
        return $this->total_price;
    }// end getter for total_price

}// end class
?>