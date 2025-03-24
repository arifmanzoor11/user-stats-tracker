<?php

// Display stats in admin panel
function ust_display_stats() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_activity';
    $results = $wpdb->get_results("SELECT * FROM $table_name");
    ?>
    <div class="wrap">
        <h1>User Stats Tracker</h1>
        <table class="widefat fixed" style="width: 100%; text-align: left;">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Page URL</th>
                    <th>Time Spent</th>
                    <th>Navigation Type</th>
                    <th>Date/Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($results)): ?>
                    <?php foreach ($results as $row): 
                        $user_info = get_userdata($row->user_id);
                        $username = $user_info ? $user_info->user_login : 'Guest';
                        $email = $user_info ? $user_info->user_email : 'N/A';

                        // Convert duration from seconds to minutes
                        $duration_minutes = floor($row->duration / 60);
                        $duration_seconds = $row->duration % 60;
                    ?>
                        <tr>
                            <td><?php echo $username; ?></td>
                            <td><?php echo $email; ?></td>
                            <td><?php echo $row->page; ?></td>
                            <td><?php echo $duration_minutes . ' min ' . $duration_seconds . ' sec'; ?></td>
                            <td><?php echo $row->navigation_type; ?></td>
                            <td><?php echo $row->date_time; ?></td>
                            <td>
                                <a href="mailto:<?php echo $email; ?>" class="button">Contact</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">No entries found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
?>
