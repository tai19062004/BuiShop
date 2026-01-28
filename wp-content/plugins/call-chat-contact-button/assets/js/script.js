jQuery(document).ready(function ($) {
    $('.cccb-main').on('click', function () {
        $('.cccb-item').toggle();
    });
});

document.addEventListener('DOMContentLoaded', function () {

    const btn = document.querySelector('.cccb-btn.contact');
    const popup = document.querySelector('.cccb-popup-overlay');
    const close = document.querySelector('.cccb-close');

    if (!btn || !popup) return;

    btn.onclick = () => popup.classList.add('active');
    close.onclick = () => popup.classList.remove('active');

    popup.onclick = e => {
        if (e.target === popup) popup.classList.remove('active');
    };
});

// CF7 gửi thành công → tự đóng popup
document.addEventListener('wpcf7mailsent', function () {
    const popup = document.querySelector('.cccb-popup-overlay');
    if (popup) popup.classList.remove('active');
});
