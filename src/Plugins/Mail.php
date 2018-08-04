<?php

use PHPMailer\PHPMailer\PHPMailer;

class Mail
{
    public $mail;

    public function __construct()
    {
        //Create a new PHPMailer instance
        $this->mail = new PHPMailer;
        //Tell PHPMailer to use SMTP
        $this->mail->isSMTP();
        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $this->mail->SMTPDebug = 2;
        //Set the hostname of the mail server
        $this->mail->Host = 'smtp.mailtrap.io';
        //Set the SMTP port number - likely to be 25, 465 or 587
        $this->mail->Port = 25;
        //Whether to use SMTP authentication
        $this->mail->SMTPAuth = true;
        //Username to use for SMTP authentication
        $this->mail->Username = 'a8271b91a3d227';
        //Password to use for SMTP authentication
        $this->mail->Password = 'fcb4929c039a1b';
        //Set who the message is to be sent from
        $this->mail->setFrom('no-reply@tku.edu.tw', 'no-reply');
    }

    public function busReserveConfirm($email)
    {
        $msg = "you have successfully made a reservation. for more detail please check https://sso.tku.edu/ilifelytest/user/bus .";
        $this->send($email, 'iBus reservation notify', $msg);
    }

    public function send($email, $receiver, $msg)
    {
        try {
            //Set who the message is to be sent to
            $this->mail->addAddress($email, $receiver);
            //Set the subject line
            $this->mail->Subject = 'no-reply';
            //Replace the plain text body with one created manually
            $this->mail->Body = (string)$msg;


            //send the message, check for errors
            if (!$this->mail->send()) {
                echo 'Mailer Error: ' . $this->mail->ErrorInfo;
            } else {
                echo 'Message sent!';
            }
        } catch (Exception $e) {
            echo 'Message could not be sent. Mailer Error: ', $this->mail->ErrorInfo;
        }
    }
}