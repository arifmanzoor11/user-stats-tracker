<?php

// Enqueue Select2 scripts and styles for admin
function ust_enqueue_admin_scripts($hook) {
    // Load only on the Settings page for the plugin
    if ('user-stats_page_ust-settings' === $hook) {
        wp_enqueue_script(
            'select2',
            'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ['jquery'],
            null,
            true
        );
        wp_enqueue_style(
            'select2',
            'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
        );

        // Inline initialization script for Select2
        wp_add_inline_script('select2', "
            jQuery(document).ready(function($) {
                $('#ust_post_types').select2({
                    placeholder: 'Select Post Types',
                    allowClear: true
                });
                $('#ust_pages').select2({
                    placeholder: 'Select Specific Pages/Posts',
                    allowClear: true
                });
            });
        ");
    }
}
add_action('admin_enqueue_scripts', 'ust_enqueue_admin_scripts');
