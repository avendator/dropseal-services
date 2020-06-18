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

if ( isset( $_GET['sms'] ) || isset( $_GET['crs'] ) ) {
    $service = $_GET['sms'] ? 'sms' : 'crs';
}

if ( $service ) :
    global $wpdb;
    $order_id   = $_GET[$service];
    $data       = Dropseal_Services_Admin::get_data( $order_id, $service );
    $meta       = Dropseal_Services_Admin::get_meta( $order_id, $service );
    $userdata   = get_userdata( $data->customer_id );
    $last_login = get_user_meta( $data->customer_id, 'wp-last-login', true )
  
    ?>

    <div class="wrap">
        <div class="dss_page_header">
            <div class="dss_title">Info about <?= strtoupper( $service ) ?> order #<?= $order_id ?></div>
        </div><hr>

        <div class="dss_sub_menu_title">Customer</div><br>
        <div class="dss_settings_container dss_info_container">
            <div>
                <div>Name:</div>               
                <input type="text" value="<?= $userdata->display_name ?>" disabled>
            </div>
            <div>
                <div>Email:</div>           
                <input type="text" value="<?= $userdata->user_email ?>" disabled>
            </div>
            <div>
                <div>Phone:</div>
                <input type="text" value="<?= get_user_meta( $data->customer_id, 'dss_phone', true ) ?>" disabled>
            </div>
            <div>
                <div>Mobile_carrier:</div>
                <input type="text" value="<?= get_user_meta( $data->customer_id, 'dss_mobile_carrier', true ) ?>" disabled>
            </div>
            <div>
                <div>Last authorization:</div>
                <input type="text" value="<?php if( $last_login ) echo date( 'M j, Y', $last_login ) ?>" disabled>
            </div>
        </div><br><hr>

        <div class="dss_sub_menu_title">Order</div><br>
        <div class="dss_settings_container dss_info_container">
            <div>
                <div>Status:</div>
                <input type="text" value="<?= $data->status ?>" disabled>
            </div>
            <div>
                <div>Date:</div>
                <input type="text" value="<?= date( 'M j, Y, g:i A', $data->process_date ) ?>" disabled>
            </div>

            <?php if ( $service == 'crs' ) : ?>
                <div>
                    <div>Order price:</div>
                    <input type="text" value="<?= $meta['order_price'] ?>" disabled>
                </div>
            <?php endif; ?>

            <?php if ( $service == 'sms' ) : ?>
                <div>
                    <div>Money amount:</div>
                    <input type="text" value="<?= $meta['money_amount'] ?>" disabled>
                </div>
                <div>
                    <div>Payment email:</div>
                    <input type="text" value="<?= $meta['payment_email'] ?>" disabled>
                </div>
                <div>
                    <div>Order price:</div>
                    <input type="text" value="<?= $meta['total_paid'] ?>" disabled>
                </div>
            <?php endif; ?>

        </div><br><hr>

        <?php if ( $meta['error_sending_message'] ) echo '<p class="dss_sending_error"><span>Error sending message:</span> '.$meta['error_sending_message'].'</p>'?> 
         
        <div class="dss_order_info_container">
            <textarea disabled><?= $meta['content'] ?></textarea>           
            <div>
			
				<?php if ( $service == 'sms' ) : ?>
					<div>
						<span>Reminder: </span>
						<input type="checkbox" <?php if ( $data->reminder ) echo 'checked'; ?> disabled>
					</div><br>
				<?php endif; ?>
				
                <div>File:</div> 
  
                <?php if ( $meta['file_path'] ) : ?>
                    <a href="<?= plugins_url( strstr( $meta['file_path'], 'dropseal-services') ) ?>" target="_blank"><?= basename( $meta['file_path'] ) ?></a><br>                      
                <?php endif; ?><br>  
          
                <div>URLs:</div>

                <?php if ( $meta['links'] ) : ?>
                
                    <?php foreach ( $meta['links'] as $k ) : ?>                                             
                        <a href="<?= $k ?>" target="_blank"><?= $k ?></a><br> 
                    <?php endforeach; ?>

                <?php endif; ?>            
            </div>
            <div>

                <?php if ( $meta['paypal_id'] ) : ?>
                    <div>PayPal payment order ID: <?= $meta['paypal_id'] ?></div>
                <?php endif; ?>
                
                <?php if ( $meta['apple_pay_id'] ) : ?>
                    <div>Apple Pay payment order ID: <?= $meta['applepay_id'] ?></div>
                <?php endif; ?>   

            </div>
        </div><hr>

        <?php if ( $service == 'sms' ) : ?>
            <div class="dss_sub_menu_title">Recipients</div><br>
            
            <?php for ( $i = 1; $i <= count( (array)$meta['recipients'] ); $i++ ) : ?>
                <div class="dss_settings_container dss_info_container"> 
                    <div>
                        <div>Name:</div>               
                        <input type="text" value="<?= $meta['recipients']->{'recipient_'.$i}->name ?>" disabled>
                    </div>
                    <div>
                        <div>Email:</div>               
                        <input type="text" value="<?= $meta['recipients']->{'recipient_'.$i}->email ?>" disabled>
                    </div>
                    <div>
                        <div>Phone:</div>               
                        <input type="text" value="<?= $meta['recipients']->{'recipient_'.$i}->phone ?>" disabled>
                    </div>
                </div><br>
            <?php endfor; ?><hr>   

        <?php endif; ?>  
        
    </div>
   
<?php endif;