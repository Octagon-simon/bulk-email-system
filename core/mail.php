<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendMail($user_email, $user_name, $subject, $body)
{
    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);
    try {
        //Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER; //Enable verbose debug output
        $mail->isSMTP(); //Send using SMTP
        $mail->Host = MAIL_HOST; //Set the SMTP server to send through
        $mail->SMTPAuth = MAIL_SMTP_AUTH; //Enable SMTP authentication
        $mail->Username = MAIL_UNAME; //SMTP username
        $mail->Password = MAIL_PASS; //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; //Enable implicit TLS encryption
        $mail->Port = MAIL_SMTP_PORT; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom(MAIL_UNAME, MAIL_SENDER);
        $mail->addAddress($user_email, $user_name); //Add a recipient
        // $mail->addAddress('ellen@example.com'); //Name is optional
        // $mail->addReplyTo('info@example.com', 'Information');
        $mail->addCC(MAIL_CC);
        $mail->addBCC(MAIL_BCC);

        //Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz'); //Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg'); //Optional name

        //Content
        $mail->isHTML(true); //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body =  $body;
        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        return 1;
    } catch (Exception $e) {
        echo "Message could not be sent to $user_email. Mailer Error: {$mail->ErrorInfo}";
        return 0;
    }
}
?>