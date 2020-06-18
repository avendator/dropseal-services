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

if ( isset( $_GET['crs-id'] ) ) :
    global $wpdb;
    $crs_id = $_GET['crs-id'];

    if( isset( $_POST["dss_update_crs_order"] ) ) echo dss_update_crs_order( $crs_id );

    $data = Dropseal_Services_Admin::get_data( $crs_id, 'crs' );
    $meta = Dropseal_Services_Admin::get_meta( $crs_id, 'crs' );
    $statuses = Dropseal_Services_Admin::get_statuses();
    
    ?>

    <div class="wrap">
        <form method="post" enctype="multipart/form-data">
            <div class="dss_page_header">
                <div class="dss_title dss_edit_title">Edit CRS order #<?= $crs_id ?></div>
                <div class="dss_order_status">
                    <div>Status</div>
                    <select name="status">                    

                        <?php foreach ( $statuses as $status ) : ?> 
                            <option value="<?= $status ?>"
                            
                                <?php if ( $data->status == $status ) echo 'selected'; ?>
                            
                            ><?= $status ?></option>
                        <?php endforeach; ?> 

                    </select>
                </div>
                <div class="dss_order_price"> 
                    <div>Price</div>
                    <input type="text" name="order_price" value="<?= $meta['order_price'] ?>">
                </div>            
                <input type="submit" name="dss_update_crs_order" class="button-primary" value="Save order">
            </div><hr><br>
            <div class="dss_order_container">  
                <textarea name="content" placeholder="Message" disabled><?= $meta['content'] ?></textarea>
                <div class="dss_order_wrapper">
                    <div>
                        <label class="dss_upload_file" data-file-size="<?= get_option( 'dss_upload_file_size' ) ?>">
                            <input type="file" name="file">  
                            <img src="<?= plugins_url( 'dropseal-services/public/img/clip.svg' ) ?>">
                            <span>Upload File</span>
                        </label>
                        <span class="dss_upload_file_name">

                            <?php if ( $meta['file_path'] ) : ?>
                                <a href="<?= plugins_url( strstr( $meta['file_path'], 'dropseal-services') ) ?>" target="_blank"><?= basename( $meta['file_path'] ) ?></a>
                                <span class="dss_delete_file_btn dss_delete_file" title="Delete file" data-crs-id ="<?= $crs_id ?>">&#10007;</span>
                            <?php endif; ?>   

                        </span>              
                    </div>
                    <div>
                        <label class="dss_add_link">
                            <img src="<?= plugins_url( 'dropseal-services/public/img/link.svg' ) ?>">
                            <span>Add link</span>
                        </label>
                    </div>
                </div>
                <div class="dss_order_wrapper">
                    <div>
                        <input type="text" name="user" value="<?= get_userdata( $data->customer_id )->display_name ?>" disabled>
                    </div>
                    <div>
                        <input type="datetime-local" name="date" value="<?= date( 'Y-m-d\TH:i', $data->process_date ) ?>" disabled>
                    </div>    
                </div>

                <!-- Modal Box -->
                <div class="dss_links_modal">
                    <div class="dss_links_modal_content">
                        <span class="dss_links_modal_close">&#10007;</span>
                        <div class="dss_links_modal_title">Add website or file by URL</div>
                            <div class="dss_links_modal_container">

                                <?php foreach ( $meta['links'] as $k => $v ) : ?> 
                                    <div class="dss_links_modal_wrapper">
                                        <input type="url" name="links[]" value="<?= $v ?>">

                                        <?php if ( !$k ) : ?>
                                            <span class="dss_links_modal_btn dss_add_link_modal" title="Add more url">+</span>
                                        <?php else: ?> 
                                            <span class="dss_links_modal_btn dss_delete_link_modal" title="Delete url">-</span>
                                        <?php endif; ?>   

                                    </div>
                                <?php endforeach; ?>

                            </div>  
                        <div class="dss_links_modal_confirm">Ok</div>
                    </div>
                </div>
                <!-- end Modal Box -->
        
                <div class="dss_order_wrapper">
                    <div>
                        <input type="text" name="phone" value="<?= get_user_meta( $data->customer_id, 'dss_phone', true ) ?>" disabled>
                    </div>
                    <div>
                        <input type="email" name="email" value="<?= get_userdata( $data->customer_id )->user_email ?>" disabled>
                    </div>
                </div>
            </div>
        </form>
    </div><br><hr>

<?php endif;

//Update order
function dss_update_crs_order( $id ) {
    global $wpdb;

    $write_error = false;
    $status      = $_POST['status'];

    //get data
    $new_order_data = [
        'status' => $status,
    ];

    //get meta
    $meta = [
        'links'       => array_filter( $_POST['links'] ) ? $_POST['links'] : '',
        'order_price' => $_POST['order_price'] ?? '',
    ];

    //file upload
    if ( $file_path = Dropseal_Services_Admin::file_upload( $_FILES ) ) {      
        $meta['file_path'] = $file_path;
    }

    //update data in db
    $res = $wpdb->update( 'dss_crs_data', $new_order_data, ['id' => $id] );

    if ( $res === false ) $write_error = true;

    //update meta in db
    foreach ( $meta as $k => $v ) {
        $res = $wpdb->update( 'dss_crs_meta', ['meta_value' => is_array( $v ) ? json_encode( $v ) : $v], ['crs_id' => $id, 'meta_key' => $k] );

        if ( $res === false ) $write_error = true;
    }

    if( !$write_error ) {

        if ( $status == 'cancelled' ) {
            $template = get_option( 'dss_template_cancelled' );
        } else {
            $template = get_option( 'dss_template_success' );
        }

        Dropseal_Services_Admin::send_message( $template, $order_id, 'crs' );

        $notice = '<div class="notice notice-success is-dismissible">
            <p>Order #'.$id.' has been successfully updated!</p>
        </div><br>';

    } else {
        $notice = '<div class="notice notice-error is-dismissible">
            <p>Error updating order!</p>
        </div><br>';
    }
    return $notice;
}