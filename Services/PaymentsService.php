<?php

namespace Servers\Services;

use Avocado\Router\AvocadoRequest;
use Avocado\Router\AvocadoResponse;
use Servers\Controllers\AuthController;
use Servers\Controllers\LogsController;
use Servers\Models\Payment;
use Servers\Models\PaymentMethods;
use Servers\Models\PaymentStatus;
use Servers\Repositories;

class PaymentsService {
    const PAYMENT_DUE_ENV_NAMES = [
        4   => "PAYPAL_DUE",
        8   => "PSC_DUE",
        32  => "G2A_DUE",
        64  => "SMS_PLUS_DUE",
        128 => "CASH_BILL_DUE",
        256 => "SMS_DUE",
    ];

    public static function createAmountRequest(AvocadoRequest $req): void {
        AuthController::authenticationMiddleware();

        $paymentMethodId = $req->body['payment_id'] ?? null;
        $amount = $req->body['amount'] ?? null;

        if (!$paymentMethodId)
            AuthController::redirect("panel", ["message" => "Metoda płatności nie jest ustawiona"]);

        if (!$amount)
            AuthController::redirect("panel", ["message" => "Kwota nie może być pusta lub niższa od 1PLN"]);

        $paymentMethod = PaymentMethods::tryFrom($paymentMethodId);

        if (!$paymentMethod)
            AuthController::redirect("panel", ["message" => "Nie znana metoda płatności"]);

        $paymentDueEnvName = self::PAYMENT_DUE_ENV_NAMES[$paymentMethod->value] ?? null;

        if (!$paymentDueEnvName || !$_ENV[$paymentDueEnvName])
            AuthController::redirect("panel", ["message" => "Niepoprawna metoda płatności"]);

        $user = Repositories::$userRepository->findOneById($_SESSION['id']);

        $title = "Doładowanie konta $user->username";

        $paymentResponse = self::createPayment($amount, $paymentMethod, $title);

        if (!$paymentResponse['success'])
            AuthController::redirect('panel', ["message" => "Płatność nie powiodła się. Spróbuj ponownie lub skontaktuj się z administratorem domeny."]);

        $url = $paymentResponse['data']['url'];
        $tid = $paymentResponse['data']['tid'];
        $ip = self::getIPAddress();
        $now = time();

        $payment = new Payment(0, $now, $ip, PaymentStatus::INCOMING->value, $amount, $paymentMethod->value, $_SESSION['id'], $tid);

        Repositories::$paymentsRepository->save($payment);
        $paymentFromDb = Repositories::$paymentsRepository->findOne([
            "tid" => $tid
        ]);

        LogsController::savePaymentLog($paymentFromDb->id);
    }

    private static function getIPAddress(): string {
        $ip = "";

        if (isset($_SERVER['REMOTE_ADDR'])) $ip = $_SERVER['REMOTE_ADDR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_CLIENT_IP'])) $ip = $_SERVER['HTTP_CLIENT_IP'];

        return $ip;
    }

    private static function createPayment(float $sum, PaymentMethods $method, string $title): array {
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
                var_dump((curl_error($ch)));
            }
        }
    }
}
