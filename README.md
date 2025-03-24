# Enhanced User Stats Tracker

## Plugin Information

**Plugin Name:** Enhanced User Stats Tracker  
**Description:** Tracks user activity on selected post types/pages, including time spent and navigation type. Provides detailed stats and contact options in the admin panel.  
**Version:** 1.2  
**Author:** Arif M.  

---

## Features
- Tracks user activity on specified post types/pages.
- Records time spent and navigation type for logged-in users.
- Provides an admin panel interface to view detailed stats and manage settings.

---

## Installation
1. **Upload via WordPress Admin:**
   - Go to `Plugins > Add New > Upload Plugin`.
   - Select the `enhanced-user-stats-tracker.zip` file and click **Install Now**.
   - Activate the plugin from the **Plugins** menu.
   
2. **Upload via FTP:**
   - Extract the `enhanced-user-stats-tracker.zip` file.
   - Upload the extracted folder to `/wp-content/plugins/` directory.
   - Activate the plugin from **Plugins** in the WordPress admin panel.

---

## Files Included
- **`inc/uat-create-table.php`**: Handles the creation of necessary database tables.
- **`inc/save-user-stats.php`**: Manages saving user activity data.
- **`inc/enqueue-scripts.php`**: Handles script enqueueing for the plugin.
- **`admin/settings-page.php`**: Provides the settings page in the admin panel.
- **`admin/display-stats.php`**: Displays user stats in the admin panel.

---

## Functions
- **`uat_enqueue_scripts()`**: Enqueues the tracker script for logged-in users and localizes data for AJAX requests.

---

## Hooks Used
- **`wp_enqueue_scripts`**: Hooks into WordPress to enqueue the tracker script.

---

## Notes
- The plugin prevents direct access by checking if `ABSPATH` is defined.
- The tracker script is only enqueued for logged-in users and on enabled post types.

---

## Usage
1. **Enable tracking** from the plugin settings page in the WordPress admin panel.
2. **View user stats** from the `User Activity Stats` section.
3. Use the provided options to **filter user activity** based on post types/pages.

---

## Support & Contributions
For issues or contributions, contact me at [http://guitarchordslyrics.com](http://guitarchordslyrics.com).

---

### Thank you for using Enhanced User Stats Tracker! 🚀

