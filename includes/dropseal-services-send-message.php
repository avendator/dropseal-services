<?php
global $wpdb;

$orders = $wpdb->get_results( "SELECT id, process_date, reminder FROM dss_sms_data WHERE status = 'processing'" ); 

$current_time = current_time( 'timestamp' );
$status = 'completed';


if( $orders ) {
    foreach ( $orders as $order ) {
        if( $order->reminder ) {

            //reminder
            $reminder = get_option( 'dss_reminder_period' ) * 60 * 60;

            if ( $order->process_date - $reminder < $current_time ) {
                wp_mail( 
                    get_user_meta( $order->customer_id, 'dss_phone', true ) . get_user_meta( $order->customer_id, 'dss_mobile_carrier', true ), 
                    'Dropseal services', 
                    Dropseal_Services_Admin::replace_template_placeholders( get_option( 'dss_template_reminder' ), $order->id, 'sms' ) 
                );
            }
            
            //sms
            if( $order->process_date < $current_time ) {
                $meta = Dropseal_Services_Admin::get_meta( $order->id, 'sms' );   
        
                foreach ( $meta['recipients'] as $recipient ) {

                    if ( $recipient->mobile_carrier ) {
                        wp_mail( 
                            $recipient->phone . $recipient->mobile_carrier, 
                            'Dropseal services', 
                            Dropseal_Services_Admin::replace_template_placeholders( get_option( 'dss_template_sms' ), $order->id, 'sms' ) 
                        );
                    } else {

                        //sending Twilio, if not a mobile carrier
                        $send_message = Dropseal_Services_Admin::send_message_Twilio( $recipient->phone, get_option( 'dss_template_sms' ), $order->id );

                        //if Twilio error
                        if( $send_message['Error'] )  {
                            $wpdb->insert( 'dss_sms_meta', ['sms_id' => $order->id, 'meta_key' => 'error_sending_message', 'meta_value' => $send_message['Error'] );

                            //send email to customer
                            wp_mail( 
                                get_userdata( $data->customer_id )->user_email, 
                                'Dropseal services', 
                                Dropseal_Services_Admin::replace_template_placeholders( get_option( 'dss_template_cancelled' ), $order->id, 'sms' ) 
                            );

                            //send email to admin
                            wp_mail( 
                                get_option( 'admin_email' ), 
                                'Dropseal services', 
                                Dropseal_Services_Admin::replace_template_placeholders( get_option( 'dss_template_cancelled' ), $order->id, 'sms' ) 
                            );

                            $status = 'cancelled';
                        }
                    } 

                    if ( $recipient->email ) {
                        wp_mail( 
                            $recipient->email, 
                            'Dropseal services', 
                            Dropseal_Services_Admin::replace_template_placeholders( get_option( 'dss_template_sms' ), $order->id, 'sms' ) 
                        );
                    }
                }
                Dropseal_Services_Admin::change_status( $order->id, 'sms', $status );     
            }
        }
    }
}
