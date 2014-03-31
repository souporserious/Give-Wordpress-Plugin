<?php

// get current URL ( get_permalink returns blank page )
function oe_current_url() {
	$pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

// submits user edited form
function oe_edit_member() {
	/* Get user info. */
	global $current_user;
	
	/* Set user ID */
	$user_id = $current_user->ID;
	
	/* If profile was saved, update profile. */
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' && wp_verify_nonce($_POST['oe_edit_nonce'], 'oe-edit-nonce') ) {
	
	    /* Update user password. */
	    if ( !empty($_POST['pass1'] ) && !empty( $_POST['pass2'] ) ) {
	        if ( $_POST['pass1'] == $_POST['pass2'] ) {
	            wp_update_user( array( 'ID' => $user_id, 'user_pass' => esc_attr( $_POST['pass1'] ) ) );
	        } else {
	            oe_errors()->add('match_password', __('The passwords you entered do not match. Your password was not updated.'));
	        }
	    }
	
	    /* Update user information. */
	    if ( !empty( $_POST['email'] ) ) {
	        if (!is_email(esc_attr( $_POST['email'] ))) {
	            oe_errors()->add('invalid_email', __('The Email you entered is not valid. Please try again.'));
	        } else if(email_exists(esc_attr( $_POST['email'] )) != $user_id ) {
	            oe_errors()->add('existing_email', __('The email you entered is already in use by another user. Please try a different one.'));
			} else {
	            wp_update_user( array ('ID' => $user_id, 'user_email' => esc_attr( $_POST['email'] )));
	        }
	    }
	    if ( !empty( $_POST['first-name'] ) )
	        update_user_meta( $user_id, 'first_name', esc_attr( $_POST['first-name'] ) );
	    if ( !empty( $_POST['last-name'] ) )
	        update_user_meta($user_id, 'last_name', esc_attr( $_POST['last-name'] ) );
	
	    // retrieve all error messages
		$errors = oe_errors()->get_error_messages();
	
	    /* Refresh to show updated info.*/
	    if(empty($errors)) {
	        do_action('edit_user_profile_update', $user_id);
	        wp_redirect( oe_current_url() );
	        exit;
	    }
	    
	}
}
add_action('init', 'oe_edit_member');

// edit form fields
function oe_edit_form_fields() {
	/* Get user info. */
	global $current_user;
	
	/* Set user ID */
	$user_id = $current_user->ID;

	ob_start();
	?>
	    <?php oe_show_error_messages(); ?>
	    
	    <form method="post" action="<?php the_permalink(); ?>">
	        <p class="form-username">
	            <label for="first-name"><?php _e('First Name', 'oe'); ?></label>
	            <input class="text-input" name="first-name" type="text" id="first-name" value="<?php the_author_meta( 'first_name', $user_id ); ?>" />
	        </p>
	        <p class="form-username">
	            <label for="last-name"><?php _e('Last Name', 'oe'); ?></label>
	            <input class="text-input" name="last-name" type="text" id="last-name" value="<?php the_author_meta( 'last_name', $user_id ); ?>" />
	        </p>
	        <p class="form-email">
	            <label for="email"><?php _e('E-mail *', 'oe'); ?></label>
	            <input class="text-input" name="email" type="text" id="email" value="<?php the_author_meta( 'user_email', $user_id ); ?>" />
	        </p>
	        <p class="form-password">
	            <label for="pass1"><?php _e('New Password *', 'oe'); ?> </label>
	            <input class="text-input" name="pass1" type="password" id="pass1" />
	        </p>
	        <p class="form-password">
	            <label for="pass2"><?php _e('Repeat Password *', 'oe'); ?></label>
	            <input class="text-input" name="pass2" type="password" id="pass2" />
	        </p>
	        
	        <?php do_action('edit_user_profile', $current_user); ?>
	        
	        <p class="form-submit">
	            <input name="updateuser" type="submit" id="updateuser" class="submit button" value="Update" />
	            <input type="hidden" name="oe_edit_nonce" value="<?php echo wp_create_nonce('oe-edit-nonce'); ?>"/>
	            <input name="action" type="hidden" id="action" value="update-user" />
	        </p>
	    </form>
	<?php 
	return ob_get_clean();
}

// user edit form
function oe_edit_form() {
	if(!is_user_logged_in()) {
		$output  = "<p>You must be logged in to edit your profile.</p>";
	} else {
		$output = oe_edit_form_fields();
	}
	return $output;
}
add_shortcode('edit_form', 'oe_edit_form');