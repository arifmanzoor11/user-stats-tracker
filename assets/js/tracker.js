jQuery(document).ready(function($) {
    let startTime = Date.now();
    let isRequestSent = false; // Flag to prevent duplicate requests
    let activityTimer;
    const ACTIVITY_INTERVAL = 30000; // 30 seconds

    // Check if the current page matches any of the enabled post types
    let enabledPostTypes = uat_ajax.enabled_post_types || [];
    let isSinglePost = false;

    enabledPostTypes.forEach(function(postType) {
        if ($('body').hasClass(`single-${postType}`)) {
            isSinglePost = true;
        }
    });

    $(window).on('beforeunload', function() {
        if (isRequestSent) return; // Prevent duplicate requests

        let endTime = Date.now();
        let duration = (endTime - startTime) / 1000; // Duration in seconds
        let currentDateTime = new Date().toISOString(); // Current date and time in ISO format

        $.post(uat_ajax.ajax_url, {
            action: 'uat_log_activity',
            user_id: uat_ajax.user_id,
            page: window.location.href,
            duration: duration,
            navigation_type: 'exit',
            date_time: currentDateTime,
            is_single: isSinglePost
        });

        isRequestSent = true; // Set the flag to true after sending the request
    });

    function trackUserActivity() {
        clearTimeout(activityTimer);
        
        const data = {
            action: 'ust_track_activity',
            page_url: window.location.href,
            scroll_depth: calculateScrollDepth(),
            active_time: getActiveTime()
        };
        
        $.post(uat_ajax.ajax_url, data);
        
        activityTimer = setTimeout(trackUserActivity, ACTIVITY_INTERVAL);
    }

    document.addEventListener('DOMContentLoaded', trackUserActivity);
});
