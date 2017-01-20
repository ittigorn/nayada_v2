<!doctype html>
<html lang="en">
<head>
<?php
	require_once("../_includes/header_addon.php");

	//////////////////// GET REQUESTS ////////////////////
	if (isset($_GET["notice"])) {
		if ($_GET["notice"] === "email_verified") {
			array_push($alert,"(header)Thank you for verifying your email. Please login to start ordering.");
		}
		if ($_GET["notice"] === "logged_out") {
			array_push($alert,"(header)You have logged out.");
		} // end if notice is logged_out
		if ($_GET["notice"] === "e_logged_out") {
			array_push($alert,"(header)Security check failed, please login again.");
		} // end if notice is logged_out
	} // end if notice is set

	if (isset($_GET["action"])) {
		if (($_GET["action"] === "logout") || ($_GET["action"] === "e_logout")) {
			// if session is set with cust info, logout with record
			if (isset($_SESSION["cust"])) {
				$email 	= $db->clean_input($_SESSION["cust"]->info->email);

				if ($db->check_email_exists($email) === true) {
					$_SESSION["cust"]->logout($db);
				}
			}

			// destroy session
			if (isset($_SESSION["cust"])){unset($_SESSION["cust"]);}
			if (isset($_SESSION)){unset($_SESSION);}
			session_unset();
			session_destroy();

			// redirect
			if ($_GET["action"] === "logout") {
				$location = $server->status->url_prefix."login.php?notice=logged_out";
				redirect($location);
			}
			else {
				$location = $server->status->url_prefix."login.php?notice=e_logged_out";
				redirect($location);
			}
		}
	}

	if ($server->info->login === "1") {
		//////////////////// POST REQUESTS ////////////////////
		if (isset($_POST["login"])) {

			if (isset($_POST["email"]) && isset($_POST["password"])) {
				$email 			= $db->clean_input($_POST["email"]);
				$password 		= $db->clean_input($_POST["password"]);

				if ($db->check_email_exists($email) === true) {

					$cust = new cust($db,$email,"email");
					$validation_response = $cust->validate_cust($db,$password);

					if ($validation_response === true) {

						// create a validated cust object
						$validated_cust = new validated_cust($db,$cust->info->id);

						// Assign cust to session
						$_SESSION["cust"] = $validated_cust;

						$location = $server->status->url_prefix."menu.php?category=2";
						redirect($location);
					}
					else {
						// display error(s)
						array_push($alert,"(header)Error(s)");

						foreach ($validation_response as $error) {
							array_push($alert,$error);
						} // end foreach
					}// end else
				}
				else {
					array_push($alert,"(header)Invalid email address or password");
				}
			}// end if email and passwords are set
			else {
				array_push($alert,"Invalid point of entry</h3>");
			}
		}
	}// end if logging in is allowed
?>
<title>Login</title>
<!-- LESS/SASS SCRIPTS GO HERE IF NECCESSARY -->
</head>
<body>
<?php
	include_once("../_includes/top_bar.php");
	if ($server->info->login === "1") {
?>
	<div id="wrapper">
		<h1 class="title">Login</h1>
		<div class="container-fluid">
			<div class="col-sm-8 col-md-6 col-lg-5 col-centered">
				<form action="<?php echo $server->status->url_prefix; ?>login.php" method="post" target="_self">
					<div class="form-group">
						<label for="email">Email Address</label>
							<input id="email" name="email" type="text" class="form-control" placeholder="iamhungry@nayadathai.com" maxlength="50" value="<?php echo (isset($_POST["email"])) ? $_POST["email"] : "" ; ?>" />
					</div>
					<div class="form-group">
						<label for="password">Password</label>
							<input id="password" name="password" type="password" class="form-control" placeholder="Password" maxlength="25" />
					</div>

					<button type="submit" name="login" class="btn btn-default">Login</button>
				</form>
			</div>
		</div>

		<div class="container-fluid">
			<div class="col-sm-8 col-md-6 col-lg-5 col-centered">
				<div class="helper_block">
					<h3>Helper</h3>
					<ul>
						<li><a href="<?php echo $server->status->url_prefix; ?>register.php" class="bold">Not a member yet? Register here to start ordering...</a></li>
						<li class="helper_link" id="forgot_password">I forgot my password</li>
						<form class="forgot_password_form helper_form" action="<?php echo $server->status->url_prefix; ?>login.php" method="post" target="_self">
							<div class="form-group">
									<input id="email" name="email" type="text" class="form-control" placeholder="iamhungry@nayadathai.com" maxlength="50" />
									<span class="help-block">Enter your email address and we'll send you a link to reset your password</span>
								<button type="submit" name="forgot_password" class="btn btn-default">Submit</button>
							</div>
						</form>
						<li class="helper_link" id="send_verification">I need the restaurant to send me verification email again</li>
						<form class="verification_form helper_form" action="<?php echo $server->status->url_prefix; ?>login.php" method="post" target="_self">
							<div class="form-group">
									<input id="email" name="email" type="text" class="form-control" placeholder="iamhungry@nayadathai.com" maxlength="50" />
									<span class="help-block">Enter your email address and we'll send you another one.</span>
								<button type="submit" name="verification" class="btn btn-default">Submit</button>
							</div>
						</form>
						<li class="helper_link" id="verification_problem">I'm having trouble verifying my email</li>
							<ul class="verification_troubleshoot">
								<li>Some email service providers might consider our email a spam.<br />Please check your junk mail folder for an email from <?php echo $server->info->email_username; ?>.</li>
								<li>You could always reach out to us at <?php echo $server->info->restaurant_phone; ?> or <a href='<?php echo $server->status->url_prefix; ?>comment.php?direction=developer'>click here to submit a message</a></li>
							</ul>
					</ul>
				</div>
			</div>
		</div>
	</div> <!-- END WRAPPER HERE -->
<script type="text/javascript">
	$(document).ready(function(){
		$("#forgot_password").click(function(){
			$(".forgot_password_form").slideToggle();
		});
		$("#send_verification").click(function(){
			$(".verification_form").slideToggle();
		});
		$("#verification_problem").click(function(){
			$(".verification_troubleshoot").slideToggle();
		});
	});
</script>
<?php
	include_once("../_includes/wait.php");
	} // end if logging in is allowed
	include_once("../_includes/bottom_bar.php"); 
?>
</body>
</html>