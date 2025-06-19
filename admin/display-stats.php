<?php
function ust_display_stats()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_activity';

    $per_page = 20;
    $paged = max(1, intval($_GET['paged'] ?? 1));
    $offset = ($paged - 1) * $per_page;

    // Handle bulk delete
    if (isset($_POST['ust_delete_selected']) && check_admin_referer('ust_bulk_delete_action', 'ust_bulk_delete_nonce')) {
        $ids_to_delete = array_map('intval', $_POST['ust_selected_ids'] ?? []);
        if (!empty($ids_to_delete)) {
            $placeholders = implode(',', array_fill(0, count($ids_to_delete), '%d'));
            $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id IN ($placeholders)", ...$ids_to_delete));
            echo '<div class="notice notice-success is-dismissible"><p>Selected entries deleted successfully.</p></div>';
        }
    }

    $search_term = sanitize_text_field($_GET['s'] ?? '');
    $where = '';
    $params = [];

    if (!empty($search_term)) {
        $like = '%' . $wpdb->esc_like($search_term) . '%';
        $where = "WHERE user_id IN (
            SELECT ID FROM {$wpdb->users}
            WHERE display_name LIKE %s OR user_email LIKE %s
        ) OR page LIKE %s";
        $params = [$like, $like, $like];
    }

    // Get total count
    $total_items = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name $where", ...$params));

    $args = array_merge($params, [$per_page, $offset]);
    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name $where ORDER BY date_time DESC LIMIT %d OFFSET %d",
        ...$args
    ));

    // Total pages
    $total_pages = ceil($total_items / $per_page);
    ?>

    <style>
        .ust-search-bar {
            margin-bottom: 15px;
        }

        .ust-search-bar input[type="search"] {
            padding: 6px;
            width: 250px;
            max-width: 100%;
            margin-right: 10px;
        }

        .ust-table-wrapper {
            overflow-x: auto;
        }

        .ust-table th,
        .ust-table td {
            vertical-align: middle;
        }

        .ust-table td {
            padding: 8px 6px;
        }

        .ust-pagination {
            margin-top: 15px;
        }

        .ust-pagination a,
        .ust-pagination span {
            display: inline-block;
            margin: 0 3px;
            padding: 6px 12px;
            border: 1px solid #ccc;
            text-decoration: none;
        }

        .ust-pagination .current {
            background: #0073aa;
            color: white;
            border-color: #0073aa;
        }

        .button-danger {
            background: #cc0000;
            border-color: #990000;
            color: white;
        }

        .button-danger:hover {
            background: #990000;
            border-color: #660000;
        }
    </style>
    <br>
    <form method="post">
        <?php wp_nonce_field('ust_bulk_delete_action', 'ust_bulk_delete_nonce'); ?>

        <div class="ust-search-bar">
            <input type="hidden" name="page" value="<?= esc_attr($_GET['page']) ?>">
            <input style="padding: 5px 10px;" type="search" name="s" value="<?= esc_attr($search_term) ?>"
                placeholder="Search username, email or page">
            <input style="padding: 5px 20px;" type="submit" class="button-primary" value="Search">
        </div>

        <div style="margin-bottom: 10px;">
            <input type="submit" name="ust_delete_selected" class="button" value="Delete Selected">
        </div>

        <div class="ust-table-wrapper">
            <table class="widefat fixed ust-table">
                <thead>
                    <tr>
                        <th style="width:20px;"><input type="checkbox" id="ust-check-all"></th>
                        <th>Property</th>
                        <th>Email</th>
                        <th style="width:60px;">Navigate</th>
                        <th>Agent Email</th>
                        <th style="width:80px;">Time Spent</th>
                        <th>Date/Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($results): ?>
                        <?php foreach ($results as $row):
                            $user = get_userdata($row->user_id);
                            $email = $user ? $user->user_email : 'N/A';
                            $duration = floor($row->duration / 60) . ' min ' . ($row->duration % 60) . ' sec';

                            $page_display = '';
                            $view_link = '#';
                            $agent_emails = [];

                            if (is_serialized($row->page)) {
                                // New serialized format
                                $page_data = unserialize($row->page);

                                $page_url = $page_data['url'] ?? '';
                                $page_title = $page_data['title'] ?? 'Unknown';
                                $agent_emails = $page_data['email'] ?? [];

                                $page_display = '<a href="' . esc_url($page_url) . '" target="_blank"><strong>' . esc_html($page_title) . '</strong></a>';
                                $view_link = $page_url;
                            } elseif (is_numeric($row->page)) {
                                // Old format using post ID
                                $post_id = intval($row->page);
                                $post = get_post($post_id);
                                $page_display = $post ? '<strong>' . esc_html(get_the_title($post)) . '</strong>' : 'Unknown Post';
                                $view_link = $post ? get_permalink($post) : '#';

                                // Get agent email from post meta
                                $Agent_Data = get_post_meta($post_id, 'Agent_Data', true);
                                if ($Agent_Data) {
                                    $data = is_serialized($Agent_Data) ? unserialize($Agent_Data) : $Agent_Data;
                                    if (is_array($data)) {
                                        foreach ($data as $agent) {
                                            if (!empty($agent['Email'])) {
                                                $agent_emails[] = $agent['Email'];
                                            }
                                        }
                                    }
                                }
                            } else {
                                // Unknown format fallback
                                $page_display = esc_html($row->page);
                            }
                            ?>
                            <tr>
                                <td><input type="checkbox" name="ust_selected_ids[]" value="<?= esc_attr($row->id) ?>"></td>
                                <td><?= $page_display ?></td>
                                <td><?= esc_html($email) ?></td>
                                <td style="text-align: center;"><?= esc_html($row->navigation_type) ?></td>
                                <td>
                                    <?php
                                    if (!empty($agent_emails)) {
                                        foreach ((array) $agent_emails as $a_email) {
                                            echo esc_html($a_email) . '<br>';
                                        }
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </td>
                                <td><?= esc_html($duration) ?></td>
                                <td><?= esc_html(date_i18n('M j, Y g:i A', strtotime($row->date_time))) ?></td>
                                <td>
                                    <a class="button" href="mailto:<?= esc_attr($email) ?>">Contact</a>
                                    <a class="button" href="<?= esc_url($view_link) ?>" target="_blank">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align:center;">No entries found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </form>

    <?php if ($total_pages > 1): ?>
        <div class="ust-pagination">
            <?php
            for ($i = 1; $i <= $total_pages; $i++) {
                if ($i === $paged) {
                    echo '<span class="current">' . $i . '</span>';
                } else {
                    echo '<a href="' . esc_url(add_query_arg(['paged' => $i])) . '">' . $i . '</a>';
                }
            }
            ?>
        </div>
    <?php endif; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkAll = document.getElementById('ust-check-all');
            const checkboxes = document.querySelectorAll('input[name="ust_selected_ids[]"]');

            if (checkAll) {
                checkAll.addEventListener('change', function () {
                    checkboxes.forEach(cb => cb.checked = checkAll.checked);
                });
            }
        });
    </script>

    <?php
}
