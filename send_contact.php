<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = "hvshop@gmail.com"; 
    $subject = $_POST['subject'];
    $message = "Từ: " . $_POST['fullname'] . "\n";
    $message .= "Email: " . $_POST['email'] . "\n";
    $message .= "Số điện thoại: " . $_POST['phone'] . "\n\n";
    $message .= $_POST['message'];
    
    $headers = "From: " . $_POST['email'];

    if (mail($to, $subject, $message, $headers)) {
        echo "<script>
            alert('Gửi liên hệ thành công!');
            window.location.href = 'contact.php';
        </script>";
    } else {
        echo "<script>
            alert('Có lỗi xảy ra, vui lòng thử lại!');
            window.history.back();
        </script>";
    }
}
?> 