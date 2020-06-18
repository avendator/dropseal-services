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

if( isset( $_POST['twilio_settings_save'] ) ) twilio_settings_save();

if( isset( $_POST['send_sms_message'] ) ) {
    $send_message = Dropseal_Services_Admin::send_message_Twilio( $_POST['phone'], $_POST['message'] );
    
    if( $send_message['Error'] ){
        echo '<div class="notice notice-error is-dismissible">
            <p>'.$send_message['Error'].'</p>
        </div><br>';
    }  
}

?>

<div class="wrap">
    <form method="post">
        <div class="dss_page_header">
            <div class="dss_title">Twilio settings</div>&emsp;
            <input type="submit" name="twilio_settings_save" class="button-primary" value="Save settings">
        </div><hr> 
        <div class="dss_settings_container twilio_container">  
            <div>
                <p>API SID:</p>
                <input type="text" name="dss_twilio_api_sid"  value="<?= get_option( 'dss_twilio_api_sid' ) ?>">
            </div>
            <div>
                <p>API TOKEN:</p>
                <input type="text" name="dss_twilio_api_token" value="<?= get_option( 'dss_twilio_api_token' )?>">
            </div>
            <div>
                <p>Sender phone:</p>
                <input type="text" name="dss_twilio_api_phone" value="<?= get_option( 'dss_twilio_api_phone' ) ?>">
            </div>
        </div><br><hr><br>
    </form>

    <form method="post">
        <div class="dss_sub_menu_title">Send SMS</div><br>     
        <div class="twilio_container">
            <div><input type="text" name="phone" placeholder="Phone"></div><br>
            <div><textarea name="message" placeholder="Message"></textarea></div><br>
            <div><input class="button-primary" type="submit" value="Send message" name="send_sms_message"></div>
        </div><br><hr>
    </form>
</div> 

<?php     

function twilio_settings_save() {
    update_option( 'dss_twilio_api_sid', $_POST['dss_twilio_api_sid'] );
    update_option( 'dss_twilio_api_token', $_POST['dss_twilio_api_token'] );
    update_option( 'dss_twilio_api_phone', $_POST['dss_twilio_api_phone'] );
}

?>