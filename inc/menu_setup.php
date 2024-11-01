<?php

/**
 *
 * @ Better separated voice to check quicker what every single value in add_menu_page is
 *
 */function woo_booking_bundle_hours_setup_menu()
{
    $parent_slug = 'woocommerce';
    $page_title  = 'WooCommerce Booking Bundle Hours Admin Page';
    $menu_title  = 'Booking Bundle Hours';
    $capability  = 'manage_options';
    $menu_slug   = 'woo_booking_bundle_hours';
    $function    = 'woo_booking_bundle_hours_setup_page_display';

    add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
}


/**
* Include the new Navigation Bar the Admin page.
*/

function add_woo_booking_bundle_to_woocommerce_navigation_bar() {

    if ( is_admin() ) {

    if ( function_exists( 'wc_admin_connect_page' ) ) {

        wc_admin_connect_page(
            
                        array(
					        'id'        => 'woo_booking_bundle_hours',
					        'screen_id' => 'woocommerce_page_woo_booking_bundle_hours',
                            'title'     => __( 'Booking Bundle Hours', 'woo_booking_bundle_hours' ),
           
                            'path'      => add_query_arg(
                                            array(
                                                'page' => 'woo_booking_bundle_hours',
                                                //'tab'  => 'ordine',
                                            ),
                                            
                                            'admin.php' ),
                            
	        			)
        );
        
    }

    }
}
