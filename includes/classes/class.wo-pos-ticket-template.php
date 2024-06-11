<?php
if ( ! class_exists( 'WooCommerce_Tickera_Bridge_Extend' ) ) {

    class WooCommerce_Tickera_Bridge_Extend {

        public function __construct() {
            add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_pos_ticket_template_field' ) );
            add_action( 'woocommerce_process_product_meta', array( $this, 'save_pos_ticket_template_field' ) );
        }

        public function add_pos_ticket_template_field() {
            echo  '<div class="options_group show_if_tc_ticket">' ;
            woocommerce_wp_select( [
                'id'          => '_ticket_pos_template',
                'label'       => __( 'POS Ticket Template', 'woocommerce-tickera-bridge-extend' ),
                'options'     => $this->get_ticket_templates_array(),
                'desc_tip'    => 'true',
                'description' => __( 'Select how the POS ticket will look.', 'woocommerce-tickera-bridge-extend' ),
            ] );
            echo  '<div class="options_group"></div>' ;
        }

        public function save_pos_ticket_template_field( $post_id ) {

            $post_id = (int) $post_id;
            // Check if product is a ticket
            $_tc_is_ticket = ( isset( $_POST['_tc_is_ticket'] ) ? 'yes' : 'no' );
            
            if ( 'yes' == $_tc_is_ticket ) {
                // Save choosen ticket template
                $ticket_pos_template = isset( $_POST['_ticket_pos_template'] ) ? sanitize_text_field( $_POST['_ticket_pos_template'] ) : '';
                update_post_meta( $post_id, '_ticket_pos_template', $ticket_pos_template );
            } else {
                delete_post_meta( $post_id, '_ticket_pos_template' );
            }
        }

        private function get_ticket_templates_array() {
            // Assuming tc_get_ticket_templates_array is a function that returns an array of ticket templates
            if ( function_exists( 'tc_get_ticket_templates_array' ) ) {
                return tc_get_ticket_templates_array();
            } else {
                return array(); // Return an empty array if the function does not exist
            }
        }
    }
}
