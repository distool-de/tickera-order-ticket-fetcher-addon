<?php
/**
 *
 * WooCommerce Tickera Bridge Extend
 * Adds a POS ticket template field to WooCommerce products of type Ticket.
 *
 * @package WooCommerce_Tickera_Bridge_Extend
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce_Tickera_Bridge_Extend' ) ) {

    class WooCommerce_Tickera_Bridge_Extend {


        /**
         * Constructor
         */
        public function __construct() {
            add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_pos_ticket_template_field' ) );
            add_action( 'woocommerce_process_product_meta', array( $this, 'save_pos_ticket_template_field' ) );
        }

        /**
         * Adds the POS ticket template select field to the product data panel.
         */
        public function add_pos_ticket_template_field() {
            global $post;

            $value = get_post_meta( $post->ID, '_ticket_pos_template_id', true );
            if( empty( $value ) ) $value = '';

            echo '<div class="options_group">';
            
            woocommerce_wp_select(
                array(
                    'id'          => '_ticket_pos_template_id',
                    'label'       => esc_html__( 'POS Ticket Template', 'woocommerce-tickera-bridge-extend' ),
                    'options'     => $this->get_ticket_templates_array(),
                    'desc_tip'    => true,
                    'description' => esc_html__( 'Select how the POS ticket will look.', 'woocommerce-tickera-bridge-extend' ),
                    'value'      => $value,
                )
            );

            echo '</div>';
        }

        /**
         * Saves the POS ticket template field value.
         *
         * @param int $post_id The ID of the product.
         * 
         */
        public function save_pos_ticket_template_field($post_id) {
            print_r($POST);
            print($post_id);
            $woocommerce_ticket_pos_template_id = $_POST['_ticket_pos_template_id'];
            if( !empty( $woocommerce_ticket_pos_template_id ) )
                update_post_meta( $post_id, '_ticket_pos_template_id', esc_attr( $woocommerce_ticket_pos_template_id ) );
            else {
                update_post_meta( $post_id, '_ticket_pos_template_id',  '' );
            }
        }

        /**
         * Retrieves available POS ticket templates.
         *
         * @return array List of ticket templates.
         */
        public function get_ticket_templates_array() {
            $templates = [];

            $args = [
                'post_type'      => 'tc_templates',
                'post_status'    => 'publish',
                'orderby'        => 'title',
                'numberposts'    => -1,
                'order'          => 'ASC',
            ];

            $templates[''] = __( 'Select a value', 'woocommerce');

            $tc_templates = get_posts( $args );
            foreach ($tc_templates as $key => $value) {
                $templates[$value->ID] = $value->post_title;
            }
            return $templates;
        }
    }

    // Initialize the class.
    new WooCommerce_Tickera_Bridge_Extend();
}
