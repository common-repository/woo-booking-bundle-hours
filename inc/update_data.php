<?php


// Don't access this directly, please

if ( ! defined( 'ABSPATH' ) ) exit;

function woo_booking_bundle_hours_update_data() {


	global $wpdb;
    $table_name = $wpdb->prefix . 'bookingbundlehours';
    
   

	if (isset($_POST['order_id']) && wp_verify_nonce( $_POST['_wpnonce'] )) {

        $bbh_order_number = intval($_POST['order_id']);
    
		$wpdb->update($table_name,
			array('date_reserved'=>$_POST['woo-datetime-bbh-reserve-'.$bbh_order_number.'']),
			array('orderid'=>$bbh_order_number));

			

			$bbh_url_to_nonce_pre = '?updated=true'.$bbh_order_number;

			$bbh_url_to_nonce = wp_nonce_url($bbh_url_to_nonce_pre,'bbh_update_reservation','_bbhnonce');

            //wp_redirect( get_permalink() . $bbh_url_to_nonce_pre );

	}

	if (isset($_POST['hours_reserved'], $_POST['orderid'])  && wp_verify_nonce( $_POST['_wpnonce'] )) {
		
		$bbh_hours_reserved = intval($_POST['hours_reserved']);
		$bbh_order_id = intval($_POST['orderid']);

		$wpdb->update($table_name,
			array('hours_reserved'=>$bbh_hours_reserved),
			array('orderid'=>$bbh_order_id));


	}

}

?>
