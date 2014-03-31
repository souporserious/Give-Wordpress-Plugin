<?php

// logs a member in after submitting a form
function oe_login_member() { 
	if(isset($_POST['oe_user_login']) && wp_verify_nonce($_POST['oe_login_nonce'], 'oe-login-nonce')) {
 
		// this returns the user ID and other info from the user name
		$user = get_user_by('login', $_POST['oe_user_login']);
 
		if(!$user) {
			// if the user name doesn't exist
			oe_errors()->add('empty_username', __('Invalid username'));
		}
 
		if(!isset($_POST['oe_user_pass']) || $_POST['oe_user_pass'] == '') {
			// if no password was entered
			oe_errors()->add('empty_password', __('Please enter a password'));
		}
 
		// check the user's login with their password
		if(!wp_check_password($_POST['oe_user_pass'], $user->user_pass, $user->ID)) {
			// if the password is incorrect for the specified user
			oe_errors()->add('empty_password', __('Incorrect password'));
		}
 
		// retrieve all error messages
		$errors = oe_errors()->get_error_messages();
 
		// only log the user in if there are no errors
		if(empty($errors)) {
 
			wp_setcookie($_POST['oe_user_login'], $_POST['oe_user_pass'], true);
			wp_set_current_user($user->ID, $_POST['oe_user_login']);	
			do_action('wp_login', $_POST['oe_user_login']);
 
			wp_redirect(home_url()); exit;
		}
	}
}
add_action('init', 'oe_login_member');

// login form fields
function oe_login_form_fields() {
	ob_start(); 
	?>
		<h3 class="oe_header"><?php _e('Login'); ?></h3>
 
		<?php oe_show_error_messages(); ?>
 
		<form id="oe_login_form"  class="oe_form" action="" method="post">
			<fieldset>
				<p>
					<label for="oe_user_login">Username</label>
					<input name="oe_user_login" id="oe_user_login" class="required" type="text"/>
				</p>
				<p>
					<label for="oe_user_pass">Password</label>
					<input name="oe_user_pass" id="oe_user_pass" class="required" type="password"/>
				</p>
				<p>
					<input type="hidden" name="oe_login_nonce" value="<?php echo wp_create_nonce('oe-login-nonce'); ?>"/>
					<input id="oe_login_submit" type="submit" value="Login"/>
				</p>
			</fieldset>
		</form>
	<?php
	return ob_get_clean();
}

// user login form
function oe_login_form() {
	if(!is_user_logged_in()) {
		$output = oe_login_form_fields();
	} else {
		$output  = "<p>You are already logged in.</p>";
		$output .= "<a href='".wp_logout_url( get_permalink() )."'>Logout</a>";
	}
	return $output;
}
add_shortcode('login_form', 'oe_login_form');