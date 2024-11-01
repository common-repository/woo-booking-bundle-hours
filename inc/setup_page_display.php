<?php


// Don't access this directly, please

if ( ! defined( 'ABSPATH' ) ) exit;

// check user permission to admin setup values

function woo_booking_bundle_hours_setup_page_display() {
	if (!current_user_can('manage_options')) {
		wp_die('Unauthorized user');
	}
	global $wpdb;
	$table_name = $wpdb->prefix . 'bookingbundlehours';

	if (isset($_POST['hours_used'], $_POST['orderid'])  && wp_verify_nonce( $_POST['_wpnonce'] )) {
		
		$bbh_hours_used = intval($_POST['hours_used']);
		$bbh_order_id = intval($_POST['orderid']);

		$wpdb->update($table_name,
			array('hours_used'=>$bbh_hours_used),
			array('orderid'=>$bbh_order_id));


		$type = 'updated';
		$message = __( 'Updated Value', 'woo-booking-bundle-hours' );
		add_settings_error('woo-booking-bundle-hours',esc_attr( 'settings_updated' ),$message, $type);
		settings_errors('woo-booking-bundle-hours');

	}

	if (isset($_POST['date_reserved_approved']) && wp_verify_nonce( $_POST['_wpnonce'] )) {
		
		$bbh_date_reserved_approved = intval($_POST['date_reserved_approved']);
		$bbh_order_id = intval($_POST['orderid']);
		$bbh_hours_used = intval($_POST['hours_used']);
		$bbh_hours_reserved = intval($_POST['hours_reserved']);

		$bbh_usedplusres = $bbh_hours_used + $bbh_hours_reserved;


		if ($bbh_date_reserved_approved > 0) {

		$wpdb->update($table_name,
			array(
				'date_reserved_approved'=>$bbh_date_reserved_approved,
				'hours_used'=>$bbh_usedplusres
			),
			array('orderid'=>$bbh_order_id));
		
		} else {
		$wpdb->update($table_name,
			array(
				'date_reserved_approved'=>$bbh_date_reserved_approved,
			),
			array('orderid'=>$bbh_order_id));

		}
	}

	if (isset($_POST['import_from_orders'])) {

        bundle_woocommerce_bbh_update_from_order();

        
    }
    
    if (isset($_POST['delete_row_bbh'], $_POST['orderid'] )) {

		$bbh_order_id = intval($_POST['orderid']);

        $wpdb->show_errors();    
                  
        $wpdb->delete($table_name, 
        array('orderid' => $bbh_order_id) 
        );

        
	}

	if (isset($_POST['remove_reservation_bbh'], $_POST['orderid'] )) {

		$bbh_order_id = intval($_POST['orderid']);

        $wpdb->show_errors();    
                  
		$wpdb->update($table_name,
		array(
			'hours_reserved' => 0,
			'date_reserved' => 0,
			'date_reserved_approved' => NULL
		),
		array('orderid' => $bbh_order_id) 	
	);

        
	}

	if (isset($_POST['woo_bbh_max_orders_number'])  && wp_verify_nonce( $_POST['_wpnonce'] )) {

        update_option('woo_bbh_max_orders_number', sanitize_text_field($_POST['woo_bbh_max_orders_number']));
		

	}


	
	if (isset($_POST['woo-bbh-datepicker'])) { 

		update_option('woo-bbh-datepicker', sanitize_text_field($_POST['woo-bbh-datepicker']));
		

		
	}

	if (isset($_POST['woo_bbh_category'])) { 

		update_option('woo_bbh_category', sanitize_text_field($_POST['woo_bbh_category']));
		

		
	}

	if (isset($_POST['woo-bbh-datepicker-2'])) { 

		update_option('woo-bbh-datepicker-2', sanitize_text_field($_POST['woo-bbh-datepicker-2']));
	   

		$type = 'updated';
        $message = __('Value Updated', 'woo-booking-bundle-hours');
        add_settings_error('woo-booking-bundle-hours', esc_attr('settings_updated'), $message, $type);
		settings_errors('woo-booking-bundle-hours');
		
		
	}


	
	include_once( plugin_dir_path( __FILE__ ) . '../inc/setup_file.php' );






}