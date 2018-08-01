<?php
//Import the PHPMailer class into the global namespace
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
    }

    public function send($email, $receiver, $msg)
    {
        try {
            //Set who the message is to be sent from
            $this->mail->setFrom('no-reply@tku.edu.tw', 'no-reply');
            //Set who the message is to be sent to
            $this->mail->addAddress($email, $receiver);
            //Set the subject line
            $this->mail->Subject = 'no-reply';
            //Replace the plain text body with one created manually
            $this->mail->AltBody = $msg;


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