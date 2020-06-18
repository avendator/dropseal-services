<?php
 
if ( is_user_logged_in() ) {
	echo sprintf( "You are already authorized on the site. <a href='%s'>Logout</a>.", wp_logout_url() );
	return;
}

$html = '';

if ( isset( $_REQUEST['errno'] ) ) {
	$errors = explode( ',', $_REQUEST['errno'] );

	foreach ( $errors as $error ) {
		switch ( $error ) {
			case 'empty_username':
				$html .= '<p class="errno">You did not provide your email address</p>';
				break;
			case 'password_reset_empty':
				$html .= '<p class="errno">Enter password!</p>';
				break;
			case 'password_reset_mismatch':
				$html .= '<p class="errno">Password mismatch!</p>';
				break;
			case 'invalid_email':
			case 'invalidcombo':
				$html .= '<p class="errno">No user with specified email was found on the site.</p>';
				break;
		}
	}
}

// to those who came here via the link from email, we show the form for setting a new password
if ( isset( $_REQUEST['login'] ) && isset( $_REQUEST['key'] ) ) {

	$html .= '
	<div class="dss-reset-pass-form">
		<h3>Enter a new password</h3>
			<form name="resetpassform" id="resetpassform" action="' . site_url( 'wp-login.php?action=resetpass' ) . '" method="post" autocomplete="off">
				<input type="hidden" id="user_login" name="login" value="' . esc_attr( $_REQUEST['login'] ) . '" autocomplete="off" />
				<input type="hidden" name="key" value="' . esc_attr( $_REQUEST['key'] ) . '" />
	 			<div>
					<label for="pass1">New Password</label>
					<input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off" />
				</div>
				<div>
					<label for="pass2">Confirm password</label>
					<input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off" />
				</div>

				<div class="description">' . wp_get_password_hint() . '</p>

				<div class="resetpass-submit">
					<button type="submit" name="submit" id="resetpass-button" class="dss-sign">Reset</button>
				</div>
			</form>
		</div>';
	echo $html;

	return;
}

// to everyone else - the usual form of password reset (step 1, where we indicate the email)
$html .= '
	<div class="dss-forgot-pass-form">
		<h3>Forgot your password?</h3>
		<p>Indicate your email address under which you are registered on the site and password recovery information will be sent to it.</p>
		<form id="lostpasswordform" action="' . wp_lostpassword_url() . '" method="post">
			<div class="form-row">
				<label for="user_login">Email</label>
				<input type="text" name="user_login" id="user_login">
			</div>
				<div class="lostpassword-submit">
				<button type="submit" name="submit" class="lostpassword-button dss-sign">Send</button>
			</div>
		</form>
	</div>';

echo $html;

return;