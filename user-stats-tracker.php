<?php
/*
Plugin Name: Enhanced User Stats Tracker
Description: Tracks user activity on selected post types/pages, including time spent and navigation type. Provides detailed stats and contact options in the admin panel.
Version: 1.2
Author: Arif M.
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

include_once(plugin_dir_path(__FILE__) . 'ic/uat-create-table.php');
include_once(plugin_dir_path(__FILE__) . 'inc/save-user-stats.php');
include_once(plugin_dir_path(__FILE__) . 'inc/enqueue-scripts.php');

// admin
include_once(plugin_dir_path(__FILE__) . 'admin/settings-page.php');
include_once(plugin_dir_path(__FILE__) . 'admin/display-stats.php');


function uat_enqueue_scripts() {
    if (is_user_logged_in() && !wp_script_is('uat-tracker', 'enqueued')) {
        // Get the enabled post types
    $enabled_post_types = get_option('ust_enabled_post_types', []);

        wp_enqueue_script('uat-tracker', plugin_dir_url(__FILE__) . 'assets/js/tracker.js', ['jquery'], '1.0', true);
        wp_localize_script('uat-tracker', 'uat_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'user_id'  => get_current_user_id(),
            'enabled_post_types' => $enabled_post_types,
        ]);
    }
}
add_action('wp_enqueue_scripts', 'uat_enqueue_scripts');
