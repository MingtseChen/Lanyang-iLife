<?php

use PHPMailer\PHPMailer\PHPMailer;

class Mail
{
    public $mail;

    public function __construct()
    {
        //Create a new PHPMailer instance
        $this->mail = new PHPMailer;
        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $this->mail->SMTPDebug = 3;
        //Charset for Chinese
        $this->mail->CharSet = 'UTF-8';
        //Set the hostname of the mail server
        $this->mail->Host = 'smtp.tku.edu.tw';
        //Set the SMTP port number - likely to be 25, 465 or 587
        $this->mail->Port = 25;
        //Whether to use SMTP authentication
        $this->mail->SMTPAuth = true;
        //Set who the message is to be sent from
        $this->mail->setFrom('no-reply@mail.tku.edu.tw', 'web-admin');
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

    public function packageConfirm($email)
    {
        $msg = "New package arrived go check it out!";
        $this->send($email, 'package notify', $msg);
    }

    public function repairNotify($email)
    {
        $msg = "您有新的報修項目 請儘速查看 https://sso.tku.edu/ilifelytest/admin";
        $this->send($email, '報修通知', $msg);
    }

    public function repairCallNotify($email, $msg)
    {
        $msg = "您的報修項目有新留言:  \"" . $msg . "\"\n請儘速查看 https://sso.tku.edu/ilifelytest/admin";
        $this->send($email, '報修催件通知', $msg);
    }
}