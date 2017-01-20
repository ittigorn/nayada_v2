<?php
	require_once("../../../../_nayada_connections/config.php");
	require_once("../../_includes/functions.php");
	session_start();
	$db 	= new mysql_db(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
	$server = new server($db);
?>