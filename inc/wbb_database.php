<?php

//https://codex.wordpress.org/Creating_Tables_with_Plugins

#####################################################

global $woo_booking_bundle_hours_db_version;
$woo_booking_bundle_hours_db_version = '1.5';

function woo_booking_bundle_hours_db_install() {
	global $wpdb;
	global $woo_booking_bundle_hours_db_version;

	$table_name = $wpdb->prefix . 'bookingbundlehours';

	$charset_collate = $wpdb->get_charset_collate();

/*time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,*/

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		orderid int(10) NOT NULL,
		product text NOT NULL,
		customer text NOT NULL,
		price int(55) NOT NULL,
		hours int(55) NOT NULL,
		hours_used int(55) NOT NULL,
		hours_reserved int(55) NOT NULL,
		date_reserved datetime DEFAULT '0000-00-00 00:00:00',
		date_reserved_approved int(1),
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'woo_booking_bundle_hours_db_version', $woo_booking_bundle_hours_db_version );
}


#####################################################

function woo_booking_bundle_hours_install_dummy_data() {
	global $wpdb;

	$order_number = '1501';
	$product_name = 'product_name';
	$hours_bundle = 5;
	$order_customer = 'John Doe';
	$order_price = '100€';

	

	$table_name = $wpdb->prefix . 'bookingbundlehours';

	/* 'time' => current_time( 'mysql' ), */
	$wpdb->insert(
		$table_name,
		array(
			
			'orderid' => $order_number,
			'product' => $product_name,
			'customer' => $order_customer,
			'price' => $order_price,
			'hours' => $hours_bundle
		)
	);

	update_option('woo_bbh_category', 'booking-bundle-hours');
}



function woo_booking_bundle_hours_update_db_check() {
    global $woo_booking_bundle_hours_db_version;
    if ( get_site_option( '"woo_booking_bundle_hours_db_version"' ) != $woo_booking_bundle_hours_db_version ) {
        woo_booking_bundle_hours_db_install();
    }
}

