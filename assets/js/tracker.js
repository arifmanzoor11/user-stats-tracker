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

    // Detect if user returns from another page
    let navigationType = 'live';
    if (performance && performance.navigation && performance.navigation.type === 2) {
        navigationType = 'return';
    }

    // Capture tab close or exit
    $(window).on('beforeunload', function() {
        if (isRequestSent) return; // Prevent duplicate requests

        let endTime = Date.now();
        let duration = (endTime - startTime) / 1000; // Duration in seconds
        let currentDateTime = new Date().toISOString();

        // Override with 'exit' if tab is closing
        let navType = document.visibilityState === 'hidden' ? 'exit' : navigationType;

        navigator.sendBeacon(uat_ajax.ajax_url, new URLSearchParams({
            action: 'uat_log_activity',
            user_id: uat_ajax.user_id,
            page_id: uat_ajax.post_id,
            duration: duration,
            navigation_type: navType,
            date_time: currentDateTime,
            is_single: isSinglePost
        }));

        isRequestSent = true;
    });

    // Track periodic user activity
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

    // Scroll depth helper
    function calculateScrollDepth() {
        let scrollTop = $(window).scrollTop();
        let docHeight = $(document).height() - $(window).height();
        return docHeight > 0 ? Math.round((scrollTop / docHeight) * 100) : 0;
    }

    // Active time helper
    function getActiveTime() {
        return Math.round((Date.now() - startTime) / 1000);
    }
});
