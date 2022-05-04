<?php
/**
 * Plugin Name: SMS Notification
 * Plugin URI: https://github.com/shid94
 * Author: Rashid Migadde
 * Author URI: https://github.com/shid94
 * Description: Sends out SMS on Order Status Woocommerce
 * Version: 0.0.1
 * License: 0.0.1
 * License URL: http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: mpenzi-sms-notification
*/
add_action('woocommerce_order_status_change', 'mapenzi_send_sms_on_new_order_status',10,4);

add_action('woocommerce_new_customer_note_notification', 'mapenzi_send_sms_on_new_order_notes', 10, 1);

function mapenzi_send_sms_on_new_order_status ($order_id, $old_status, $new_status, $order ){
    //get the order object
    $my_order = wc_get_order($order_id);
    $first_name = $my_order->get_billing_firstname();//firstname
    $phone = $my_order->get_billing_phone();//phone
    $shop_name = get_option('woocommerce_email_from_name');//website name
    $default_sms_message = "Thank you $first_name. Your Order #$order_id is $new_status";
    mapenzi_send_sms_to_customer($phone,$default_sms_message,$shop_name);
}

function mapenzi_send_sms_on_new_order_notes($email_args){
    $order =wc_get_order($email_args['order_id']);
    $note = $email_args['customer_note'];
    $phone = $order->get_billing_phone();//phone
    $shop_name = get_option('woocommerce_email_from_name');//website name

    mapenzi_send_sms_to_customer($phone,$note,$shop_name);

}
function mapenzi_send_sms_to_customer($phone = 'NULL',$default_sms_message,$shop_name){
    if('NULL' === $phone){
        return;

    }
    $msgdata = array(
		'method' => 'SendSms',
		'userdata' => array(
			'username' => 'Username',
			'password' => 'password',
		),
		'msgdata' => array(
			array(
				'number' => $phone,
				'message' => $default_sms_message,
				'senderid' => $shopname,
			)
		)
	);
	
	

	$arguments = array(
		'method' => 'POST',
		'body' => json_encode( $msgdata ),
	);
    $url = 'http://sms.sukumasms.com/api/v1/json/';

	$response = wp_remote_post( $url, $arguments );
	
	if ( is_wp_error( $response ) ) {
		$error_message = $response->get_error_message();
		return "Something went wrong: $error_message";
	}

}

?>