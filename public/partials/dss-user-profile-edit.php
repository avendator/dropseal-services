<?php 

if ( isset($_POST['update_user']) ) {

	$user_id = get_current_user_id();

	update_user_meta( $user_id, 'dss_phone', $_POST['phone'] );

	update_user_meta( $user_id, 'dss_mobile_carrier', $_POST['mobile_carrier'] );

	$userdata = [
		'ID' => $user_id,
		'user_email' => $_POST['email'],
	];
	$user_id = wp_update_user( $userdata );

	if ( is_wp_error( $user_id ) ) {

		$notice = '<div class="message-error">'.$user_id->get_error_message().'</div>';
	}
	else {
		$notice = '<div>Your profile has been updated successfully!</div>';

        if ( $_POST['phone'] && $_POST['mobile_carrier'] ) {

        	$user_name = get_userdata( $user_id )->display_name;
	        //send sms to customer
			wp_mail( 
				$_POST['phone'] . $_POST['mobile_carrier'], 
				'Dropseal services', 
				( $user_name.', your profile has been updated successfully!' ) 
			);
            //send email to customer
	        wp_mail( 
	            $_POST['email'], 
	            'Dropseal services', 
	            ( $user_name.', your profile has been updated successfully!') 
	        );
    	}
	}
}

if ( isset($_POST['dss_change_pswd']) ) {

	$notice = '';
	switch ( $_POST['dss_change_pswd'] ) {
		case 'existing_pswd':
			$notice = '<div class="message-error">Please enter a valid existing password!</div>';
			break;
		case 'is_too_short_pswd':
			$notice = '<div class="message-error">Your password is too short!</div>';
			break;
		default:
			$notice = '<div>Your password has been updated successfully!</div>';
			break;
	}
}

if ( isset($_POST['email']) ) {
	$email = $_POST['email'];
} else {
	$email = $current_user->user_email;
}
if ( isset($_POST['phone']) ) {
	$phone = $_POST['phone'];
} else {
	$phone = get_user_meta( $current_user->ID, 'dss_phone', true );
}
if ( isset($_POST['mobile_carrier']) ) {
	$carrier = $_POST['mobile_carrier'];
} else {
	$carrier = get_user_meta( $current_user->ID, 'dss_mobile_carrier', true );
}
?>

<div class="dashboard-menu-container">
	<div class="dss-account-menu-select-mob">Edit Profile<i class="fas fa-chevron-right"></i></div>
	<div class="dss-account-menu-select">Edit Profile<i class="fas fa-chevron-right"></i></div>
	<ul>
		<li><a href="/my-account/">Messages</a></li>
		<li><a href="/my-account/crs-orders/">CRS Orders</a></li>
		<li><a href="/sms-service/">New SMS</a></li>
		<li><a href="/custom-service/">New CRS</a></li>
		<li><a href="/my-payment/">My Payment</a></li>
	</ul>
</div>

<div class="dss-user-profile-container">	
	<div>
		<div class="dss-user-notice">
			<?php echo $notice; ?>
		</div>
		<div><?php echo 'Your Login: '.$current_user->user_login; ?></div>
		<div class="sms-block-title">
			<span>Profile</span>
		</div>
		<div class="user-profile-block">
			<form method="post">
				<div>
					<label for="user-email" class="dss-field-title">Email:</label>
					<span id="valid_email_message" class="message-error"></span>
					<input type="email" name="email" class="dss-form-field" value="<?php echo $email; ?>">
				</div>
				<div>
					<label for="user-phone" class="dss-field-title">Phone Number:</label>
					<span id="invalid_phone_message" class="message-error"></span>
					<input type="tel" name="phone" placeholder="e.g. 1617555121" class="dss-form-field" value="<?php echo $phone; ?>">
				</div>
				<div>
					<label class="dss-field-title">Select Mobile Carrier</label>
				    <select name="mobile_carrier">
				    	<option value="" selected></option>			
						<?php if ( $mobile_carriers ): ?>

							<?php foreach ( $mobile_carriers as $mobile_carrier ): ?>
	                        	<?php if ( $mobile_carrier->checked ) : ?>
                        			<option value="<?php echo $mobile_carrier->sms; ?>"

									<?php if ( $carrier == $mobile_carrier->sms ) echo 'selected'; ?>	
																			
									><?php echo $mobile_carrier->name; ?></option>
                    			<?php endif; ?>
							<?php endforeach; ?>

						<?php endif; ?>
					</select>
				</div>
				<div class="dss-reg-submit">					
					<button type="submit" name="update_user" class="dss-sign">Update</button>
				</div>
			</form>
		</div>
	</div>

	<div class="change-password-container">
		<form method="post">
			<div class="sms-block-title">
				<span>Password</span>
			</div>
			<div class="user-profile-block">
				<div>
					<label for="user-password" class="dss-field-title">Old Password:</label>
					<input type="password" name="old_password" class="dss-form-field">
				</div>
				<div>
					<label for="new-password" class="dss-field-title">New Password:</label>
					<span id="valid_password_message" class="message-error"></span>
					<input type="password" name="new_password" id="new-password" class="dss-form-field" autocomplete="off">
					<input type="hidden" name="email" value="<?php echo $current_user->user_email; ?>">
				</div>
				<div class="dss-confirm-pass">
					<label for="user-password" class="dss-field-title">Confirm Password:</label>
					<input type="password" id="dss-confirm-password" class="dss-form-field" autocomplete="off">
				</div>
				<div class="dss-reg-submit">
					<button type="submit" name="update_pass" class="dss-sign">Update</button>
				</div>
			</div>
		</form>
	</div>
</div>

<?php ?>