<?php 

if( is_user_logged_in() ) { ?>
    <div>
        <h2>You are already registered</h2>
    </div>
	<?php 

	return;
}

if( isset($_POST['dss_register']) ) {
	
	echo '<p class="errno">'.$_POST['dss_error'].'</p>';
}
?>
<div class="dss-registration-form">
	<div class="dss-user-notice"><p><?php echo $notice; ?></p></div>
	<form method="post">
		<div>
			<label for="name" class="required">Name / Login:</label>				
			<input type="text" name="user_name" required value="<?= $_POST['user_name']; ?>">
		</div>
		<div>
			<label for="email" class="required">Email:</label>
			<span id="valid_email_message" class="message-error"></span>
			<input type="email" name="email" required value="<?= $_POST['email']; ?>">
		</div>
		<div>
			<label for="phone" class="required">Phone Number:</label>
			<span id="invalid_phone_message" class="message-error"></span>
			<input type="tel" name="phone" placeholder="e.g. 1617555121" required value="<?= $_POST['phone']; ?>">		
		</div>
		<div>
			<label class="dss-field-title">Select Mobile Carrier</label>
		    <select name="mobile_carrier">
	    		<option value="" selected></option>				
				<?php if ( $mobile_carriers ): ?>

					<?php foreach ( $mobile_carriers as $mobile_carrier ): ?>
						<?php if ( $mobile_carrier->checked == 'checked' ): ?>
							<option value="<?php echo $mobile_carrier->sms; ?>"><?php echo $mobile_carrier->name; ?></option>	
						<?php endif; ?>
					<?php endforeach; ?>

				<?php endif; ?>
			</select>
		</div>
		<div>
			<label for="password" class="required">Password:</label>
			<span id="valid_password_message" class="message-error"></span>
			<input type="password" name="password" required>
		<div class="dss-reg-submit">
			<button type="submit" name="dss_register" class="dss-sign">Sign In</button>			 
		</div>
		<div class="dss-sign-links">
			<div>
				<a href="<?php echo home_url().'/login'; ?>">Log In</a>
			</div>
			<div>
				<a href="<?php echo home_url().'/forgot-password'; ?>" title="Forgot password?">Forgot Password</a>
			</div>
		</div>
	</form>
</div>

<?php ?>