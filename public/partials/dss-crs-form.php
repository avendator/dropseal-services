<?php 

if ( !is_user_logged_in() ) {

	$url = home_url() . '/login';

	echo "<script>window.location.href = '{$url}'; </script>";
}	
?>
<br>
<div class="dss-crs-container">
	<div class="dss-notification-container"></div>
	<div>Explain Request (ex: Purchase & Send; Print & Mail; etc...)</div>
	<form method="post" enctype="multipart/form-data" class="rfield crs-form">
		<div class="dss-message-block">		
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

		<div class="dss-message-block">
			<p class="dss-date"><span class="dss-field-title">Select Delivery Date & Time( USA - Central Time Zone )</span></p>
			<div>
				<span id="dss-ampm" data-dss-date="<?php echo current_time('timestamp'); ?>"></span>
			</div>
			<label for="dss-input-date">
				<div class="dss-datetime">
					<input type='text' name="datetime" class="datepicker-here empty-field" id="dss-input-date" data-timepicker="true" readonly>
				</div>
				<span class="calendar-icon-wrap"><img src="<?php echo dirname(plugin_dir_url( __FILE__ )) . '/img/calendar.svg'; ?>"></span>
			</label>
		</div>
		<div class="crs-submit-wrap">
			<button type="submit" id="crs-submit" class="dss-send disabled">Submit</button>
		</div>
	</form>
</div><!-- //end div dss-service-container-->
