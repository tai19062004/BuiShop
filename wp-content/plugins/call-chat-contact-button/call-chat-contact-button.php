<?php
/*
Plugin Name: Call Chat Contact Button
Description: Floating Call / Chat / Contact Button
Version: 1.0
Author: Tai Nguyen
Text Domain: call-chat-contact-button
Domain Path: /languages
*/

// Ngăn truy cập trực tiếp file plugin
if (!defined('ABSPATH')) exit;

/**
 * =====================================================
 * LOAD TEXT DOMAIN
 * =====================================================
 */
add_action('plugins_loaded', function () {
    load_plugin_textdomain(
        'call-chat-contact-button',
        false,
        dirname(plugin_basename(__FILE__)) . '/languages'
    );
});

/**
 * =====================================================
 * PHẦN 1: LOAD CSS CHO FRONTEND
 * =====================================================
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'cccb-style',
        plugin_dir_url(__FILE__) . 'assets/css/style.css',
        [],
        filemtime(plugin_dir_path(__FILE__) . 'assets/css/style.css')
    );

    wp_enqueue_script(
        'cccb-script',
        plugin_dir_url(__FILE__) . 'assets/js/script.js',
        [],
        filemtime(plugin_dir_path(__FILE__) . 'assets/js/script.js'),
        true
    );
});

/**
 * =====================================================
 * PHẦN 2: HIỂN THỊ BUTTON FLOAT Ở FRONTEND
 * =====================================================
 */
add_action('wp_footer', function () {

    $phone        = get_option('cccb_phone');
    $zalo         = get_option('cccb_zalo');
    $cf7_form_id  = get_option('cccb_cf7_form_id');
    $position     = get_option('cccb_position', 'bottom-left');
    $phoneColor   = get_option('cccb_phone_color', '#0084ff');
    $zaloColor    = get_option('cccb_zalo_color', '#ff3a3a');
    $formColor    = get_option('cccb_form_color', '#00b894');

    if (empty($phone) && empty($zalo) && empty($cf7_form_id)) return;

    $zaloLink = $zalo ? 'https://zalo.me/' . $zalo : '';
    ?>

    <div class="zalo-float-container pos-<?php echo esc_attr($position); ?>">

        <?php if ($zalo): ?>
            <a href="<?php echo esc_url($zaloLink); ?>"
               target="_blank"
               class="zalo-float-btn zalo-btn"
               style="background-color: <?php echo esc_attr($zaloColor); ?>;"
               aria-label="<?php esc_attr_e('Chat Zalo', 'call-chat-contact-button'); ?>">
            </a>
        <?php endif; ?>

        <?php if ($phone): ?>
            <a href="tel:<?php echo esc_attr($phone); ?>"
               class="zalo-float-btn call-btn"
               style="background-color: <?php echo esc_attr($phoneColor); ?>;"
               aria-label="<?php esc_attr_e('Call now', 'call-chat-contact-button'); ?>">
            </a>
        <?php endif; ?>

        <?php if ($cf7_form_id): ?>
            <button class="cccb-btn contact zalo-float-btn contact-btn"
                    style="background:<?php echo esc_attr($formColor); ?>">
                <?php esc_html_e('Contact', 'call-chat-contact-button'); ?>
            </button>
        <?php endif; ?>
    </div>

    <!-- post_type_exists dùng để kiểm tra xem Contact Form 7 có tồn tại không -->
    <?php if ($cf7_form_id && post_type_exists('wpcf7_contact_form')): ?>
        <div class="cccb-popup-overlay">
            <div class="cccb-popup">
                <button class="cccb-close">&times;</button>
                <?php echo do_shortcode('[contact-form-7 id="' . intval($cf7_form_id) . '"]'); ?>
            </div>
        </div>
    <?php endif; ?>
<?php
});

/**
 * =====================================================
 * PHẦN 3: TẠO MENU CÀI ĐẶT TRONG ADMIN
 * =====================================================
 */
add_action('admin_menu', function () {
    add_menu_page(
        __('Call Chat Button', 'call-chat-contact-button'),
        __('Call Chat Button', 'call-chat-contact-button'),
        'manage_options',
        'cccb-setting',
        'cccb_setting_page'
    );
});

/**
 * =====================================================
 * PHẦN 4: LOAD FILE GIAO DIỆN SETTING
 * =====================================================
 */
function cccb_setting_page() {
    include plugin_dir_path(__FILE__) . 'includes/setting.php';
}
