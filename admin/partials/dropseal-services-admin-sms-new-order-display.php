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

if( isset( $_POST["dss_save_new_sms_order"] ) ) echo dss_save_new_sms_order();

$qty_recipients = 3;
$users = get_users( ['role' => 'subscriber', 'fields' => ['ID', 'display_name']] );
$mobile_carriers = json_decode( get_option( 'dss_mobile_carriers' ) );
$statuses = Dropseal_Services_Admin::get_statuses();

?>  

<div class="wrap">
    <form method="post" enctype="multipart/form-data">
        <div class="dss_page_header">
            <div class="dss_title dss_edit_title">New SMS order</div>
            <div class="dss_order_status">
                <div>Status</div>
                <select name="status">                    

                    <?php foreach ( $statuses as $status ) : ?> 
                        <option value="<?= $status ?>"><?= $status ?></option>
                    <?php endforeach; ?> 

                </select>
            </div>
            <input type="submit" name="dss_save_new_sms_order" class="button-primary" value="Add order">
        </div><hr><br>
        <div class="dss_order_container" data-qty-recipients="<?= $qty_recipients ?>">  
            <textarea name="content" placeholder="Message" required></textarea>
            <div class="dss_order_wrapper">
                <div>
                    <label class="dss_upload_file" data-file-size="<?= get_option( 'dss_upload_file_size' ) ?>">
                        <input type="file" name="file">  
                        <img src="<?= plugins_url( 'dropseal-services/public/img/clip.svg' ) ?>">
                        <span>Upload File</span>
                    </label>    
                    <span class="dss_upload_file_name"></span>
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
                    <select name="user" required>
                        <option selected hidden value="">User</option>

                        <?php foreach ( $users as $user ) : ?> 
                            <option value="<?=$user->ID?>"><?= $user->display_name ?></option>
                        <?php endforeach; ?> 

                    </select>
                </div>
                <div>
                    <input type="datetime-local" name="date" required>
                </div>    
            </div>

            <!-- Modal Box -->
            <div class="dss_links_modal">
                <div class="dss_links_modal_content">
                    <span class="dss_links_modal_close">&#10007;</span>
                    <div class="dss_links_modal_title">Add website or file by URL</div>                  
                    <div class="dss_links_modal_container">
                        <div class="dss_links_modal_wrapper">
                            <input type="url" name="links[]" placeholder=" URL">
                            <span class="dss_links_modal_btn dss_add_link_modal" title="Add more url">+</span>
                        </div>
                    </div> 
                    <div class="dss_links_modal_confirm">Ok</div>
                </div>
            </div>
            <!-- end Modal Box -->

            <div class="dss_recipient_wrapper">
                <div class="dss_recipient_header">            
                    <div>
                        <span>Recipient</span>
                        <span class="dss_recipient_btn dss_add_recipient" title="Add recipient">+</span>
                    </div>                
                    <div>
                        <span>Reminder</span>
                        <input type="checkbox" name="reminder" value="1">
                    </div>
                </div>    
                <div class="dss_order_wrapper">
                    <div>
                        <input type="text" name="recipient_name_1" placeholder="Name" required>
                    </div>
                    <div>
                        <input type="email" name="recipient_email_1" placeholder="Email">
                    </div>
                </div>
                <div class="dss_order_wrapper">
                    <div>
                        <input type="text" name="recipient_phone_1" placeholder="Phone" required>
                    </div>
                    <div>
                        <select name="recipient_mobile_carrier_1">
                            <option value="">Mobile carrier no choice</option>

                            <?php foreach ( $mobile_carriers as $mobile_carrier ) : ?> 

                                <?php if ( $mobile_carrier->checked ) : ?> 
                                    <option value="<?= $mobile_carrier->sms ?>"><?= $mobile_carrier->name ?></option>
                                <?php endif; ?> 

                            <?php endforeach; ?> 

                        </select>
                    </div>
                </div>
            </div>    
        </div>
    </form>
</div><br><hr>

<?php

//Save new order in db
function dss_save_new_sms_order() {
    global $wpdb;

    $qty_recipients = 3;
    $write_error    = false;
    $user_id        = $_POST['user'];
    $status         = $_POST['status'];

    //get data
    $new_order_data = [
        'customer_id'  => $user_id,
        'process_date' => strtotime( $_POST['date'] ),
        'status'       => $status,
        'reminder'     => $_POST['reminder'] ?? 0,
    ];

    //get meta
    $meta = [
        'content'   => $_POST['content'],
        'links'     => array_filter( $_POST['links'] ) ? $_POST['links'] : '',
        'file_path' => Dropseal_Services_Admin::file_upload( $_FILES ) ?: '',
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

    //add data in db
    $wpdb->insert( 'dss_sms_data', $new_order_data );

    if ( !$order_id = $wpdb->insert_id ) $write_error = true;

    //add link id
    $wpdb->update( 'dss_sms_data', ['link' => wp_hash_password( $order_id )], ['id' => $order_id] );

    //add meta in db
    foreach ( $meta as $k => $v ) {
        $new_order_meta = [
            'sms_id'     => $order_id,
            'meta_key'   => $k,
            'meta_value' => is_array( $v ) ? json_encode( $v ) : $v,
        ];
        $wpdb->insert( 'dss_sms_meta', $new_order_meta );

        if ( !$wpdb->insert_id ) $write_error = true;
    }

    if( !$write_error ) {

        if ( $status == 'cancelled' ) {
            $template = get_option( 'dss_template_cancelled' );
        } else {
            $template = get_option( 'dss_template_success' );
        }
        
        Dropseal_Services_Admin::send_message( $template, $order_id, 'sms' );

        $notice = '<div class="notice notice-success is-dismissible">
            <p>Order #'.$order_id.' successfully created!</p>
        </div><br>';
    } else {
        $notice = '<div class="notice notice-error is-dismissible">
            <p>Error creating order!</p>
        </div><br>';
    }
    return $notice;
}