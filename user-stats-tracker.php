<?php
/*
Plugin Name: Enhanced User Stats Tracker
Plugin URI: https://guitarchordslyrics.com
Description: Tracks user activity on selected post types/pages, including time spent and navigation type. Provides detailed stats and contact options in the admin panel.
Version: 2.3
Author: Arif M.
Author URI: https://arifm.guitarchordslyrics.com
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: user-stats-tracker
*/

if (!defined('ABSPATH')) {
    exit;
}

// Core includes
include_once(plugin_dir_path(__FILE__) . 'inc/uat-create-table.php');
include_once(plugin_dir_path(__FILE__) . 'inc/save-user-stats.php');
include_once(plugin_dir_path(__FILE__) . 'inc/enqueue-scripts.php');
include_once(plugin_dir_path(__FILE__) . 'inc/delete-stats.php');

// Admin includes
include_once(plugin_dir_path(__FILE__) . 'admin/settings-page.php');
include_once(plugin_dir_path(__FILE__) . 'admin/display-stats.php');
include_once(plugin_dir_path(__FILE__) . 'admin/delete-stats-page.php');

function uat_enqueue_scripts() {
    if (is_user_logged_in() && !wp_script_is('uat-tracker', 'enqueued')) {
        $enabled_post_types = get_option('ust_enabled_post_types', []);
        
        $tracker_path = plugin_dir_path(__FILE__) . 'assets/js/tracker.js';
        $tracker_version = filemtime($tracker_path);

        $tracker_url = plugin_dir_url(__FILE__) . 'assets/js/tracker.js?v=' . $tracker_version . '&nocache=' . time();

        wp_enqueue_script('uat-tracker', $tracker_url, ['jquery'], null, true);

        wp_localize_script('uat-tracker', 'uat_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'user_id'  => get_current_user_id(),
            'post_id'  => get_the_ID(),
            'enabled_post_types' => $enabled_post_types,
        ]);
    }
}
add_action('wp_enqueue_scripts', 'uat_enqueue_scripts');



function ust_add_admin_menu() {
    add_menu_page(
        'User Stats Tracker',
        'User Stats',
        'edit_posts',
        'user-stats-tracker',
        'ust_display_stats',
        'dashicons-analytics',
        3
    );

    add_submenu_page(
        'user-stats-tracker',
        'Delete Stats',
        'Delete Stats',
        'edit_posts',
        'ust-delete-stats',
        'uat_delete_stats_page'
    );

    add_submenu_page(
        'user-stats-tracker',
        'Settings',
        'Settings',
        'manage_options',
        'ust-settings',
        'ust_settings_page'
    );
}
add_action('admin_menu', 'ust_add_admin_menu');
