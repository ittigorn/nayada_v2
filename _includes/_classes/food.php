<?php
class food {

	public $food_info;
	public $status;

	function __construct($db,$server,$food_id,$request = array("food_id","food_name_en","description","price_base","available","category")) {

		$this->status = new stdClass();

		$this->food_info = $this->get_food_info($db,$food_id,$request);
		$this->status->ordering_ok = $this->check_food_available($server);
	} // end construct function

	private function get_food_info($db,$food_id,$request){

		if (is_array($request)){
			$request = implode(",", $request);
			preg_replace("/'/", "`", $request);
			$request = $db->clean_input($request);
		}

		$query   = "SELECT $request ";
		$query  .= "FROM `food` ";
		$query  .= "WHERE `food_id` = $food_id;";
		$result = $db->query_and_check($query);

		if (mysqli_num_rows($result) > 0) {
			$food_info = mysqli_fetch_object($result);
			return $food_info;
		}
		else {return NULL;}
	} // end function

	public static function generate_gallery_thumbnail($file_name) {
		$thumb_id = "food_id_".preg_replace('/.jpg/', "", $file_name);
		return "
			<img id='{$thumb_id}' class='img-thumbnail' src='_images/menu/gallery/{$file_name}' data-toggle='modal' data-target='#food_preview' />
		";
	}// end function

	public static function generate_gallery_thumbnail_with_caption($food_id,$caption = "") {
		$thumb_id = "food_id_".preg_replace('/.jpg/', "", $food_id);
		$image_url = "_images/menu/small/{$food_id}.jpg";
		if (file_exists($image_url) === false) {
			$image_url = "_images/menu/small/default.jpg";
		}
		return "
			<div id='{$thumb_id}' class='img-thumbnail' data-toggle='modal' data-target='#food_preview'>
				<img src='{$image_url}' />
				<p class='caption'>{$caption}</p>
			</div>
		";
	}// end function

	private function check_food_available($server){

		// check if ordering is disabled
		$available = true;
		if ($server->status->ordering_ok !== true) {$available = false;}
		if ($this->food_info->available !== "1") {$available = false;}

		// if lunch, check lunch hours
		if ($this->food_info->category === "lun") {
			if ($server->status->lunch_ok !== true) {$available = false;}
		}

		return $available;
	}// end function

	public function generate_menu_item($server) {

		$list_class  = ($this->status->ordering_ok === true) ? "available" : "unavailable";
		$list_class .= (cart::check_food_exists($this->food_info->food_id) === true) ? " in_cart" : ""; 

		$html  = "<li id='food_list_{$this->food_info->food_id}' class='{$list_class} menu_item' data-toggle='modal' data-target='#food_preview'>";
		$html .= 	 "<h4 class='food_name_en'>{$this->food_info->food_name_en}</h4>
					  <span class='price_base'>$ {$this->food_info->price_base}</span>
					  <p class='description'>{$this->food_info->description}</p>
				  </li>";

		return $html;
	}// end function

	public static function search_food($db,$keyword,$field = "any",$strict = false,$info_depth = "basic") {

		if ($strict === true) {
			$keyword = "= '{$keyword}'";
		}
		else {
			$keyword = "LIKE '%{$keyword}%'";
		}

		if ($info_depth === "id") {
			$select_statement = 'SELECT `food_id` ';
		}
		elseif ($info_depth === "basic") {
			$select_statement = 'SELECT `food_id`, `food_name_en` ';
		}

		if ($field === "any"){
			$query 	= $select_statement;
			$query .= 'FROM food ';
			$query .= 'WHERE ((`food_name_en` '.$keyword.') ';
			$query .= 'OR (`description` '.$keyword.') ';
			$query .= 'OR (`food_id` '.$keyword.'));';

			$result = $db->query_and_check($query);
		}
		else {
			$field = $db->clean_input($field);

			$query 	= $select_statement;
			$query .= 'FROM food ';
			$query .= 'WHERE `'.$field.'` '.$keyword.';';

			$result = $db->query_and_check($query);
		}

		$result_array = array();
		while ($row = mysqli_fetch_object($result)) {
			array_push($result_array, $row);
		}
		return $result_array;
	}// end function

	public function generate_data_for_menu_modal($db,$server) {

		// see if cust is logged in and do security check
		if (isset($_SESSION["cust"])) {
			$cust_logged_in = $_SESSION["cust"]->security_check($db,$server);
		}
		else {$cust_logged_in = false;}

		$request = array(
							"food_id",
							"category",
							"food_name_en",
							"price_base",
							"option_main_set",
							"option_spice",
							"not_spicy_possible",
							"with_rice",
							"available",
							"description"
						);
		$this->food_info = $this->get_food_info($db,$this->food_info->food_id,$request);

		$html = "";

		if ($this->food_info->with_rice !== "0") {$html .= $this->generate_with_rice_badge();}

		// only query for form items if cust is logged in
		if ($cust_logged_in === true) {

			if ($this->food_info->category === "lun") {$html .= $this->generate_sub_option($db,"lun","Side");}
			if ($this->food_info->option_main_set !== "0") {
				if ($this->food_info->option_main_set === "1") {
					$html .= $this->generate_meat_option($db);
				}
				else {
					$html .= $this->generate_sub_option($db,$this->food_info->option_main_set,"Options");
				}
			}
			if ($this->food_info->option_spice !== "0") {$html .= $this->generate_sub_option($db,"spice","Spice");}

			$html .= "<label>Qty <input id='quantity' name='quantity' type='number' value='1' min='1' max='20' /></label>";

		}// end if cust is logged in

		return $html;
	}// end function

	private function generate_with_rice_badge(){
		if ($this->food_info->with_rice === "1") {
			return "<p class='comes_with_rice'><span class='glyphicon glyphicon-check'></span> Comes with rice</p>";
		}
		else {return "";}
	} // end function

	private function generate_meat_option($db){
		// query for main option list
		$query  = "SELECT `option_code` FROM `option_main_1` ORDER BY `id`;";
		$result = $db->query_and_check($query);

		$query  = "SELECT ";
		while ($row = mysqli_fetch_array($result)) {
			$query .= "`{$row['option_code']}`, ";
		}// end while
		$query  = rtrim($query,', '); // removes last comma
		$query .= " FROM `food` ";
		$query .= "WHERE `food_id` = '{$this->food_info->food_id}' LIMIT 1;";

 		$result = $db->query_and_check($query);

 		$option_array = mysqli_fetch_assoc($result);
 		$option_list = array();
		foreach ($option_array as $key => $value) {
			if ($value === "1") {
				array_push($option_list, $key);
			}
		}
		
		// Assemble HTML
		$html  = "
			<label>Meat 
				<select name='option_main'>";

		foreach ($option_list as $current_option) {

			$query  = "SELECT * FROM `option_main_1` WHERE `option_code` = '{$current_option}' LIMIT 1;";
			$result = $db->query_and_check($query);
			$option_info = mysqli_fetch_object($result);
			
			if ($option_info->option_available !== "0") {
				$html .= "<option value='{$current_option}'>{$option_info->option_name_en}";
				if ($option_info->option_price != "0.00") {
					$html .= " $ {$option_info->option_price}";
				}
				$html .= "</option>";
			}

		}// end foreach

		$html .= "
				</select>
			</label>";

		return $html;

	} // end function

	private function generate_sub_option($db,$option_set,$option_label){

		// assemble option set before using it to query
		if (is_numeric($option_set)) {
			$option_set = "option_main_".$option_set;
		}
		else {
			$option_set = "option_".$option_set;
		}
		$result_array = $this->get_option_set_info($db,$option_set);

		// reset main option set to only "option_main" before inserting that as <select> name
		if (substr($option_set, 0, 12) === "option_main_") {
			$option_set = "option_main";
		}

		// Assemble HTML
		$html  = "
			<label>{$option_label} 
				<select name='{$option_set}'>";

		foreach ($result_array as $row) {
			
			if ($row->option_available !== "0") {
				if (($option_set === "option_spice") && ($row->option_code === "0")) {

		      		if ($this->food_info->not_spicy_possible === "1") {
		      			$html .= "<option value='{$row->option_code}'>{$row->option_name_en}";
						if ($row->option_price != "0.00") {
							$html .= " $ {$row->option_price}";
						}
						$html .= "</option>";
		      		}
		      	}
		      	else {
		      		$html .= "<option value='{$row->option_code}'>{$row->option_name_en}";
					if ($row->option_price != "0.00") {
						$html .= " $ {$row->option_price}";
					}
					$html .= "</option>";
		      	}
			}
		}// end foreach

		$html .= "
				</select>
			</label>";

		return $html;

	} // end function

	public function get_option_set_info($db,$option_set) {
		$query 	= "SELECT * ";
		$query .= "FROM `{$option_set}`;";
 		$result = $db->query_and_check($query);

 		$result_array = array();
 		while ($row = mysqli_fetch_object($result)) {
 			array_push($result_array, $row);
 		}// end while

 		return $result_array;
	}// end function

	public function get_option_info($db,$option_set,$option_code) {
		$query 	= "SELECT * ";
		$query .= "FROM `{$option_set}` ";
		$query .= "WHERE `option_code` = '{$option_code}' LIMIT 1;";
 		$result = $db->query_and_check($query);

 		$row = mysqli_fetch_object($result);

 		return $row;
	}// end function

}// end class food
?>