<?php
require_once(SITE_ROOT."_includes/php_mailer/PHPMailerAutoload.php");

class mailer {

	private $email_username;
	private $email_password;
	private $email_host;
	private $email_endpoint;
	private $slip_header;

	function __construct($db,$server) {
		$this->email_username	=	$server->info->email_username;
		$this->email_password	=	$server->info->email_password;
		$this->email_host		=	$server->info->email_host;
		$this->email_endpoint	=	$server->info->email_endpoint;

		if ($this->email_endpoint == 1) {
			$this->email_endpoint	= $this->email_username;
		}// end if set one
		elseif ($this->email_endpoint == 2) {
			$this->email_endpoint	= $server->info->developers_email;
		}// end if set two
		else {
			die("Invalid Email Endpoint");
		}// end else

		$this->slip_header =   '<p style="margin: 0; text-align: center; width: 900px;"><strong>Nayada Thai Cuisine Restaurant</strong></p>
								<p style="margin: 0; text-align: center; width: 900px;"><strong>Online Ordering Systems</strong></p>
								<p style="margin: 0; text-align: center; width: 900px;">11401 Carson Street, Suite A,</p>
								<p style="margin: 0; text-align: center; width: 900px;">Lakewood, CA. 90715</p>
								
								<p style="margin: 0; text-align: center; width: 900px;">'.$server->info->restaurant_phone.'</p>
								<p style="margin: 0; text-align: center; width: 900px;">www.NayadaThai.com</p>';
	}// end constructor

	private function send_mail($email_address,$recipient_name,$subject,$body,$alt_body) {

		$mail = new PHPMailer;
		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = $this->email_host;					  // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = $this->email_username;     		  // SMTP username
		$mail->Password = $this->email_password; 	          // SMTP password
		$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		$mail->CharSet = 'UTF-8';							  // Set charset
		$mail->Port = 587;                                    // TCP port to connect to
		$mail->From = $this->email_username; 
		$mail->FromName = 'Online Ordering Systems';
		$mail->isHTML(true);                                  // Set email format to HTML
		$mail->addAddress($email_address, $recipient_name);   // Add a recipient
		$mail->Subject = $subject;
		$mail->Body    = $body;
		$mail->AltBody = $alt_body;

		// send it	
		if(!$mail->send()) {
			// echo 'Mailer Error: ' . $mail->ErrorInfo;
			return false;
		}
		else {
			return true;
		}

	}// end function

	public function send_verification_email($server,$new_customer) {
		
		// setup parameters
		$email_address  = $new_customer->email;
		$recipient_name = $new_customer->name_first.' '.$new_customer->name_last;
		$subject 		= 'Please verify your email address';

		///////////////////////// BODY //////////////////////////
		$body 			= '
			<table align="center" width="750" border="0" cellspacing="0" cellpadding="0" style="background-color: #fff; color: #000; font-family: Helvetica, Arial, sans-serif; border: #610000 solid 2px;">

				<tr>
			    	<td rowspan="2" style="width: 200px; padding: 10px 0 0 20px;"><img width="200px" height="167px" title="Nayada Thai Logo" src="'.$server->info->email_resource_path.'logo_small.png" /></td>
			        <td style="padding: 40px 40px 0 0; font-size: 25px; font-weight: bold; text-align:center;">
			            <p style="padding: 0; margin: 0;">Hello '.$new_customer->name_first.' '.$new_customer->name_last.'</p>
			            <p style="padding: 0; margin: 15px 0 0 0; font-size: 0.8em;">Thank you for creating an account at '.substr($server->info->restaurant_domain_name,4).'</p></td>
				</tr>
			    <tr>
			    	<td style="font-size: 1.1em; text-align:center; padding: 15px 40px 0 0;">Please verify your email address by clicking<br />on the button below to complete your registration</td>
				</tr>
				<tr>
			    	<td style="text-align:center; padding: 90px 0 0 0;" colspan="2">
			        	<a href="'.$server->status->url_prefix.'verify_email.php?action=verify&email='.urlencode($new_customer->email).'&hash='.urlencode($new_customer->email_verification_hash).'">
			                <div style="border-radius: 6px;
			                            cursor: pointer;
			                            display: inline-table;
			                            padding-top: 10px;
			                            padding-right: 15px;
			                            padding-bottom: 10px;
			                            padding-left: 15px;
			                            margin-right: 10px;
			                            margin-left: 10px;
			                            box-shadow: 0px 1px 5px #b00000;
			                            background-color: #730000;
			                            color: #fff;
			                            margin-top: 5px;
			                            margin-bottom: 5px;
			                            font-size: 1.1em;">Verify My Email Address
			                 </div>
			             </a>
			        </td>
			    </tr>
			    <tr>
			    	<td style="text-align:center; padding: 30px 0 10px 0; font-weight: bold;" colspan="2">
			        	If the button doesn\'t appear, please copy the URL below and paste it in your browser window
			        </td>
				</tr>
			    <tr>
			    	<td style="text-align:center; padding: 0 0 30px 0;" colspan="2">
			        	'.$server->status->url_prefix.'verify_email.php?action=verify&email='.urlencode($new_customer->email).'&hash='.urlencode($new_customer->email_verification_hash).'
			        </td>
			    </tr>
			    <tr>
			    	<td style="text-align:center; padding: 60px 0 0px 0; font-weight: bold; font-size: 1.1em;" colspan="2">
			            <p style="text-align: center; padding: 0px;">'.$server->info->restaurant_name.'\'s Online Ordering Systems</p>
			            <p style="text-align: center; padding: 0px;"></p>
			        </td>
			    </tr>
			    <tr>
			    	<td style="text-align:center; padding: 0 0 10px 0;" colspan="2">
			            <p style="text-align: center; padding: 0px;">'.$server->info->restaurant_address.'</p>
			            <p style="text-align: center; padding: 0px;">'.$server->info->restaurant_phone.'</p>
			            <p style="text-align: center; padding: 0px;"><a href="'.$server->status->url_prefix.'" style="text-decoration: none; color: #000;">'.$server->info->restaurant_domain_name.'</a></p>
			        </td>
			    </tr>
			    <tr>
			    	<td colspan="2" style="padding: 30px 30px 0 30px; color: #fff; background-color: #610000;">This email was sent to you because your email address has been submitted to '.$server->info->restaurant_name.'&acute;s online ordering systems.
			        </td>
			    </tr>
			    <tr>
			        <td colspan="2" style="padding: 10px 30px 30px 30px; color: #fff; background-color: #610000;">If you did not submit this or you\'re having trouble verifying your email, please call us as soon as possible and we will resolve your problem in timely manner.
			        </td>
			    </tr>
			</table>';

		///////////////////////// ALT BODY //////////////////////////
		$alt_body = 'Hello, '.$new_customer->name_first.' '.$new_customer->name_last.'. Thank you for completing your registration with us.
		
		Please verify your email address by copying it to your browser\'s address bar

		'.$server->status->url_prefix.'verify_email.php?action=verify&email='.urlencode($new_customer->email).'&hash='.urlencode($new_customer->email_verification_hash).' 

		This email was sent to you because your email address has been submitted to '.$server->info->restaurant_name.'\'s online ordering system.
		If you did not submit this, please reply to '.$this->email_username.' with a brief explaination using this same email address and we will remove you from the mailing list as soon as possible.

		If you have any problem verifying your email address, please give us a call at '.$server->info->restaurant_phone;
			
		// SEND
		$mail = $this->send_mail($email_address,$recipient_name,$subject,$body,$alt_body);

	}// end function




	public function send_kitchen_slip($db,$server,$invoice_id) {

		// query for bill info
		$bill = new bill();
		$bill->get_bill_info($db,$server,$invoice_id,true);
		
		// setup parameters
		$email_address  = $this->email_endpoint;
		$recipient_name = $server->info->restaurant_name;
		$subject 		= 'kitchen_slip '.$invoice_id;

		///////////////////////// BODY //////////////////////////
		// initiate body
		$body 			= "";

		// Slip header
		$body    .= '<br /><p style="text-align: center; width: 900px; background-color: #000; color: #FFF; font-size: 120%; margin: 50px 0 30px 0; padding: 5px 0 5px 0;"><strong>Online Slip # '.$bill->invoice_id.'</strong></p>';
		
		// FOOD LIST AREA
		$body    .= '<div style="margin: 0 0 0 10px; font-family: \'Microsoft Sans Serif\';">';
		foreach ($bill->order_array as $order) {

			// check if should print to kitchen
			if ($order->print_to_kitchen === "1") {

				// display food name
				$body    .= '<p style="font-size: 140%; margin:0;"><strong>'.$order->quantity.' '.$order->food_name_th.'</strong></p>';

				// display options
				if (!empty($order->option_main)) {
					if ($order->option_main->option_price == "0.00") {$option_main_price = "";}
					else {$option_main_price = " : $ ".$order->option_main->option_price;}
					$body    .= '<p style="font-size: 130%; margin: 10px 0 10px 30px;"><strong>'.$order->option_main->option_name_th.$option_main_price.'</strong></p>';
				}// end if
				if (!empty($order->option_spice)) {
					if ($order->option_spice->option_price == "0.00") {$option_spice_price = "";}
					else {$option_spice_price = " : $ ".$order->option_spice->option_price;}
					$body    .= '<p style="font-size: 130%; margin: 10px 0 10px 30px;"><strong>'.$order->option_spice->option_name_th.$option_spice_price.'</strong></p>';
				}// end if
				if (!empty($order->option_lun)) {
					if ($order->option_lun->option_price == "0.00") {$option_lun_price = "";}
					else {$option_lun_price = " : $ ".$order->option_lun->option_price;}
					$body    .= '<p style="font-size: 130%; margin: 10px 0 10px 30px;"><strong>'.$order->option_lun->option_name_th.$option_lun_price.'</strong></p>';
				}// end if

				// display separator
				$body    .= '<p style="margin: 0;">---------------------------------------------</p>';

			}// end if should print to kitchen
		}// end foreach order_array

		// end FOOD LIST AREA

		$body    .= '</div><br />
						<p style="margin: 0;">เวลาสั่ง            : '.$bill->time_placed.'</p>
						<p style="margin: 0;">เวลารอ              : '.$bill->wait_time.' นาที</p>
						<p style="margin: 0;">เวลารอเพิ่ม   : '.$bill->additional_wait_time.' นาที</p>
						<p style="margin: 0;">เวลามารับ : '.$bill->estimated_pickup_time.'</p>';

		// if test mode is on
		// if ($bill->test_mode === "1") {
		// 	$mail->Body    .= '<p style="text-align: center; width: 900px; background-color: #000; color: #FFF; font-size: 150%; margin: 50px 0 0 0; padding: 5px 0 5px 0;"><strong>TEST MODE</strong></p>';
		// }// end if test mode

		///////////////////////// ALT BODY //////////////////////////
		$alt_body 		= "";
			
		// SEND
		$mail = $this->send_mail($email_address,$recipient_name,$subject,$body,$alt_body);

	}// end function



	public function send_paypal_pickup_slip($db,$server,$invoice_id) {

		// query for bill info
		$bill = new bill();
		$bill->get_bill_info($db,$server,$invoice_id,false);
		
		// setup parameters
		$email_address  = $this->email_endpoint;
		$recipient_name = $server->info->restaurant_name;
		$subject 		= 'paypal_pickup_slip '.$invoice_id;

		///////////////////////// BODY //////////////////////////
		// initiate body
		$body 			= "";

		// Slip header
		$body    .= '<p style="text-align: center; width: 900px; background-color: #000; color: #FFF; font-size: 120%; margin: 0 0 20px 0; padding: 5px 0 5px 0;"><strong>Pick-up Slip # '.$bill->invoice_id.'</strong></p>';
		
		// INFO AREA
		$body    .= '	<p style="font-size: 70%; margin: 0;">Payment Method         : PayPal</p>
						<p style="font-size: 70%; margin: 0;">Payer ID  		     : '.$bill->transaction_info->payer_id.'</p>
						<p style="font-size: 70%; margin: 0;">Payer Email  		     : '.$bill->transaction_info->payer_email.'</p>
						<p style="font-size: 70%; margin: 0;">Payer Name  		     : '.$bill->transaction_info->payer_name_first.' '.$bill->transaction_info->payer_name_last.'</p>
						<p style="font-size: 70%; margin: 0;">Transaction ID         : '.$bill->transaction_info->transaction_id.'</p>
						<p style="font-size: 70%; margin: 0;">Transaction Type       : '.$bill->transaction_info->transaction_type.'</p>
						<p style="font-size: 70%; margin: 0;">Subtotal               : '.$bill->subtotal.'</p>
						<p style="font-size: 70%; margin: 0;">Tax ('.$bill->tax_rate.'%) : $ '.$bill->tax.'</p>
						<p style="font-size: 70%; margin: 0;">Tip					: $ '.$bill->tip.'</p>
						<p style="margin: 0;">Amount Charged         : $ '.$bill->transaction_info->amount_charged.'</p>
						<p style="margin: 0;">Payment Status         : '.$bill->transaction_info->payment_status.'</p>
						<p style="font-size: 70%; margin: 0;">Order Date             : '.$bill->date_placed.'</p>
						<p style="font-size: 70%; margin: 0;">Order Time             : '.$bill->time_placed.'</p>
						<p style="font-size: 70%; margin: 0;">Wait Time              : '.$bill->wait_time.'</p>
						<p style="font-size: 70%; margin: 0;">Additional Wait Time   : '.$bill->additional_wait_time.'</p>
						<p style="margin: 0;">Estimated Pick-up Time : '.$bill->estimated_pickup_time.'</p>
						<p style="margin: 0; text-align: center;">---------------------------------------------</p>
						<p style="margin: 0;">Customer ID : '.$bill->cust_info->id.'</p>
						<p style="margin: 0;">'.$bill->cust_info->phone.' ( primary # )</p>';
	if (!empty($bill->cust_info->alt_phone)){
		$body    .= '<p style="margin: 0;">'.$bill->cust_info->alt_phone.' ( alternate # )</p>';
	}
		$body    .= '<p style="margin: 0; text-align: center;">---------------------------------------------</p>
						  <p style="font-size: 90%; text-align: center; margin-bottom: 300px;">I certify that I have received<br />all item(s) from order #'.$bill->invoice_id.'</p>
						  <p>&nbsp;</p>
						  <p style="text-align: center; margin: 0;">__________________________</p>
						  <p style="margin: 0; font-size: 90%; text-align: center;">'.$bill->transaction_info->payer_name_first.' '.$bill->transaction_info->payer_name_last.'</p>
						  <p style="text-align: center;">THANK YOU</p>';
		// END INFO AREA

		// if test mode is on
		// if ($bill->test_mode === "1") {
		// 	$mail->Body    .= '<p style="text-align: center; width: 900px; background-color: #000; color: #FFF; font-size: 150%; margin: 50px 0 0 0; padding: 5px 0 5px 0;"><strong>TEST MODE</strong></p>';
		// }// end if test mode

		///////////////////////// ALT BODY //////////////////////////
		$alt_body 		= "";
			
		// SEND
		$mail = $this->send_mail($email_address,$recipient_name,$subject,$body,$alt_body);

	}// end function




	public function send_checker_slip($db,$server,$invoice_id) {

		// query for bill info
		$bill = new bill();
		$bill->get_bill_info($db,$server,$invoice_id,true);
		
		// setup parameters
		$email_address  = $this->email_endpoint;
		$recipient_name = $server->info->restaurant_name;
		$subject 		= 'checker_slip '.$invoice_id;

		///////////////////////// BODY //////////////////////////
		// initiate body
		$body 			= "";

		// Slip header
		$body    .= '<p style="margin: 0; text-align: center;"><img src="'.$server->info->email_resource_path.'bw_logo_small.png" width="350px" height="350px" /></p>'
					.$this->slip_header
					.'<p style="text-align: center; width: 900px; background-color: #000; color: #FFF; font-size: 120%; margin: 30px 0 30px 0; padding: 5px 0 5px 0;"><strong>Checker Slip # '.$bill->invoice_id.'</strong></p>';
		
		// FOOD LIST AREA
		$body    .= '<div style="margin: 0 0 0 10px; font-family: \'Microsoft Sans Serif\';">';

			foreach ($bill->order_array as $order) {

					// display food name
					$body 	 .= '<p style="margin: 0;"><strong>'.$order->quantity.' '.$order->food_name_en.'</strong></p>';
					$body 	 .= '<p style="margin: 0;">:: $ '.$order->price_base.'</p>';

					// display options
					if (!empty($order->option_main)) {
						if ($order->option_main->option_price == "0.00") {$option_main_price = "";}
						else {$option_main_price = " : $ ".$order->option_main->option_price;}
						$body 	 .= '<p style="margin: 0 0 0 30px;">'.$order->option_main->option_name_en.$option_main_price.'</p>';
					}// end if
					if (!empty($order->option_spice)) {
						if ($order->option_spice->option_price == "0.00") {$option_spice_price = "";}
						else {$option_spice_price = " : $ ".$order->option_spice->option_price;}
						$body 	 .= '<p style="margin: 0 0 0 30px;">'.$order->option_spice->option_name_en.$option_spice_price.'</p>';
					}// end if
					if (!empty($order->option_lun)) {
						if ($order->option_lun->option_price == "0.00") {$option_lun_price = "";}
						else {$option_lun_price = " : $ ".$order->option_lun->option_price;}
						$body    .= '<p style="margin: 0 0 0 30px;">'.$order->option_lun->option_name_en.$option_lun_price.'</p>';
					}// end if

					// display separator
					$body    .= '<p style="margin: 0;">---------------------------------------------</p>';

			}// end foreach order_array

		$body    .= '</div>';
		// end FOOD LIST AREA

		// PAYMENT INFO AREA
		$body    .= '	<p style="font-size: 70%; margin: 0;">Payment Method        : '.strtoupper($bill->payment_method).'</p>
						<p style="font-size: 70%; margin: 0;">Transaction ID        : '.$bill->transaction_info->transaction_id.'</p>';

		if ($bill->payment_method === "card") {
			$body    .= '	<p style="font-size: 70%; margin: 0;">Auth Code    		    : '.$bill->transaction_info->auth_code.'</p>
							<p style="font-size: 70%; margin: 0;">Card Type    		    : '.$bill->transaction_info->card_type.'</p>
							<p style="font-size: 70%; margin: 0;">Card Number 			: '.$bill->transaction_info->card_number.'</p>';
		}// END IF CARD

		elseif ($bill->payment_method === "paypal") {
			$body    .= '	<p style="font-size: 70%; margin: 0;">Payer ID    		    : '.$bill->transaction_info->payer_id.'</p>
							<p style="font-size: 70%; margin: 0;">Payer Email  		    : '.$bill->transaction_info->payer_email.'</p>
							<p style="font-size: 70%; margin: 0;">Payer Name  		    : '.$bill->transaction_info->payer_name_first.' '.$bill->transaction_info->payer_name_last.'</p>
							<p style="font-size: 70%; margin: 0;">Transaction Type      : '.$bill->transaction_info->transaction_type.'</p>
							<p style="font-size: 70%; margin: 0;">Payment Status        : '.$bill->transaction_info->payment_status.'</p>';
		} // END IF PAYPAL

		// GENERAL INFO FOR BOTH TRANSACTIONS
		$body    .= '	<p style="font-size: 70%; margin: 0;">Subtotal              : '.$bill->subtotal.'</p>
						<p style="font-size: 70%; margin: 0;">Tax ('.$bill->tax_rate.' %) : $ '.$bill->tax.'</p>
						<p style="font-size: 70%; margin: 0;">Tip					: $ '.$bill->tip.'</p>
						<p style="margin: 0;">Amount Charged         				: $ '.$bill->transaction_info->amount_charged.'</p>
						<p style="font-size: 70%; margin: 0;">Order Date            : '.$bill->date_placed.'</p>
						<p style="font-size: 70%; margin: 0;">Order Time            : '.$bill->time_placed.'</p>
						<p style="font-size: 70%; margin: 0;">Wait Time             : '.$bill->wait_time.'</p>
						<p style="font-size: 70%; margin: 0;">Additional Wait Time  : '.$bill->additional_wait_time.'</p>
						<p style="margin: 0;">Estimated Pick-up Time 				: '.$bill->estimated_pickup_time.'</p>
						<p style="text-align: center; width: 900px; background-color: #000; color: #FFF; font-size: 120%; margin: 30 0 30px 0; padding: 5px 0 5px 0;"><strong>Customer\'s Info</strong></p>
						<p style="margin: 0;">Customer ID : '.$bill->cust_info->id.'</p>
						<p style="margin: 0;">'.$bill->cust_info->name_first.' '.$bill->cust_info->name_last.'</p>
						<p style="margin: 0;">'.$bill->cust_info->phone.'  ( primary # )</p>';

		if (!empty($bill->cust_info->alt_phone)){
			$body    .= '<p style="margin: 0;">'.$bill->cust_info->alt_phone.' ( alternate # )</p>';
		}

		$body    .= '	<p style="margin: 0; text-align: center;">---------------------------------------------</p>
						<p style="margin: 0; text-align: center;">Got comments or questions? Please visit</p>
						<p style="margin: 0; text-align: center;">'.$server->info->restaurant_domain_name.'/comment</p>';
		$body    .= '	<p>&nbsp;</p>'; // blank footer

		// if test mode is on
		// if ($bill->test_mode === "1") {
		// 	$mail->Body    .= '<p style="text-align: center; width: 900px; background-color: #000; color: #FFF; font-size: 150%; margin: 50px 0 0 0; padding: 5px 0 5px 0;"><strong>TEST MODE</strong></p>';
		// }// end if test mode

		///////////////////////// ALT BODY //////////////////////////
		$alt_body 		= "";
			
		// SEND
		$mail = $this->send_mail($email_address,$recipient_name,$subject,$body,$alt_body);

	}// end function




	public function send_order_confirmation($db,$server,$invoice_id) {
		
		// query for bill info
		$bill = new bill();
		$bill->get_bill_info($db,$server,$invoice_id,true);

		// setup parameters
		$email_address  = $bill->cust_info->email;
		$recipient_name = $bill->cust_info->name_first.' '.$bill->cust_info->name_last;
		$subject 		= 'Order Confirmation';

		///////////////////////// BODY //////////////////////////
		// initiate body
		$body 			 = "";

		///////////////////////// BODY //////////////////////////
		$body 			.= '
			<table align="center" width="750" border="0" cellspacing="0" cellpadding="0" style="background-color: #fff; color: #000; font-family: Helvetica, Arial, sans-serif; border: #610000 solid 2px;">

				<tr>
			    	<td rowspan="2" style="width: 200px; padding: 10px 0 0 20px;"><img width="200px" height="167px" title="Nayada Thai Logo" src="'.$server->info->email_resource_path.'logo_small.png" /></td>
			        <td style="padding: 40px 40px 0 0; font-size: 25px; font-weight: bold; text-align:center;">
			            <p style="padding: 0; margin: 0;">Hello '.$bill->cust_info->name_first.' '.$bill->cust_info->name_last.'</p>
			            <p style="padding: 0; margin: 15px 0 0 0; font-size: 0.8em;">Thank you for placing your order with us.</p></td>
				</tr>
			    <tr>
			    	<td style="font-size: 1.1em; text-align:center; padding: 15px 40px 0 0;">Here\'s your order # '.$bill->invoice_id.' detail.<br />Please keep this for your record.</td>
				</tr>
				<tr>
			    	<td style="text-align:left; padding: 40px 40px 0 40px;" colspan="2">';


		// FOOD LIST AREA
		$body    .= '<div style="margin: 0 0 0 10px; font-family: \'Microsoft Sans Serif\';">';

			foreach ($bill->order_array as $order) {

					// display food name
					$body 	 .= '<p style="margin: 0;"><strong>'.$order->quantity.' '.$order->food_name_en.'</strong></p>';
					$body 	 .= '<p style="margin: 0;">:: $ '.$order->price_base.'</p>';

					// display options
					if (!empty($order->option_main)) {
						if ($order->option_main->option_price == "0.00") {$option_main_price = "";}
						else {$option_main_price = " : $ ".$order->option_main->option_price;}
						$body 	 .= '<p style="margin: 0 0 0 30px;">'.$order->option_main->option_name_en.$option_main_price.'</p>';
					}// end if
					if (!empty($order->option_spice)) {
						if ($order->option_spice->option_price == "0.00") {$option_spice_price = "";}
						else {$option_spice_price = " : $ ".$order->option_spice->option_price;}
						$body 	 .= '<p style="margin: 0 0 0 30px;">'.$order->option_spice->option_name_en.$option_spice_price.'</p>';
					}// end if
					if (!empty($order->option_lun)) {
						if ($order->option_lun->option_price == "0.00") {$option_lun_price = "";}
						else {$option_lun_price = " : $ ".$order->option_lun->option_price;}
						$body    .= '<p style="margin: 0 0 0 30px;">'.$order->option_lun->option_name_en.$option_lun_price.'</p>';
					}// end if

					// display separator
					$body    .= '<p style="margin: 0;">---------------------------------------------</p>';

			}// end foreach order_array

		$body   .= '</div>';
		// end FOOD LIST AREA


		// SUMMARY AREA
		$body 	.= 	   '<tr>
				    		<td style="text-align:left; padding: 100px 40px 0 40px;" colspan="2">
					    		<p style="font-size: 100%; margin: 0;">Subtotal              : '.$bill->subtotal.'</p>
								<p style="font-size: 100%; margin: 0;">Tax ('.$bill->tax_rate.' %) : $ '.$bill->tax.'</p>
								<p style="font-size: 100%; margin: 0;">Tip					: $ '.$bill->tip.'</p>
								<p style="margin: 0;">Amount Charged         				: $ '.$bill->transaction_info->amount_charged.'</p>
								<p style="font-size: 100%; margin: 0;">Order Date            : '.$bill->date_placed.'</p>
								<p style="font-size: 100%; margin: 0;">Order Time            : '.$bill->time_placed.'</p>
								<p style="font-size: 100%; margin: 0;">Wait Time             : '.$bill->wait_time.'</p>
								<p style="font-size: 100%; margin: 0;">Additional Wait Time  : '.$bill->additional_wait_time.'</p>
								<p style="margin: 0;">Estimated Pick-up Time 				: '.$bill->estimated_pickup_time.'</p>
							</td>
						</tr>';


		// RESTAURANT'S CONTACT INFO
		$body 			.= '
		    		</td>
			    </tr>
			    <tr>
			    	<td style="text-align:center; padding: 60px 0 0px 0; font-weight: bold; font-size: 1.1em;" colspan="2">
			            <p style="text-align: center; padding: 0px;">'.$server->info->restaurant_name.'\'s Online Ordering Systems</p>
			            <p style="text-align: center; padding: 0px;"></p>
			        </td>
			    </tr>
			    <tr>
			    	<td style="text-align:center; padding: 0 0 10px 0;" colspan="2">
			            <p style="text-align: center; padding: 0px;">'.$server->info->restaurant_address.'</p>
			            <p style="text-align: center; padding: 0px;">'.$server->info->restaurant_phone.'</p>
			            <p style="text-align: center; padding: 0px;"><a href="'.$server->status->url_prefix.'" style="text-decoration: none; color: #000;">'.$server->info->restaurant_domain_name.'</a></p>
			        </td>
			    </tr>
			    <tr>
			    	<td colspan="2" style="padding: 30px 30px 0 30px; color: #fff; background-color: #610000;">';
		// FOOTER MESSAGE 1
		$body 		.= 'This email was sent to you because your email address has been registered at '.$server->info->restaurant_name.'&acute;s online ordering systems.';
		$body 		.= '
					</td>
			    </tr>
			    <tr>
			        <td colspan="2" style="padding: 10px 30px 30px 30px; color: #fff; background-color: #610000;">';
		// FOOTER MESSAGE 2
		$body 		.= 'If you have any question or concern, please call us at '.$server->info->restaurant_phone;
		$body 		.= '</td>
			    </tr>
			</table>';

		///////////////////////// ALT BODY //////////////////////////
		$alt_body = '';
			
		// SEND
		$mail = $this->send_mail($email_address,$recipient_name,$subject,$body,$alt_body);

	}// end function

}// end mailer class