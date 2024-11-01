<?php

// Don't access this directly, please

if (!defined('ABSPATH')) exit;

?>

<div class="wrap">
<h1>
    <?php



    $plugin_data = get_plugin_data(plugin_dir_path(__FILE__) .'../woo-booking-bundle-hours.php', true, true);
    $plugin_version = $plugin_data['Version'];

    if ( is_admin() ) {
        echo __(
            'WooCommerce Booking Bundle Hours '
            .$plugin_version, 'woo-booking-bundle-hours'
        );

    }
    ?>
</h1>


<?php

global $wpdb;

$table_name = $wpdb->prefix . 'bookingbundlehours';

$query = $wpdb->get_results("SELECT * FROM $table_name ORDER BY orderid DESC");

?>
<table border="0" cellpadding="10">

    <tr bgcolor="#d3d3d3">
        <td><strong><?php echo __('Order', 'woo-booking-bundle-hours') ?></strong></td>
        <td><strong><?php echo __('Product', 'woo-booking-bundle-hours') ?></strong></td>
        <td><strong><?php echo __('Customer', 'woo-booking-bundle-hours') ?></strong></td>
        <td><strong><?php echo __('Price', 'woo-booking-bundle-hours') ?></strong></td>
        <td><strong><?php echo __('Hours', 'woo-booking-bundle-hours') ?></strong></td>
        <td><strong><?php echo __('Hours Used', 'woo-booking-bundle-hours') ?></strong></td>
        <td><strong><?php echo __('Date Reserved', 'woo-booking-bundle-hours') ?></strong></td>
        <td><strong><?php echo __('Hours Reserved', 'woo-booking-bundle-hours') ?></strong></td>
        <td><strong><?php echo __('Approved', 'woo-booking-bundle-hours') ?></strong></td>
        <td><strong><?php echo __('Save', 'woo-booking-bundle-hours') ?></strong></td>
        <td><strong><?php echo __('Completed', 'woo-booking-bundle-hours') ?></strong></td>
        <td><strong><?php echo __('Remove Reservation', 'woo-booking-bundle-hours') ?></strong></td>
        <td><strong><?php echo __('Delete', 'woo-booking-bundle-hours') ?></strong></td>
        
    </tr>

    <?php


    foreach ($query as $item) {


        ?>


        <tr bgcolor='#f5fffa'>
            <td align="center"><?php echo $item->orderid; ?></td>
            <td><?php echo $item->product ?></td>
            <td><?php echo $item->customer ?></td>
            <td align="center"><?php echo $item->price ?></td>
            <td align="center"><?php echo $item->hours ?></td>
            <td class="<?php


                         if (($item->date_reserved == '0000-00-00 00:00:00') && (0 == $item->date_reserved_approved)) {

                            echo 'reserv-empty';
                        } elseif (($item->date_reserved !== '0000-00-00 00:00:00') && (0 == $item->date_reserved_approved)) {

                            echo 'reserv-wait';
                        } elseif (1 == $item->date_reserved_approved) {

                            echo 'reserv-conf';
                        }
                        
            
            ?>">

                <form id="hours_used" method="POST">
                    <input type="hidden" name="orderid" id="orderid" value="<?php echo $item->orderid; ?>" />
                    <?php wp_nonce_field() ?>

                    <label for="hours_hours_used">
                        <?php echo  __('Hours Used', 'woo-booking-bundle-hours'); ?></label>

                    <input type="number" max="<?php echo $item->hours ?>" min=0 name="hours_used" placeholder="" value="<?php echo $item->hours_used; ?>">


            </td>

            <td class="<?php

                        if (($item->date_reserved == '0000-00-00 00:00:00') && (0 == $item->date_reserved_approved)) {

                            echo 'reserv-empty';
                        } elseif (($item->date_reserved !== '0000-00-00 00:00:00') && (0 == $item->date_reserved_approved)) {

                            echo 'reserv-wait';
                        } elseif (1 == $item->date_reserved_approved) {

                            echo 'reserv-conf';
                        }



                        ?>"><?php echo $item->date_reserved ?></td>
            
            <td class="<?php

if (($item->date_reserved == '0000-00-00 00:00:00') && (0 == $item->date_reserved_approved)) {

    echo 'reserv-empty';
} elseif (($item->date_reserved !== '0000-00-00 00:00:00') && (0 == $item->date_reserved_approved)) {

    echo 'reserv-wait';
} elseif (1 == $item->date_reserved_approved) {

    echo 'reserv-conf';
}



?>" >
            
            <?php echo $item->hours_reserved; ?>
            <input type="hidden" name="hours_reserved" value=<?php echo $item->hours_reserved ?> /> 
        
        
            </td>

            
            
            <td align="center"><?php $appornotapp = $item->date_reserved_approved ?>

                <input type="hidden" name="date_reserved_approved" value="0" />
                <input type="checkbox" name="date_reserved_approved" id="date_reserved_approved" value="1"
                <?php if (1 == $appornotapp) echo 'checked';
                else echo '';
                ?>>


            </td>




            <td>

                <input type="submit" value="<?php echo  __('Save', 'woo-booking-bundle-hours'); ?>" class="button button-primary button-large">

                </form>

            </td>

            <td>

                <?php

                if ($item->hours_used == $item->hours) {

                    echo __('Completed!', 'woo-booking-bundle-hours');
                }

                ?>
            </td>

            </td>

            <!-- ############################################################ -->

            <td>

            <form id="remove_reservation_bbh" method="POST" onsubmit="return confirm('<?php 
            echo  __('Are you sure you want to delete this reservation?', 'woo-booking-bundle-hours'); ?>');">

            <input type="hidden" name="orderid" id="orderid" value="<?php echo $item->orderid; ?>" />

            <input type="submit" value="<?php echo  __('Remove', 'woo-booking-bundle-hours'); ?>" 
            name="remove_reservation_bbh" class="button button-primary button-large">

            </form>

            <!-- ############################################################ -->


            </td>

            <td>
                <!-- ############################################################ --> 




                <form id="delete_row_bbh" method="POST" onsubmit="return confirm('<?php
                
                 echo  __('Are you sure you want to delete all the data of '.$item->product.'?', 'woo-booking-bundle-hours'); ?>');">

                    <input type="hidden" name="orderid" id="orderid" value="<?php echo $item->orderid; ?>" />

                    <input type="submit" value="<?php echo  __('Delete', 'woo-booking-bundle-hours'); ?>" 
                    name="delete_row_bbh" class="button button-primary button-large">

                </form>

                <!-- ############################################################ -->






        </tr>



    <?php

    }
    ?>

</table>

<table>

<tr>
    <td width="20">
    <div class="bbh-square-wait">&nbsp;</div>
    </td>
    <td><?php echo  __('Date confirmed', 'woo-booking-bundle-hours'); ?></td>

    <td width="20">
    <div class="bbh-square-ok">&nbsp;</div>
    </td>
<td>
<?php echo  __('Date reserved waiting to be confirmed', 'woo-booking-bundle-hours'); ?>
</td>
    <td width="20">
    <div class="bbh-square-no">&nbsp;</div>
    </td>
<td>
<?php echo  __('Date not reserved', 'woo-booking-bundle-hours'); ?>
</td>

</tr>

</table>
 
<hr>

<table border="0" width="800" cellspacing="6">

    <tr>
        <td bgcolor="#006799" align="center" colspan="4">

            <span style="color:white"><?php echo  __('SETTINGS for Import data from orders', 'woo-booking-bundle-hours'); ?></span>

        </td>
    </tr>
    <tr>
        <td>
        <?php echo  __('Where the product has this category:<br>', 'woo-booking-bundle-hours'); ?>
                <form method="POST">

                <?php wp_nonce_field() ?>

                <input type="text" id="woo_bbh_category" class="woo_bbh_category" name="woo_bbh_category" value="
<?php print get_option('woo_bbh_category'); ?>
" size="20">

        </td>


        <td bgcolor="#F7F7F7" align="center" width="33,33%">


            <label for="woo_bbh_max_orders_number"><?php echo  __('Max number of Orders', 'woo-booking-bundle-hours'); ?></label>

            <input type="number" max="" min=0 name="woo_bbh_max_orders_number" placeholder="0" size="3" 
            value="<?php echo get_option('woo_bbh_max_orders_number'); ?>">



        </td>


        <td bgcolor="#F7F7F7" align="center" width="33,33%">


            <p>
                <label for="woo-bbh-datepicker">
                    <?php echo  __('Select orders date begin', 'woo-booking-bundle-hours') ?>
                </label>

                <input type="text" id="woo-bbh-datepicker" class="woo-bbh-datepicker" name="woo-bbh-datepicker" value="
                <?php print get_option('woo-bbh-datepicker'); ?>" >

            </p>



        </td>

        <td bgcolor="#F7F7F7" align="center" width="33,33%">



            <form method="POST">

                <p>
                    <label for="woo-bbh-datepicker-2">
                        <?php echo __('Select orders date end', 'woo-booking-bundle-hours') ?>
                    </label>

                    <input type="text" id="woo-bbh-datepicker-2" class="woo-bbh-datepicker-2" name="woo-bbh-datepicker-2" value="
                    <?php print get_option('woo-bbh-datepicker-2') ?>">

                </p>


        </td>
    </tr>

    <tr >
        <td bgcolor="#F7F7F7" colspan="4" align="center">

            <button type="submit" name="bbh date orders range" value="Seleziona" class="button button-primary">
                <?php echo __('Save') ?>
            </button>

        </td>
    </tr>

    </form>


    </td>
    </tr>

</table>


<hr>
<form id="import_from_orders" method="POST">

    <input type="hidden" name="import_from_orders" id="import_from_orders">

    <input type="submit" value="<?php echo  __('Import Data from Orders', 'woo-booking-bundle-hours'); ?>" 
    class="button button-primary button-large">
</form>

</div>