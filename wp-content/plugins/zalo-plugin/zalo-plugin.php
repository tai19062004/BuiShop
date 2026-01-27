<?php
/*
Plugin Name: Call Chat Contact Button
Description: Floating Call / Chat / Contact Button
Version: 1.0
Author: Tai Nguyen
*/

// Ngăn truy cập trực tiếp file plugin
if (!defined('ABSPATH')) exit;

/**
 * =====================================================
 * PHẦN 1: LOAD CSS CHO FRONTEND
 * =====================================================
 * - Chỉ load CSS ở phía người dùng (frontend)
 * - File CSS nằm trong: assets/css/style.css
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'cccb-style',
        plugin_dir_url(__FILE__) . 'assets/css/style.css',
        [],
        filemtime(plugin_dir_path(__FILE__) . 'assets/css/style.css')
    );
});

/**
 * =====================================================
 * PHẦN 2: HIỂN THỊ BUTTON FLOAT Ở FRONTEND
 * =====================================================
 * - Hook vào wp_footer để đảm bảo HTML nằm cuối trang
 * - Lấy dữ liệu từ option đã lưu trong database
 */
add_action('wp_footer', function () {

    // Lấy số điện thoại để gọi
    $phone = get_option('phone');

    // Lấy link Zalo chat
    $zalo  = get_option('zalo_phone');

    // Lấy vị trí hiển thị
    $position = get_option('cccb_position', 'bottom-left');

    // Lấy màu của 2 nút
    $zalo_color = get_option('cccb_zalo_color', '#ff3a3a');
    $phone_color = get_option('cccb_phone_color', '#0084ff');

    if(empty($phone) && empty($zalo)){
        return;
    }

    $zalo_link = 'https://zalo.me/' . $zalo;

    ?>
    
    <!-- Container chứa các nút nổi -->
    <div class="zalo-float-container pos-<?php echo esc_attr($position) ?>">
        <!-- Nút Chat Zalo -->
        <?php if(!empty($zalo)){ ?>
            <a href="<?= esc_url($zalo_link) ?>" 
            target="_blank" 
            class="zalo-float-btn zalo-btn"
            style="background-color: <?= esc_attr($zalo_color) ?>;"
            aria-label="Chat Zalo">
            </a>
        <?php } ?>
        <!-- Nút Gọi điện -->
        <?php if(!empty($phone)){ ?>
            <a href="tel:<?= esc_attr($phone) ?>" 
            class="zalo-float-btn call-btn"
            style="background-color: <?= esc_attr($phone_color); ?>;"
            aria-label="Gọi ngay">
            </a>
        <?php } ?>  
    </div>

    <?php
});

/**
 * =====================================================
 * PHẦN 3: TẠO MENU CÀI ĐẶT TRONG ADMIN
 * =====================================================
 * - Thêm menu riêng trong Dashboard
 * - Chỉ admin (manage_options) mới thấy
 */
add_action('admin_menu', function () {
    add_menu_page(
        'Call Chat Button',      // Page title
        'Call Chat Button',      // Menu title
        'manage_options',        // Quyền truy cập
        'cccb-setting',          // Slug
        'cccb_setting_page'      // Callback hiển thị nội dung
    );
});

/**
 * =====================================================
 * PHẦN 4: LOAD FILE GIAO DIỆN SETTING
 * =====================================================
 * - File setting.php chứa form cấu hình
 * - Đường dẫn: assets/admin/setting.php
 */
function cccb_setting_page() {
    include plugin_dir_path(__FILE__) . './includes/admin/setting.php';
}
