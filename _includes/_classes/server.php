<?php
class server {

	public $info;
	public $status;
	public $alert;

	function __construct($db){

		$this->status = new stdClass();

		// fetch general info
		$this->info = $this->get_server_info($db);

		// fetch hours info
		$this->info->restaurant_hours = $this->get_hours($db,"restaurant_hours");
		$this->info->lunch_hours = $this->get_hours($db,"lunch_hours");

		// calculate current status
		$this->status->lunch_ok 			= $this->check_lunch_ok();
		$this->status->restaurant_open 		= $this->check_restaurant_open();
		$this->status->ordering_ok			= $this->check_ordering_available();
		$this->status->last_call			= $this->check_last_call();
		$this->status->url_prefix			= $this->get_url_prefix($db);

		// generate and store global alerts
		$this->alert = $this->generate_alert();

	} // end constructor

	private function generate_alert() {
		$alert = array();
		
		if ($this->info->login !== "1") {array_push($alert,"Customer login is temporarily disabled");}
		if ($this->info->registration !== "1") {array_push($alert,"Customer registration is temporarily disabled");}
		if ($this->status->restaurant_open !== true) {
			array_push($alert,"We are closed");
		}
		else {
			if ($this->info->ordering !== "1") {array_push($alert,"Online ordering is temporarily disabled");}
			if ($this->info->card_checkout !== "1") {array_push($alert,"Credit Card checkout is temporarily disabled");}
			if ($this->info->paypal_checkout !== "1") {array_push($alert,"PayPal checkout is temporarily disabled");}
		}// end else

		return $alert;
	}// end function

	private function get_url_prefix($db) {
		$query  = "SELECT `prefix` FROM `url_set` WHERE `set_id` = '{$this->info->url_set}' LIMIT 1;";
		$result = $db->query_and_check($query);
		$row = mysqli_fetch_array($result);
		return $row[0];
	}// end function

	private function check_restaurant_open() {
		
		if ($this->info->restaurant_always_open === "1") {
			return true;
		}
		else {
			date_default_timezone_set($this->info->time_zone);
			$day = strtolower(date("D"));
			$open = false;

			foreach ($this->info->restaurant_hours as $hours) {
				$last_call_ends = ($this->info->last_call_ends * 60);
				$start 			= strtotime($hours->start);
				$end 			= (strtotime($hours->end) - $last_call_ends);

				if ($hours->day === $day) {
					if ((time() >= $start) &&
						(time() < $end)) {
						$open = true;
					}
				}
			}// end foreach
			return $open;
		}// end else
	}// end function

	private function check_last_call(){
		
		if ($this->info->restaurant_always_open === "1") {
			return false;
		}
		else {
			date_default_timezone_set($this->info->time_zone);
			$day 		= strtolower(date("D"));
			$hurry_up 	= false;

			foreach ($this->info->restaurant_hours as $hours) {
				$last_call_ends		= ($this->info->last_call_ends * 60);
				$last_call_period 	= ($this->info->last_call_period * 60);
				$start 				= ((strtotime($hours->end) - $last_call_ends)-$last_call_period);
				$end 				= (strtotime($hours->end) - $last_call_ends);

				if ($hours->day === $day) {
					if ((time() >= $start) &&
						(time() < $end)) {
						$hurry_up = true;
					}
				}
			}// end foreach
			return $hurry_up;
		}// end else
	}// end function

	private function check_ordering_available(){
		if (($this->check_restaurant_open() === true) && ($this->info->ordering === "1")) {
			return true;
		}
		else {
			return false;
		}
	} // end function

	private function check_lunch_ok(){
		if ($this->info->lunch_always_on === "1") {
			return true;
		}
		else {
			if ($this->info->lunch_special === "1") {
				
				$lunch_ok = false;
				date_default_timezone_set($this->info->time_zone);
				$day = strtolower(date("D"));

				foreach ($this->info->lunch_hours as $hours) {
					$start = strtotime($hours->start);
					$end = strtotime($hours->end);

					if ($hours->day === $day) {
						if ((time() >= $start) &&
							(time() < $end)) {
							$lunch_ok = true;
						}
					}
				}// end foreach
				return $lunch_ok;
			}
			else {
				return false;
			}
		}

	} // end function

	private function get_server_info($db){
		$query  = "SELECT * FROM `server`;";
		$result = $db->query_and_check($query);

		if (mysqli_num_rows($result) > 0) {
			$info = mysqli_fetch_object($result);
			return $info;
		}
		else {return NULL;}
	} // end function

	private function get_hours($db,$type){

		if ($type === "restaurant_hours") {
			$query  = "SELECT * FROM `restaurant_hours`;";
			$result = $db->query_and_check($query);

			$restaurant_hours = array();
			while ($row = mysqli_fetch_object($result)) {
				array_push($restaurant_hours, $row);
			}
			return $restaurant_hours;
		}

		elseif ($type === "lunch_hours") {
			$query  = "SELECT * FROM `lunch_hours`;";
			$result = $db->query_and_check($query);

			$lunch_hours = array();
			while ($row = mysqli_fetch_object($result)) {
				array_push($lunch_hours, $row);
			}
			return $lunch_hours;
		}

	} // end function

	public function validate_ajax_key(){

		if (!isset($_SESSION["ajax_key"])){
			die("invalid key");
		}

		if ($_SESSION["ajax_key"] === $this->info->ajax_key) {
			return true;
		}
		else {
			die("invalid key");
		}
	}// end function

	public function validate_recaptcha($recaptcha_response) {
		//verify Google's ReCaptcha
		$validation = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$this->info->recaptcha_secret."&response=".$recaptcha_response);
		$validation = json_decode($validation, true);
		if($validation["success"] === true){
			return true;
		}
		else{
			return false;
		}
	}// end function

}// end class server
?>