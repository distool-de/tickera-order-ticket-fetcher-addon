<?php
/**
 * Plugin Name: Tickera Order Ticket Fetcher Addon
 * Plugin URI: https://github.com/distool-de/Tickera-Order-Ticket-Fetcher-Addon/
 * Description: A Tickera addon for querying ticket instances based on order information.
 * Author: Distool.de
 * Author URI: https://github.com/distool-de/
 * Version: 0.8.8
 * Text Domain: tcotf
 * Update URI: https://github.com/distool-de/Tickera-Order-Ticket-Fetcher-Addon/
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// Exit if accessed directly

// Add Updater
include_once( plugin_dir_path( __FILE__ ) . 'includes/classes/class.updater.php' );
// Include the POS Ticket Template class
require_once plugin_dir_path(__FILE__) . 'includes/classes/class.tc-pos-ticket-template.php';

$updater = new tcotf_Updater( __FILE__ );
$updater->set_username( 'distool-de' );
$updater->set_repository( 'Tickera-Order-Ticket-Fetcher-Addon' );
$updater->initialize();

// Add a function to init hook
add_action('init', 'get_ticket_instances_from_orderid');

function get_ticket_instances_from_orderid() {
    // Get the variables from the URL
    $order_key = ( isset( $_GET['order_key'] ) ? sanitize_key( $_GET['order_key'] ) : '' );
    $order_id = ( isset( $_GET['order_id'] ) ? (int) $_GET['order_id'] : '' );

    if ( !empty($order_id) && !empty($order_key)) {
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
                    'post_type'   => 'tc_tickets_instances',
                    'post_status' => 'any',
                    'post_parent' => $order_id,
                ] );

                $response = array();

                foreach ( $ticket_instances as $ticket_instance ) {
                    $ticket_id = $ticket_instance->ID;
                    $ticket_type_id = get_post_meta( $ticket_id, 'ticket_type_id', true );
                    $ticket_template_POS = get_post_meta( $ticket_type_id, 'ticket_template_POS', true );

                    $response[] = array(
                        'ticket_id' => $ticket_id,
                        'ticket_template_POS' => $ticket_template_POS,
                    );
                }

                wp_send_json( array( 'status' => '200', 'data' => $response ) );
            } else {
                wp_send_json( array( 'status' => '401' ) );
            }
        } else {
            wp_send_json( array( 'status' => '401' ) );
        }
    }
}
?>
