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

if ( isset( $_GET['sms-id'] ) ) :
    global $wpdb;
    $sms_id = $_GET['sms-id'];

    if( isset( $_POST["dss_update_sms_order"] ) ) echo dss_update_sms_order( $sms_id );

    $data            = Dropseal_Services_Admin::get_data( $sms_id, 'sms' );
    $meta            = Dropseal_Services_Admin::get_meta( $sms_id, 'sms' );
    $qty_recipients  = 3;
    $users           = get_users( ['role' => 'subscriber', 'fields' => ['ID', 'display_name']] );
    $mobile_carriers = json_decode( get_option( 'dss_mobile_carriers' ) );
    $statuses        = Dropseal_Services_Admin::get_statuses();
    
    ?>

    <div class="wrap">
        <form method="post" enctype="multipart/form-data">
            <div class="dss_page_header">
                <div class="dss_title dss_edit_title">Edit SMS order #<?= $sms_id ?></div>
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
                <input type="submit" name="dss_update_sms_order" class="button-primary" value="Update order">
            </div><hr><br>
            <div class="dss_order_container" data-qty-recipients="<?= $qty_recipients ?>" data-editable-qty-recipients="<?= count( (array)$meta['recipients'] ) ?>">  
                <textarea name="content" placeholder="Message" required><?= $meta['content'] ?></textarea>
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
                                <span class="dss_delete_file_btn dss_delete_file" title="Delete file" data-sms-id ="<?= $sms_id ?>">&#10007;</span>
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
                        <input type="datetime-local" name="date" value="<?= date( 'Y-m-d\TH:i', $data->process_date ) ?>" required>
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

                <?php for ( $i = 1; $i <= count( (array)$meta['recipients'] ); $i++ ) : ?> 

                    <div class="dss_recipient_wrapper">
                        <div class="dss_recipient_header">            
                            
                            <?php if ( $i == 1 ) : ?>      
                                <div>
                                    <span>Recipient</span>
                                    <span class="dss_recipient_btn dss_add_recipient" title="Add recipient">+</span>
                                </div>
                                <div>
                                    <span>Reminder</span>
                                    <input type="checkbox" name="reminder" value="1" <?php if ( $data->reminder ) echo 'checked'; ?>>
                                </div>
                            <?php else: ?> 
                                <div>
									<span>Recipient</span>
									<span class="dss_recipient_btn dss_delete_recipient" title="Delete recipient">-</span>
                                </div>                                
                            <?php endif; ?> 

                        </div>    
                        <div class="dss_order_wrapper">
                            <div>
                                <input type="text" name="recipient_name_<?= $i ?>" value="<?= $meta['recipients']->{'recipient_'.$i}->name ?>" required>
                            </div>
                            <div>
                                <input type="email" name="recipient_email_<?= $i ?>" value="<?= $meta['recipients']->{'recipient_'.$i}->email ?>">
                            </div>
                        </div>
                        <div class="dss_order_wrapper">
                            <div>
                                <input type="text" name="recipient_phone_<?= $i ?>" value="<?= $meta['recipients']->{'recipient_'.$i}->phone ?>" required>
                            </div>
                            <div>
                                <select name="recipient_mobile_carrier_<?= $i ?>">
                                    <option value="">Mobile carrier no choice</option>

                                    <?php foreach ( $mobile_carriers as $mobile_carrier ) : ?> 

                                        <?php if ( $mobile_carrier->checked ) : ?> 
                                            <option value="<?= $mobile_carrier->sms ?>" 
                                            
                                                <?php if ( $meta['recipients']->{'recipient_'.$i}->mobile_carrier == $mobile_carrier->sms ) echo 'selected'; ?>
                                            
                                            ><?= $mobile_carrier->name ?></option>
                                        <?php endif; ?> 

                                    <?php endforeach; ?> 

                                </select>
                            </div>
                        </div>
                    </div>
                
                <?php endfor; ?> 

            </div>
        </form>
    </div><br><hr>

<?php endif;

//Update order
function dss_update_sms_order( $id ) {
    global $wpdb;

    $qty_recipients = 3;
    $write_error    = false;
    $status         = $_POST['status'];

    //get data
    $new_order_data = [
        'process_date' => strtotime( $_POST['date'] ),
        'status'       => $status,
        'reminder'     => $_POST['reminder'] ?? 0,
    ];

    //get meta
    $meta = [
        'content' => $_POST['content'],
        'links'   => array_filter( $_POST['links'] ) ? $_POST['links'] : '',
    ];

    for ( $i = 1; $i <= $qty_recipients; $i++ ) {        
        if ( isset( $_POST['recipient_name_'.$i] ) ) {
            $meta['recipients']['recipient_'.$i] = [
                'name'           => $_POST['recipient_name_'.$i],
                'email'          => $_POST['recipient_email_'.$i] ?? '',
                'phone'          => $_POST['recipient_phone_'.$i],
                'mobile_carrier' => $_POST['recipient_mobile_carrier_'.$i] ?? '',
            ];
        }
    }

    //file upload
    if ( $file_path = Dropseal_Services_Admin::file_upload( $_FILES ) ) {      
        $meta['file_path'] = $file_path;
    }

    //update data in db
    $res = $wpdb->update( 'dss_sms_data', $new_order_data, ['id' => $id] );

    if ( $res === false ) $write_error = true;

    //update meta in db
    foreach ( $meta as $k => $v ) {
        $res = $wpdb->update( 'dss_sms_meta', ['meta_value' => is_array( $v ) ? json_encode( $v ) : $v], ['sms_id' => $id, 'meta_key' => $k] );

        if ( $res === false ) $write_error = true;
    }



    if( !$write_error ) {

        if ( $status == 'cancelled' ) {
            $template = get_option( 'dss_template_cancelled' );
        } else {
            $template = get_option( 'dss_template_success' );
        }

        Dropseal_Services_Admin::send_message( $template, $order_id, 'sms' );

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