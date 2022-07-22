<?php

namespace Servers\Utils;

use Avocado\HTTP\JSON\JSON;
use Avocado\Router\AvocadoRequest;
use Avocado\Router\AvocadoResponse;

class Environment {
    public static function getBaseURL(): string {
        return $_ENV['ENVIRONMENT'] === "PRODUCTION" ? "http://{$_SERVER['HTTP_HOST']}/" : "http://{$_SERVER['HTTP_HOST']}/srv/";
    }

    public static function domainNumberFormat(float $amount): string {
        return number_format($amount, 2);
    }

    public static function validateApiKey(AvocadoRequest $request, AvocadoResponse $response) {
        $isValidRequest = ($request->headers['x-api-key'] ?? "") === $_ENV['X_API_KEY'];

        if (!$isValidRequest) $response->json(new JSON(["message" => "Provide valid API key."]));

        return $isValidRequest;
    }

}
