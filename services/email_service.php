<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function send_email($to, $subject, $body)
{
    $config = require '../config/email_config.php';
    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host       = $config['smtp']['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['smtp']['username'];
        $mail->Password   = $config['smtp']['password'];
        $mail->SMTPSecure = $config['smtp']['encryption'];
        $mail->Port       = $config['smtp']['port'];
        $mail->CharSet    = $config['smtp']['charset'];

        $mail->setFrom($config['smtp']['from_email'], $config['smtp']['from_name']);
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

function send_otp_email($to, $otp)
{
    $subject = 'One-time Passcode';

    $body = file_get_contents('../templates/email_template_otp_code.html');
    $body = str_replace('%OTP%', $otp, $body);

    return send_email($to, $subject, $body);
}

function send_reset_password_link($to, $link)
{
    $subject = 'Reset Password';

    $body = file_get_contents('../templates/email_template_reset_password.html');
    $body = str_replace('%LINK%', $link, $body);

    return send_email($to, $subject, $body);
}

function send_verify_account_link($to, $link)
{
    $subject = 'Verify Account';

    $body = file_get_contents('../templates/email_template_verify_account.html');
    $body = str_replace('%LINK%', $link, $body);

    return send_email($to, $subject, $body);
}
