

jQuery(document).ready(function($) {
    let startTime = Date.now();
    let isRequestSent = false; // Flag to prevent duplicate requests

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
});



// jQuery(document).ready(function ($) {
//     let startTime = Date.now();

//     // Function to send data to the server
//     function sendUserStats(data) {
//         $.post(ustData.ajaxUrl, data)
//             .done(function (response) {
//                 if (response.success) {
//                     console.log('Stats sent successfully:', response.data.message);
//                 } else {
//                     console.error('Failed to send stats:', response.data.message);
//                 }
//             })
//             .fail(function (error) {
//                 console.error('AJAX request failed:', error);
//             });
//     }

//     // Send data on page unload
//     $(window).on('beforeunload', function () {
//         const endTime = Date.now();
//         const timeSpent = Math.round((endTime - startTime) / 1000);

//         const data = {
//             action: 'ust_save_user_stats',
//             page_url: window.location.href,
//             time_spent: timeSpent,
//             navigation_type: 'exit',
//         };

//         sendUserStats(data);
//     });
// });
