<?php 
// Create a database table for user activity
function uat_create_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'user_activity';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        page TEXT NOT NULL,
        navigation_type VARCHAR(255) NOT NULL,
        duration FLOAT NOT NULL,
        timestamp DATETIME NOT NULL,
        date_time DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

register_activation_hook(__FILE__, 'uat_create_table');