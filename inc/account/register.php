<?php

// register a new user
function oe_add_new_member() {
  	if (isset( $_POST["oe_user_login"] ) && wp_verify_nonce($_POST['oe_register_nonce'], 'oe-register-nonce')) {
		$user_login		= $_POST["oe_user_login"];	
		$user_email		= $_POST["oe_user_email"];
		$user_first 	= $_POST["oe_user_first"];
		$user_last	 	= $_POST["oe_user_last"];
		$user_pass		= $_POST["oe_user_pass"];
		$pass_confirm 	= $_POST["oe_user_pass_confirm"];
 
		// this is required for username checks
		require_once(ABSPATH . WPINC . '/registration.php');
 
		if(username_exists($user_login)) {
			// Username already registered
			oe_errors()->add('username_unavailable', __('Username already taken'));
		}
		if(!validate_username($user_login)) {
			// invalid username
			oe_errors()->add('username_invalid', __('Invalid username'));
		}
		if($user_login == '') {
			// empty username
			oe_errors()->add('username_empty', __('Please enter a username'));
		}
		if(!is_email($user_email)) {
			//invalid email
			oe_errors()->add('email_invalid', __('Invalid email'));
		}
		if(email_exists($user_email)) {
			//Email address already registered
			oe_errors()->add('email_used', __('Email already registered'));
		}
		if($user_pass == '') {
			// passwords do not match
			oe_errors()->add('password_empty', __('Please enter a password'));
		}
		if($user_pass != $pass_confirm) {
			// passwords do not match
			oe_errors()->add('password_mismatch', __('Passwords do not match'));
		}
 
		$errors = oe_errors()->get_error_messages();
 
		// only create the user in if there are no errors
		if(empty($errors)) {
 
			$new_user_id = wp_insert_user(array(
					'user_login'		=> $user_login,
					'user_pass'	 		=> $user_pass,
					'user_email'		=> $user_email,
					'first_name'		=> $user_first,
					'last_name'			=> $user_last,
					'user_registered'	=> date('Y-m-d H:i:s'),
					'role'				=> 'stripe_user'
				)
			);
			if($new_user_id) {
				// send an email to the admin alerting them of the registration
				wp_new_user_notification($new_user_id);
 
				// log the new user in
				wp_setcookie($user_login, $user_pass, true);
				wp_set_current_user($new_user_id, $user_login);	
				do_action('wp_login', $user_login);
 
				// send the newly created user to the home page after logging them in
				wp_redirect(home_url()); exit;
			}
 
		}
 
	}
}
add_action('init', 'oe_add_new_member');

// registration form fields
function oe_registration_form_fields() {
 
	ob_start(); ?>	
		<h3 class="oe_header"><?php _e('Register New Account'); ?></h3>
 
		<?php 
		// show any error messages after form submission
		oe_show_error_messages(); ?>
 
		<form id="oe_registration_form" class="oe_form" action="" method="POST">
			<fieldset>
				<p>
					<label for="oe_user_Login"><?php _e('Username'); ?></label>
					<input name="oe_user_login" id="oe_user_login" class="required" type="text"/>
				</p>
				<p>
					<label for="oe_user_email"><?php _e('Email'); ?></label>
					<input name="oe_user_email" id="oe_user_email" class="required" type="email"/>
				</p>
				<p>
					<label for="oe_user_first"><?php _e('First Name'); ?></label>
					<input name="oe_user_first" id="oe_user_first" type="text"/>
				</p>
				<p>
					<label for="oe_user_last"><?php _e('Last Name'); ?></label>
					<input name="oe_user_last" id="oe_user_last" type="text"/>
				</p>
				<p>
					<label for="password"><?php _e('Password'); ?></label>
					<input name="oe_user_pass" id="password" class="required" type="password"/>
				</p>
				<p>
					<label for="password_again"><?php _e('Password Again'); ?></label>
					<input name="oe_user_pass_confirm" id="password_again" class="required" type="password"/>
				</p>
				<p>
					<input type="hidden" name="oe_register_nonce" value="<?php echo wp_create_nonce('oe-register-nonce'); ?>"/>
					<input type="submit" value="<?php _e('Register Your Account'); ?>"/>
				</p>
			</fieldset>
		</form>
	<?php
	return ob_get_clean();
}

// user registration login form
function oe_registration_form() {
 
	// only show the registration form to non-logged-in members
	if(!is_user_logged_in()) {
 
		global $oe_load_css;
 
		// set this to true so the CSS is loaded
		$oe_load_css = true;
 
		// check to make sure user registration is enabled
		$registration_enabled = get_option('users_can_register');
 
		// only show the registration form if allowed
		if($registration_enabled) {
			$output = oe_registration_form_fields();
		} else {
			$output = __('User registration is not enabled');
		}
		return $output;
	}
}
add_shortcode('register_form', 'oe_registration_form');