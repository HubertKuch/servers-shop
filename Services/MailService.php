<?php

namespace Servers\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Servers\Models\Server;
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
        $mailer->setFrom($_ENV['EMAIL_FROM'], $_ENV['EMAIL_FROM'],);
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
                var_dump($this->mailer->ErrorInfo);
                // TODO: REDIRECT TO INTERNAL ERROR PAGE
            }
        } catch (\Exception $exception) {
            var_dump($exception);
            // TODO: REDIRECT TO INTERNAL ERROR PAGE
        }
    }

    public function sendRememberPasswordEmail(User $user): void {
        $url = $user->generateRememberPasswordURL();

        $emailBody = "<h1>Twoj link resetujacy haslo.</h1><br><br><a target='_blank' href='$url'>Zresetuj haslo</a>";


        $this->mailer->Body = $emailBody;
        $this->mailer->Subject = "[MC Servers] Resetowanie hasla";
        $this->mailer->addAddress($user->getEmail());

        $this->mailer->send();
    }

    public function sendServerExpiredEmail(User $user, Server $server) {
        $deleteAfterTimeType = $_ENV['DELETE_SERVER_SINCE_TYPE'];
        $deleteAfterTime = $_ENV['DELETE_SERVER_SINCE'];

        $timeToNotification = match ($deleteAfterTimeType) {
            "MONTHS" => "miesiecy",
            "MINUTES" => "minut",
            default => "dni"
        };

        $body = "
            <h1>
                Twoj server `{$server->getTitle()}` wygasl. Jesli nie zostanie oplacony
                w ciagu {$deleteAfterTime}{$timeToNotification} zostanie usuniety.
            </h1>";

        $this->mailer->Body = $body;
        $this->mailer->Subject = "[MC Servers] Serwer wygasl";
        $this->mailer->addAddress($user->getEmail());

        $this->mailer->send();
    }

    public function sendServerDeletedEmail(User $user, Server $server) {
        $deleteAfterTimeType = $_ENV['DELETE_SERVER_SINCE_TYPE'];

        $body = "
            <h1>
                Twoj server `{$server->getTitle()}` zostal usuniety z powodu nie uiszczenia platnosci w terminie..
            </h1>";

        $this->mailer->Body = $body;
        $this->mailer->Subject = "[MC Servers] Serwer usuniety";
        $this->mailer->addAddress($user->getEmail());

        $this->mailer->send();
    }
}
