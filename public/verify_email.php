<!doctype html>
<html lang="en">
<head>
<?php
	require_once("../_includes/header_addon.php");
	$page->enable_mailer($db,$server);

	$section = "undefined";
	if (isset($_GET["action"]) && isset($_GET["email"])) {

		$email 			= $db->clean_input($_GET["email"]);
		$encoded_email 	= urlencode($email);

		if ($db->check_email_exists($email) === true) {

			$cust			= new cust($db,$email,"email");

			if ($cust->status->email_verified === false) {

				if ($_GET["action"] === "verify") {
					if (isset($_GET["hash"])) {
						if ($_GET["hash"] === $cust->info->email_verification_hash) {
							$cust->verify_email($db);
							$location = $server->status->url_prefix."login.php?notice=email_verified";
							redirect($location);
						}
						else {
							array_push($alert,"Invalid verification detail. Please make sure the URL is correct.");
							array_push($alert,"If you're having trouble verying your email, please call us at {$server->info->restaurant_phone} or <a href='{$server->status->url_prefix}comment.php?direction=developer'>click here to submit a message.</a>");
						}
					}
					else {
						array_push($alert,"Invalid verification detail. Please make sure the URL is correct.");
						array_push($alert,"If you're having trouble verying your email, please call us at {$server->info->restaurant_phone} or <a href='{$server->status->url_prefix}comment.php?direction=developer'>click here to submit a message.</a>");
					}
				}
				if ($_GET["action"] === "email_sent") {
					if ($cust->info->verification_email_sent === "1") {
						array_push($alert,"A verification email has been sent to you");
					}
					else {
						array_push($alert,"(header)A total of {$cust->info->verification_email_sent} verification emails have been sent to you.");
					}
					array_push($alert,"Please follow the instruction inside the email to complete registration</li>");
					array_push($alert,"If no email is received within 15 minutes, please check your junk mail folder</li>");
					if ($cust->info->verification_email_sent < "5") {array_push($alert,"(header)<a class='show_wait' href='{$server->status->url_prefix}verify_email.php?action=send_again&email={$encoded_email}'>Click here to have us send you another verification email</a>");}
					array_push($alert,"(header)To prevent possible abuse, we only allow up to 5 verification emails to be sent per address");
					array_push($alert,"If you're having trouble verying your email, please call us at {$server->info->restaurant_phone} or <a href='{$server->status->url_prefix}comment.php?direction=developer'>click here to submit a message</a>");
					array_push($alert,"Email verification is enforced to help prevent fraud and provide safer services");
				}
				if ($_GET["action"] === "send_again") {

					if ($cust->info->verification_email_sent < "5") {
						$cust_info = new stdClass();
						$cust_info = $cust->info;
						$mailer->send_verification_email($server,$cust_info);
						$cust->plus_verification_email_sent($db);
						$location = $server->status->url_prefix."verify_email.php?action=email_sent&email={$encoded_email}";
						redirect($location);
					}
					else {
						array_push($alert,"(header)You have exceeded maximum number of verification emails");
						array_push($alert,"(header)To prevent possible abuse, we only allow up to 5 verification emails to be sent per address");
						array_push($alert,"If you're having trouble verying your email, please call us at {$server->info->restaurant_phone} or <a href='{$server->status->url_prefix}comment.php?direction=developer'>click here to submit a message</a>");
						array_push($alert,"If no email is received within 15 minutes, please check your junk mail folder");
						array_push($alert,"Email verification is enforced to help prevent fraud and provide safer services");
						
					}
				}
			}// end if email has not already been verified
			else {
				array_push($alert,"(header)Your email address has already been verified. Please go to login page.");
			}
		}// end if email exists
		else {
			array_push($alert,"(header)Invalid email address");
			array_push($alert,"If you're having trouble verying your email, please call us at {$server->info->restaurant_phone} or <a href='{$server->status->url_prefix}comment.php?direction=developer'>click here to submit a message</a></p>");
		}
	}// end if both get are set
	else {
		array_push($alert,"(header)Invalid point of entry");
		array_push($alert,"If you're having trouble verying your email, please call us at {$server->info->restaurant_phone} or <a href='{$server->status->url_prefix}comment.php?direction=developer'>click here to submit a message</a>");
	}
?>
<title>Email Verification</title>
<!-- LESS/SASS SCRIPTS GO HERE IF NECCESSARY -->
</head>
<body>
<?php include_once("../_includes/top_bar.php"); ?>
<div id="wrapper">






</div> <!-- END WRAPPER HERE -->
<?php
	include_once("../_includes/wait.php");
	include_once("../_includes/bottom_bar.php");
?>
</body>
</html>