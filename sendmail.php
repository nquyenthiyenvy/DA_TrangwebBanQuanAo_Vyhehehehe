<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'PHPMailer-master\src\Exception.php';
    require 'PHPMailer-master\src\PHPMailer.php';
    require 'PHPMailer-master\src\SMTP.php';

    
    //Hàm gửi mail
    function sendMail($toemail, $code){
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail-> Host = 'smtp.gmail.com';
            $mail->SMTPAuth=true;
            $mail->Username='hungly2452003@gmail.com';
            $mail->Password = 'isncvqrmrvfryxiw';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
    
            $mail->setFrom('hungly2452003@gmail.com');
            $mail->addAddress($toemail);

            $mail->isHTML(true);
            $mail->FromName='H&V Store';
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'H&V Store - Cập nhật mật khẩu mới';
            $mail->Body = 'Mã xác thực quên mật khẩu của bạn là:'." ".$code;

            $mail->send();
            return true;
        } catch (Exception  $e) {
            error_log('Lỗi gửi email: ' . $mail->ErrorInfo);
            return false;
        }
    }
    
?>