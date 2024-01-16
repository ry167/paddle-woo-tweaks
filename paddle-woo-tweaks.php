<?php
/**
 * Plugin Name: WooCommerce Paddle Gateway - Tweaks
 * Description: Tweaks to the WooCommerce Paddle Gateway plugin including country and post code support and adding a small tax disclaimer to the checkout.
 * Version: 1.0
 * Author: <a href="mailto:ryan@viennaandbailey.co.nz">Ryan Halliday</a>
 * Plugin URI: https://viennaandbailey.co.nz/
 */

 /**
  * !!!! WARNING !!!! 
  * At the moment this requires a small modification to the Paddle Gateway plugin.
  * In `wp-smartpay-woo/includes/gateways/paddle/paddle.php` add the following
  * to the `_create_paylink` function below the `$pay_link_data` variable in 
  * the `'oneoff' == $type` if statement (line 1502 @ v1.2.3):
  *
  * $pay_link_data = apply_filters('smartpay_paddle_oneoff_paylink_data', $pay_link_data, $order);
  */

function paddle_tweak_filter_checkout_countries($countries)
{
	$paddle_countries = require_once __DIR__ . '/data/allowed-country-list.php';
	return array_intersect_key($countries, $paddle_countries);
}

function paddle_tweak_require_postcode_for_some_countries( $locale ) {
	$postcode_required_countries = require_once __DIR__ . '/data/postcode-required-country-list.php';
	foreach ($postcode_required_countries as $country_code) {
		$locale[$country_code]['postcode']['required'] = true;
	}

	return $locale;
}

function paddle_tweak_include_country_and_postcode($paylink_data, $order){
	$paylink_data['customer_country'] = $order->get_billing_country();

	$postcode = $order->get_billing_postcode();
	if (!empty($postcode)){
		$paylink_data['customer_postcode'] = $postcode;
	}

	return $paylink_data;
}

function paddle_tweak_add_checkout_tax_notice(){
	if (wc_prices_include_tax()) return;


	?>
	<tr class="paddle-tweaks-tax-notice">
		<td colspan="2">
			<?php esc_html_e( 'Any applicable taxes will be calculated in the next step.', 'paddle-woo-tweaks' ); ?>
		</td>
	</tr>
	<?php
}

function paddle_tweak_product_tax_notice(){
	if (wc_prices_include_tax()) return;

	?>
	<div class="paddle-tweaks-product-tax-notice">
		<?php esc_html_e( 'Any sales taxes will be calculated in the checkout.', 'paddle-woo-tweaks' ); ?>
	</div>
	<?php
}

function paddle_tweak_missing_dependencies()
{
    echo __('<div class="error notice-warning"><p>You must install and active <code>WooCommerce</code> and <code>WooCommerce Paddle Gateway</code> to use the <code>WooCommerce Paddle Gateway - Tweaks</code> plugin.</p></div>', 'paddle-woo-tweaks');
}

function paddle_tweak_more_than_one_gateway_notice()
{
	echo __('<div class="error notice-warning"><p>You have more than one payment gateway enabled. This may cause issues with the Paddle gateway.</p></div>', 'paddle-woo-tweaks');
}

function paddle_tweak_setup(){
	if(!defined('WC_VERSION') || !defined('SMARTPAY_WC_VERSION')){
		add_action('admin_notices', 'paddle_tweak_missing_dependencies');
		return;
	}

	add_action('admin_init', function(){
		$gateways = WC()->payment_gateways->get_available_payment_gateways();

		if (!empty($gateways['smartpay_paddle']) && count($gateways) > 1){
			add_action('admin_notices', 'paddle_tweak_more_than_one_gateway_notice');
		}
	});

	// Only allow certain countries through the checkout
	add_filter('woocommerce_countries', 'paddle_tweak_filter_checkout_countries');

	// Require postcode for some countries
	add_filter( 'woocommerce_get_country_locale', 'paddle_tweak_require_postcode_for_some_countries', 10, 1 );

	// Include country and postcode in the paylink data
	add_filter('smartpay_paddle_oneoff_paylink_data', 'paddle_tweak_include_country_and_postcode', 10, 2);

	// Add a notice to the checkout page if taxes are not included
	add_action('woocommerce_review_order_after_order_total', 'paddle_tweak_add_checkout_tax_notice');

	// Add a notice to the product page if taxes are not included
	add_action('woocommerce_single_product_summary', 'paddle_tweak_product_tax_notice', 11);
}
add_action('init', 'paddle_tweak_setup');
