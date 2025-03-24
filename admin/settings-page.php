<?php

// Settings page for selective tracking
function ust_settings_page() {
    if ($_POST['ust_save_settings']) {
        update_option('ust_enabled_post_types', $_POST['ust_enabled_post_types'] ?? []);
        update_option('ust_enabled_pages', $_POST['ust_enabled_pages'] ?? []);
    }

    $post_types = get_post_types(['public' => true], 'objects');
    $enabled_post_types = get_option('ust_enabled_post_types', []);
    $enabled_pages = get_option('ust_enabled_pages', []);

    echo '<div class="wrap"><h1>User Stats Tracker Settings</h1>';
    echo '<form method="post">';

    echo '<h2>Select Post Types</h2>';
    echo '<select id="ust_post_types" name="ust_enabled_post_types[]" multiple="multiple" style="width: 100%;">';
    foreach ($post_types as $post_type) {
        $selected = in_array($post_type->name, $enabled_post_types) ? 'selected' : '';
        echo '<option value="' . esc_attr($post_type->name) . '" ' . $selected . '>' . esc_html($post_type->label) . '</option>';
    }
    echo '</select>';

    // echo '<h2>Select Specific Pages/Posts</h2>';
    // echo '<select id="ust_pages" name="ust_enabled_pages[]" multiple="multiple" style="width: 100%;">';
    // $all_pages = get_posts([
    //     'post_type' => 'any',
    //     'numberposts' => -1,
    //     'post_status' => 'publish',
    // ]);
    // foreach ($all_pages as $page) {
    //     $selected = in_array($page->ID, $enabled_pages) ? 'selected' : '';
    //     echo '<option value="' . esc_attr($page->ID) . '" ' . $selected . '>' . esc_html($page->post_title) . ' (' . esc_html($page->post_type) . ')</option>';
    // }
    // echo '</select>';

    echo '<br><br><input type="submit" name="ust_save_settings" class="button-primary" value="Save Settings">';
    echo '</form></div>';
}