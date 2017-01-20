
<?php
class validated_cust extends cust {

	public $info;
	public $status;

	function __construct($db,$id) {

		// fetch general info
		$this->info = $this->get_cust_info($db,$id,"id");
		$this->status = new stdClass();
		$this->status->logged_in = true;

	}// end constructor

	public function security_check($db,$server) {
		$all_ok = true;

		if ($server->info->login !== "1") {
			$all_ok = false;
		}

		if ($this->info->suspended === "1") {
			$all_ok = false;
		}

		if ($this->info->deactivated === "1") {
			$all_ok = false;
		}

		if ($this->check_email_verified() === false) {
			$all_ok = false;
		}

		$email = $db->clean_input($this->info->email);
		if ($db->check_email_exists($email) !== true) {
			$all_ok = false;
		}
		
		if ($all_ok !== true) {
			if (isset($_SESSION["cust"])){unset($_SESSION["cust"]);}
			if (isset($_SESSION)){unset($_SESSION);}
			session_unset();
			session_destroy();
			$location = $server->status->url_prefix."login.php?action=e_logout";
			redirect($location);
			return false;
		}
		else {
			return true;
		}
	} // end function

	public function logout($db) {
		$query 	= 'UPDATE customers SET ';
		$query .= 'logout_count = "'.($this->info->logout_count + 1).'" ';
		$query .= 'WHERE `id` = "'.$this->info->id.'" ';
		$query .= 'LIMIT 1;';
		$db->query_and_check($query);
	} // end function

} // end class verified_cust
?>