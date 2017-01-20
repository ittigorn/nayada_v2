<?php 
	require_once("../../_includes/ajax_header_addon.php");

	if ($server->validate_ajax_key() === true) {
		
		if (!isset($_POST["request_type"]) || !isset($_POST["food_id"])) {
			die("invalid request");
		}
		else {

			$request_type = $db->clean_input($_POST["request_type"]);

			if ($request_type === "menu_modal") {
				$food_id = $db->clean_input($_POST["food_id"]);
				$food = new food($db,$server,$food_id);
				$data = $food->generate_data_for_menu_modal($db,$server);
				echo $data;
			}
		}
		
	}

?>