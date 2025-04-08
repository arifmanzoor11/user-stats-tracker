<?php
if (!defined('ABSPATH')) {
    exit;
}

function uat_delete_stats() {
    // Verify nonce and capabilities
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'uat_delete_stats') || !current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized access');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'user_activity';
    $delete_type = sanitize_text_field($_POST['delete_type']);
    $success = false;

    switch ($delete_type) {
        case 'all':
            $success = $wpdb->query("TRUNCATE TABLE $table_name");
            break;

        case 'user':
            $user_id = intval($_POST['user_id']);
            $success = $wpdb->delete($table_name, ['user_id' => $user_id], ['%d']);
            break;

        case 'date_range':
            $start_date = sanitize_text_field($_POST['start_date']);
            $end_date = sanitize_text_field($_POST['end_date']);
            $success = $wpdb->query($wpdb->prepare(
                "DELETE FROM $table_name WHERE date_time BETWEEN %s AND %s",
                $start_date,
                $end_date . ' 23:59:59'
            ));
            break;
    }

    if ($success !== false) {
        wp_send_json_success('Stats deleted successfully');
    } else {
        wp_send_json_error('Error deleting stats');
    }
}
add_action('wp_ajax_uat_delete_stats', 'uat_delete_stats');