<?php
class page {

	public $content;

	function __construct($db = "undefined",$page_name = "undefined") {

		if (($db === "undefined") || ($page_name === "undefined")){
			return NULL;
		}// end if

		// if page CSS exists, echo CSS style sheet link
		if ($this->check_page_css_exists($db,$page_name) === true) {
			echo '
					<!-- PAGE-SPECIFIC CSS -->
					<link href="_styles/'.$page_name.'.css" rel="stylesheet" type="text/css">';
				
		}// end if page CSS exists

		// if table exists, query for page content
		$table_name = "page_{$page_name}";
		if ($this->check_page_cms_exists($db,DB_NAME,$table_name) === true) {
			$this->content = $this->get_page_content($db,$table_name);
		}
		else {return NULL;}

	}// end construct function

	private function get_page_content($db,$table_name){

		$query  = "SELECT `element`, `content` FROM `{$table_name}`;";
		$result = $db->query_and_check($query);

		$content_array = array();

		if (mysqli_num_rows($result) > 0) {
			while ($row = mysqli_fetch_object($result)) {
				$key = $row->element;
				$value = $row->content;
				$content_array[$key] = $value;
			}// end while
			return $content_array;
		}
		else {return NULL;}

	}// end function

	private function check_page_cms_exists($db,$db_name,$table_name){

		$query  = "SELECT COUNT(*) as table_number ";
		$query .= "FROM information_schema.tables ";
		$query .= "WHERE table_schema = '{$db_name}' "; 
		$query .= "AND table_name = '{$table_name}';";

		$result = $db->query_and_check($query);

		$result = mysqli_fetch_object($result);
		if ($result->table_number != 0){return true;}
		else {return false;}
		
	}// end function

	private function check_page_css_exists($db,$page_name){
		$file_path = "_styles/";
		$file_name = $page_name.".css";
		if (file_exists($file_path.$file_name)){return true;}
		else {return false;}
		
	}// end function

	public function enable_ajax() {
		global $server;
		$_SESSION["ajax_key"] = $server->info->ajax_key;
	} // end function

	public function enable_mailer($db,$server) {
		require_once(SITE_ROOT."_includes/_classes/mailer.php");
		global $mailer;
		$mailer = new mailer($db,$server);
	} // end function

	public static function generate_alert($alert){

		if (!empty($alert)) {
			$html  = '<section class="alert_box">';
			$html .= "<ul>";

			foreach ($alert as $message) {
				if (!empty($message)) {
					if (substr($message,0,8) === "(header)") {
						$html .= "<li class='header'>";
						$html .= substr($message, 8);
					}
					else {
						$html .= "<li>";
						$html .= $message;
					}
					
					$html .= "</li>";
				}
			} // end foreach

			$html .= "</ul>";
			$html .= '</section>';
			return $html;
		}
	} // end function
}// end class page
?>