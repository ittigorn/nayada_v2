<?php 
	require_once("../../_includes/ajax_header_addon.php");

	if ($server->validate_ajax_key() === true) {
			
		if (!isset($_POST["request_type"])) {
			die("invalid request");
		}
		else {

			///////////////////////// ADD TO CART ////////////////////////
			if ($_POST["request_type"] === "add_to_cart") {
				$db->clean_simple_POST();
				
				// assemble submitted option array
				$option_array = new stdClass();
				if (isset($_POST["option_main"])) {$option_array->option_main 	= $_POST["option_main"];}
				if (isset($_POST["option_spice"])) {$option_array->option_spice = $_POST["option_spice"];}
				if (isset($_POST["option_lun"])) {$option_array->option_lun 	= $_POST["option_lun"];}

				// instantiate cart_item
				$cart_item = new cart_item($db,$server,$_POST["food_id"],$option_array,$_POST["quantity"]);

				// check if cart_item is ok to be added to cart
				if ($cart_item->all_ok === true) {
					cart::add_item($cart_item);
					$response = array(
										"success" => "yes", 
										"item_count" => cart::count_item()->total_item
									);
					echo json_encode($response);
				} // end if
				else {
					$combined_alert = array_merge($server->alert,$cart_item->error_array);
					$combined_alert = page::generate_alert($combined_alert);
					$response = array(
										"success" => "no", 
										"combined_alert" => $combined_alert
									);
					echo json_encode($response);
				}
			}

			///////////////////////// REMOVE FROM CART ////////////////////////
			if ($_POST["request_type"] === "remove_from_cart") {
				$all_ok = true;
				$hash = $db->clean_input($_POST["hash"]);
				if (cart::remove_item($hash) !== true) {$all_ok = false;}

				$item_list = cart::generate_item_list();
				$summary = cart::calculate_summary($server, true);

				$response 	= (object) array(
										"item_list" => $item_list,
										"summary"	=> $summary
										);
				echo json_encode($response);
			}// end if remove_from_cart

			///////////////////////// UPDATE TIP ////////////////////////
			if ($_POST["request_type"] === "update_tip") {
				$tip_amount = $db->clean_input($_POST["tip_amount"]);
				if (is_numeric($tip_amount)) {
					cart::set_tip($tip_amount);
				}
				$summary 	= cart::calculate_summary($server);
				$response 	= (object) array(
										"summary"	=> $summary
										);
				echo json_encode($response);
			}// end if update tip amount
		}
	}
?>