<?php ?>
<br>
<div class="dss-crs-container" data-crs-id="<?php echo $crs_id; ?>">
	<div class="dss-notification-container"></div>
	<div class="dss-message-block">
		<textarea col=4 row=6 disabled><?php echo $meta['content']; ?></textarea>	
		<div class="dss-file-info">
            <div>File:</div>
			<div>
            <?php if ( $meta['file_path'] ) : ?>
                <a href="<?php echo plugins_url( strstr( $meta['file_path'], 'dropseal-services') ); ?>" target="_blank"><?php echo basename( $meta['file_path'] ); ?></a>                               
            <?php endif; ?>
        	</div>
		</div>
		<div class="dss-links-info">
			<div>Links:</div>
			<div>
            <?php if ( $meta['links'] ) : ?>
            
                <?php foreach ( $meta['links'] as $k ) : ?>                                             
                    <a href="<?= $k ?>" target="_blank"><?= $k ?></a><br> 
                <?php endforeach; ?>

            <?php endif; ?>
            </div> 
        </div> 
	</div>

	<div class="dss-crs-datetime-container">
		<div class="dss-datetime-info">
			<span>Delivery Date:</span>
		    <input type="text" placeholder="<?php echo date('M j, Y, g:i A', $delivery_data->process_date); ?>" class="dss-form-field" disabled>
		</div>
	</div>	
	<?php if ( $meta['order_price'] ) : ?>
		<hr class="crs-payment-line">
		<div class="crs-checkout-container">	
			<div class="dss-checkout-info">				
				<div>Price:</div>
				<div>
					<span>$<?php echo $meta['order_price']; ?></span>
				</div>
			</div>
			<?php if ( $delivery_data->status == 'pending' && $meta['order_price'] ) : ?>
			<div class="crs-payment-buttons">
				<div>
					<input type="radio" name="pay" id="dss-pay-pal" value="pay_pal" checked><img src="<?php echo dirname(plugin_dir_url( __FILE__ )) . '/img/paypal.svg'; ?>">
				</div>
				<div>
				<?php if ( is_plugin_active( 'wp-stripe-checkout/main.php' ) ) : ?>
					<input type="radio" name="pay" id="dss-apple-pay" value="apple_pay"><img src="<?php echo dirname(plugin_dir_url( __FILE__ )) . '/img/apple-pay.svg'; ?>">			
				<?php endif; ?>
				</div>
			</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<?php if ( $delivery_data->status == 'cancelled' ||  $delivery_data->status == 'completed' || $delivery_data->status == 'processing') : ?>
		<div class="crs-back-wrap">
			<button type="submit" id="crs-back" class="dss-cancel">Back</button>
		</div>
	<?php endif; ?>
	<div class="dss-crs-submit-container">
	<?php if ( $delivery_data->status == 'pending' && !$meta['order_price'] ) : ?>
		<div class="crs-back-cancel-wrap">
			<div>
				<button type="submit" id="crs-back" class="dss-cancel crs-back">Back</button>
			</div>
			<div>
				<button type="submit" id="crs-cancel" class="dss-cancel">Cancel</button>
			</div>
		</div>
	<?php endif; ?>
	<?php if ( $delivery_data->status == 'pending' && $meta['order_price'] ) : ?>
		<div class="crs-cancel-wrap">
			<button type="submit" id="crs-cancel" class="dss-cancel">Cancel</button>
		</div>
		<div class="crs-paypal-wrap">
		<?php echo do_shortcode( '[wp_paypal button="buynow" name="CRS Order" amount="'.$meta['order_price'].'" item_number="'.$crs_id.'" custom="crs" button_image="' . dirname(plugin_dir_url( __FILE__ )) . '/img/pay-now.svg" return="' . home_url() . '/thank-you-page/?payment-crs-id='.$crs_id.'" cancel_return="'. home_url() . '/edit-crs?id='.$crs_id.'"]');
		?>
		</div>
		<div class="crs-applepay-wrap">
		<?php echo do_shortcode( '[wp_stripe_checkout item_name="1" name="CRS Order" description="crs" amount="'.$meta['order_price'].'" label="Pay Now" item_number="1" custom="crs" success_url="' .home_url() . '/thank-you-page/?payment-crs-id='.$crs_id.'"" cancel_url="'. home_url() . '/edit-crs/?id='.$crs_id.'"]' );
		?>
		</div>
	<?php endif; ?>
	</div>
</div><!-- //end div dss-service-container-->
