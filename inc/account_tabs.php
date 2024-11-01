<?php

if (!defined('ABSPATH')) {
    exit;
}


/**
 * Endpoint HTML content.
 */
function woo_booking_bundle_hours_content()
{

    global $current_user;
    wp_get_current_user();


    echo __('Hello ', 'woo-booking-bundle-hours'). $current_user->user_email . "!";

    ###########################################

    $account_email = $current_user->user_email;

    global $wpdb;

    $table_name = $wpdb->prefix . 'bookingbundlehours';

    $where = "WHERE customer = '$account_email'";

    $query = $wpdb->get_results("

SELECT * FROM $table_name
$where");

    if ($wpdb->num_rows <= 0) {

        echo "<p>"
        . __('Here you could find a <b>Bundle Hours</b> product if you buy it in the shop<br><br>
        When the order will be completed will be displayed:<br>
        <br>- the total amount of hours
        <br>- the hours used
        <br> and the reservation date to use them 
        <hr>
        elapsed some time from booking, if you don\'t see the bundle please check if you used the correct email of your account
        
        ', 'woo-booking-bundle-hours') . 
        "</p>";

        return;
    } else {

        ?>
        <table border="0" cellpadding="6" class="table-myaccount-page">

            <?php

            foreach ($query as $item) {

                ?>
                <tr>

                <th class="table-myaccount-page"><strong><?php echo __('Order', 'woo-booking-bundle-hours') ?></strong></th>
                <th class="table-myaccount-page"><strong><?php echo __('Course', 'woo-booking-bundle-hours') ?></strong></th>
                <th class="table-myaccount-page"><strong><?php echo __('Hours', 'woo-booking-bundle-hours') ?></strong></th>
                <th class="table-myaccount-page" colspan="2"><strong><?php echo __('Hours Used', 'woo-booking-bundle-hours') ?></strong></th>

                </tr>

                <tr>

                    <td class="bbh-course-row"><?php echo $item->orderid; ?></td>
                    <td class="bbh-course-row"><?php echo $item->product; ?></td>
                    <td class="bbh-course-row"><?php echo $item->hours; ?></td>
                    <td class="bbh-course-row"><?php echo $item->hours_used;

                                            if ($item->hours_used == $item->hours) {
                                                echo __(' --> Completed!', 'woo-booking-bundle-hours');
                                            }
                                            ?>
                    </td>


                </tr>

        

                <tr>
                    <td class="bbh-center" colspan="4">

                        <form method="POST">

                            <?php wp_nonce_field() ?>

                            <label for="datetime-bbh-reserve">
                                <?php echo __('Reserve date and time', 'woo-booking-bundle-hours') ?>
                            </label>

                            <input type="text" id="woo-datetime-bbh-reserve-<?php echo $item->orderid; ?>" class="woo-datetime-bbh-reserve" name="woo-datetime-bbh-reserve-<?php echo $item->orderid; ?>" value="<?php echo $item->date_reserved; ?>" size="18">
                            <input id="order_id" name="order_id" type="hidden" value="<?php echo $item->orderid; ?>">

                            <?php wp_nonce_field() ?>

                            <input type="hidden" name="orderid" id="orderid" value="<?php echo $item->orderid; ?>" /> 

                            <label for="hours_hours_reserved">
                            <?php 
                            
                            if (1 == $item->date_reserved_approved) {
                            echo  __('Hours Reserved', 'woo-booking-bundle-hours'); 
                            
                            } else {

                            echo  __('Hours to reserve', 'woo-booking-bundle-hours');

                            }                             
                            ?></label>
                            <input type="number" max="<?php echo ($item->hours - $item->hours_used) ?>" min=0 name="hours_reserved" id="hours_reserved" placeholder="" value="<?php echo $item->hours_reserved; ?>">
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="bbh-center">
                    <button type="submit" name="" value="Seleziona" class="table-myaccount-page" <?php if ((1 == $item->date_reserved_approved) || ($item->hours_used == $item->hours)) { echo 'disabled ';} ?>>
                                <?php echo __('Save', 'woo-booking-bundle-hours') ?>
                            </button>
                
                </td>
                
                </tr>


                <tr>
                    <td class="<?php

                                if (1 == $item->date_reserved_approved) {

                                    echo 'reserv-conf';
                                } elseif (($item->date_reserved == '0000-00-00 00:00:00') && (0 == $item->date_reserved_approved)) {

                                    echo 'reserv-empty';
                                }

                                else {

                                    echo 'reserv-wait';

                                }
                                ?>" colspan="4">

                    <?php
                        if (isset($_GET['updated'])) {

                            if ($_GET['updated'] == 'true' . $item->orderid) {


                                ?> <p style="color: green"><?php echo __('Reserve date request has been updated', 'woo-booking-bundle-hours') ?></p>

                            <?php
                        }


                        $bbh_url = get_site_url()."/my-account/woo-booking-bundle-hours/";
                        echo "<script type='text/javascript'>
                        setTimeout(function(){location.href='".$bbh_url."'} , 3000);  
                        </script>";


                    }
                    ?>

                        <strong>
                            <?php if ($item->hours_used == $item->hours) {
                                echo __('Reservation date Approved! <br>Bundle Hours Completed!', 'woo-booking-bundle-hours');
                            } elseif (1 == $item->date_reserved_approved) {

                                echo __('Reservation date Approved!', 'woo-booking-bundle-hours');
                            } elseif (($item->date_reserved == '0000-00-00 00:00:00') && (0 == $item->date_reserved_approved)) {

                                echo __('No reservation date', 'woo-booking-bundle-hours');
                            }

                            else {

                                echo __('Reservation date to be approved', 'woo-booking-bundle-hours');
                            }


                            ?>
                        </strong>
                        


                                 

                        </form>

                    </td>
                </tr>

                <tr>
                    <td class="bbh-separator" colspan="4">&nbsp;<hr class="bbh-separator"></td>
                </tr>

            <?php
        }

        ?>

        </table>










    <?php


}
}

############################################################


/*
 * Change endpoint TITLE
 *
 * @param string $title
 * @return string
 */
function woo_booking_bundle_hours_endpoint_title($title)
{
    global $wp_query;

    $is_endpoint = isset($wp_query->query_vars['woo-booking-bundle-hours']);

    if ($is_endpoint && !is_admin() && is_main_query() && in_the_loop() && is_account_page()) {
        // New page title.
        $title = __('Booking Bundle Hours', 'woocommerce');

        remove_filter('the_title', 'woo_booking_bundle_hours_endpoint_title');
    }

    return $title;
}





/**
 * Insert the new endpoint into the My Account menu.
 *
 * @param array $items
 * @return array
 */
function woo_booking_bundle_hours_menu_items($items)
{
    $new_items = array();
    $new_items['woo-booking-bundle-hours'] = __('Bundle Hours', 'woocommerce');

    // Add the new item after `orders`.
    return woo_booking_bundle_hours_insert_after_helper($items, $new_items, 'orders');
}

/**
 * Custom help to add new items into an array after a selected item.
 *
 * @param array $items
 * @param array $new_items
 * @param string $after
 * @return array
 */
function woo_booking_bundle_hours_insert_after_helper($items, $new_items, $after)
{
    // Search for the item position and +1 since is after the selected item key.
    $position = array_search($after, array_keys($items)) + 1;

    // Insert the new item.
    $array = array_slice($items, 0, $position, true);
    $array += $new_items;
    $array += array_slice($items, $position, count($items) - $position, true);

    return $array;
}

/**
 * Flush rewrite rules on plugin activation.
 */
function woo_booking_bundle_hours_flush_rewrite_rules()
{
    add_rewrite_endpoint('woo-booking-bundle-hours', EP_ROOT | EP_PAGES);
    flush_rewrite_rules();
}

/**
 * Add new query var.
 *
 * @param array $vars
 * @return array
 */
function woo_booking_bundle_hours_query_vars($vars)
{
    $vars[] = 'woo-booking-bundle-hours';

    return $vars;
}

/**
 * Register new endpoint to use inside My Account page.
 *
 * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
 */
function woo_booking_bundle_hours_endpoints()
{
    add_rewrite_endpoint('woo-booking-bundle-hours', EP_ROOT | EP_PAGES);
}