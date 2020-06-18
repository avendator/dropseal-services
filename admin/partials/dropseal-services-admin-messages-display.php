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

if( isset( $_POST["save_dss_messages"] ) ) save_dss_messages();

$template_placeholders = Dropseal_Services_Admin::get_template_placeholders();

?>

<div class="wrap">
    <form method="post">
        <div class="dss_page_header">
            <div class="dss_title">Messages</div>&emsp;
            <input type="submit" name="save_dss_messages" class="button-primary" value="Save settings">
        </div><hr><br>
        
        <div class="dss_sub_menu_title">Warning</div>
        <div class="dss_settings_container messages_container">     
            <div>
                <p>Mobile carrier tooltip:</p>
                <textarea name="dss_mobile_carrier_tooltip"><?= get_option( 'dss_mobile_carrier_tooltip' ) ?></textarea>       
            </div>
            <div>
                <p>Excess pending messages:</p>
                <textarea name="dss_excess_pending_messages"><?= get_option( 'dss_excess_pending_messages' ) ?></textarea>       
            </div>
            <div>
                <p>Ban on editing:</p>
                <textarea name="dss_ban_on_editing"><?= get_option( 'dss_ban_on_editing' ) ?></textarea>       
            </div>
            <div>
                <p>Cancellation:</p>
                <textarea name="dss_cancellation_warning"><?= get_option( 'dss_cancellation_warning' ) ?></textarea>       
            </div>         
        </div><br><hr><br>

        <div class="dss_sub_menu_title">Templates</div>
        <div class="dss_settings_container messages_container">     
            <div>
                <p>Welcome:</p>
                <textarea name="dss_template_welcome"><?= get_option( 'dss_template_welcome' ) ?></textarea>          
            </div>
            <div>
                <p>Successfully:</p>
                <textarea name="dss_template_success"><?= get_option( 'dss_template_success' ) ?></textarea>   
            </div>          
        </div><br>
        <div class="dss_settings_container messages_container">     
            <div>
                <p>SMS:</p>
                <textarea name="dss_template_sms"><?= get_option( 'dss_template_sms' ) ?></textarea>   
            </div>  
            <div>
                <p>Reminder:</p>
                <textarea name="dss_template_reminder"><?= get_option( 'dss_template_reminder' ) ?></textarea>   
            </div>  
            <div>
                <p>Cancelled:</p>
                <textarea name="dss_template_cancelled"><?= get_option( 'dss_template_cancelled' ) ?></textarea>   
            </div>         
        </div><br>
        <div>
            <b>Available placeholders:</b>

            <?php foreach ( $template_placeholders as $template_placeholder ) : ?> 
                &emsp;<span><?= $template_placeholder ?></span>
            <?php endforeach; ?> 
       
        </div><br><hr>    
    </form>
</div>

<?php

function save_dss_messages() {
    $option_data = [
        'dss_mobile_carrier_tooltip',
        'dss_excess_pending_messages',
        'dss_ban_on_editing',
        'dss_cancellation_warning',

        'dss_template_welcome',

        'dss_template_success',
        'dss_template_sms',
        'dss_template_reminder',
        'dss_template_cancelled',
    ];

    foreach ( $option_data as $data ) {
        update_option( $data, $_POST[$data] );
    } 
}