<?php 
class paypal_transaction {

	public $invoice_id;
	public $amount_charged;
	public $time_placed;
	public $payer_id;
	public $payer_status;
	public $payer_email;
	public $payer_name_first;
	public $payer_name_last;
	public $transaction_id;
	public $transaction_type;
	public $payment_type;
	public $payment_status;
	public $pending_reason;
	public $reason_code;
	public $currency_code;
	public $settle_amount;
	public $exchange_rate;

	public function insert_new_record(
										$db,
										$invoice_id,
										$time_placed,
										$paypal_transaction_info ) {

		$this->invoice_id 			= $invoice_id;
		$this->amount_charged 		= $db->clean_input($paypal_transaction_info->amount_charged);
		$this->time_placed			= $time_placed;
		$this->payer_id				= $db->clean_input($_SESSION["cart"]->payment_info->paypal->payer_id);
		$this->payer_status			= $db->clean_input($_SESSION["cart"]->payment_info->paypal->payer_status);
		$this->payer_email			= $db->clean_input($_SESSION["cart"]->payment_info->paypal->payer_email);
		$this->payer_name_first		= $db->clean_input($_SESSION["cart"]->payment_info->paypal->payer_name_first);
		$this->payer_name_last		= $db->clean_input($_SESSION["cart"]->payment_info->paypal->payer_name_last);
		$this->payer_country_code	= $db->clean_input($_SESSION["cart"]->payment_info->paypal->payer_country_code);
		$this->transaction_id 		= $db->clean_input($paypal_transaction_info->transaction_id);
		$this->transaction_type 	= $db->clean_input($paypal_transaction_info->transaction_type);
		$this->payment_type 		= $db->clean_input($paypal_transaction_info->payment_type);
		$this->payment_status 		= $db->clean_input($paypal_transaction_info->payment_status);
		$this->pending_reason 		= $db->clean_input($paypal_transaction_info->pending_reason);
		$this->reason_code 			= $db->clean_input($paypal_transaction_info->reason_code);
		$this->currency_code		= $db->clean_input($paypal_transaction_info->currency_code);
		$this->settle_amount		= $db->clean_input($paypal_transaction_info->settle_amount);
		$this->exchange_rate 		= $db->clean_input($paypal_transaction_info->exchange_rate);

		$query  = "INSERT INTO `transaction_paypal` (";
			$query .= "`invoice_id`, ";
			$query .= "`amount_charged`, ";
			$query .= "`time_placed`, ";
			$query .= "`payer_id`, ";
			$query .= "`payer_status`, ";
			$query .= "`payer_email`, ";
			$query .= "`payer_name_first`, ";
			$query .= "`payer_name_last`, ";
			$query .= "`payer_country_code`, ";
			$query .= "`transaction_id`, ";
			$query .= "`transaction_type`, ";
			$query .= "`payment_type`, ";
			$query .= "`payment_status`, ";
			$query .= "`pending_reason`, ";
			$query .= "`reason_code`, ";
			$query .= "`currency_code`, ";
			$query .= "`settle_amount`, ";
			$query .= "`exchange_rate`";
		$query .= ") VALUES (";
			$query .= "'{$this->invoice_id}', ";
			$query .= "'{$this->amount_charged}', ";
			$query .= "'{$this->time_placed}', ";
			$query .= "'{$this->payer_id}', ";
			$query .= "'{$this->payer_status}', ";
			$query .= "'{$this->payer_email}', ";
			$query .= "'{$this->payer_name_first}', ";
			$query .= "'{$this->payer_name_last}', ";
			$query .= "'{$this->payer_country_code}', ";
			$query .= "'{$this->transaction_id}', ";
			$query .= "'{$this->transaction_type}', ";
			$query .= "'{$this->payment_type}', ";
			$query .= "'{$this->payment_status}', ";
			$query .= "'{$this->pending_reason}', ";
			$query .= "'{$this->reason_code}', ";
			$query .= "'{$this->currency_code}', ";
			$query .= "'{$this->settle_amount}', ";
			$query .= "'{$this->exchange_rate}'";
		$query .= ");";

		$db->query_and_check($query);

	}// end function


	
}// end class
?>