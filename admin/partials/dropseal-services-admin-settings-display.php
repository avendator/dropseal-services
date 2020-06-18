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

if( isset( $_POST["save_dss_settings"] ) ) save_dss_settings();

$blocked_numbers = get_option( 'dss_blocked_numbers' );
$blocked_emails  = get_option( 'dss_blocked_emails' );
$blocked_domains = get_option( 'dss_blocked_domains' );

?>

<div class="wrap">
    <form method="post">
        <div class="dss_page_header">
            <div class="dss_title">Settings</div>&emsp;
            <input type="submit" name="save_dss_settings" class="button-primary" value="Save settings">
        </div><hr><br>

        <div class="dss_sub_menu_title">Limitations</div><br>
        <div class="dss_settings_container limitations_container">
            <div>
                <label>
                    <input type="checkbox" name="dss_auto_confirm" value="1" <?php if ( get_option( 'dss_auto_confirm' ) ) echo 'checked'; ?>>
                    <span>Auto confirm</span>
                </label>    
            </div>
            <div>
                <label>
                    <input type="checkbox" name="dss_special_request" value="1" <?php if ( get_option( 'dss_special_request' ) ) echo 'checked'; ?>>
                    <span>Special request</span>
                </label> 
            </div>
        </div><br><hr>
        <div class="dss_settings_container limitations_container">     
            <div>
                <p>Max. pending messages:</p>
                <input type="text" name="dss_max_pending_messages" value="<?= get_option( 'dss_max_pending_messages' ) ?>">
            </div>
            <div>
                <p>Qty paid messages:</p>
                <input type="text" name="dss_qty_paid_messages" value="<?= get_option( 'dss_qty_paid_messages' ) ?>">
            </div>
            <div>
                <p>Upload file size (MB):</p>
                <input type="text" name="dss_upload_file_size" value="<?= get_option( 'dss_upload_file_size' ) ?>">
            </div>
            <div>
                <p>Reminder period (hours):</p>
                <input type="text" name="dss_reminder_period"  value="<?= get_option( 'dss_reminder_period' ) ?>">
            </div>
            <div>
                <p>Log out period (min):</p>
                <input type="text" name="dss_log_out_period" value="<?= get_option( 'dss_log_out_period' ) ?>">
            </div>
               
        </div><br><hr><br>

        <div class="dss_sub_menu_title">Payments</div>
        <div class="dss_settings_container payments_container">     
            <div>
                <p>Excess SMS:</p>
                <input type="text" name="dss_sms_cost" value="<?= get_option( 'dss_sms_cost' ) ?>">
            </div>
            <div>
                <p>No mobile carrier:</p>
                <input type="text" name="dss_mobile_carrier_cost" value="<?= get_option( 'dss_mobile_carrier_cost' ) ?>">
            </div>
            <div>
                <p>Transfer percentage:</p>
                <input type="text" name="dss_transfer_percentage" value="<?= get_option( 'dss_transfer_percentage' ) ?>">
            </div>    
        </div>
    </form><br><hr><br>
   
    <form method="post">
        <div class="dss_sub_menu_title">Blocking</div><br>
        <div class="dss_settings_container blocking_container">     
            <div>
                <input type="text" name="numbers" placeholder="Phone" required>           
                <span class="button-primary dss_add_lock" data-type="numbers">Add</span>
                <div class="dss_lock_exists">Already exists</div>  
            </div>
            <div>
                <input type="text" name="emails" placeholder="Email" required>
                <span class="button-primary dss_add_lock" data-type="emails">Add</span>
                <div class="dss_lock_exists">Already exists</div> 
            </div>
            <div>
                <input type="text" name="domains" placeholder="Domain" required>
                <span class="button-primary dss_add_lock" data-type="domains">Add</span>
                <div class="dss_lock_exists">Already exists</div> 
            </div>          
        </div>
    
        <div class="dss_settings_container blocking_container">
            <div class="dss_blocked_numbers">
                <div>Numbers<hr></div>

                <?php if ( $blocked_numbers ): ?>
                    <?php show_locked_items( $blocked_numbers, 'numbers' )?>
                <?php endif; ?>

            </div>
            <div class="dss_blocked_emails">
                <div>Emails<hr></div>

                <?php if ( $blocked_emails ): ?>
                    <?php show_locked_items( $blocked_emails, 'emails' )?>
                <?php endif; ?>

            </div>
            <div class="dss_blocked_domains">
                <div>Domains<hr></div>

                <?php if ( $blocked_domains ): ?>
                    <?php show_locked_items( $blocked_domains, 'domains' )?>
                <?php endif; ?>  

            </div>     
        </div>    
    </form>
</div><br><hr>

<?php

/**
 * Save settings
 */
function save_dss_settings() {
    $option_data = [
        'dss_auto_confirm',
        'dss_special_request',

        'dss_max_pending_messages',
        'dss_qty_paid_messages',
        'dss_upload_file_size',
        'dss_reminder_period',
        'dss_log_out_period',

        'dss_mobile_carrier_cost',
        'dss_sms_cost',
        'dss_transfer_percentage',
    ];

    foreach ( $option_data as $data ) {
        update_option( $data, $_POST[$data] );
    }
}

/**
 * Show locked items
 */
function show_locked_items( $items, $key ) {
    foreach ( $items as $item ) {                          
        echo '<div>
            <span class="dss_btn dss_delete_button dss_delete_lock" data-type="'.$key.'" data-val="'.$item.'" title="Delete">&#10007;</span> 
            <span>'.$item.'</span>
        </div>';
    }
}