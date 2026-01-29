<?php
/*
Plugin Name: Quick Product Editor
Description: Sửa nhanh sản phẩm WooCommerce
Version: 1.1
Author: Tai Nguyen
Text Domain: quick-product-editor
Domain Path: /languages
*/

if (!defined('ABSPATH')) exit; // Chặn truy cập trực tiếp file plugin

/**
 * =====================================================
 * LOAD TEXT DOMAIN (đa ngôn ngữ)
 * =====================================================
 * Load file dịch trong thư mục /languages
 * Để __(), _e(), esc_html_e() hoạt động
 */
add_action('plugins_loaded', function () {
    load_plugin_textdomain(
        'quick-product-editor', // Text domain (PHẢI trùng)
        false,
        dirname(plugin_basename(__FILE__)) . '/languages'
    );
});

/**
 * =====================================================
 * MENU CHA + MENU CON (Admin)
 * =====================================================
 * Tạo menu giống RankMath:
 * - Menu cha: Quick Product Editor
 * - Menu con: Danh sách sản phẩm
 */
add_action('admin_menu', function () {

    // Menu cha
    add_menu_page(
        __('Quick Product Editor', 'quick-product-editor'), // Page title
        __('Sửa nhanh sản phẩm', 'quick-product-editor'),   // Menu title
        'manage_woocommerce',                               // Quyền truy cập
        'quick-product-editor',                             // Slug
        'qpe_page_products',                                // Callback render page
        'dashicons-products',                               // Icon
        32                                                   // Vị trí menu
    );

    // Menu con (trỏ lại cùng page)
    add_submenu_page(
        'quick-product-editor',                             // Slug menu cha
        __('Danh sách sản phẩm', 'quick-product-editor'),
        __('Danh sách sản phẩm', 'quick-product-editor'),
        'manage_woocommerce',
        'quick-product-editor',
        'qpe_page_products'
    );
});

/**
 * =====================================================
 * LOAD CSS + JS (chỉ load trong trang plugin)
 * =====================================================
 */
add_action('admin_enqueue_scripts', function ($hook) {

    // Chỉ load khi đang ở trang plugin
    if (strpos($hook, 'quick-product-editor') === false) return;

    // CSS
    wp_enqueue_style(
        'qpe-admin-css',
        plugin_dir_url(__FILE__) . 'assets/css/product.css',
        [],
        '1.1'
    );

    // JS
    wp_enqueue_script(
        'qpe-admin-js',
        plugin_dir_url(__FILE__) . 'assets/js/product.js',
        ['jquery'],
        '1.1',
        true
    );

    /**
     * Truyền biến PHP → JS
     * - ajax_url: URL admin-ajax.php
     * - nonce: bảo mật AJAX
     * - i18n: text đa ngôn ngữ cho JS
     */
    wp_localize_script('qpe-admin-js', 'QPE', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('qpe_nonce'),
        'i18n'     => [
            'saving' => __('Đang lưu...', 'quick-product-editor'),
            'saved'  => __('Đã lưu', 'quick-product-editor'),
            'error'  => __('Lỗi khi lưu', 'quick-product-editor'),
        ]
    ]);
});

/**
 * =====================================================
 * PAGE: DANH SÁCH SẢN PHẨM
 * =====================================================
 * Render bảng chỉnh sửa nhanh sản phẩm
 */
function qpe_page_products() {

    // Lấy danh sách sản phẩm (50 sp)
    $products = get_posts([
        'post_type'      => 'product',
        'posts_per_page' => 50,
        'post_status'    => 'any'
    ]);
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Danh sách sản phẩm', 'quick-product-editor'); ?></h1>

        <table class="widefat striped qpe-table">
            <thead>
                <tr>
                    <th><?php esc_html_e('Tên', 'quick-product-editor'); ?></th>
                    <th><?php esc_html_e('Giá', 'quick-product-editor'); ?></th>
                    <th><?php esc_html_e('Giá sale', 'quick-product-editor'); ?></th>
                    <th><?php esc_html_e('Mô tả ngắn', 'quick-product-editor'); ?></th>
                    <th><?php esc_html_e('Lưu', 'quick-product-editor'); ?></th>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($products as $p): ?>
                    <?php
                        // Chuyển WP_Post → WC_Product
                        $product = wc_get_product($p->ID);
                        if (!$product) continue;

                        // Chỉ cho sửa giá nếu là SIMPLE
                        $is_simple = $product->is_type('simple');
                    ?>
                    <tr data-id="<?php echo esc_attr($p->ID); ?>">

                        <!-- TÊN SẢN PHẨM -->
                        <td>
                            <input type="text"
                                   class="qpe-title"
                                   value="<?php echo esc_attr($p->post_title); ?>">
                        </td>

                        <!-- GIÁ -->
                        <td>
                            <input type="number"
                                class="qpe-price"
                                value="<?php echo esc_attr($product->get_regular_price()); ?>"
                                <?php disabled(!$is_simple); ?>>

                            <!-- Hiển thị loại sp nếu bị disable -->
                            <?php if (!$is_simple): ?>
                                <small style="color:#999">
                                    (<?php echo esc_html($product->get_type()); ?>)
                                </small>
                            <?php endif; ?>
                        </td>

                        <!-- GIÁ SALE -->
                        <td>
                            <input type="number"
                                class="qpe-sale"
                                value="<?php echo esc_attr($product->get_sale_price()); ?>"
                                <?php disabled(!$is_simple); ?>>
                        </td>

                        <!-- MÔ TẢ NGẮN -->
                        <td>
                            <textarea class="qpe-excerpt"><?php echo esc_textarea($p->post_excerpt); ?></textarea>
                        </td>

                        <!-- BUTTON LƯU -->
                        <td>
                            <button class="button button-primary qpe-save">
                                <?php esc_html_e('Lưu', 'quick-product-editor'); ?>
                            </button>
                        </td>

                    </tr>
                <?php endforeach; ?>

            </tbody>
        </table>
    </div>
    <?php
}

/**
 * =====================================================
 * AJAX: SAVE PRODUCT
 * =====================================================
 * Nhận dữ liệu từ JS và lưu sản phẩm
 */
add_action('wp_ajax_qpe_save_product', function () {

    // Check bảo mật
    check_ajax_referer('qpe_nonce', 'nonce');

    // Validate ID
    $id = intval($_POST['id']);
    if (!$id) {
        wp_send_json_error(__('ID không hợp lệ', 'quick-product-editor'));
    }

    // Lấy sản phẩm WooCommerce
    $product = wc_get_product($id);
    if (!$product) {
        wp_send_json_error(__('Không tìm thấy sản phẩm', 'quick-product-editor'));
    }

    /**
     * LUÔN cho sửa:
     * - Tên sản phẩm
     * - Mô tả ngắn
     */
    wp_update_post([
        'ID'           => $id,
        'post_title'   => sanitize_text_field($_POST['title']),
        'post_excerpt' => sanitize_textarea_field($_POST['excerpt']),
    ]);

    /**
     * CHỈ sửa giá nếu là SIMPLE
     * Variable / Grouped / External → KHÔNG sửa
     */
    if ($product->is_type('simple')) {

        $regular = wc_format_decimal($_POST['price'] ?? '');
        $sale    = wc_format_decimal($_POST['sale'] ?? '');

        $product->set_regular_price($regular);
        $product->set_sale_price($sale);
        $product->save();
    }

    // Trả kết quả thành công
    wp_send_json_success(__('Đã lưu', 'quick-product-editor'));
});