<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://upsite.top/
 * @since      1.0.0
 *
 * @package    Dropseal_Services
 * @subpackage Dropseal_Services/admin/partials
 */
?>

<?php 

global $wpdb;

if( isset( $_POST["delete_selected_crs_orders"] ) ) Dropseal_Services_Admin::delete_selected_orders( 'crs' );
$orders = $wpdb->get_results( "SELECT * FROM dss_crs_data WHERE status != 'trash' AND status != 'on_hold' AND hide_to_admin IS NULL ORDER BY id DESC" ); 

?>  

<div class="wrap">
    <form method="post">
        <div class="dss_page_header">
            <div class="dss_title">CRS orders</div>
        </div><hr><br>
        <?php if ( $orders ) : ?>     
            <div class="dss_orders_container">
                <button type="sabmit" name="delete_selected_crs_orders" class="dss_delete_selected_orders">Delete selected</button>
                <input type="text" class="dss_orders_filter" placeholder="Search">
                <table rules="rows">
                    <thead>
                        <tr>
                            <td><input type="checkbox" class="dss_select_all_checked"></td>
                            <td>#</td>
                            <td>Name</td>
                            <td>Phone</td>
                            <td>Email</td>
                            <td>Date</td>
                            <td>Status</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach ( $orders as $order ) : ?> 
                            <?php $user = get_userdata( $order->customer_id ); ?>           
                            <tr>
                                <td><input type="checkbox" name="dss_checked_orders[]" value="<?= $order->id ?>"></td>
                                <td><?= $order->id ?></td>
                                <td><h4><?= $user->display_name ?></h4></td>
                                <td><?= get_user_meta( $user->ID, 'dss_phone', true ) ?></td>
                                <td><?= $user->user_email ?></td>
                                <td>
                                    <div><?= date( 'M j, Y', $order->process_date ); ?><div>
                                    <div><?= date( 'g:i A', $order->process_date ); ?><div>
                                </td>
                                <td><?=$order->status?></td>              
                                <td><a href="<?= admin_url( 'admin.php?page=order-info&crs='.$order->id ); ?>" class="dss_btn dss_info_button" title="Info">&#128712;</a></td>     
                                <td><a href="<?= admin_url( 'admin.php?page=crs-edit-order&crs-id='.$order->id ); ?>" class="dss_btn dss_edit_button" title="Edit">&#9998;</a></td> 
                                <td><div class="dss_btn dss_delete_button dss_delete_order" data-crs-id="<?= $order->id ?>" title="Delete">&#10007;</div></td>                                           
                            </tr>
                        <?php endforeach; ?>   

                    </tbody>
                </table>
            </div><br><hr>
        <?php endif; ?>   

    </form>
</div>