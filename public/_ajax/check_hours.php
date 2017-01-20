<?php 
	require_once("../../_includes/ajax_header_addon.php");

	if ($server->validate_ajax_key() === true) {
		
		$response 	=  	array(
							"restaurant_open" 	=> $server->status->restaurant_open,
							"ordering_ok" 		=> $server->status->ordering_ok,
							"lunch_ok"			=> $server->status->lunch_ok,
							"last_call"			=> $server->status->last_call
						);
		return json_encode($response);
	}// end if
?>