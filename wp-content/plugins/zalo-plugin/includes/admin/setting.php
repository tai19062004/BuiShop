<?php
if (!defined('ABSPATH')) exit;

$errors  = [];
$success = false;

if (isset($_POST['cccb_save'])) {

    $phone_raw = trim($_POST['phone'] ?? '');
    $zalo_raw  = trim($_POST['zalo_phone'] ?? '');

    $position = sanitize_text_field($_POST['cccb_position'] ?? 'bottom-left');

    // ✅ ĐÚNG CHO COLOR
    $phone_color = sanitize_hex_color($_POST['phone_color'] ?? '#0084ff');
    $zalo_color  = sanitize_hex_color($_POST['zalo_color'] ?? '#ff3a3a');

    // Chỉ giữ số
    $phone = preg_replace('/\D/', '', $phone_raw);
    $zalo  = preg_replace('/\D/', '', $zalo_raw);

    // Validate PHONE
    if (!empty($phone_raw) && !preg_match('/^0\d{8,10}$/', $phone)) {
        $errors[] = 'Số điện thoại không hợp lệ (9–11 số, bắt đầu bằng 0)';
    }

    // Validate ZALO
    if (!empty($zalo_raw) && !preg_match('/^0\d{8,10}$/', $zalo)) {
        $errors[] = 'Số Zalo không hợp lệ';
    }

    if (empty($errors)) {
        update_option('phone', $phone);
        update_option('zalo_phone', $zalo);
        update_option('cccb_position', $position);
        update_option('cccb_phone_color', $phone_color);
        update_option('cccb_zalo_color', $zalo_color);

        $success = true;
    }
}
?>

<div class="wrap">
    <h1>Call / Zalo Button Settings</h1>

    <?php if ($success) : ?>
        <div class="notice notice-success is-dismissible">
            <p>✅ Đã lưu cài đặt thành công</p>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)) : ?>
        <div class="notice notice-error">
            <ul>
                <?php foreach ($errors as $err) : ?>
                    <li>❌ <?php echo esc_html($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post">
        <table class="form-table">

            <tr>
                <th>Số điện thoại gọi</th>
                <td>
                    <input type="text" name="phone"
                        value="<?= esc_attr(get_option('phone')) ?>"
                        class="regular-text">

                    <input type="color"
                        name="phone_color"
                        value="<?= esc_attr(get_option('cccb_phone_color', '#0084ff')) ?>">
                </td>
            </tr>

            <tr>
                <th>Zalo</th>
                <td>
                    <input type="text" name="zalo_phone"
                        value="<?= esc_attr(get_option('zalo_phone')) ?>"
                        class="regular-text">

                    <input type="color"
                        name="zalo_color"
                        value="<?= esc_attr(get_option('cccb_zalo_color', '#ff3a3a')) ?>">
                </td>
            </tr>

            <tr>
                <th scope="row">Tùy chỉnh vị trí hiển thị</th>
                <td>
                    <select name="cccb_position">
                        <?= $poc = get_option('cccb_position', 'bottom-left') ?>
                        <option value="bottom-left" <?= selected($poc, 'bottom-left') ?>>Góc trái dưới</option>
                        <option value="top-left" <?= selected($poc, 'top-left') ?>>Góc trái trên</option>
                        <option value="bottom-right" <?= selected($poc, 'bottom-right') ?>>Góc phải dưới</option>
                        <option value="top-right" <?= selected($poc, 'top-right') ?>>Góc phải trên</option>
                    </select>
                </td>
            </tr>

        </table>

        <p>
            <button type="submit" name="cccb_save" class="button button-primary">
                Lưu cài đặt
            </button>
        </p>
    </form>
</div>
