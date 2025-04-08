<?php
if (!defined('ABSPATH')) {
    exit;
}

function uat_delete_stats_page() {
    ?>
    <div class="wrap">
        <h1>Delete User Statistics</h1>
        <div class="notice notice-warning">
            <p><strong>Warning:</strong> Deleted statistics cannot be recovered. Please be careful with this operation.</p>
        </div>

        <div class="card">
            <form id="uat-delete-stats-form">
                <?php wp_nonce_field('uat_delete_stats', 'nonce'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">Delete Type</th>
                        <td>
                            <select name="delete_type" id="delete-type" class="regular-text">
                                <option value="">Select deletion type</option>
                                <option value="all">All Statistics</option>
                                <option value="user">By User</option>
                                <option value="date_range">By Date Range</option>
                            </select>
                        </td>
                    </tr>

                    <tr id="user-select-row" style="display:none;">
                        <th scope="row">Select User</th>
                        <td>
                            <select name="user_id" class="regular-text">
                                <?php
                                $users = get_users(['fields' => ['ID', 'user_login', 'user_email']]);
                                foreach ($users as $user) {
                                    echo sprintf(
                                        '<option value="%d">%s (%s)</option>',
                                        $user->ID,
                                        esc_html($user->user_login),
                                        esc_html($user->user_email)
                                    );
                                }
                                ?>
                            </select>
                        </td>
                    </tr>

                    <tr id="date-range-row" style="display:none;">
                        <th scope="row">Date Range</th>
                        <td>
                            <input type="date" name="start_date" class="regular-text"> to 
                            <input type="date" name="end_date" class="regular-text">
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" class="button button-primary" id="delete-stats-button">Delete Statistics</button>
                </p>
            </form>
        </div>
    </div>

    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#delete-type').on('change', function() {
            $('#user-select-row, #date-range-row').hide();
            switch($(this).val()) {
                case 'user':
                    $('#user-select-row').show();
                    break;
                case 'date_range':
                    $('#date-range-row').show();
                    break;
            }
        });

        $('#uat-delete-stats-form').on('submit', function(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to delete these statistics? This action cannot be undone.')) {
                return false;
            }

            const formData = $(this).serialize();
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: formData + '&action=uat_delete_stats',
                success: function(response) {
                    if (response.success) {
                        alert('Statistics deleted successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + response.data);
                    }
                },
                error: function() {
                    alert('An error occurred while processing your request.');
                }
            });
        });
    });
    </script>
    <?php
}