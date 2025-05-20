<?php
/**
 * POS Ticket Template for Tickera
 *
 * @package TC_POSTicketTemplate
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TC_POSTicketTemplate' ) ) {

	class TC_POSTicketTemplate {

		/**
		 * Constructor.
		 */
		public function __construct() {
			//add_filter( 'tc_ticket_fields', array( $this, 'add_pos_ticket_template_field' ) );
			add_action( 'save_post_tc_tickets', array( $this, 'save_pos_ticket_template_field' ) );
		}

		/**
		 * Adds the POS ticket template field to the ticket fields.
		 *
		 * @param array $fields The current ticket fields.
		 * @return array Modified ticket fields.
		 */
		public function add_pos_ticket_template_field( $fields ) {
			$fields[] = array(
				'field_name'       => '_ticket_pos_template',
				'field_title'      => esc_html__( 'POS Ticket Template', 'tc' ),
				'field_type'       => 'function',
				'function'         => array( $this, 'tc_get_ticket_templates_POS' ),
				'tooltip'          => wp_kses(
					sprintf(
						/* translators: %s: link to ticket templates */
						__( 'Layout of the POS ticket that the customer will be downloading. <br/>You can create and manage ticket templates <a href="%s" target="_blank">here</a>.', 'tc' ),
						esc_url( admin_url( 'edit.php?post_type=tc_events&page=tc_ticket_templates' ) )
					),
					array(
						'a'      => array(
							'href'   => array(),
							'target' => array(),
						),
						'br'     => array(),
					)
				),
				'table_visibility' => false,
				'post_field_type'  => 'post_meta',
				'metabox_context'  => 'side',
			);

			return $fields;
		}

		/**
		 * Outputs the select box for POS ticket templates or returns available templates.
		 *
		 * @param string $field_name The meta field name.
		 * @param int    $post_id    The ticket post ID.
		 * @return array|null
		 */
		public function tc_get_ticket_templates_POS( $field_name = '', $post_id = 0 ) {
			$templates = array();

			$args            = array(
				'post_type'      => 'tc_templates',
				'post_status'    => 'publish',
			);
			$templates_query = new WP_Query( $args );

			if ( $templates_query->have_posts() ) {
				while ( $templates_query->have_posts() ) {
					$templates_query->the_post();
					$templates[ get_the_ID() ] = get_the_title();
				}
				wp_reset_postdata();
			}

			if ( ! empty( $field_name ) ) {
				$current_value = get_post_meta( $post_id, $field_name, true );
				$field_id      = str_replace( '_', '-', $field_name );

				echo '<select name="' . esc_attr( $field_name ) . '" id="' . esc_attr( $field_id ) . '">';
				foreach ( $templates as $id => $title ) {
					printf(
						'<option value="%1$s"%2$s>%3$s</option>',
						esc_attr( $id ),
						selected( $current_value, $id, false ),
						esc_html( $title )
					);
				}
				echo '</select>';

				return null;
			}

			return $templates;
		}

		/**
		 * Saves the POS ticket template selection.
		 *
		 * @param int $post_id The ID of the ticket post.
		 */
		public function save_pos_ticket_template_field( $post_id ) {
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			if ( isset( $_POST['_ticket_pos_template'] ) ) {
				update_post_meta(
					$post_id,
					'_ticket_pos_template',
					sanitize_text_field( wp_unslash( $_POST['_ticket_pos_template'] ) )
				);
			}
		}
	}

	// Initialize class.
	new TC_POSTicketTemplate();
}
