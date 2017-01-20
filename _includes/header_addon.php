<?php
	$current_page = preg_replace('/.php/',"",basename($_SERVER['PHP_SELF']));
	$alert	= array();
	require_once("../../../_nayada_connections/config.php");
	require_once("../_includes/functions.php");
	session_start();
	$db 	= new mysql_db(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
	$page 	= new page($db,$current_page);
	$server = new server($db);
	cart::check_cart_exists();

	// check if customer is valid
	if (isset($_SESSION["cust"])) {
		$cust_logged_in = $_SESSION["cust"]->security_check($db,$server);
	}
	else {$cust_logged_in = false;}

	$alert = array_merge($alert,$server->alert);
	array_push($alert, "This is a demo site. No real transaction will be processed");
?>

<!--META TAG-->
<meta http-equiv="Content-Type" name="viewport" content="width=device-width, initial-scale=1">
<meta charset="utf-8">

<!--JAVASCRIPT LIBRARIES-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<!-- <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular.min.js"></script> -->

<!-- BOOTSTRAP'S SCRIPT -->
<script type="text/javascript" src="_bootstrap/js/bootstrap.min.js"></script>

<!-- CSS -->
<link href="_bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">

<!--SITE ICON-->
<link rel="icon" type="image/png" href="_images/icon.png" sizes="32x32">

<!--WEB FONTS-->
<link href='https://fonts.googleapis.com/css?family=Russo+One' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Play:400,700' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<link href='https://fonts.googleapis.com/css?family=Josefin+Sans' rel='stylesheet' type='text/css'>