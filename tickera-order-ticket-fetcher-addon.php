<?php
/**
 * Plugin Name: Tickera Order Ticket Fetcher Addon
 * Plugin URI: https://github.com/distool-de/Tickera-Order-Ticket-Fetcher-Addon/
 * Description: A Tickera addon for querying ticket instances based on order information.
 * Author: Distool.de
 * Author URI: https://github.com/distool-de/
 * Version: 2.0.0
 * Text Domain: tcotf
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

// Autoload or require necessary class files.
require_once plugin_dir_path( __FILE__ ) . 'includes/classes/class.wo-pos-ticket-template.php';


// Register hook to fetch ticket instances via URL query.
add_action( 'init', 'tcotf_fetch_ticket_instances_from_order' );

/**
 * Fetches ticket instances based on the order ID and key.
 *
 * @return void
 */
function tcotf_fetch_ticket_instances_from_order() {
    if ( ! isset( $_GET['order_key'], $_GET['order_id'] ) ) {
        return;
    }

    $order_key = sanitize_text_field( wp_unslash( $_GET['order_key'] ) );
    $order_id  = intval( $_GET['order_id'] );

    if ( empty( $order_id ) || empty( $order_key ) ) {
        return;
    }

    if ( ! class_exists( 'TC_Order' ) ) {
        return;
    }

    $order            = new TC_Order( $order_id );
    $order_status     = $order->details->post_status ?? '';
    $order_date       = strtotime( $order->details->post_date ?? '' );
    $order_modified   = strtotime( $order->details->post_modified ?? '' );
    $tc_order_date    = $order->details->tc_order_date ?? '';
    $alt_paid_date    = $order->details->_tc_paid_date ?? '';

    $valid_statuses = apply_filters( 'tc_validate_downloadable_ticket_order_status', [ 'order_paid' ], $order );

    if ( ! in_array( $order_status, $valid_statuses, true ) ) {
        wp_send_json( [ 'status' => 401, 'message' => 'Invalid order status.', 'data' => [] ] );
    }

    if (
        $order_key !== (string) $order_date &&
        $order_key !== (string) $order_modified &&
        $order_key !== (string) $tc_order_date &&
        $order_key !== (string) $alt_paid_date
    ) {
        wp_send_json( [ 'status' => 401, 'message' => 'Invalid order key.', 'data' => [] ] );
    }

    $ticket_instances = get_posts(
        [
            'post_type'   => 'tc_tickets_instances',
            'post_status' => 'any',
            'post_parent' => $order_id,
            'numberposts' => -1,
        ]
    );

    $response = [];

    foreach ( $ticket_instances as $ticket_instance ) {
        $ticket_id         = $ticket_instance->ID;
        $ticket_type_id    = get_post_meta( $ticket_id, 'ticket_type_id', true );
        $ticket_template   = get_post_meta( $ticket_type_id, '_ticket_pos_template_id', true );

        $response[] = [
            'ticket_id'          => $ticket_id,
            'ticket_template_POS'=> $ticket_template,
            'hash'               => wp_hash( $ticket_id . $order_key ),
        ];
    }

    wp_send_json(
        [
            'status' => 200,
            'message' => 'Ticket instances retrieved successfully.',
            'data'   => $response,
        ]
    );
}