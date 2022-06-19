<?php

namespace Servers\Utils;

class Environment {
    public static function getBaseURL(): string {
        return $_ENV['ENVIRONMENT'] === "PRODUCTION" ? "http://{$_SERVER['HTTP_HOST']}/" : "http://{$_SERVER['HTTP_HOST']}/srv/";
    }
}
