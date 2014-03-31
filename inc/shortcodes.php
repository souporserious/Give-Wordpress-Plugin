<?php
function oe_stripe_payment_form($atts, $content = null) {
 
	extract( shortcode_atts( array(
		'amount' => ''
	), $atts ) );
 
	global $stripe_options;
 
	if(isset($_GET['payment']) && $_GET['payment'] == 'paid') {
		echo '<p class="success">' . __('Thank you for your payment.', 'oe_stripe') . '</p>';
	} else { ?>
		<span class="payment-errors"></span>
		<form action="" method="POST" id="stripe-payment-form">
			<div class="form-row">
				<label>Name</label>
				<input type="text" size="20" name="name" class="pay-name"/>
			</div>
			<div class="form-row">
				<label>Email</label>
				<input type="text" size="20" name="email"/>
			</div>
			<div class="form-row">
				<label>Payment Type</label>
				<input type="radio" name="recurring" value="no" checked="checked"/><span>One time payment</span>
				<input type="radio" name="recurring" value="yes"/><span>Recurring monthly payment</span>
			</div>
			<div class="form-row" id="stripe-single">
				<label>Amount</label>
				<input type="text" size="20" class="amount" name="amount"/>
			</div>
			<div class="form-row" id="stripe-plans" style="display:none;">
				<label>Choose Your Plan</label>
				<select name="plan_id" id="stripe_plan_id">
					<?php 
						$plans = oe_get_stripe_plans();
						if($plans) {
							foreach($plans as $id => $plan) {
								echo '<option value="' . $id . '">' . $plan . '</option>';
							}
						}
					?>
				</select>
			</div>
			<div class="form-row">
				<label>Card Number</label>
				<input type="text" size="20" autocomplete="off" class="card-number" value="4242424242424242"/>
			</div>
			<div class="form-row">
				<label>CVC</label>
				<input type="text" size="4" autocomplete="off" class="card-cvc" value="222"/>
			</div>
			<div class="form-row">
				<label>Expiration (MM/YYYY)</label>
				<input type="text" size="2" class="card-expiry-month" value="12"/>
				<span> / </span>
				<input type="text" size="4" class="card-expiry-year" value="15"/>
			</div>
			<input type="hidden" name="action" value="stripe"/>
			<input type="hidden" name="redirect" value="<?php echo get_permalink(); ?>"/>
			<input type="hidden" name="stripe_nonce" value="<?php echo wp_create_nonce('stripe-nonce'); ?>"/>
			<button type="submit" id="stripe-submit">Submit Payment</button>
		</form>
		<div class="payment-errors"></div>
		<?php
	}
}
add_shortcode('payment_form', 'oe_stripe_payment_form');