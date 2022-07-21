<?php

namespace Servers\Services;

use Avocado\Router\AvocadoRequest;
use Servers\Controllers\AuthController;
use Servers\Controllers\LogsController;
use Servers\Models\enumerations\LogType;
use Servers\Models\enumerations\PaymentMethods;
use Servers\Models\enumerations\PaymentStatus;
use Servers\Models\enumerations\PaymentType;
use Servers\Models\Log;
use Servers\Models\Notification;
use Servers\Models\Payment;
use Servers\Models\User;
use Servers\Repositories;
use Servers\Utils\Environment;

class PaymentsService {
    const PAYMENT_DUE_ENV_NAMES = [
        4   => "PAYPAL_DUE",
        8   => "PSC_DUE",
        32  => "G2A_DUE",
        64  => "SMS_PLUS_DUE",
        128 => "CASH_BILL_DUE",
        256 => "SMS_DUE",
    ];

    public static function validateAmountRequest(AvocadoRequest $req): void {
        $paymentMethodId = $req->body['payment_id'] ?? null;
        $amount = $req->body['amount'] ?? null;

        if (!$paymentMethodId)
            AuthController::redirect("recharge", ["message" => "Metoda płatności nie jest ustawiona"]);

        if (!floatval($amount))
            AuthController::redirect("recharge", ["message" => "Niepoprawna kwota"]);

        if (!$amount)
            AuthController::redirect("recharge", ["message" => "Kwota nie może być pusta lub niższa od 1PLN"]);

        $paymentMethod = PaymentMethods::tryFrom($paymentMethodId);

        if (!$paymentMethod)
            AuthController::redirect("recharge", ["message" => "Nie znana metoda płatności"]);

        $paymentMethod = PaymentMethods::tryFrom($paymentMethodId);

        if (!$paymentMethod)
            AuthController::redirect("recharge", ["message" => "Nie znana metoda płatności"]);

        $paymentDueEnvName = self::PAYMENT_DUE_ENV_NAMES[$paymentMethod->value] ?? null;

        if (!$paymentDueEnvName || !$_ENV[$paymentDueEnvName])
            AuthController::redirect("recharge", ["message" => "Niepoprawna metoda płatności"]);
    }

    public static function createAmountRequest(AvocadoRequest $req): void {
        AuthController::authenticationMiddleware();

        $paymentMethodId = $req->body['payment_id'] ?? null;
        $amount = $req->body['amount'] ?? null;
        $paymentMethod = PaymentMethods::tryFrom($paymentMethodId);
        $email = $req->body['email'] ?? null;

        self::validateAmountRequest($req);

        $user = Repositories::$userRepository->findOneById($_SESSION['id']);
        $principalUser = $user;

        $subjectUser = $user;
        $principal = $user->getUsername();
        $paymentType = PaymentType::OWN;

        if ($email) {
            $user = Repositories::$userRepository->findOne(["email" => $email]);

            if (!$user) {
                AuthController::redirect('recharge-friend', ["message" => "Użytkownik z emailem $email nie istnieje."]);
            }

            $paymentType = PaymentType::FUND;
        }

        $title = "Doładowanie konta {$user->getUsername()}";

        $paymentResponse = self::createPaymentRequest($amount, $paymentMethod, $title);

        if (!$paymentResponse['success'])
            AuthController::redirect('recharge-friend', ["message" => "Płatność nie powiodła się. Spróbuj ponownie lub skontaktuj się z administratorem domeny."]);

        $url = $paymentResponse['data']['url'];

        $payment = self::createPayment($req, $paymentResponse, $principalUser, $paymentType, $email ? $user : null);
        Repositories::$paymentsRepository->save($payment);

        $paymentFromDb = Repositories::$paymentsRepository->findOne([
            "tid" => $payment->getTid()
        ]);

        $paymentDue = $payment->calculateDue(self::PAYMENT_DUE_ENV_NAMES[$paymentMethodId]);
        $formatAmount = Environment::domainNumberFormat($amount);

        if ($email) {
            Repositories::$notificationsRepository->save(
                new Notification("Zleciles doladowanie konta uzytkownika: {$user->getUsername()}, kwota $formatAmount PLN (po prowizji {$paymentDue}PLN).", time(), $subjectUser)
            );

            Repositories::$notificationsRepository->save(
                new Notification("Uzytkownik $principal zlecil doladowanie Twojego konta kwota $formatAmount PLN (po prowizji ${paymentDue}PLN).", time(), $user)
            );
        }

        LogsController::savePaymentLog($paymentFromDb->getId());

        header("Location: $url");
    }

    public static function createPayment(AvocadoRequest $req, array $paymentResponse, User $user, PaymentType $paymentType, User $userToCharge = null): Payment {
        AuthController::authenticationMiddleware();

        $paymentMethodId = $req->body['payment_id'] ?? null;
        $amount = $req->body['amount'] ?? null;
        $tid = $paymentResponse['data']['tid'];
        $ip = self::getIPAddress();
        $now = time();
        $paymentMethod = PaymentMethods::tryFrom($paymentMethodId);

        return new Payment(NULL, $now, $ip, PaymentStatus::INCOMING->value, $amount, NULL, $paymentMethod, $user->getId(), $tid, $userToCharge?->getId(), $paymentType);
    }

    public static function getIPAddress(): string {
        $ip = "";

        if (isset($_SERVER['REMOTE_ADDR'])) $ip = $_SERVER['REMOTE_ADDR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_CLIENT_IP'])) $ip = $_SERVER['HTTP_CLIENT_IP'];

        return $ip;
    }

    public static function paymentNotify(AvocadoRequest $req): void {
        $status = $req->body['status'];
        $key = $req->body['key'];
        $tid = $req->body['tid'];

        /** @var $payment Payment*/
        $payment = Repositories::$paymentsRepository->findOne(["tid" => $tid]);
        $user = Repositories::$userRepository->findOneById($payment->getUserId());

        if (!($key === $_ENV['PUBLIC_KEY'])) { return; }

        if (intval($status) === 3) {
            self::resolvePayment($payment, $user);
        }

        if (intval($status) === 4 || intval($status) === 5) {
            self::rejectPayment($payment);
            echo "error";
        }
    }

    private static function resolvePayment(Payment $payment, User $user): void {
        Repositories::$paymentsRepository->updateOneById([
            "paymentDate" => time(),
            "payment_status" => 3,
            "status" => PaymentStatus::RESOLVED->value,
            "wallet_after_operation" => $payment->getPaymentType() == PaymentType::OWN ? $user->getWallet() + $payment->getAfterDue() : NULL
        ], $payment->getId());

        if ($payment->getPaymentType() == PaymentType::FUND) {
            /* @var $principalUser User */
            $user = Repositories::$userRepository->findOneById($payment->getChargedUserId());
            $principalUser = Repositories::$userRepository->findOneById($payment->getUserId());

            $notificationForPrincipal = new Notification("Uzytkownik {$user->getUsername()} otrzymal Twoja wplate {$payment->getSum()}PLN (po prowizji {$payment->getAfterDue()}PLN)", time(), $principalUser);
            $notificationForChargedUp = new Notification("Twoje konto zostalo doladowane przez {$principalUser->getUsername()} kwota {$payment->getSum()}PLN (po prowizji {$payment->getAfterDue()}PLN)", time(), $user);

            $principalUserLog = new Log(LogType::PAYMENT->value, $principalUser->getId(), null, null, "Uzytkownik `{$principalUser->getUsername()}` doladowal konto `{$user->getUsername()}` kwota {$payment->getSum()}PLN (po prowizji {$payment->getAfterDue()}PLN)");
            $chargedUpUserLog = new Log(LogType::PAYMENT->value, $user->getId(), null, null, "Uzytkownik {$user->getUsername()} otrzymal wplace od `{$principalUser->getUsername()}` na kwote {$payment->getSum()}PLN (po prowizji {$payment->getAfterDue()}PLN)");

            Repositories::$notificationsRepository->saveMany($notificationForChargedUp, $notificationForPrincipal);
            Repositories::$logsRepository->saveMany($chargedUpUserLog, $principalUserLog);
        }

        self::fundAccount($user, $payment);
        echo "OK";
    }

    private static function fundAccount(User $user, Payment $payment): void {
        $paymentMethodId = intval($payment->getMethod());

        Repositories::$userRepository->updateOneById([
           "wallet" => $user->getWallet() + $payment->calculateDue(self::PAYMENT_DUE_ENV_NAMES[$paymentMethodId])
        ], $user->getId());
    }

    private static function rejectPayment(Payment $payment): void {
        Repositories::$paymentsRepository->updateOneById([
            "status" => PaymentStatus::REJECTED->value
        ], $payment->getId());
    }

    public static function createPaymentRequest(float $sum, PaymentMethods $method, string $title): array {
        $signature = self::createPaymentSignature($title, $method, $sum);

        return self::sendPaymentRequest([
            "key"   => $_ENV['PUBLIC_KEY'],
            "method" => $method->value,
            "price" => $sum,
            "title" => $title,
            "url_return" => $_ENV['RETURN_URL'],
            "url_notify" => $_ENV['NOTIFY_URL'],
            "signature" => $signature,
        ]);
    }

    private static function createPaymentSignature(string $title, PaymentMethods $paymentMethod, float $price): string {
        return hash_hmac("SHA256",
            $_ENV['PUBLIC_KEY'].$paymentMethod->value.$price.$title.$_ENV['RETURN_URL'].$_ENV['NOTIFY_URL'],
            $_ENV['PRIVATE_KEY']);
    }

    private static function sendPaymentRequest($data = array()): array{
        $ch = curl_init();
        if($ch){
            $dataJSON = json_encode($data);

            curl_setopt($ch, CURLOPT_URL, $_ENV['GATEWAY_URL']);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataJSON);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_ENCODING, "");
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
            ));
            $result = curl_exec($ch);

            if($result !== false){
                $json = json_decode($result, true);
                if($json === null){
                    throw new Exception($result . ' - Invalid json response. ('.json_last_error().') '.json_last_error_msg());
                }

                return $json;
            } else {
                return [];
            }
        }

        return [];
    }
}
