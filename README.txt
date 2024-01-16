=== WooCommerce Paddle Gateway - Tweaks ===
Contributors: ryanhalliday
Tags: paddle woocommerce
Stable tag: 1.0
Requires PHP: 7.2.0
Requires at least: 6.0
Tested up to:      6.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Tweaks to the WooCommerce Paddle Gateway plugin including country and post code support and adding a small tax disclaimer to the checkout & product page.

You can get the main gateway plugin from here: https://wpsmartpay.com/paddle-for-woocommerce/

If you want to know about plugin updates, please Watch the repository on Github: https://github.com/ry167/paddle-woo-tweaks

== Installation ==

1. In `wp-smartpay-woo/includes/gateways/paddle/paddle.php` add the following to the `_create_paylink` function below the `$pay_link_data` variable in the `'oneoff' == $type` if statement (line 1502 @ v1.2.3):
  $pay_link_data = apply_filters('smartpay_paddle_oneoff_paylink_data', $pay_link_data, $order);
2. Because of the above change, I recommend renaming the plugin by editing `wp-smartpay-woo.php` and changing line 6 to be `* Plugin Name: WooCommerce Paddle Gateway (WITH MODIFICATIONS - CHECK BEFORE UPDATE)`
3. Upload the zip file to your site.
4. Activate the plugin.


== Frequently Asked Questions ==

* Why isn't this on the WordPress plugin repository?

It's an extension to a paid plugin for a specific payment provider, a bit niche. Hopefully the paid plugin integrates these features at some point.
