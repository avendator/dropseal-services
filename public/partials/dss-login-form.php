<?php 

// login process handler in Dropseal_Services_User class (dss_user_login)
if( is_user_logged_in() ) { ?>
    <div>
        <h2>You are already registered</h2>
    </div>
	<?php 

	return;
}
?>
<div class="dss-login-form">
	<form method="post">
		<div>
			<label for="name" class="required">Login:</label>				
			<input type="text" name="login" required>
		</div>
		<div>
			<label for="password" class="required">Password:</label>
			<input type="password" name="password" id="user-password" required>
			<span id="valid_password_message" class="message-error"></span>
		<div class="dss-login-submit">
			<button type="submit" name="dss_login" class="dss-sign">Log In</button>		 
		</div>
		<div class="dss-sign-links">
			<div>
				<a href="<?php echo home_url().'/register'; ?>">Sign In</a>
			</div>
			<div>
				<a href="<?php echo home_url().'/forgot-password'; ?>" title="Forgot password?">Forgot Password</a>
			</div>
		</div>
	</form>
</div>

<?php ?>