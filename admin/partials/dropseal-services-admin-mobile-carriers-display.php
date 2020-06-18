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

if( isset( $_POST["add_mobile_carrier"] ) ) add_mobile_carrier();
if( isset( $_POST["delete_mobile_carrier"] ) ) delete_mobile_carrier();  
if( isset( $_POST["save_mobile_carriers_settings"] ) ) save_mobile_carriers_settings(); 

?>

<div class="wrap">
    <div class="dss_page_header">
        <div class="dss_title">Mobile Carriers settings</div>&emsp;
        <input type="submit" name="save_mobile_carriers_settings" class="button-primary" value="Save settings">
    </div><hr><br>

    <form method="post">
        <input type="text" name="new_mobile_carrier_name" placeholder="Name">
        <input type="text" name="new_mobile_carrier_sms" placeholder="SMS">
        <input type="text" name="new_mobile_carrier_mms" placeholder="MMS">
        <input type="submit" name="add_mobile_carrier" class="button-primary" value="Add"> 
    </form><br><br>

    <?php if ( $mobile_carriers = json_decode( get_option( 'dss_mobile_carriers' ) ) ): ?>

        <form method="post">
            <table class="mobile_carriers_container" frame="above" rules="rows">
                <tr>
                    <td></td>
                    <td>Name</td>
                    <td>SMS</td>
                    <td>MMS</td>
                    <td>Del.</td>
                </tr>

                <?php foreach ( $mobile_carriers as $mobile_carrier ) : ?>           
                    <tr>
                        <td><input type="checkbox" name="checked_mobile_carriers[]" value="<?=$mobile_carrier->name?>" <?=$mobile_carrier->checked?>></td>
                        <td><h4><?=$mobile_carrier->name?>:</h4></td>
                        <td><?=$mobile_carrier->sms?></td>
                        <td><?=$mobile_carrier->mms?></td>
                        <td><button type="submit" name="delete_mobile_carrier" value="<?=$mobile_carrier->name?>" class="dss_btn dss_delete_button">&#10007;</button></td>                                           
                    </tr>
                <?php endforeach; ?>

            </table>     
        </form><br><hr>

    <?php endif; ?>
</div> 

<?php     
/**
 * Add mobile carrier
 */
function add_mobile_carrier() {
    $mobile_carriers = json_decode( get_option( 'dss_mobile_carriers' ), true );
    $mobile_carriers[] = [
        'name' => $_POST['new_mobile_carrier_name'],
        'sms'  => $_POST['new_mobile_carrier_sms'],
        'mms'  => $_POST['new_mobile_carrier_mms'],
        'checked' => 'checked'
    ];
    update_option( 'dss_mobile_carriers', json_encode( $mobile_carriers ) );
}

/**
 * Delete mobile carrier
 */
function delete_mobile_carrier() {
    $mobile_carriers = json_decode( get_option( 'dss_mobile_carriers' ) );

    foreach ( $mobile_carriers as $k => $v ) {

        if ( $_POST['delete_mobile_carrier'] == $v->name ) unset( $mobile_carriers[$k] );     
    }
    update_option( 'dss_mobile_carriers', json_encode( $mobile_carriers ) );
}

/**
 * Save mobile carriers settings
 */
function save_mobile_carriers_settings() {
    $mobile_carriers = json_decode( get_option( 'dss_mobile_carriers' ) );

    if( $mobile_carriers ) {

        foreach ( $mobile_carriers as $mobile_carrier ) {

            if ( in_array( $mobile_carrier->name , $_POST['checked_mobile_carriers'] ) ) {
                $mobile_carrier->checked = 'checked';
            } else {
                $mobile_carrier->checked = '';
            }
        }
        update_option( 'dss_mobile_carriers', json_encode( $mobile_carriers ) );
    }
}