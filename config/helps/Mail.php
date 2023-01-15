<?php

namespace Config\helps;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
final class Mail
{
    /**
     * @param $userMail
     * @param $userName
     * @param $message
     * @return bool
     * @throws Exception
     */
    public static function SendMail($userMail, $userName, $message): bool
    {
        try {
            $email = getenv('EMAIL_CREDENTIAL');
            $password = getenv('PASSWORD_CREDENTIAL');
            // PHPMailer instance
            $mail = new PHPMailer();

            // It is required to be able to use an SMTP server such as gmail
            $mail->isSMTP();

            //Set the hostname of the mail server
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 465;

            // Property to set encryption security for communication
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

            // To enable server SMTP authentication
            $mail->SMTPAuth = true;

            // Account credentials
            $mail->Username = $email;
            $mail->Password = $password;

            // Who sends this message
            $mail->setFrom($email, 'AnyEmail');

            // Addressee
            $mail->addAddress($userMail, 'Hola ', $userName);

            // Subject of the email
            $mail->Subject = 'Recuperacion de contraseña';

            // Content
            $mail->IsHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->MsgHTML($message);

            // Alt text
            $mail->AltBody = 'BANCA RAPIDA LE INFORMA';

            // Send the mail
            if (!$mail->send()) {
                throw new Exception($mail->ErrorInfo);
            }

            return 'Mail sent';
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }

}