<?php

namespace Servers\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Servers\Models\User;

class MailService {
    private PHPMailer $mailer;

    public function __construct() {
        $mailer = new PHPMailer();
        $mailer->SMTPDebug = SMTP::DEBUG_SERVER;
        $mailer->isSMTP();
        $mailer->Host = $_ENV['EMAIL_HOST'];
        $mailer->SMTPAuth = true;
        $mailer->Username = $_ENV['EMAIL_USERNAME'];
        $mailer->Password = $_ENV['EMAIL_PASSWORD'];
        $mailer->SMTPSecure = $_ENV['EMAIL_PROTOCOL'] == "SSL" ? PHPMAiler::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mailer->Port = $_ENV['EMAIL_PORT'];
        $mailer->setFrom($mailer->Username, 'MinecraftServers');
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
        var_dump($this->mailer->send());
//
//        try {
//            if(!$this->mailer->send()) {
//                var_dump($this->mailer->ErrorInfo);
//                // TODO: REDIRECT TO INTERNAL ERROR PAGE
//            }
//        } catch (\Exception $exception) {
//            var_dump($exception);
//            // TODO: REDIRECT TO INTERNAL ERROR PAGE
//        }
    }

    public function sendRememberPasswordEmail(User $user): void {
        $url = $user->generateRememberPasswordURL($user->getRememberPasswordToken());

        $emailBody = "<h1>Twoj link resetujacy haslo.</h1><br><br><a target='_blank' href='$url'>Zresetuj haslo</a>";
        var_dump($emailBody);
    }
}
