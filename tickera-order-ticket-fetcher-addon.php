<?php

 /**
 * Plugin Name: Tickera Order Ticket Fetcher Addon
 * Plugin URI: https://github.com/distool-de/Tickera-Order-Ticket-Fetcher-Addon/
 * Description: A Tickera addon for querying ticket instances based on order information.
 * Author: Distool.de
 * Author URI: https://github.com/distool-de/
 * Version: 0.8.6
 * Text Domain: tcotf
 * Requires at least: 6.5.2
 * Requires PHP: 8.2.18
 * Update URI: https://github.com/distool-de/Tickera-Order-Ticket-Fetcher-Addon/
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// Exit if accessed directly

// Add a function to init hook
add_action('init', 'get_ticket_instances_from_orderid');

function get_ticket_instances_from_orderid() {
    // Get the variables from the URL

    $order_key = ( isset( $_GET['order_key'] ) ? sanitize_key( $_GET['order_key'] ) : '' );
    $order_id = ( isset( $_GET['order_id'] ) ? (int) $_GET['order_id'] : '' );

    if ( !empty($order_id) && !empty($order_key)) 
    {
        $order = new TC_Order( $order_id );
        $order_status = $order->details->post_status;
        $order_date = strtotime( $order->details->post_date );
        $order_modified = strtotime( $order->details->post_modified );
        $tc_order_date = $order->details->tc_order_date;
        $alt_paid_date = $order->details->_tc_paid_date;
        $valid_order_statuses = apply_filters( 'tc_validate_downloadable_ticket_order_status', [ 'order_paid' ], $order );
        if ( in_array( $order_status, $valid_order_statuses ) ) {
            
            if ( $order_key == $order_date || $order_key == $order_modified || $order_key == $tc_order_date || $alt_paid_date == $order_key ) {
                $ticket_instances = get_posts( [
                    'post_type'      => 'tc_tickets_instances',
                    'post_status'    => 'any',
                    'post_parent'    => $order_id,
                ] );

                $response = array();

                $response[] = array(
                    'status' => '200',
                    'IDs' => $ticket_instances
                );

                wp_send_json($response);
            } else {
                $response = array();

                $response[] = array(
                    'status' => '401'
                );
                wp_send_json($response);
            }
        
        } else {
            $response = array();

            $response[] = array(
                'status' => '401'
            );
            wp_send_json($response);
        }
    }
}
