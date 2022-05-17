<?php

namespace Servers\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class MailService {
    private PHPMailer $mailer;

    public function __construct() {
        $mailer = new PHPMailer();
        $mailer->isSMTP();
        $mailer->Host = 'smtp.gmail.com';
        $mailer->SMTPAuth = true;
        $mailer->Username = 'mcservers.contact123@gmail.com';
        $mailer->Password = 'MC_SERVERS_SMTP_!@#';
        $mailer->SMTPSecure = PHPMAiler::ENCRYPTION_STARTTLS;
        $mailer->Port = 587;
        $mailer->setFrom($mailer->Username, 'ServersContact');
        $mailer->isHTML();

        $this->mailer = $mailer;
    }

    public function sendVerificationMail(string $to, int $activationCode): void {
        $activationCode = number_format($activationCode, 0, ' ', ' ');

        $this->mailer->Subject = "[MC Servers] Aktywacja konta";
        $this->mailer->Body = sprintf("
            <h2>Aktywacja konta</h2>
            <div>Żeby aktywować konto podaj poniższy kod na stronie aktywacyjnej</div>
            <div style='font-weight: bold;'>%s</div>
        ", $activationCode);
        $this->mailer->addAddress($to);

        try {
            if(!$this->mailer->send()) {
                // TODO: REDIRECT TO INTERNAL ERROR PAGE
            }
        } catch (\Exception $exception) {
            // TODO: REDIRECT TO INTERNAL ERROR PAGE
        }
    }
}
