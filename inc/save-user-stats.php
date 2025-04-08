<?php
function uat_log_activity() {

    if (!isset($_POST['user_id'], $_POST['page'], $_POST['duration'], $_POST['navigation_type'], $_POST['date_time'], $_POST['is_single'])) {
        wp_send_json_error('Invalid data');
    }

    // Validate the `is_single` value
    $is_single = filter_var($_POST['is_single'], FILTER_VALIDATE_BOOLEAN);
    if (!$is_single) {
        wp_send_json_error('This is not a single post');
    }

    global $wpdb;

    $table_name = $wpdb->prefix . 'user_activity';

    $user_id          = intval($_POST['user_id']);
    $page             = sanitize_text_field($_POST['page']);
    $navigation_type  = sanitize_text_field($_POST['navigation_type']);
    $duration         = floatval($_POST['duration']);
    $date_time        = sanitize_text_field($_POST['date_time']); // Expecting ISO format

    $wpdb->insert($table_name, [
        'user_id'         => $user_id,
        'page'            => $page,
        'navigation_type' => $navigation_type,
        'duration'        => $duration,
        'timestamp'       => current_time('mysql'),
        'date_time'       => $date_time,
    ]);

    wp_send_json_success('Activity logged');
}
add_action('wp_ajax_uat_log_activity', 'uat_log_activity');
