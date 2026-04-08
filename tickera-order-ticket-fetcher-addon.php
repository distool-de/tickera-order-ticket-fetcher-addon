<?php
/**
 * Plugin Name: Tickera Order Ticket Fetcher Addon
 * Plugin URI: https://github.com/distool-de/Tickera-Order-Ticket-Fetcher-Addon/
 * Description: A Tickera addon for querying ticket instances based on order information and POS Tempalte Select in an Woocommerce Product.
 * Author: Distool.de
 * Author URI: https://github.com/distool-de/
 * Version: 0.9.2
 * Text Domain: tcotf
 * Requires at least: 6.8.1
 * Requires PHP: 8.0
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

// Autoload or require necessary class files.
require_once plugin_dir_path( __FILE__ ) . 'includes/classes/class.wo-pos-ticket-template.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/classes/class.tc-pos-ticket-template.php';


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

    $order = wc_get_order( $order_id );

    if ( ! $order ) {
        wp_send_json( [ 'status' => 401, 'message' => 'Invalid order ID.', 'data' => [] ] );
        exit;
    }

    if ( $order->get_status() !== 'completed' ) {
        wp_send_json( [ 'status' => 401, 'message' => 'Invalid order status.', 'data' => [] ] );
        exit;
    }

    if ( $order->get_order_key() !== $order_key ) {
        wp_send_json( [ 'status' => 401, 'message' => 'Invalid order key.', 'data' => [] ] );
        exit;
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