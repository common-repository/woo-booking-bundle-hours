<?php

/*
 * Plugin Name: WooCommerce Booking Bundle Hours
 * Plugin URI:  https://bookingbundles.com/woo
 * Description: Booking Bundle Hours allows you to buy woocommerce packages of time services, use them from time to time and keep track of hours still available and hours already used
 * Version:     0.7.3
 * Contributors: wpnetworkit, cristianozanca
 * Author:      WPNetworkit
 * Author URI:  https://wpnetwork.it
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: woo-booking-bundle-hours
 * Domain Path: /languages
 * WC requires at least: 7.0
 * WC tested up to: 8.7
*/

if (!defined('ABSPATH')) {

	die(); // Exit if accessed directly

}

function woo_booking_bundle_hours_textdomain()

{
	load_plugin_textdomain('woo-booking-bundle-hours', FALSE, basename(dirname(__FILE__)) . '/languages');
}

add_action('plugins_loaded', 'woo_booking_bundle_hours_textdomain');


if (!class_exists('woo_booking_bundle_hours')) : {
		class woo_booking_bundle_hours
		{

			public function __construct()
			{

				include_once plugin_dir_path(__FILE__) . 'inc/account_tabs.php';
				include_once plugin_dir_path(__FILE__) . 'inc/menu_setup.php';
				include_once plugin_dir_path(__FILE__) . 'inc/setup_page_display.php';
				include_once plugin_dir_path(__FILE__) . 'inc/update_data.php';


				add_action( 'plugins_loaded', 'woo_booking_bundle_hours_update_db_check' );

				add_action('admin_menu', 'woo_booking_bundle_hours_setup_menu');

				add_action('admin_menu', 'add_woo_booking_bundle_to_woocommerce_navigation_bar');

				add_action('init', 'woo_booking_bundle_hours_endpoints');

				add_action('init', 'woo_booking_bundle_hours_update_data');

				add_filter('query_vars', 'woo_booking_bundle_hours_query_vars', 0);

				register_activation_hook(__FILE__, 'woo_booking_bundle_hours_flush_rewrite_rules');
				
				register_deactivation_hook(__FILE__, 'woo_booking_bundle_hours_flush_rewrite_rules');

				add_filter('woocommerce_account_menu_items', 'woo_booking_bundle_hours_menu_items');

				add_filter('the_title', 'woo_booking_bundle_hours_endpoint_title');

				add_action('woocommerce_account_woo-booking-bundle-hours_endpoint', 'woo_booking_bundle_hours_content');

				add_action('admin_enqueue_scripts', array($this, 'register_woo_booking_bundle_hours_styles_and_scripts'));

				add_action('wp_enqueue_scripts', array($this, 'probb_enqueue_ui_styles_scripts'));

				add_action('admin_notices', array($this, 'woo_fic_admin_notices'));

				add_action( 'before_woocommerce_init', function() {
					if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
						\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
					}
				} );

				

				
			
				/**
				 * cerca categoria in ordine e aggiungi dati in Database bundle
				*/

				//add_action( 'woocommerce_order_status_completed', 'bundle_woocommerce_bbh_update_from_order',30, 1 );


				function bundle_woocommerce_bbh_update_from_order()
				{

					$woo_bbh_max_orders_number = get_option('woo_bbh_max_orders_number');

					$date_bbh_start = get_option('woo-bbh-datepicker');

					$date_bbh_end = get_option('woo-bbh-datepicker-2');

					$args = array(
						'limit' => $woo_bbh_max_orders_number,
						'date_created' => $date_bbh_start . '...' . $date_bbh_end
					);


					$orders = wc_get_orders($args);

					// Loop through each WC_Order object
					foreach ($orders as $order) {

						foreach ($order->get_items() as $item) {

							//print_r($item);

							//$categories = array('booking-bundle-hours');

							$woo_bbh_category = get_option('woo_bbh_category');

							$categories = array($woo_bbh_category);

							// Just for a defined product category
							if (has_term($categories, 'product_cat', $item->get_product_id())) {

								$_product = $item->get_product();

								$bundle_bh_email = $order->get_billing_email();
								$bundle_bh_product = $_product->get_name();
								$bundle_bh_order = $order->get_order_number();
								$bundle_bh_hours = $_product->get_attribute('hours');
								$bundle_bh_price = $order->get_total();

								global $wpdb;

								$table_name = $wpdb->prefix . 'bookingbundlehours';

								$args = array(

									$bundle_bh_order,
									$bundle_bh_product,
									$bundle_bh_hours,
									$bundle_bh_email,
									$bundle_bh_price
								);

								#######################

								$sql = $wpdb->prepare("SELECT orderid FROM $table_name WHERE orderid = %d LIMIT 1", $bundle_bh_order);

								if ($wpdb->get_var($sql)) {

									//	print_r($bundle_bh_order);

									//	return;


									//$where = "WHERE orderid = $bundle_bh_order";

									$where = "WHERE $bundle_bh_order LIMIT 0";

									$wpdb->query(
										$wpdb->prepare(
											"
						UPDATE $table_name 
						SET orderid = %d,
						product = %s,
						hours = %d, 
						customer = %s,
						price = %d
						$where	
					",

											$args

										)
									);
								} else {

									#############################





									$wpdb->query(
										$wpdb->prepare(
											"
						INSERT INTO $table_name
						( orderid, product, hours, customer, price )
						VALUES ( %d, %s, %d, %s, %f )
					",
											$args
										)
									);
								}





								#################################à

							}
						}
					}
				}



				###################################################
				//https://www.proy.info/woocommerce-allow-only-1-product-per-category/
				// Allow only one(or pre-defined) product per category in the cart
				add_filter('woocommerce_add_to_cart_validation', 'allowed_quantity_per_category_in_the_cart', 10, 2);

				function allowed_quantity_per_category_in_the_cart($passed, $product_id)
				{
					$max_num_products = 1; // change the maximum allowed in the cart
					$running_qty = 0;
					$restricted_product_cats = array();

					//Restrict particular category/categories by category slug
					$restricted_product_cats[] = get_option('woo_bbh_category');;

					//$restricted_product_cats[] = 'cat-slug-two';
					// Getting the current product category slugs in an array
					$product_cats_object = get_the_terms($product_id, 'product_cat');
					foreach ($product_cats_object as $obj_prod_cat) $current_product_cats[] = $obj_prod_cat->slug;

					// Iterating through each cart item
					foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {

						// Restrict $max_num_products from each category
						// if( has_term( $current_product_cats, 'product_cat', $cart_item['product_id'] )) {
						// Restrict $max_num_products from restricted product categories
						if (array_intersect($restricted_product_cats, $current_product_cats) && has_term($restricted_product_cats, 'product_cat', $cart_item['product_id'])) {
							// count(selected category) quantity
							$running_qty += (int)$cart_item['quantity'];
							// More than allowed products in the cart is not allowed
							if ($running_qty >= $max_num_products) {
								wc_add_notice(sprintf('You can only register %s ' . ($max_num_products > 1 ? 'bundles' : 'bundle') . ' at a time. If you need more hours, please choose a differnt option after the option "hours", Thank You.',  $max_num_products), 'error');
								$passed = false; // don't add the new product to the cart
								// We stop the loop
								break;
							}
						}
					}
					return $passed;
				}








				add_option("woo_booking_bundle_hours_db_version", "1.0");

				include_once plugin_dir_path(__FILE__) . 'inc/wbb_database.php';




				register_activation_hook(__FILE__, 'woo_booking_bundle_hours_db_install');
				register_activation_hook(__FILE__, 'woo_booking_bundle_hours_install_dummy_data');



				#####################################################################

			}

			/**
			 * 
			 * check if plugin WooCommerce is active 
			 * 			
			*/

			function woo_fic_admin_notices()
			{
				if (!is_plugin_active('woocommerce/woocommerce.php')) {
					echo "<div class='notice error is-dismissible'><p>".__(
						'To use the plug-in <b>WooCommerce Booking Bundle Hours</b> the
						 <b>WooCommerce</b> plug-in must be installed and activated!', 'woo-booking-bundle-hours'
					)."</div>";
				}
			}



			/**
			 *
			 * Custom stylesheet to load image and js scripts only on backend page
			 *
			 */
			function register_woo_booking_bundle_hours_styles_and_scripts($hook)
			{

				$current_screen = get_current_screen();

				if (strpos($current_screen->base, 'woo_booking_bundle_hours') === false) {

					return;
				} else {

					wp_enqueue_style('style-admin', plugins_url('assets/css/woo_booking_bundle_hours.css', __FILE__));


					/* Load the datepicker jQuery-ui plugin script */

					wp_enqueue_script('jquery-ui-datepicker');
					wp_enqueue_script(
						'wp-jquery-date-picker',
						plugins_url(
							'assets/js/woo_booking_bundle.js',
							__FILE__,
							array('jquery', 'jquery-ui-core'),
							time(),
							true
						)
					);

					/*    wp_enqueue_style( 'jquery-ui-datepicker' ); */

					wp_register_style('jquery-ui', plugins_url('assets/css/jquery-ui.css', __FILE__));					
					wp_enqueue_style('jquery-ui');
				}
			}


			##################################

			function probb_enqueue_ui_styles_scripts()
			{

				if ( is_plugin_active('woocommerce/woocommerce.php') && is_account_page() ) {

					if (!wp_script_is('jquery-ui-slideraccess', 'registered')) {

						wp_register_script('jquery-ui-slideraccess', plugins_url('assets/js/jquery-ui-sliderAccess.js', __FILE__), array(), '1.6.3');
					}

					if (!wp_script_is('jquery-ui-timepicker', 'registered')) {
						wp_register_script(
							'jquery-ui-timepicker',
							plugins_url('assets/js/jquery-ui-timepicker-addon.min.js', __FILE__),
							array(
								'jquery',
								'jquery-ui-core',
								'jquery-ui-datepicker',
								'jquery-ui-slider',
								'jquery-ui-slideraccess',
							),
							'1.6.3'
						);
					}

					if (!wp_style_is('jquery-ui-timepicker', 'registered')) {
						wp_register_style('jquery-ui-timepicker', 
						plugins_url('assets/css/jquery-ui-timepicker-addon.min.css', __FILE__),
						array(), '1.6.3');
					}



					wp_enqueue_script('jquery-ui-timepicker');
					wp_enqueue_style('jquery-ui-timepicker');
					wp_enqueue_style('jquery-ui-custom1', plugins_url('assets/css/normalize.css', __FILE__ ));
					wp_enqueue_style('jquery-ui-custom2', plugins_url('assets/css/datepicker.css', __FILE__));
					wp_register_style('jquery-ui', plugins_url('assets/css/jquery-ui.css', __FILE__));
					
					wp_enqueue_script('jquery-ui-datepicker');

					wp_enqueue_style('jquery-ui');


					wp_enqueue_script(
						'wp-jquery-date-picker',

						plugins_url(
							'assets/js/woo_booking_bundle.js',
							__FILE__,
							array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'),
							time(),
							true
						)
					);

					wp_enqueue_style('style-myaccount', plugins_url('assets/css/woo_booking_bundle_hours_myaccount.css', __FILE__));
				}



			}


			################################





		}
	}

	//Creates a new instance
	new woo_booking_bundle_hours;

endif;
