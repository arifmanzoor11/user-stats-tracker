<?php
function ust_display_stats() {
    global $wpdb;
    
    // Setup variables
    $table_name = $wpdb->prefix . 'user_activity';
    $per_page = 20;
    $paged = max(1, intval($_GET['paged'] ?? 1));
    $offset = ($paged - 1) * $per_page;
    $search_term = sanitize_text_field($_GET['s'] ?? '');
    
    // Build search query
    $search_sql = '';
    $search_args = [];
    if ($search_term) {
        $search_sql = "AND (user_id IN (SELECT ID FROM {$wpdb->users} WHERE user_login LIKE %s OR user_email LIKE %s) OR page LIKE %s)";
        $like = '%' . $wpdb->esc_like($search_term) . '%';
        $search_args = [$like, $like, $like];
    }
    
    // Get data
    $total_items = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE 1=1 $search_sql", ...$search_args));
    $total_pages = ceil($total_items / $per_page);
    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE 1=1 $search_sql ORDER BY date_time DESC LIMIT %d OFFSET %d",
        ...array_merge($search_args, [$per_page, $offset])
    ));
    ?>
    
    <div class="wrap">
        <h1>User Stats Tracker</h1>
        
        <style>
            .ust-table-wrapper { overflow-x: auto; }
            .ust-table th, .ust-table td { padding: 10px; vertical-align: middle; }
            .ust-pagination { display: flex; gap: 10px; align-items: center; margin-top: 20px; }
            .ust-pagination a, .ust-pagination input { padding: 6px 12px; border: 1px solid #ccc; border-radius: 4px; }
            .ust-pagination .current-page { background: #2271b1; color: #fff; font-weight: bold; }
            .ust-search-bar { margin: 15px 0; }
            .ust-search-bar input[type="search"] { padding: 6px; width: 300px; border: 1px solid #ccc; border-radius: 4px; }
            .ust-search-bar input[type="submit"] { padding: 6px 12px; background: #2271b1; color: #fff; border: none; border-radius: 4px; }
        </style>
        
        <!-- Search Form -->
        <form class="ust-search-bar" method="get">
            <input type="hidden" name="page" value="<?= esc_attr($_GET['page']) ?>">
            <input type="search" name="s" value="<?= esc_attr($search_term) ?>" placeholder="Search username, email or page">
            <input type="submit" value="Search">
        </form>
        
        <!-- Results Table -->
        <div class="ust-table-wrapper">
            <table class="widefat fixed ust-table">
                <thead>
                    <tr>
                        <th>Property</th>
                        <th>Email</th>
                        <th style="width:60px">Navigate</th>
                        <th>Time Spent</th>
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
                            
                            // Handle page display
                            if (is_numeric($row->page)) {
                                $post = get_post(intval($row->page));
                                $page_display = $post ? '<strong>' . esc_html(get_the_title($post)) . '</strong>' : 'Unknown Post';
                                $view_link = $post ? get_permalink($post) : '#';
                            } else {
                                $page_display = '<a href="' . esc_url($row->page) . '" target="_blank">' . esc_html($row->page) . '</a>';
                                $view_link = $row->page;
                            }
                        ?>
                            <tr>
                                <td><?= $page_display ?></td>
                                <td><?= esc_html($email) ?></td>
                                <td style="text-align: center;"><?= esc_html($row->navigation_type) ?></td>
                                <td><?= esc_html($duration) ?></td>
                                <td><?= esc_html(date_i18n('M j, Y g:i A', strtotime($row->date_time))) ?></td>
                                <td>
                                    <a class="button" href="mailto:<?= esc_attr($email) ?>">Contact</a>
                                    <a class="button" href="<?= esc_url($view_link) ?>" target="_blank">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align: center;">No entries found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="ust-pagination">
                <?php
                // First page
                printf('<a class="%s" href="%s">1</a>', 
                    $paged == 1 ? 'current-page' : '', 
                    esc_url(add_query_arg('paged', 1))
                );
                
                // Ellipsis if needed
                if ($paged > 4) echo '<span>...</span>';
                
                // Pages around current
                $start = max(2, $paged - 1);
                $end = min($total_pages - 1, $paged + 1);
                for ($i = $start; $i <= $end; $i++) {
                    printf('<a class="%s" href="%s">%d</a>', 
                        $paged == $i ? 'current-page' : '', 
                        esc_url(add_query_arg('paged', $i)), 
                        $i
                    );
                }
                
                // Ellipsis and last page
                if ($end < $total_pages - 1) echo '<span>...</span>';
                if ($total_pages > 1) {
                    printf('<a class="%s" href="%s">Last</a>', 
                        $paged == $total_pages ? 'current-page' : '', 
                        esc_url(add_query_arg('paged', $total_pages))
                    );
                }
                ?>
                
                <!-- Jump to page -->
                <form method="get" style="display: inline-flex; align-items: center;">
                    <input type="hidden" name="page" value="<?= esc_attr($_GET['page']) ?>">
                    <input type="number" name="paged" min="1" max="<?= $total_pages ?>" value="<?= $paged ?>" style="width: 70px;">
                    <input type="submit" value="Go">
                </form>
            </div>
        <?php endif; ?>
    </div>
    <?php
}