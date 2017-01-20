<!doctype html>
<html lang="en">
<head>
<?php 
	require_once("../_includes/header_addon.php");

	// if registration is allowed
	if ($server->info->registration === "1") {
		$page->enable_mailer($db,$server);

		// evaluate post variables
		if (isset($_POST["submit"])) {
			$all_ok = true;
			$error_array = array();
			 
			//validate Google's ReCaptcha
			if ($server->validate_recaptcha($_POST['g-recaptcha-response']) === false) {
				$all_ok = false;
				array_push($error_array, "reCaptcha validation failed");
			}// end if
			
			// Process first name
			$name_first	= $db->clean_input($_POST["name_first"]);
			$regex = "/^[a-zA-Z ]*$/"; // Condition to check
			if ((preg_match($regex, $name_first) !== 1) || (strlen($name_first) < 2)) {
				$all_ok = false;
				array_push($error_array, "Invalid first name");
			} // end if
			
			// Process last name
			$name_last	= $db->clean_input($_POST["name_last"]);
			$regex = "/^[a-zA-Z ]*$/"; // Condition to check
			if ((preg_match($regex, $name_last) !== 1) || (strlen($name_last) < 2)) {
				$all_ok = false;
				array_push($error_array, "Invalid last name");
			} // end if
			
			// Process phone number
			$regex = "/^(\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$/i"; // Condition to check
			$phone = $db->clean_input($_POST["phone"]); // Use clean_input function
			if (preg_match($regex, $phone) === 1){ // Check if valid
				$phone = preg_replace('/\D+/', '', $phone); // Strip off any dash or parentheses
				
				if ((strlen($phone) == 11) && ((substr($phone, 0, 1))=="1")){
					$phone = substr($phone, 1); // Strip off any country code
				}

				if (strlen($phone) != 10){
					$all_ok = false;
					array_push($error_array, "Invalid phone number");
				}
			}
			else {
				$all_ok = false;
				array_push($error_array, "Invalid phone number");
			}
			
			// Process email address
			$email	= $db->clean_input($_POST["email"]);
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // Check if valid
				$all_ok = false;
				array_push($error_array, "Invalid email");
			} 
			if ($db->check_email_exists($email) === true) {
				$all_ok = false;
				array_push($error_array, "This email has already been used");
			}
			
			// Process password1
			if ((preg_match('/\s/', $_POST["password1"]) === 1) || (preg_match('/\\\\/', $_POST["password1"]) === 1)){
				$_POST["password1"] = ""; // disqualifies password that contains forbidden characters
			}
			$password1 = $db->clean_input($_POST["password1"]); // Use clean_input function
			if ((strlen($password1) < 5) || (strlen($password1) > 25)){ // check length
				$all_ok = false;
				array_push($error_array, "Invalid password format");
			}
			
			// Process agree checkbox
			if (!isset($_POST["terms"])) {
				$all_ok = false;
				array_push($error_array, "Please agree with our terms");
			}
			
			// Process subscription checkbox
			if (isset($_POST["subscription"])){
				$subscription = 1;
			}
			else {
				$subscription = 0;
			}
			
			// Evaluate all oks
			if ($all_ok === true){

				$password_hash	 			= $db->hash_password($password1);
				$email_verification_hash 	= urlencode(generate_random_hash());

				$new_customer 	= (object) 	array(
													"name_first"				=> $name_first,
													"name_last"					=> $name_last,
													"phone"						=> $phone,
													"email"						=> $email,
													"password"					=> $password_hash,
													"subscription"				=> $subscription,
													"email_verification_hash"	=> $email_verification_hash
											);
				cust::create_new_customer_record($db,$new_customer);
				$mailer->send_verification_email($server,$new_customer);
				$encoded_email = urlencode($email);
				$location = $server->status->url_prefix."verify_email.php?action=email_sent&email={$encoded_email}";
				redirect($location);
			}// end if all ok
			else {
				// display error(s)
				array_push($alert,"(header)Please fix the folling error(s)");
				foreach ($error_array as $error) {
					array_push($alert,$error);
				} // end foreach
			}
		} // end if $_POST submit is set
	}// end if registration is allowed
?>
<title>Register</title>
<!-- LESS/SASS SCRIPTS GO HERE IF NECCESSARY -->
<script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<body>
<?php
	include_once("../_includes/top_bar.php");
	if ($server->info->registration === "1") {
?>
	<div id="wrapper">
		<h1 class="title">Register</h1>
		<div class="container-fluid">
			<div class="col-sm-8 col-md-6 col-lg-5 col-centered">
				<form action="<?php echo $server->status->url_prefix; ?>register.php" method="post" target="_self">
					<div class="form-group">
						<label for="name_first">First Name</label>
							<input id="name_first" name="name_first" type="text" class="form-control" placeholder="Iam" maxlength="50" value="<?php echo (isset($_POST["name_first"])) ? $_POST["name_first"] : "" ; ?>" />
							<span class="help-block">* First and last name are required for identification purposes when picking up your order.</span>
					</div>
					<div class="form-group">
						<label for="name_last">Last Name</label>
							<input id="name_last" name="name_last" type="text" class="form-control" placeholder="Hungry" maxlength="50" value="<?php echo (isset($_POST["name_last"])) ? $_POST["name_last"] : "" ; ?>" />
							<span class="help-block">* First and last name are required for identification purposes when picking up your order.</span>
					</div>
					<div class="form-group">
						<label for="phone">Phone Number</label>
							<input id="phone" name="phone" type="text" class="form-control" placeholder="562-860-6108" maxlength="20" value="<?php echo (isset($_POST["phone"])) ? $_POST["phone"] : "" ; ?>" />
							<span class="help-block">* Phone number is required in case there's a problem with your order.</span>
					</div>
					<div class="form-group">
						<label for="email">Email Address <span>( This becomes your login name )</span></label>
							<input id="email" name="email" type="email" class="form-control" placeholder="iamhungry@nayadathai.com" maxlength="50" value="<?php echo (isset($_POST["email"])) ? $_POST["email"] : "" ; ?>" />
							<span class="help-block">* Email verification is required for security purposes.</span>
					</div>
					<div class="container-fluid password_block">
						<fieldset>
							<div class="form-group">
							<label for="password1">Password <span>( 5 - 25 characters )</span></label>
								<ul>
									<li>Password must be between 5 - 25 characters</li>
									<li>Cannot contain space or slash</li>
								</ul>
								<input id="password1" name="password1" type="password" class="form-control" placeholder="Password" maxlength="25" value="<?php echo (isset($_POST["password1"])) ? $_POST["password1"] : "" ; ?>" />
							</div>
							<div class="form-group">
							<label for="password2">Re-Type Password</label>
								<input id="password2" name="password2" type="password" class="form-control" placeholder="Re-Type Password" maxlength="25" value="<?php echo (isset($_POST["password2"])) ? $_POST["password2"] : "" ; ?>" />
							</div>
						</fieldset>
					</div>
					<div class="g-recaptcha" data-sitekey="6LfRWgwTAAAAAIsW9OzVY8FnWZ-vO0946taiRJkz" data-callback="enable_submit"></div>
					<div class="checkbox">
						<label>
							<input id="terms" class"terms" type="checkbox" name="terms" <?php echo (isset($_POST["terms"])) ? "checked='checked'" : "" ; ?> /> By checking this box, you hereby agree to our <a href="<?php echo $server->status->url_prefix; ?>privacy_policy.php" target="_blank">Privacy Policy</a> and <a href="<?php echo $server->status->url_prefix; ?>terms.php" target="_blank">Terms of Use</a>
						</label>
					</div>
					<div class="checkbox">
						<label>
							<input type="checkbox" name="subscription" checked="checked" /> Send me emails about news and promotions ( optional )
						</label>
					</div>
					
					<button type="submit" name="submit" class="btn btn-default" id="submit">Submit</button>
					<span class="help-block">* Plase correct all error(s) before submitting</span>
				</form>
			</div>
		</div>
	</div> <!-- END WRAPPER HERE -->
	<script type="text/javascript">

		function enable_submit() {
			$("#submit").prop("disabled",false);
			$("#submit").siblings(".help-block").removeAttr("style");
			$("#submit").siblings(".help-block").hide();
		}

		$(document).ready(function(){

			function paint_red($element_id) {
				$($element_id).css({"border-color" : "#b30000", "border-width" : "3px"});
			} // end function

			function clear_red($element_id) {
				$($element_id).removeAttr("style");
			} // end function

			function match_password() {
				var $all_ok = true;

				if($("input#password2").val() !== $("input#password1").val()) {
					paint_red("input#password2");
					$all_ok = false;
				}
				else {
					clear_red("input#password2");
				}

				return $all_ok;
			} // end function

			function validate_name_fields($element_id) {
				var $all_ok = true;

				// display warning if number is found in name field
				if($($element_id).val().search(/\d/) !== -1) {
					paint_red($($element_id));
					$all_ok = false;
				}
				else {
					clear_red($($element_id));
				}

				// display warning if field is empty
				var	$name = $($element_id).val();
				var $name = $name.search(/[a-z]/g); // search for any character
				
				if ($name === -1) {
					paint_red($element_id);
					$all_ok = false;
				}
				else {
					clear_red($element_id);
				}

				return $all_ok;
			}// end function

			function validate_phone_field($element_id) {
				var $all_ok = true;

				// display warning if number is not valid
				if (/^(\+\d{1,2}\s)?\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{4}$/.test($($element_id).val()) === false) {
					paint_red($($element_id));
					$all_ok = false;
				}
				else {
					clear_red($($element_id));
				}

				return $all_ok;
			}// end function

			function validate_email_field($element_id) {
				var $all_ok = true;

				// display warning if length is less than 5 characters
				if ($($element_id).val().length < 5) {
					paint_red($($element_id));
					$all_ok = false;
				}
				else {
					clear_red($($element_id));
				}

				return $all_ok;
			}// end function

			function validate_password_field($element_id) {
				var $all_ok = true;

				// display warning if length is less than 5 characters
				if (($($element_id).val().length < 5) || ($($element_id).val().length > 25)) {
					paint_red($($element_id));
					$all_ok = false;
				}
				else {
					if ($($element_id).val().search(/\\/) != -1) {
						paint_red($($element_id));
						$all_ok = false;
					}
					if ($($element_id).val().search(/\//) != -1) {
						paint_red($($element_id));
						$all_ok = false;
					}
					if ($($element_id).val().search(/ /) != -1) {
						paint_red($($element_id));
						$all_ok = false;
					}
					if ($all_ok === true) {
						clear_red($($element_id));
					}
					
				}

				return $all_ok;
			}// end function

			function validate_terms_checkbox($element_id) { 
				var $all_ok = true;

				if ($($element_id).prop("checked") !== true) {
					$($element_id).parent("label").css({"color" : "#b30000", "font-weight" : "bold"});
					$all_ok = false;
				}
				else {
					clear_red($($element_id).parent("label"));
				}

				return $all_ok;
			}// end function

			function validate_recaptcha() {
				if ($('#g-recaptcha-response').val() === "") {
					$("div.g-recaptcha iframe").css({"border" : "solid #b30000 3px"});
					return false;
				}
				else {
					clear_red("div.g-recaptcha iframe");
					return true;
				}
			}// end function

			$("input").change(function(){
				var $element_id = "#" + $(this).attr("id");

				if (($element_id === "#name_first") || ($element_id === "#name_last")) {
					validate_name_fields($element_id);
				}// end if input is name or last name

				if ($element_id === "#phone") {
					validate_phone_field($element_id);
				}

				if ($element_id === "#email") {
					validate_email_field($element_id)
				}
	
				if ($element_id === "#password1") {
					validate_password_field($element_id);
				}

				if ($element_id === "#password2") {
					match_password($element_id);
				}
			}); // end if input is changed

			$("input").click(function(){
				var $element_id = "#" + $(this).attr("id");

				if ($element_id === "#terms") {
					validate_terms_checkbox($element_id);
				}
			}); // end if input is clicked

			$("#submit").click(function(){
				$all_ok = true;

				if (validate_name_fields("#name_first") === false) 	{$all_ok = false;}
				if (validate_name_fields("#name_last") === false) 	{$all_ok = false;}
				if (validate_phone_field("#phone") === false) 		{$all_ok = false;}
				if (validate_email_field("#email") === false) 		{$all_ok = false;}
				if (validate_password_field("#password1") === false) {$all_ok = false;}
				if (match_password("#password2") === false) 		{$all_ok = false;}
				if (validate_terms_checkbox("#terms") === false) 	{$all_ok = false;}
				if (validate_recaptcha() === false)					{$all_ok = false;}

				if ($all_ok !== true) {
					$("#submit").prop("disabled",true);
					$("#submit").siblings(".help-block").css({"font-weight" : "bold"});
					$("#submit").siblings(".help-block").slideDown();
					hide_wait();
				}// end if all ok not true
				else {
					show_wait();
				}
			});

			$("form").change(function(){
				$("#submit").prop("disabled",false);
				$("#submit").siblings(".help-block").removeAttr("style");
				$("#submit").siblings(".help-block").hide();
			});

			// Functions for showing and hiding helper texts
			$("form .help-block").hide();
			$("input").focusin(function() {
				$(this).siblings("form .help-block").slideDown();
			});
			$("input").focusout(function() {
				$(this).siblings("form .help-block").slideUp();
			});

		}); // end document.ready
	</script>
<?php
	include_once("../_includes/wait.php");
	} // end if registration is allowed
	include_once("../_includes/bottom_bar.php");
?>
</body>
</html>