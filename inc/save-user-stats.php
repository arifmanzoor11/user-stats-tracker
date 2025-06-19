<?php
function uat_log_activity()
{

    if (!isset($_POST['user_id'], $_POST['duration'], $_POST['navigation_type'], $_POST['date_time'], $_POST['is_single'])) {
        wp_send_json_error('Invalid data');
    }

    $is_single = filter_var($_POST['is_single'], FILTER_VALIDATE_BOOLEAN);
    if (!$is_single) {
        wp_send_json_error('This is not a single post');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'user_activity';

    $page_url = esc_url_raw($_POST['page_url']);
    $user_id = intval($_POST['user_id']);
    $page_id = intval($_POST['page_id']); // Use page_id instead of page URL
    $navigation_type = sanitize_text_field($_POST['navigation_type']);
    $duration = floatval($_POST['duration']);
    $date_time = sanitize_text_field($_POST['date_time']);

    $page_title = get_the_title($page_id);
    $Agent_Data = get_post_meta($page_id, 'Agent_Data', true);

    $email_values = []; // Collect email(s)
        if ($Agent_Data) {
            $data = is_serialized($Agent_Data) ? unserialize($Agent_Data) : $Agent_Data;

            if (is_array($data)) {
                foreach ($data as $key) {
                    if (isset($key['Email'])) {
                      $email_values[] = $key['Email'];
                    }
                }
            }
        }

    // Prepare serialized data with email(s)
    $serialize_data = serialize(array(
        'url'    => $page_url,
        'title'  => $page_title,
        'email'  => $email_values,
    ));

    $wpdb->insert($table_name, [
        'user_id' => $user_id,
        'page' => $serialize_data, // fallback if ID not usable
        'navigation_type' => $navigation_type,
        'duration' => $duration,
        'timestamp' => current_time('mysql'),
        'date_time' => $date_time,
    ]);


    wp_send_json_success('Activity logged');
}
add_action('wp_ajax_uat_log_activity', 'uat_log_activity');
add_action('wp_ajax_nopriv_uat_log_activity', 'uat_log_activity');
