<?php 
if ( !is_user_logged_in() ) {

	$url = home_url() . '/login';

	echo "<script>window.location.href = '{$url}'; </script>";
}

$notices = [];
$user = new Dropseal_Services_User();
$reminders = 'true';
if ( !$user->isset_phone_or_carrier() ) {
	$notices['phone_or_carrier'] = 'To receive notifications on your mobile phone, you need to specify the number and mobile operator in your profile';
	$reminders = 'false';
}
$sms_limit_value = $sms_limit - 1;
if ( !(int)$sms_limit ) {
	$notices['payment_notice'] = 'The limit of available messages has been reached. The next message will be paid!';
	$sms_qty_paid--;
	$sms_limit_value = $sms_qty_paid;
}
?>
<br>
<div class="dss-sms-container" data-qty-recipients="<?php echo $qty_recipients; ?>">
	<div class="dss-notification-container">
	<?php if ( $notices) {

		foreach ($notices as $key => $value) {
			echo '<div class="message-error">'.$value.'</div>';
		}
	} ?>
	</div>
	<form method="post" enctype="multipart/form-data" class="rfield sms-form">
		<div class="sms-service-part">
			<div class="sms-block-title">
				<span>Content</span>
			</div>
			<div class="dss-message-block">
				<span class="dss-field-title">Message:</span>			
				<textarea col=4 row=6 name="sms_text" class="message-text empty-field"></textarea>
				<div class=dss-attachement>
					<div class="dss-upload-file-name"></div>
					<div class="dss-attachement-wrap">
		                <label class="dss-upload-file" data-file-size="<?php echo get_option( 'dss_upload_file_size' ); ?>">
		                    <input type="file" name="file" size="30">
		                    <img src="<?php echo plugins_url( 'dropseal-services/public/img/clip.svg' ); ?>">
		                    <span>Upload File</span>
		                </label>               
					   	<span class="dss-url-wrapper">
					   		<img class="sms-url-icon" src="<?php echo dirname(plugin_dir_url( __FILE__ )) . '/img/link.svg'; ?>">
						   	<span>Add Link</span>
					    </span>
					</div>
				</div>
			    <!-- Modal Box -->
			    <div class="dss-add-links-modal" id="dss-add-links-modal">
			    	<div class="dss-add-links-modal-content">
			    		<span class="dss-links-modal-close">&#10007;</span>
			    		<div class="dss-links-modal-title">Add website or file by URL</div>
			    		<div class="dss-links-container">
			    			<div class="dss-links-wrapper">
	                    		<input type="url" name="links[]" placeholder="http://">
	                    		<span class="dss-add-links-btn dss-links-btn" title="Add more url">+</span>
	                		</div>
			    		</div>
			    		<div class="dss-confirm-links-btn">Ok</div>
			    	</div>				    	
			    </div>
			    <!-- end Modal Box -->
				<span class="max-size-file-warning">The total file size must not exceed <?php echo get_option( 'dss_upload_file_size' ); ?>MB</span>
			</div>
		</div>
		
		<div class="sms-service-part">
			<div class="sms-block-title">
				<span>Recipient</span>
			</div>
			<div class="dss-message-block">
				<div class="sms-recipient-container">
					<div class="dss-recipient-wrapper" data-recipient="1">
						<div class="dss-recipient-row">
							<div class="dss-recipient-name">
								<label class="required dss-field-title">Name:</label>
								<span class="input-error"></span>				
								<input type="text" name="name_1" class="dss-form-field empty-field">
							</div>
							<div class="dss-recipient-email" data-recipient-email="1">
								<input type="checkbox" name="email_delivery" id="email-delivery-1">
								<label for="email-delivery-1" class="dss-field-title">Add e-mail delivery</label>
							</div>
						</div>
						<div class="dss-recipient-row">
							<div class="dss-recipient-phone">
								<label class="required dss-field-title">Phone Number:</label>
								<span class="input-error"></span>
								<input type="tel" name="phone_1" placeholder="e.g. 1617555121" class="dss-form-field empty-field">
							</div>
							<div>
								<label class="dss-field-title">Select Mobile Carrier</label>
								<a href="#" data-tooltip="<?php echo get_option( 'dss_mobile_carrier_tooltip' ); ?>" class="dss-tooltipe-link">
									<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="mdi-alpha-i-circle-outline" width="24" height="24" viewBox="0 0 24 24" style="fill: #191970"><path d="M14,7V9H13V15H14V17H10V15H11V9H10V7H14M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4Z" />
									</svg>
								</a>
								<select name="mobile_carrier_1" class="sms-mobile-carriers">
									<option value="" selected></option>	
									<?php if ( $mobile_carriers ): ?>

										<?php foreach ($mobile_carriers as $mobile_carrier) : ?>
											<?php if ( $mobile_carrier->checked) : ?>
												<option value="<?php echo $mobile_carrier->sms; ?>"><?php echo $mobile_carrier->name; ?></option>
											<?php endif; ?>
										<?php endforeach; ?>

									<?php endif; ?>
								</select>
							</div>
						</div>
					</div>
					<div class="add-recipient-wrap">					
						<button id="add-recipient" class="add-recipient"><i class="fas fa-plus"></i>Add Recipient</button>
					</div>
				</div>
			</div>
		</div>
		
		<div class="sms-service-part">
			<div class="sms-block-title">
				<span>Send Options</span>
			</div>
			<div class="dss-send-options-container">
				<div class="dss-send-options-blok">
					<div class="dss-date">Select Delivery Date & Time( USA - Central Time Zone )</div>
					<div>
						<span id="dss-ampm" data-dss-date="<?php echo current_time('timestamp'); ?>"></span>
						<input type="hidden" id="dss-date"  value="">
					</div>
					<label for="dss-input-date">
						<div class="dss-datetime">
							<input type='text' name="datetime" class="datepicker-here empty-field" id="dss-input-date" data-timepicker="true" readonly>
						</div>
						<span class="calendar-icon-wrap"><img src="<?php echo dirname(plugin_dir_url( __FILE__ )) . '/img/calendar.svg'; ?>"></span>
					</label>
				</div>
				<div class="dss-checkout-container">				
					<div class="dss-checkout">
						<div class="dss-package-sms">
							<div>SMS Package:</div>
							<div>
								<span>$</span>
								<span id="dss-sms-package">
								<?php if( !(int)$sms_limit ) {
									echo $package_sms_cost; 
								} ?>															
								</span>
							</div>
						</div>
						<div class="dss-mobile-carrier">
							<div>Mobile Carrier:</div>
							<div>
								<span>$</span>
								<span id="dss-mobile-carrier"><?php echo $mobile_carrier_cost; ?></span>
							</div>
						</div>
						<div class="add-service-wrapper">
							<div>Money Transfer:</div>
							<div>
								<span>$</span>
								<span id="dss-transfer"></span>
							</div>
						</div>
						<div class="add-service-wrapper">
							<div>Commission:</div>
							<div>
								<span>$</span>
								<span id="dss-commission"></span>
							</div>
						</div>
						<input type="hidden" id="mobile-carrier-cost" value="<?php echo $mobile_carrier_cost; ?>">
						<input type="hidden" id="dss-percentage" value="<?php echo $percentage; ?>">
						<input type="hidden" id="sms-limit" name="sms_limit" data-sms-limit="<?php echo $sms_limit; ?>" data-sms-package-qty="<?php echo $sms_qty_paid; ?>" data-sms-package-cost="<?php echo $package_sms_cost; ?>" value="<?php echo $sms_limit_value; ?>">
						<hr class="payment-line">
						<div class="dss-service-total">
							<div>Total:</div>
							<div>
								<span>$</span>
								<span id="dss-total-pay"><?php echo $total; ?></span>
							</div>
						</div>
					</div>
					<div class="dss-payment-buttons">
						<div><input type="radio" name="pay" id="dss-pay-pal" value="pay_pal" checked><img src="<?php echo dirname(plugin_dir_url( __FILE__ )) . '/img/paypal.svg'; ?>"></div>
						<div>
						<?php if ( is_plugin_active( 'wp-stripe-checkout/main.php' ) ) : ?>
							<input type="radio" name="pay" id="dss-apple-pay" value="apple_pay"><img src="<?php echo dirname(plugin_dir_url( __FILE__ )) . '/img/apple-pay.svg'; ?>">			
						<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div>
			<div class="sms-block-title"></div>
			<div class="dss-message-block">
				<div class="dss-send-money-container">
					<input type="checkbox" name="send_money" id="sms-send-money">
					<label for="sms-send-money" class="dss-field-title">I want to send money</label>
					<div class="dss-send-money-wrap">
						<div class="sms-pay-email">		
							<input type="email" name="pay_email" class="dss-form-field" placeholder="@e-mail of recipient">
						</div>
						<div class="sms-send-money">
							<input type="text" name="money" class="dss-form-field" id="sms-money-amount" placeholder=" Enter the amount of money">
						</div>
					</div>
				</div>


				<div class="dss-form-line">
					<input type="checkbox" name="reminders" id="sms-reminders" data-sms-reminders="<?php echo $reminders; ?>">
					<label for="sms-reminders" class="dss-field-title">I would like to receive status reports and reminders about this order</label>
				</div>
				<div class="dss-form-line">
					<input type="checkbox" name="conditions" id="sms-conditions" class="empty-field">
					<label for="sms-conditions" class="dss-field-title requir">I agree to the <a href="<?php echo home_url().'/terms-conditions'; ?>">Terms and Conditions</a></label>
				</div>
			</div>
		</div>
		<div class="sms-next-wrap">
			<button type="submit" id="sms-next" class="dss-send disabled">Submit</button>				
		</div>
	</form>

	<div class="sms-paypal-wrap">
	<?php echo do_shortcode( '[wp_paypal button="buynow" name="SMS Order" amount="1" item_number="1" custom="sms" button_image="' . dirname(plugin_dir_url( __FILE__ )) . '/img/pay-now.svg" return="' .home_url() . '/thank-you-page/" cancel_return="'. home_url() . '/sms-service/"]' );
	?>
	</div>
	<div class="sms-applepay-wrap">
	<?php echo do_shortcode( '[wp_stripe_checkout item_name="1" name="SMS Order" description="sms" amount="1" label="Pay Now" item_number="1" custom="sms" success_url="' .home_url() . '/thank-you-page/" cancel_url="'. home_url() . '/sms-service/"]' );
	?>
	</div>
</div><!-- //end div dss-sms-container-->

<?php ?>