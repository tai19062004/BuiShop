jQuery(function ($) {

    /**
     * =====================================================
     * CLICK NÚT "LƯU" TRÊN TỪNG DÒNG SẢN PHẨM
     * =====================================================
     */
    $('.qpe-save').on('click', function () {

        /**
         * -------------------------------------------------
         * 1. LẤY DÒNG <tr> CHỨA SẢN PHẨM
         * -------------------------------------------------
         * Mỗi dòng tương ứng 1 product ID
         */
        let row = $(this).closest('tr');

        /**
         * -------------------------------------------------
         * 2. GOM DỮ LIỆU GỬI LÊN AJAX
         * -------------------------------------------------
         * Dữ liệu này map trực tiếp với PHP $_POST
         */
        let data = {
            action: 'qpe_save_product',   // Hook wp_ajax_
            nonce: QPE.nonce,             // Nonce bảo mật
            id: row.data('id'),           // ID sản phẩm
            title: row.find('.qpe-title').val(),     // Tên sản phẩm
            price: row.find('.qpe-price').val(),     // Giá thường
            sale: row.find('.qpe-sale').val(),       // Giá sale
            excerpt: row.find('.qpe-excerpt').val()  // Mô tả ngắn
        };

        /**
         * -------------------------------------------------
         * 3. DISABLE NÚT + HIỂN THỊ TRẠNG THÁI
         * -------------------------------------------------
         */
        let btn = $(this);
        btn.prop('disabled', true).text(QPE.i18n.saving);

        /**
         * -------------------------------------------------
         * 4. GỬI AJAX LÊN ADMIN-AJAX.PHP
         * -------------------------------------------------
         */
        $.post(QPE.ajax_url, data, function (res) {

            /**
             * Hoàn tất → bật lại nút
             */
            btn.prop('disabled', false).text('Lưu');

            /**
             * -------------------------------------------------
             * 5. XỬ LÝ KẾT QUẢ TRẢ VỀ
             * -------------------------------------------------
             */
            if (res.success) {

                /**
                 * Hiển thị icon ✔ thành công
                 */
                btn.after('<span class="qpe-ok">✔</span>');

                /**
                 * Tự động ẩn icon sau 0.8s
                 */
                setTimeout(() => {
                    row.find('.qpe-ok').fadeOut(300, function () {
                        $(this).remove();
                    });
                }, 800);

            } else {
                /**
                 * Có lỗi từ server
                 */
                alert(QPE.i18n.error);
            }
        });

    });

});
