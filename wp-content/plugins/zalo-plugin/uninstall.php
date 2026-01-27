<?php
/**
 * Fired when the plugin is uninstalled.
 */

// Nếu không phải WordPress gọi uninstall → thoát
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Xoá option đã lưu
delete_option('cccb_phone');
delete_option('cccb_zalo');
delete_option('cccb_messenger');

// Nếu có multisite → xoá toàn bộ site
if (is_multisite()) {
    global $wpdb;
    $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

    foreach ($blog_ids as $blog_id) {
        switch_to_blog($blog_id);

        delete_option('cccb_phone');
        delete_option('cccb_zalo');
        delete_option('cccb_messenger');

        restore_current_blog();
    }
}