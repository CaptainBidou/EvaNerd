<?php 
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;

    //Load Composer's autoloader
    require 'vendor/autoload.php';

    // TODO : Support TLS 
    class Email {
        private $mailer = null;

        public function __construct($host, $username, $password, $port, $debug = 0) {
            $this->mailer = new PHPMailer(true);
            if($debug) $this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;

            $this->mailer->Host = $host; // Specify main SMTP server
            $this->mailer->Username = $username; // SMTP username
            $this->mailer->Password = $password; // SMTP password
            $this->mailer->Port = $port;
        }

        public function sendMail($sender, $mailTo, $subject, $body, $html = true) {
            $this->mailer->setFrom($sender); // Set sender of the mail
            $this->mailer->addAddress($mailTo);
            $this->mailer->isHTML($html);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->send();

        }
    }
?>