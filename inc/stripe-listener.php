<?php
function oe_stripe_event_listener() {

	if(isset($_GET['listener']) && $_GET['listener'] == 'stripe') {
 
		global $stripe_options;
 
		require_once(STRIPE_BASE_DIR . '/lib/Stripe.php');
 
		if(isset($stripe_options['test_mode']) && $stripe_options['test_mode']) {
			$secret_key = $stripe_options['test_secret_key'];
		} else {
			$secret_key = $stripe_options['live_secret_key'];
		}
 
		Stripe::setApiKey($secret_key);
 
		// retrieve the request's body and parse it as JSON
		$body = @file_get_contents('php://input');
 
		// grab the event information
		$event_json = json_decode($body);
 
		// this will be used to retrieve the event from Stripe
		$event_id = $event_json->id;
 
		if(isset($event_json->id)) {
 
			try {
 
				// to verify this is a real event, we re-retrieve the event from Stripe 
				$event = Stripe_Event::retrieve($event_id);
				$invoice = $event->data->object;
 
				// successful payment
				if($event->type == 'charge.succeeded') {
					// send a payment receipt email here
 
					// retrieve the payer's information
					$customer = Stripe_Customer::retrieve($invoice->customer);
					
					$email = $customer->email;
 
					$amount = $invoice->amount / 100; // amount comes in as amount in cents, so we need to convert to dollars
 
					$subject = __('Payment Receipt', 'oe_stripe');
					$headers = 'From: "' . html_entity_decode(get_bloginfo('name')) . '" <' . get_bloginfo('admin_email') . '>';
					$message = "Hello " . $customer_name . "\n\n";
					$message .= "You have successfully made a payment of " . $amount . "\n\n";
					$message .= "Thank you.";
 
					wp_mail($email, $subject, $message, $headers);
				}
 
				// failed payment
				if($event->type == 'charge.failed') {
					// send a failed payment notice email here
 
					// retrieve the payer's information
					$customer = Stripe_Customer::retrieve($invoice->customer);
					$email = $customer->email;
 
					$subject = __('Failed Payment', 'oe_stripe');
					$headers = 'From: "' . html_entity_decode(get_bloginfo('name')) . '" <' . get_bloginfo('admin_email') . '>';
					$message = "Hello " . $customer_name . "\n\n";
					$message .= "We have failed to process your payment of " . $amount . "\n\n";
					$message .= "Please get in touch with support.\n\n";
					$message .= "Thank you.";
 
					wp_mail($email, $subject, $message, $headers);
				}
 
			} catch (Exception $e) {
				wp_mail("ftntravis@gmail.com", "Stripe Mail Error", $e->getMessage());
			}
		}
	}
}
add_action('init', 'oe_stripe_event_listener');