<?php

namespace Servers\Controllers;

use Avocado\Router\AvocadoRequest;
use Servers\Models\PaymentMethods;
use Servers\Repositories;
use Servers\Services\PaymentsService;

class PaymentsController {
    public static final function fundToUser(AvocadoRequest $req): void {
        AuthController::authenticationMiddleware();

        $paymentMethodId = $req->body['payment_id'] ?? null;
        $paymentMethod = PaymentMethods::tryFrom($paymentMethodId);
        $amount = $req->body['amount'] ?? null;

        $user = Repositories::$userRepository->findOneById($_SESSION['id']);
        $title = "Doładowanie konta dla użytkownika: {$user->getUsername()}";
        $email = $req->body['email'] ?? null;

        if (!$email)
            AuthController::redirect('');

        $userToFund = Repositories::$userRepository->findOne(["email" => $email]);

        if (!$userToFund)
            AuthController::redirect('');

        $paymentResponse = PaymentsService::createPaymentRequest($amount, $paymentMethod, $title);
        $payment = PaymentsService::createPayment($req, $paymentResponse, $user);

        if (!$paymentResponse['success'])
            AuthController::redirect('panel', ["message" => "Płatność nie powiodła się. Spróbuj ponownie lub skontaktuj się z administratorem domeny."]);

        $url = $paymentResponse['data']['url'];

        $paymentFromDb = Repositories::$paymentsRepository->findOne([
            "tid" => $payment->getTid()
        ]);

        LogsController::savePaymentLog($paymentFromDb->getId());

        header("Location: $url");
    }
}
