<?php 
	require_once("../../_includes/ajax_header_addon.php");

	if ($server->validate_ajax_key() === true) {
		
		if (!isset($_POST["request_type"]) || !isset($_POST["food_id"])) {
			die("invalid request");
		}
		else {

			$request_type = $db->clean_input($_POST["request_type"]);
			if (!isset($_POST["request"])) { $_POST["request"] = ""; }

			if ($request_type === "basic_info") {
				$food_id = $db->clean_input($_POST["food_id"]);
				$request = $db->clean_array($_POST["request"]);
				$food = new food($db,$server,$food_id,$request);
				$reply = json_encode($food);
				echo $reply;
			}
		}
	}
?>