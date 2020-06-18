<?php

$user = new Dropseal_Services_User();
$reminders = 'true';
if ( !$user->isset_phone_or_carrier() ) {
	$notices['phone_or_carrier'] = 'To receive notifications on your mobile phone, you need to specify the number and mobile operator in your profile';
	$reminders = 'false';
	$reminder = '';
}
?>
<br>
<div class="dss-sms-container" data-qty-recipients="<?php echo $qty_recipients; ?>" data-editable-qty-recipients="<?php echo $editable_qty_recipients; ?>" data-sms-id="<?php echo $sms_id; ?>">
<div class="dss-notification-container">
	<?php if ( $notices) {

		foreach ($notices as $key => $value) {
			echo '<div class="message-error">'.$value.'</div>';
		}

	} ?>
	</div>
	<form method="post" enctype="multipart/form-data" class="rfield sms-form">
		<div>
			<div class="sms-block-title">
				<span>Content</span>
			</div>
			<div class="dss-message-block">
				<span class="dss-field-title">Message:</span>			
				<textarea col=4 row=6 name="sms_text" class="message-text"><?php echo $meta['content']; ?></textarea>				
				<div class=dss-attachement>
					<div class="dss-upload-file-name">
		                <?php if ( $meta['file_path'] ) : ?>		            	
			                    <a href="<?php echo plugins_url( strstr( $meta['file_path'], 'dropseal-services') ); ?>" target="_blank"><?php echo basename( $meta['file_path'] ); ?></a>                               
			                    <span class="dss-delete-file" id="dss-delete-file" data-sms-id="<?php echo $sms_id; ?>" title="Delete file">&#10007;</span>
			            	
		                <?php endif; ?>
	                </div>
					<div class="dss-attachement-wrap">
	                    <label class="dss-upload-file" data-file-size="<?php echo get_option( 'dss_upload_file_size' ); ?>">
	                        <input type="file" name="file">  
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

	                        <?php foreach ( $meta['links'] as $k => $v ) : ?> 
	                            <div class="dss-links-wrapper">
	                                <input type="url" name="links[]" value="<?php echo $v; ?>">

	                                <?php if ( !$k ) : ?>
	                                    <span class="dss-add-links-btn dss-links-btn" title="Add more url">+</span>
	                                <?php else: ?> 
	                                    <span class="dss-delete-links-btn dss-links-btn" title="Add more url">-</span>
	                                <?php endif; ?>   

	                            </div>
	                        <?php endforeach; ?>
			    		</div>
			    		<div class="dss-confirm-links-btn">Ok</div>
			    	</div>				    	
			    </div>
			    <!-- end Modal Box -->
				<span class="max-size-file-warning">The total file size must not exceed <?php echo get_option( 'dss_upload_file_size' ); ?>MB</span>
			</div>
		</div>
		
		<div>
			<div class="sms-block-title">
				<span>Recipient</span>
			</div>
			<div class="dss-message-block">
				<div class="sms-recipient-container">

				<?php for ( $i = 1; $i <= $qty_recipients; $i++ ) : ?>

					<?php if ( $meta['recipients']->{'recipient_'.$i}->phone ) : ?>
						<div class="dss-recipient-wrapper">
							<div class="dss-recipient-row">
								<div class="dss-recipient-name">
									<label class="required dss-field-title">Name:</label>
									<span class="input-error"></span>			
									<input type="text" name="name_<?php echo $i; ?>" class="dss-form-field" value="<?php echo $meta['recipients']->{'recipient_'.$i}->name; ?>">
								</div>
								<div class="dss-recipient-email">
								<?php if ( $meta['recipients']->{'recipient_'.$i}->email ) : ?>
									<span>E-mail delivery</span>
									<input type="email" name="email_<?php echo $i; ?>" class="dss-form-field" value="<?php echo $meta['recipients']->{'recipient_'.$i}->email; ?>">
								<?php endif; ?>
								</div>
							</div>
							<div class="dss-recipient-row">
								<div class="dss-recipient-phone">
									<label class="required dss-field-title">Phone Number:</label><span class="input-error"></span>
									<input type="tel" name="phone_<?php echo $i; ?>" placeholder="e.g. 1617555121" class="dss-form-field" value="<?php echo $meta['recipients']->{'recipient_'.$i}->phone; ?>">
								</div>
								<div>
									<span>Mobile Carrier:</span> 
								    <select name="mobile_carrier_<?php echo $i; ?>" class="sms-mobile-carrier-option">		
										<?php if ( $mobile_carriers ) : ?>
											<?php if ( !$meta['recipients']->{'recipient_'.$i}->mobile_carrier ) : ?>
												<option value="" selected>Mobile carrier no choice</option>
											<?php endif; ?>

		                                    <?php foreach ( $mobile_carriers as $mobile_carrier ) : ?> 
					                        	<?php if ( $mobile_carrier->checked) : ?>
				                        			<option value="<?php echo $mobile_carrier->sms; ?>"	                                        
		                                            <?php if ( $meta['recipients']->{'recipient_'.$i}->mobile_carrier == $mobile_carrier->sms ) echo 'selected'; ?>
		                                        
		                                        	><?php echo $mobile_carrier->name; ?></option>
		                                        <?php endif; ?>
		                                    <?php endforeach; ?>

		                                <?php endif; ?>
									</select>
								</div>
							</div>
						</div>
					<?php endif; ?>

				<?php endfor; ?> 

				</div>
			</div>
		</div>
		
		<div>
			<div class="sms-block-title">
				<span>Send Options</span>
			</div>
			<div class="dss-message-block">
				<p class="dss-date"><span class="dss-field-title">Select Delivery Date & Time( USA - Central Time Zone )</span></p>
				<div>
					<span id="dss-ampm" data-dss-date="<?php echo current_time('timestamp'); ?>"></span>
				</div>
				<label for="dss-input-date">
					<div class="dss-datetime">
					    <input type="text" name="datetime" class="datepicker-here" data-timepicker="true" placeholder="<?php echo date('M j, Y, g:i A', $delivery_data->process_date); ?>" value="" readonly>
					</div>
					<span class="calendar-icon-wrap"><img src="<?php echo dirname(plugin_dir_url( __FILE__ )) . '/img/calendar.svg'; ?>"></span>
				</label>				
				<?php if ( $meta['payment_email'] ) : ?>
					<div class="dss-recipient-row">
						<div>
							<input type="checkbox" checked disabled>
							<span>I want to send money</span>		
							<input type="text" value="<?php echo $meta['payment_email']; ?>" class="dss-form-field" disabled>
						</div>
						<div>
							<span>Money Amount:</span>
							<input type="text" value="<?php echo $meta['money_amount']; ?>" class="dss-form-field" disabled>
						</div>
					</div>
				<?php endif; ?>		
				<?php if ( $meta['total_paid'] ) : ?>
					<hr>			
					<div class="dss-checkout-info">
						<div>Paid:</div>
						<div>$<?php echo $meta['total_paid']; ?></div>
					</div>
				<?php endif; ?>

				<div class="dss-form-line">
					<input type="checkbox" name="reminders" id="sms-reminders" value="1" data-sms-reminders="<?php echo $reminders; ?>" <?php echo $reminder; ?>>
					<label for="sms-reminders" class="dss-field-title">I would like to receive status reports and reminders about this order</label>
				</div>
				<div class="dss-form-line">
					<input type="checkbox" name="conditions" id="sms-conditions" class="empty-field">
					<label for="sms-conditions" class="dss-field-title requir">I agree to the <a href="<?php echo home_url().'/terms-conditions'; ?>">Terms and Conditions</a></label>
				</div>
			</div>
			<div class="sms-submit-wrap">
				<div>
					<button type="submit" id="sms-cancel" class="dss-cancel sms-cancel-but">Cancel</button>
				</div>
				<div>
					<button type="submit" id="sms-update" class="dss-send sms-update-but disabled">Update</button>
				</div>
			</div>
		</div>
	</form>
</div><!-- //end div dss-sms-container-->

<?php ?>