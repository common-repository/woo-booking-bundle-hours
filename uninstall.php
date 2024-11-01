<?php
// If uninstall not called from WordPress exit
if( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	die;


// drop a custom database table
global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}bookingbundlehours");

// delete db version from wp_options table
delete_option('woo_booking_bundle_hours_db_version');

// delete bbh options

delete_option('woo_bbh_max_orders_number');

delete_option('woo-bbh-datepicker');

delete_option('woo_bbh_category');

delete_option('woo-bbh-datepicker-2');



