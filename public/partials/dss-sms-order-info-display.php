<?php ?>
<br>
<div class="dss-notification-container"></div>
<div class="dss-sms-container">
	<div>
		<div class="sms-block-title">
			<span>Content</span>
		</div>
		<div class="dss-message-block">
			<span class="dss-field-title">Message:</span>			
			<textarea col=4 row=6 disabled><?php echo $meta['content']; ?></textarea>				
			<div>
            <?php if ( $meta['file_path'] ) : ?>
            	<span>File:</span>
                <a href="<?php echo plugins_url( strstr( $meta['file_path'], 'dropseal-services') ); ?>" target="_blank"><?php echo basename( $meta['file_path'] ); ?></a>                               
            <?php endif; ?>
			</div>
			<div>				
            <?php if ( $meta['links'][0] ) : ?>
            	<span>Links:</span>

                <?php foreach ( $meta['links'] as $k ) : ?>                                             
                    <a href="<?= $k ?>" target="_blank"><?= $k ?></a><br> 
                <?php endforeach; ?>

            <?php endif; ?> 
            </div> 
		</div>
	</div>
	
	<div>
		<div class="sms-block-title">
			<span>Recipient</span>
		</div>
		<div class="dss-message-block">
			<div class="sms-recipient-container">

			<?php for ( $i = 1; $i <= $qty_recipients; $i++ ) : ?> 
				
				<?php $checked_email = ''; 
				if ( $email = $meta['recipients']->{'recipient_'.$i}->email ) {
					$checked_email = ' checked';
				}
				?>
				<?php if ( $meta['recipients']->{'recipient_'.$i}->phone ) : ?>
					<div class="dss-recipient-wrapper">
						<div class="dss-recipient-row">
							<div>
								<span>Name:</span>				
								<input type="text" class="dss-form-field" value="<?php echo $meta['recipients']->{'recipient_'.$i}->name; ?>" disabled>
							</div>
							<div>
							<?php if ( $email = $meta['recipients']->{'recipient_'.$i}->email ) : ?>
								<span>E-mail delivery</span>
									<input type="text" class="dss-form-field" value="<?php echo $meta['recipients']->{'recipient_'.$i}->email; ?>"disabled>
							<?php endif; ?>
							</div>
						</div>
						<div class="dss-recipient-row">
							<div>
								<span>Phone Number:</span>
								<input type="text" placeholder="e.g. 1617555121" class="dss-form-field" value="<?php echo $meta['recipients']->{'recipient_'.$i}->phone; ?>"disabled>
							</div>
							<div>
							<?php if ( $meta['recipients']->{'recipient_'.$i}->mobile_carrier ) : ?>
								<span>Mobile Carrier:</span>            
	                            <input type="text" class="dss-form-field" value="<?php echo $meta['recipients']->{'recipient_'.$i}->mobile_carrier; ?>" disabled>
	                        <?php endif; ?>
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
			<div class="dss-datetime-info">
				<span>Delivery Date:</span>
			    <input type="text" placeholder="<?php echo date('M j, Y, g:i A', $delivery_data->process_date); ?>" class="dss-form-field" disabled>
			</div>				

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
				<hr class="payment-line">			
				<div class="dss-checkout-info">
					<div>Paid:</div>
					<div>$<?php echo $meta['total_paid']; ?></div>
				</div>
			<?php endif; ?>

			<div class="dss-form-line">
				<input type="checkbox" <?php echo $reminder; ?> disabled>
				<label class="dss-field-title">I would like to receive status reports and reminders about this order</label>
			</div>
		</div>
	</div>
	<div class="sms-back-wrap">
		<button type="submit" id="sms-back" class="dss-cancel">Back</button>
	</div>
</div><!-- //end div dss-sms-container-->

<?php ?>