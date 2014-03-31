<?php
 
function oe_stripe_settings_setup() {
	add_menu_page('Stripe', 'Stripe', 'manage_options', 'stripe-menu', '', plugin_dir_url(__FILE__) . 'images/menu-icon.png'); 
	add_submenu_page('stripe-menu', 'Settings', 'Settings', 'manage_options', 'stripe-settings', 'oe_stripe_render_options_page');
}
add_action('admin_menu', 'oe_stripe_settings_setup');
 
function oe_stripe_render_options_page() {
	global $stripe_options;
	?>
	<div class="wrap">
		<h2>Stripe Settings</h2>
		<form method="post" action="options.php">
 
			<?php settings_fields('stripe_settings_group'); ?>
 
			<table class="form-table">
				<tbody>
					<tr valign="top">	
						<th scope="row" valign="top">
							Test Mode
						</th>
						<td>
							<input id="stripe_settings[test_mode]" name="stripe_settings[test_mode]" type="checkbox" value="1" <?php checked(1, $stripe_options['test_mode']); ?> />
							<label class="description" for="stripe_settings[test_mode]">Check this to use the plugin in test mode.</label>
						</td>
					</tr>
				</tbody>
			</table>	
 
			<h3 class="title">API Keys</h3>
			<table class="form-table">
				<tbody>
					<tr valign="top">	
						<th scope="row" valign="top">
							Live Secret
						</th>
						<td>
							<input id="stripe_settings[live_secret_key]" name="stripe_settings[live_secret_key]" type="text" class="regular-text" value="<?php echo $stripe_options['live_secret_key']; ?>"/>
							<label class="description" for="stripe_settings[live_secret_key]">Paste your live secret key.</label>
						</td>
					</tr>
					<tr valign="top">	
						<th scope="row" valign="top">
							Live Publishable
						</th>
						<td>
							<input id="stripe_settings[live_publishable_key]" name="stripe_settings[live_publishable_key]" type="text" class="regular-text" value="<?php echo $stripe_options['live_publishable_key']; ?>"/>
							<label class="description" for="stripe_settings[live_publishable_key]">Paste your live publishable key.</label>
						</td>
					</tr>
					<tr valign="top">	
						<th scope="row" valign="top">
							Test Secret
						</th>
						<td>
							<input id="stripe_settings[test_secret_key]" name="stripe_settings[test_secret_key]" type="text" class="regular-text" value="<?php echo $stripe_options['test_secret_key']; ?>"/>
							<label class="description" for="stripe_settings[test_secret_key]">Paste your test secret key.</label>
						</td>
					</tr>
					<tr valign="top">	
						<th scope="row" valign="top">
							Test Publishable
						</th>
						<td>
							<input id="stripe_settings[test_publishable_key]" name="stripe_settings[test_publishable_key]" class="regular-text" type="text" value="<?php echo $stripe_options['test_publishable_key']; ?>"/>
							<label class="description" for="stripe_settings[test_publishable_key]">Paste your test publishable key.</label>
						</td>
					</tr>
				</tbody>
			
			</table>
 
			<p class="submit">
				<input type="submit" class="button-primary" value="Save Options" />
			</p>
			
			<ul>
				<li>Card number: 4242424242424242</li>
				<li>CVC: 222 (can be any 3-digit number)</li>
				<li>Expiration: 12 / 2015 (can be any date in the future)</li>
			</ul>
 
		</form>
	<?php
}
 
function oe_stripe_register_settings() {
	// creates our settings in the options table
	register_setting('stripe_settings_group', 'stripe_settings');
}
add_action('admin_init', 'oe_stripe_register_settings');