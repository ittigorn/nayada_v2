<?php
class cust {

	public $info;
	public $status;

	function __construct($db,$id,$method = "id") {
		
		// fetch general info
		$this->info = $this->get_cust_info($db,$id,$method);

		// check status
		$this->status = new stdClass();

			// check if email is verified
			$this->status->email_verified = $this->check_email_verified();

			// check if cust is suspended
			$this->status->suspended = $this->check_suspended();

			// check if cust is deactivated
			$this->status->deactivated = $this->check_deactivated();

	}// end constructor

	public static function create_new_customer_record($db,$new_customer_info) {
		$time = time();

		$query  = "INSERT INTO customers ( ";
		$query .= "name_first, ";
		$query .= "name_last, ";
		$query .= "phone, ";
		$query .= "email, ";
		$query .= "password, ";
		$query .= "email_verification_hash, ";
		$query .= "subscription, ";
		$query .= "registration_time, ";
		$query .= "verification_email_sent";
		
		$query .= " ) VALUES ( ";

		$query .= "'{$new_customer_info->name_first}', ";
		$query .= "'{$new_customer_info->name_last}', ";
		$query .= "'{$new_customer_info->phone}', ";
		$query .= "'{$new_customer_info->email}', ";
		$query .= "'{$new_customer_info->password}', ";
		$query .= "'{$new_customer_info->email_verification_hash}', ";
		$query .= "'{$new_customer_info->subscription}', ";
		$query .= "'{$time}', ";
		$query .= "'1' );";
		
		$db->query_and_check($query);
	} // end function

	protected function get_cust_info($db,$id,$method) {
		$info = new stdClass();
		$columns = "`id`,
					`name_first`,
					`name_last`,
					`phone`,
					`alt_phone`,
					`email`,
					`verification_email_sent`,
					`email_verification_hash`,
					`email_verification_time`,
					`wrong_password_count`,
					`wrong_password_streak`,
					`suspended`,
					`deactivated`";
		if ($method === "email") { $query  = "SELECT {$columns} FROM `customers` WHERE `email` = '{$id}' LIMIT 1;"; }
		if ($method === "id") { $query  = "SELECT {$columns} FROM `customers` WHERE `id` = '{$id}' LIMIT 1;"; }
		$result = $db->query_and_check($query);

		$info = mysqli_fetch_object($result);
		return $info;
	} // end function

	protected function check_email_verified() {
		return ($this->info->email_verification_time === NULL) ? false : true;
	}// end function

	private function check_suspended() {
		return ($this->info->suspended === "1") ? true : false;
	}// end function

	private function check_deactivated() {
		return ($this->info->deactivated === "1") ? true : false;
	}// end function

	public function verify_email($db) {
		$query 	= 'UPDATE customers SET ';
		$query .= 'email_verification_time = "'.time().'" ';
		$query .= 'WHERE `email` = "'.$this->info->email.'" ';
		$query .= 'LIMIT 1;';
		$db->query_and_check($query);
	} // end function

	public function plus_verification_email_sent($db) {
		$verification_email_sent = $this->info->verification_email_sent + 1;

		$query 	= 'UPDATE customers SET ';
		$query .= 'verification_email_sent = "'.$verification_email_sent.'" ';
		$query .= 'WHERE `email` = "'.$this->info->email.'" ';
		$query .= 'LIMIT 1;';
		$db->query_and_check($query);
	} // end function

	private function check_cust_exists_in_session() {
		if (isset($_SESSION["cust"])) {
			if (isset($_SESSION["cust"]->status->logged_in)) {
				if ($_SESSION["cust"]->status->logged_in === true) {
					return true;
				}
			}
		}
		return false;
	} // end function

	public function validate_cust($db,$password) {
		$error_array = array();
		$all_ok = true;

		if ($this->verify_password($db,$password) === false) {
			array_push($error_array, "Invalid email address or password");
			$all_ok = false;
		}

		if ($this->info->suspended === "1") {
			array_push($error_array, "Your accound has been suspended");
			$all_ok = false;
		}

		if ($this->info->deactivated === "1") {
			array_push($error_array, "Your accound has been deactivated");
			$all_ok = false;
		}

		if ($this->check_email_verified() === false) {
			array_push($error_array, "You have not verify your email");
			$all_ok = false;
		}

		if ($this->check_cust_exists_in_session() === true) {
			array_push($error_array, "You're already logged in");
			$all_ok = false;
		}
		
		if ($all_ok === true) {return $all_ok;}
		else {return $error_array;}
	} // end function

	
	private function verify_password($db,$password) {
		// query for password hash
		$query  = "SELECT `password` FROM `customers` WHERE `id` = '{$this->info->id}' LIMIT 1;";
		$result = $db->query_and_check($query);
		$password_hash = mysqli_fetch_object($result)->password;

		// validate
		if (password_verify($password, $password_hash) === true) {
			$query 	= 'UPDATE customers SET ';
			$query .= 'last_login_time = "'.time().'", ';
			$query .= 'wrong_password_streak = "0", ';
			$query .= 'login_count = "'.($this->info->login_count + 1).'" ';
			$query .= 'WHERE `email` = "'.$this->info->email.'" ';
			$query .= 'LIMIT 1;';
			$db->query_and_check($query);
			return true;
		}
		else {
			$query 	= 'UPDATE customers SET ';
			$query .= 'wrong_password_count = "'.($this->info->wrong_password_count + 1).'", ';
			$query .= 'wrong_password_streak = "'.($this->info->wrong_password_streak + 1).'" ';
			$query .= 'WHERE `email` = "'.$this->info->email.'" ';
			$query .= 'LIMIT 1;';
			$db->query_and_check($query);
			return false;
		}
	}// end function
}// end class cust
?>