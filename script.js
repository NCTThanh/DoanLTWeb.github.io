// JavaScript External - Các script xử lý sự kiện chung (ví dụ)
document.addEventListener('DOMContentLoaded', function() {
    console.log('Trang web đã tải hoàn tất!');

    // Ví dụ về xử lý sự kiện trên một nút (nếu bạn có nút cụ thể với ID)
    const contactButton = document.querySelector('.hero a.button');
    if (contactButton) {
        contactButton.addEventListener('click', function(event) {
            console.log('Nút "Xem các mẫu xe" đã được nhấp!');
            // Bạn có thể thêm hành động khác ở đây, ví dụ: chuyển trang
        });
    }
});
document.addEventListener('DOMContentLoaded', function() {
    var navbar = document.querySelector('header');
    var lastScrollTop = 0;

    window.addEventListener('scroll', function() {
        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        if (scrollTop > lastScrollTop) {
            // Đang cuộn xuống
            navbar.style.transform = 'translateY(-100%)'; // Trượt tiêu đề lên
        } else {
            // Đang cuộn lên
            navbar.style.transform = 'translateY(0)';    // Trượt tiêu đề xuống
        }

        lastScrollTop = scrollTop;
    });
});
