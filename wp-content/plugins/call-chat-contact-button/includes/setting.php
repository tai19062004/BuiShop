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
    $position = sanitize_text_field($_POST['cccb_position'] ?? 'bottom-left');

    // Màu nút (sanitize theo chuẩn hex)
    $phoneColor = sanitize_hex_color($_POST['cccb_phone_color'] ?? '#0084ff');
    $zaloColor  = sanitize_hex_color($_POST['cccb_zalo_color'] ?? '#ff3a3a');

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

        </table>

        <!-- SUBMIT -->
        <p>
            <button type="submit" name="cccb_save" class="button button-primary">
                <?php _e('Save Settings', 'call-chat-contact-button'); ?>
            </button>
        </p>
    </form>
</div>
