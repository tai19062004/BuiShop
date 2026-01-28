<?php
/**
 * Admin settings page for Call / Chat / Contact Button plugin
 *
 * @package Call_Chat_Contact_Button
 */

if (!defined('ABSPATH')) {
    exit; // 🚫 Ngăn truy cập trực tiếp file
}

// Mảng chứa lỗi validate
$errors  = [];

$cf7_forms = [];

if (post_type_exists('wpcf7_contact_form')) {
    // Lấy tất cả form của Contact Form 7
    $cf7_forms = get_posts([
        'post_type'      => 'wpcf7_contact_form',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'suppress_filters' => true,
    ]);
}

error_log('CF7 post type exists: ' . count($cf7_forms));

// Cờ hiển thị thông báo thành công
$success = false;

/**
 * ============================
 * HANDLE FORM SUBMIT
 * ============================
 */
if (isset($_POST['cccb_save'])) {

    /**
     * 🔐 SECURITY: Nonce check
     * Ngăn CSRF attack
     */
    if (
        !isset($_POST['cccb_nonce']) ||
        !wp_verify_nonce($_POST['cccb_nonce'], 'cccb_save_settings')
    ) {
        wp_die(__('Security check failed', 'call-chat-contact-button'));
    }

    /**
     * ============================
     * GET & SANITIZE INPUT
     * ============================
     */

    // Raw input (giữ nguyên để validate +)
    $phoneRaw = trim($_POST['cccb_phone'] ?? '');
    $zaloRaw  = trim($_POST['cccb_zalo'] ?? '');

    // Vị trí hiển thị
    // Làm sạch text để tránh XSS
    // ❌ Loại bỏ HTML / JS (<script>, <b>, <img>…)
    // ❌ Loại bỏ ký tự nguy hiểm
    // ❌ Xóa xuống dòng, tab dư thừa
    $position = sanitize_text_field($_POST['cccb_position'] ?? 'bottom-left');

    // Màu nút (sanitize theo chuẩn hex)
    // Ví dụ: #ff0000
    $phoneColor = sanitize_hex_color($_POST['cccb_phone_color'] ?? '#0084ff');
    $zaloColor  = sanitize_hex_color($_POST['cccb_zalo_color'] ?? '#ff3a3a');

    // absint chỉ cho phép số nguyên dương tránh XSS, SQL Injection
    $form_id = absint($_POST['cccb_cf7_form_id'] ?? 0);

    // Màu nút liên hệ
    $formColor = sanitize_hex_color($_POST['cccb_form_color'] ?? '#00b894');

    /**
     * ============================
     * VALIDATION
     * ============================
     * Chuẩn E.164:
     * - Có thể có dấu +
     * - 9 → 15 chữ số
     */

    if ($phoneRaw && !preg_match('/^\+?\d{9,15}$/', $phoneRaw)) {
        $errors[] = __('Invalid phone number', 'call-chat-contact-button');
    }

    if ($zaloRaw && !preg_match('/^\+?\d{9,15}$/', $zaloRaw)) {
        $errors[] = __('Invalid Zalo number', 'call-chat-contact-button');
    }

    // Validate Contact Form 7 form ID
    if ($form_id && get_post_type($form_id) !== 'wpcf7_contact_form') {
        $errors[] = __('Invalid Contact Form selected', 'call-chat-contact-button');
    }

    /**
     * ============================
     * SAVE DATA
     * ============================
     */
    if (empty($errors)) {

        /**
         * Chuẩn hoá số:
         * - Giữ số
         * - Bỏ dấu +
         * → khi render frontend sẽ thêm lại +
         */
        $phone = preg_replace('/[^\d]/', '', $phoneRaw);
        $zalo  = preg_replace('/[^\d]/', '', $zaloRaw);

        // Lưu option vào database
        update_option('cccb_phone', $phone);
        update_option('cccb_zalo', $zalo);
        update_option('cccb_position', $position);
        update_option('cccb_phone_color', $phoneColor);
        update_option('cccb_zalo_color', $zaloColor);
        update_option('cccb_cf7_form_id', $form_id);
        update_option('cccb_form_color', $formColor);
        $success = true;
    }
}
?>

<!-- ============================
     ADMIN UI
============================ -->

<div class="wrap">
    <h1><?php _e('Call / Zalo Button Settings', 'call-chat-contact-button'); ?></h1>

    <!-- ✅ Thông báo lưu thành công -->
    <?php if ($success): ?>
        <div class="notice notice-success is-dismissible">
            <p>✅ <?php _e('Settings saved successfully', 'call-chat-contact-button'); ?></p>
        </div>
    <?php endif; ?>

    <!-- ❌ Hiển thị lỗi -->
    <?php if (!empty($errors)): ?>
        <div class="notice notice-error">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li>❌ <?php echo esc_html($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- ============================
         SETTINGS FORM
    ============================ -->
    <form method="post">
        <?php
        /**
         * Nonce field
         */
        wp_nonce_field('cccb_save_settings', 'cccb_nonce');
        ?>

        <table class="form-table">

            <!-- PHONE -->
            <tr>
                <th><?php _e('Phone number', 'call-chat-contact-button'); ?></th>
                <td>
                    <input type="text"
                           name="cccb_phone"
                           class="regular-text"
                           placeholder="+84987654321"
                           value="<?php echo esc_attr(get_option('cccb_phone')); ?>">

                    <input type="color"
                           name="cccb_phone_color"
                           value="<?php echo esc_attr(get_option('cccb_phone_color', '#0084ff')); ?>">
                </td>
            </tr>

            <!-- ZALO -->
            <tr>
                <th><?php _e('Zalo number', 'call-chat-contact-button'); ?></th>
                <td>
                    <input type="text"
                           name="cccb_zalo"
                           class="regular-text"
                           placeholder="+84987654321"
                           value="<?php echo esc_attr(get_option('cccb_zalo')); ?>">

                    <input type="color"
                           name="cccb_zalo_color"
                           value="<?php echo esc_attr(get_option('cccb_zalo_color', '#ff3a3a')); ?>">
                </td>
            </tr>

            <!-- POSITION -->
            <tr>
                <th scope="row"><?php _e('Display position', 'call-chat-contact-button'); ?></th>
                <td>
                    <?php $pos = get_option('cccb_position', 'bottom-left'); ?>
                    <select name="cccb_position">
                        <option value="bottom-left" <?php selected($pos, 'bottom-left'); ?>>
                            <?php _e('Bottom left', 'call-chat-contact-button'); ?>
                        </option>
                        <option value="top-left" <?php selected($pos, 'top-left'); ?>>
                            <?php _e('Top left', 'call-chat-contact-button'); ?>
                        </option>
                        <option value="bottom-right" <?php selected($pos, 'bottom-right'); ?>>
                            <?php _e('Bottom right', 'call-chat-contact-button'); ?>
                        </option>
                        <option value="top-right" <?php selected($pos, 'top-right'); ?>>
                            <?php _e('Top right', 'call-chat-contact-button'); ?>
                        </option>
                    </select>
                </td>
            </tr>

            <!-- CONTACT FORM -->
            <tr>
                <th><?php _e('Contact form (CF7)', 'call-chat-contact-button'); ?></th>
                <td>
                    <?php
                    $selected_form = get_option('cccb_cf7_form_id');
                    ?>

                    <select name="cccb_cf7_form_id">
                        <option value="">
                            <?php _e('-- Select a contact form --', 'call-chat-contact-button'); ?>
                        </option>

                        <?php foreach ($cf7_forms as $form): ?>
                            <option value="<?php echo esc_attr($form->ID); ?>"
                                <?php selected($selected_form, $form->ID); ?>>
                                <?php echo esc_html($form->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label>
                        <input type="color"
                            name="cccb_form_color"
                            value="<?php echo esc_attr(get_option('cccb_form_color', '#00b894')); ?>">
                    </label>
                </td>
            </tr>
        </table>

        <!-- SUBMIT -->
        <p>
            <button type="submit" name="cccb_save" class="button button-primary">
                <?php _e('Save Settings', 'call-chat-contact-button'); ?>
            </button>
        </p>
    </form>
</div>
