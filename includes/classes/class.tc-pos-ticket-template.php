<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class TC_POSTicketTemplate {
    
    public function __construct() {
        add_filter('tc_ticket_fields', array($this, 'add_pos_ticket_template_field'));
        add_action('save_post_tc_tickets', array($this, 'save_pos_ticket_template_field'));
    }

    public function add_pos_ticket_template_field($fields) {
        $fields[] = array(
            'field_name' => 'ticket_template_POS',
            'field_title' => __('POS Ticket template', 'tc'),
            'field_type' => 'function',
            'function' => array($this, 'tc_get_ticket_templates_POS'),
            'tooltip' => __('Layout of the POS ticket that the customer will be downloading. <br/>You can create new and manage existing ticket templates <a href="' . esc_url(admin_url('edit.php?post_type=tc_events&page=tc_ticket_templates')) . '" target="_blank">here</a>', 'tc'),
            'table_visibility' => false,
            'post_field_type' => 'post_meta',
            'metabox_context' => 'side'
        );

        return $fields;
    }

    public function tc_get_ticket_templates_POS($field_name = '', $post_id = '') {
        $templates = array();

        $args = array(
            'post_type'      => 'tc_templates',
            'posts_per_page' => -1,
            'post_status'    => 'publish'
        );

        $templates_query = new WP_Query($args);

        if ($templates_query->have_posts()) {
            while ($templates_query->have_posts()) {
                $templates_query->the_post();
                $templates[get_the_ID()] = get_the_title();
            }
            wp_reset_postdata();
        }

        if (!empty($field_name)) {
            $current_value = get_post_meta($post_id, $field_name, true);
            $field_id = str_replace('_', '-', $field_name);

            echo '<select name="' . esc_attr($field_name) . '" id="' . esc_attr($field_id) . '">';
            foreach ($templates as $id => $title) {
                echo '<option value="' . esc_attr($id) . '"' . selected($current_value, $id, false) . '>' . esc_html($title) . '</option>';
            }
            echo '</select>';
        } else {
            return $templates;
        }
    }

    public function save_pos_ticket_template_field($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }

        if (isset($_POST['ticket_template_POS'])) {
            update_post_meta($post_id, 'ticket_template_POS', sanitize_text_field($_POST['ticket_template_POS']));
        }
    }
}

new TC_POSTicketTemplate();
?>
