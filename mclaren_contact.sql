--
-- Cấu trúc database cho 'mclaren_contact'
--
CREATE DATABASE IF NOT EXISTS `mclaren_contact` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `mclaren_contact`;

--
-- Cấu trúc bảng cho bảng `messages`
--

DROP TABLE IF EXISTS `messages`; -- Xóa bảng cũ nếu nó tồn tại để tạo mới

CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dữ liệu mẫu cho bảng `messages` (Tùy chọn)
-- Bạn có thể xóa phần này nếu không muốn chèn dữ liệu mẫu
--

INSERT INTO `messages` (`name`, `email`, `phone`, `subject`, `message`, `created_at`) VALUES
('Nguyễn Văn A', 'nguyenvana@example.com', '0912345678', 'thong_so_ky_thuat', 'Tôi muốn biết thêm về thông số kỹ thuật của mẫu P1.', '2025-05-23 08:00:00'),
('Trần Thị B', 'tranthib@example.com', NULL, 'gia_ca_va_khuyen_mai', 'Có chương trình khuyến mãi nào cho mẫu Artura không?', '2025-05-23 09:30:00'),
('Phạm Quang C', 'phamquangc@example.com', '0987654321', 'dich_vu_bao_duong', 'Lịch bảo dưỡng định kỳ cho xe 720S là khi nào?', '2025-05-23 10:45:00');

-- Hết file SQL