<?php
// Sử dụng các namespace của PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Đặt header để đảm bảo phản hồi là plain text hoặc HTML
header('Content-Type: text/plain; charset=utf-8');

// 1. Kiểm tra xem form đã được gửi bằng phương thức POST chưa
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Lấy dữ liệu từ form HTML và làm sạch (sanitize)
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
    $subject_selected = isset($_POST['subject']) ? htmlspecialchars(trim($_POST['subject'])) : '';
    $message_content = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';

    // Kiểm tra các trường bắt buộc
    if (empty($name) || empty($email) || empty($subject_selected) || empty($message_content)) {
        echo "❌ Lỗi: Vui lòng điền đầy đủ các trường bắt buộc (Tên, Email, Vấn đề hỗ trợ, Nội dung tin nhắn).";
        exit();
    }

    // Để có tên chủ đề (subject text) dễ đọc hơn từ value đã gửi
    $display_subject = '';
    switch ($subject_selected) {
        case 'thong_so_ky_thuat': $display_subject = 'Thông số kỹ thuật của xe'; break;
        case 'cac_phien_ban_xe': $display_subject = 'Các phiên bản và tùy chọn'; break;
        case 'gia_ca_va_khuyen_mai': $display_subject = 'Giá cả và chương trình khuyến mãi'; break;
        case 'mau_sac_noi_that_ngoai_that': $display_subject = 'Màu sắc nội thất và ngoại thất'; break;
        case 'tinh_nang_cong_nghe': $display_subject = 'Tính năng và công nghệ trên xe'; break;
        case 'phu_kien_chinh_hang': $display_subject = 'Phụ kiện chính hãng'; break;
        case 'thoi_gian_bao_hanh': $display_subject = 'Thời gian và điều kiện bảo hành'; break;
        case 'dich_vu_bao_duong': $display_subject = 'Dịch vụ bảo dưỡng định kỳ'; break;
        case 'sua_chua_va_phu_tung': $display_subject = 'Sửa chữa và phụ tùng thay thế'; break;
        case 'dat_lich_hen_dich_vu': $display_subject = 'Đặt lịch hẹn dịch vụ'; break;
        case 'ho_tro_su_co': $display_subject = 'Hỗ trợ sự cố trên đường'; break;
        case 'quy_trinh_dat_hang': $display_subject = 'Quy trình đặt hàng xe mới'; break;
        case 'cac_hinh_thuc_thanh_toan': $display_subject = 'Các hình thức thanh toán'; break;
        case 'thu_tuc_mua_xe_tra_gop': $display_subject = 'Thủ tục mua xe trả góp'; break;
        case 'thay_doi_huy_bo_don_hang': $display_subject = 'Thay đổi hoặc hủy bỏ đơn hàng'; break;
        case 'thoi_gian_giao_xe': $display_subject = 'Thời gian giao xe dự kiến'; break;
        case 'hinh_thuc_van_chuyen': $display_subject = 'Hình thức vận chuyển xe'; break;
        case 'kiem_tra_xe_khi_nhan': $display_subject = 'Kiểm tra xe khi nhận bàn giao'; break;
        case 'thu_tuc_dang_ky_xe': $display_subject = 'Thủ tục đăng ký và biển số xe'; break;
        case 'khac': $display_subject = 'Khác'; break;
        default: $display_subject = 'Không xác định'; break;
    }

    // --- Cấu hình Database ---
    $servername = "localhost";
    $username = "root"; // Tên người dùng MySQL của bạn (thường là root)
    $password = "";     // Mật khẩu MySQL của bạn (để trống nếu không có)
    $dbname = "mclaren_contact"; // Tên database bạn đã tạo

    // Tạo kết nối database
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Kiểm tra kết nối database
    if ($conn->connect_error) {
        echo "❌ Gửi thất bại: Không thể kết nối đến cơ sở dữ liệu. Vui lòng thử lại sau.";
        // Ghi log lỗi kết nối chi tiết hơn để debug nếu cần
        // error_log("Database Connection Failed: " . $conn->connect_error);
        exit();
    }

    // --- Lưu dữ liệu vào Database ---
    // Sử dụng prepared statements để ngăn chặn SQL Injection
    $stmt = $conn->prepare("INSERT INTO messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
    if ($stmt === false) {
        echo "❌ Gửi thất bại: Lỗi chuẩn bị truy vấn database.";
        // error_log("Database Prepare Error: " . $conn->error);
        $conn->close();
        exit();
    }
    
    // Bind các tham số
    // 'sssss' nghĩa là tất cả 5 tham số đều là kiểu string (s)
    $stmt->bind_param("sssss", $name, $email, $phone, $subject_selected, $message_content);

    // Thực thi truy vấn
    if ($stmt->execute()) {
        // Dữ liệu đã được lưu thành công vào database
        // Không cần echo ở đây vì sẽ gửi phản hồi cuối cùng sau khi gửi email
    } else {
        echo "❌ Gửi thất bại: Lỗi khi lưu dữ liệu vào database. " . $stmt->error;
        // error_log("Database Insert Error: " . $stmt->error);
        $stmt->close();
        $conn->close();
        exit();
    }
    
    // Đóng statement sau khi thực thi
    $stmt->close();
    
    // 2. Nhúng thư viện PHPMailer
    // Đảm bảo đường dẫn này đúng với cấu trúc thư mục của bạn
    require 'PHPMailer-master/PHPMailer-master/src/Exception.php';
    require 'PHPMailer-master/PHPMailer-master/src/PHPMailer.php';
    require 'PHPMailer-master/PHPMailer-master/src/SMTP.php';

    $mail = new PHPMailer(true); // true để bật Exceptions

    try {
        // Cấu hình server SMTP (thông tin Gmail của bạn)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'thanhdayroi3004@gmail.com'; // Thay bằng Gmail của bạn
        $mail->Password   = 'sige ekrm ydzq jznq';       // App Password của Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Sử dụng STARTTLS cho cổng 587
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';                         // Thiết lập charset cho email
        
        // Tùy chọn: Bật gỡ lỗi SMTP nếu gặp vấn đề
        // $mail->SMTPDebug = 2; // Đặt thành 2 để xem thông tin debug
        // $mail->Debugoutput = 'html'; // Hiển thị debug output dưới dạng HTML

        // 3. Người gửi và người nhận (CÁCH 3: NGƯỜI NHẬN LÀ EMAIL NGƯỜI DÙNG)
        // **LƯU Ý QUAN TRỌNG:** Việc đặt người gửi (setFrom) là email của chính bạn
        // và người nhận (addAddress) là email của người dùng có thể gây ra
        // email bị đánh dấu spam hoặc bị từ chối bởi các máy chủ email
        // do các chính sách bảo mật (SPF/DKIM).
        
        $mail->setFrom('thanhdayroi3004@gmail.com', 'McLaren VN Contact'); // NGƯỜI GỬI là email của bạn
        $mail->addAddress($email, $name); // NGƯỜI NHẬN là email mà người dùng điền vào form
        $mail->addReplyTo($email, $name); // Địa chỉ trả lời sẽ là email của người dùng

        // 4. Nội dung email
        $mail->isHTML(true); // Đặt định dạng email là HTML
        // Chủ đề email sẽ cho biết đây là một tin nhắn liên hệ từ website
        $mail->Subject = "Tin nhắn liên hệ từ McLaren VN: " . $display_subject; 

        $body_html = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { width: 80%; margin: 20px auto; border: 1px solid #eee; padding: 20px; border-radius: 8px; background-color: #f9f9f9; }
                    h2 { color: #182a46; }
                    strong { color: #555; }
                    .message-box { border: 1px solid #ddd; padding: 15px; margin-top: 15px; background-color: #fff; border-radius: 5px; }
                    .footer { margin-top: 30px; font-size: 0.9em; color: #888; text-align: center; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h2>Chào bạn {$name},</h2>
                    <p>Bạn đã gửi một tin nhắn cho McLaren Việt Nam với thông tin sau:</p>
                    <p><strong>Email của bạn:</strong> {$email}</p>
                    <p><strong>Số điện thoại:</strong> " . ($phone ? $phone : 'Không cung cấp') . "</p>
                    <p><strong>Vấn đề hỗ trợ:</strong> {$display_subject}</p>
                    <p><strong>Nội dung tin nhắn:</strong></p>
                    <div class='message-box'>
                        <p>{$message_content}</p>
                    </div>
                    <p>Chúng tôi đã nhận được tin nhắn của bạn và sẽ phản hồi sớm nhất có thể. Cảm ơn bạn đã liên hệ!</p>
                </div>
                <div class='footer'>
                    Đây là email tự động từ website McLaren Việt Nam. Vui lòng không trả lời email này.
                </div>
            </body>
            </html>
        ";

        $mail->Body = $body_html;
        $mail->AltBody = "Chào bạn {$name},\nBạn đã gửi một tin nhắn cho McLaren Việt Nam với thông tin sau:\nEmail của bạn: {$email}\nSố điện thoại: " . ($phone ? $phone : 'Không cung cấp') . "\nVấn đề hỗ trợ: {$display_subject}\nNội dung tin nhắn: {$message_content}\nChúng tôi đã nhận được tin nhắn của bạn và sẽ phản hồi sớm nhất có thể. Cảm ơn bạn đã liên hệ!\n\nĐây là email tự động từ website McLaren Việt Nam. Vui lòng không trả lời email này.";

        // 5. Gửi email
        $mail->send();

        // 6. Gửi phản hồi thành công về client (JavaScript) sau khi cả lưu database và gửi email đều thành công
        echo '✅ Gửi tin nhắn và lưu dữ liệu thành công! Vui lòng kiểm tra email của bạn.';

    } catch (Exception $e) {
        // Gửi thông báo lỗi về client
        echo "❌ Gửi thất bại: Có lỗi xảy ra khi gửi email. Chi tiết: {$mail->ErrorInfo}";
        // Ghi log lỗi email chi tiết hơn để debug nếu cần
        // error_log("Email Sending Failed: " . $mail->ErrorInfo);
    }
    
    // Đóng kết nối database sau khi mọi thao tác hoàn tất
    $conn->close();

} else {
    // Nếu truy cập trực tiếp file PHP mà không phải từ form POST
    echo "❌ Lỗi: Vui lòng gửi biểu mẫu liên hệ.";
}