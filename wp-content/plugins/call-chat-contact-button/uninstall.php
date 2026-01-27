<?php
if (!defined('WP_UNINSTALL_PLUGIN')) exit;

$options = [
    'cccb_phone',
    'cccb_zalo',
    'cccb_position',
    'cccb_phone_color',
    'cccb_zalo_color'
];

// Xoá option site thường
foreach ($options as $option) {
    delete_option($option);
}

// Multisite
if (is_multisite()) {
    global $wpdb;
    $blogIds = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

    foreach ($blogIds as $blogId) {
        switch_to_blog($blogId);

        foreach ($options as $option) {
            delete_option($option);
        }

        restore_current_blog();
    }
}
