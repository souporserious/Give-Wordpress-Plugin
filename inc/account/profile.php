<?php
// Set Up Custom Options
$prefix = 'stripe_';
$stripe_meta_fields = array(
	array(
		'label' => 'Address',
		'id'	=> $prefix.'address_1',
		'type'	=> 'text',
		'desc'	=> ''
	),
	array(
		'label' => 'Address',
		'id'	=> $prefix.'address_2',
		'type'	=> 'text',
		'desc'	=> ''
	),
	array(
		'label' => 'Customer ID',
		'id'	=> $prefix.'customer_id',
		'type'	=> 'text',
		'desc'	=> ''
	)
);

// Add Custom Fields
function add_stripe_fields( $user ) {
	
	global $stripe_meta_fields;
	
	// Begin the field table and loop
	echo '<table class="form-table">';
	
	foreach ($stripe_meta_fields as $field) {
		
		echo '<tr>
				<th><label for="'.$field['id'].'">'.$field['label'].'</label></th>
				<td>';
		
		$meta = get_the_author_meta($field['id'], $user->ID);
		
		switch($field['type']) {
		
			// text
			case 'text':
				
				echo '<input type="text" name="'.$field['id'].'" id="'.$field['id'].'" class="regular-text" value="'.$meta.'" />';
				echo '<p class="description">';
					_e($field['desc']);
				echo '</p>';
			break;
							
		} //end switch
		
		echo '</td></tr>';
		
	} // end foreach
	
	echo '</table>'; // end table
}

// Save the data
function save_stripe_fields( $user_id ) {	
	global $stripe_meta_fields;
	
	if ( !current_user_can( 'edit_user', $user_id ) ) return FALSE;
		
	foreach ($stripe_meta_fields as $field) {
		if($field['type'] == 'editor'){
			$content = $_POST[$field['id']];
			update_user_meta( $user_id, $field['id'], wpautop($content));
		} else {
			update_user_meta( $user_id, $field['id'], $_POST[$field['id']]);
		}	
	}

	update_user_meta( $user_id, 'user_image', $_POST['user_image'] );
}

// Check role to only add fields to necessary user
function check_stripe_add( $user ) {
    $user_role = get_user_role($user->ID);
    
    if($user_role == 'stripe_user') {
    	add_stripe_fields($user);
	}
}
add_action('show_user_profile', 'check_stripe_add');
add_action('edit_user_profile', 'check_stripe_add');

// Check role to only save fields for necessary user
function check_stripe_save( $user_id ) {
	// Get current user
	global $current_user;
	
	// Set ID
	$id = $current_user->ID;

    if(($user_id || $id) == 'stripe_user') {
    	save_stripe_fields($user_id);
	}
}
add_action( 'personal_options_update', 'check_stripe_save' );
add_action( 'edit_user_profile_update', 'check_stripe_save' );