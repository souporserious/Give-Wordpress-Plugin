<?php
/*
Plugin Name: OE Stripe Payment Form
Description: A simple payment form using the stripe payment gateway <code>[payment_form]</code>
Author: Travis Arnold
Author URI: http://owleyes.co/
Version: 1
*/
 
/**********************************
* constants and globals
**********************************/
 
if(!defined('STRIPE_BASE_URL')) {
	define('STRIPE_BASE_URL', plugin_dir_url(__FILE__));
}
if(!defined('STRIPE_BASE_DIR')) {
	define('STRIPE_BASE_DIR', dirname(__FILE__));
}
 
$stripe_options = get_option('stripe_settings');

/**********************************
* create user role "stripe_user" on plugin activation
**********************************/
function add_stripe_role() {
	add_role('stripe_user', 'Stripe User', array('read' => true));
	//remove_role( 'stripe_user' );
}
register_activation_hook( __FILE__, "add_stripe_role" );

// used for tracking error messages
function oe_errors(){
    static $wp_error; // Will hold global variable safely
    return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
}

// displays error messages from form submissions
function oe_show_error_messages() {
	if($codes = oe_errors()->get_error_codes()) {
		echo '<p class="oe_errors">';
		    // Loop error codes and display errors
		   foreach($codes as $code){
		        $message = oe_errors()->get_error_message($code);
		        echo '<span class="error"><strong>' . __('Error') . '</strong>: ' . $message . '</span><br/>';
		    }
		echo '</p>';
	}	
}

// Set new users admin bar to false by default
function admin_bar_false($user_id) {
	if($user_id == 'stripe_user') {
   		update_user_meta( $user_id, 'show_admin_bar_front', 'false' );
    	update_user_meta( $user_id, 'show_admin_bar_admin', 'false' );
    }
}
add_action("user_register", "admin_bar_false", 10, 1);

/**********************************
* includes
**********************************/
	include(STRIPE_BASE_DIR . '/inc/account/profile.php');
if(is_admin()) {
	include(STRIPE_BASE_DIR . '/inc/settings.php');
} else {
	include(STRIPE_BASE_DIR . '/inc/scripts.php');
	include(STRIPE_BASE_DIR . '/inc/shortcodes.php');
	include(STRIPE_BASE_DIR . '/inc/stripe-functions.php');
	include(STRIPE_BASE_DIR . '/inc/account/register.php');
	include(STRIPE_BASE_DIR . '/inc/account/login.php');
	include(STRIPE_BASE_DIR . '/inc/account/edit.php');
	include(STRIPE_BASE_DIR . '/inc/process-payment.php');	
}