<?php
class mysql_db extends mysqli {

	function __construct() {
		parent::__construct(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
		if (mysqli_connect_error()) {
            die('Connect Error [ ' . mysqli_connect_errno() . ' ] ' . mysqli_connect_error());
        }// end if
	}// end construct function
	
	public function query_and_check($query){
		$result = $this->query($query);
		$this->check_query($result);
		return $result;
	}// end function
	
	private function check_query($result){
		if(!$result){
			die('Database Query failed! [ '. mysqli_error($this) .' ]');
		}// end if
	}// end function

	private function reset_auto_increment($table_name) {
		$query = "ALTER TABLE '{$table_name}' AUTO_INCREMENT = 1;";
		$this->query_and_check($query);
	}// end function

	public function clean_input($input) {
		$input = trim($input);
		$input = stripslashes($input);
		$input = htmlspecialchars($input);
		$input = mysqli_real_escape_string($this, $input);
		return $input;
	}// end function

	public function clean_array($input){
		$cleaned_input = array();
		while ($input) {
			$current_input = array_shift($input);
			$current_input = $this->clean_input($current_input);
			array_push($cleaned_input,$current_input);
		}
		return $cleaned_input;
	}// end function

	public function clean_simple_POST() {
		foreach ($_POST as $key => $value) {
			$_POST[$key] = $this->clean_input($value);
		}// end foreach
	}// end function

	public function hash_password($password) {
		return password_hash($password, PASSWORD_BCRYPT);
	}// end function

	public function check_email_exists($email) {
		$query = "SELECT `email` FROM `customers` WHERE `email` = '{$email}' LIMIT 1";
		$result = $this->query_and_check($query);
		$count = 0;
		while ($row = mysqli_fetch_array($result)) {
			$count++;
		}// end while
		if ($count > 0) {return true;}
		else {return false;}
	}// end function

	public function get_auto_increment_value($table_name){
		$query  		 = "SELECT `AUTO_INCREMENT` FROM INFORMATION_SCHEMA.TABLES ";
		$query 			.= "WHERE TABLE_SCHEMA = '".DB_NAME."' AND TABLE_NAME = '{$table_name}';";
		$result   		 = $this->query_and_check($query);
		return mysqli_fetch_object($result)->AUTO_INCREMENT;
	}// end function

}// end class mysql_db
?>