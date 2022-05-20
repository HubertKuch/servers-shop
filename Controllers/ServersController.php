<?php

namespace Servers\Controllers;

use Avocado\Router\AvocadoRequest;
use HCGCloud\Pterodactyl\Pterodactyl;
use Servers\Models\JavaVersion;
use Servers\Models\MinecraftEggNames;
use Servers\Models\Server;
use Servers\Models\ServerStatus;
use Servers\Repositories;

class ServersController {
    private static Pterodactyl $pterodactyl;

    public static final function init(Pterodactyl $pterodactyl) {
        self::$pterodactyl = $pterodactyl;
    }

    public static final function create(AvocadoRequest $req): void {
        $eggType = $req->body['egg_type'] ?? null;
        $eggId = intval($req->body['egg_id']) ?? null;
        $packageId = intval($req->body['package_id']) ?? null;
        $name = $req->body['server_name'] ?? null;
        $minecraftVersion = $req->body['mc_version'] ?? null;
        $javaVersion = $req->body['java_version'] ?? null;
        $pterodactylUserId = $_SESSION['pterodactyl_user_id'];
        $user = Repositories::$userRepository->findOneById($_SESSION['id']);

        if (!$eggType || !$packageId || !$name || !$minecraftVersion || !$javaVersion)
            AuthController::redirect('servers', ["message" => "Wszystkie dane muszą być podane"]);

        $eggType = strtolower(trim($eggType));
        $package = Repositories::$packagesRepository->findOneById(intval($packageId));

        if(!self::isValidEggType($eggType))
            AuthController::redirect('servers', ["message" => "Nieporawny typ servera"]);

        if(!$eggId)
            AuthController::redirect('servers', ["message" => "Niepoprawne id typu servera"]);

        if (!$package)
            AuthController::redirect('servers', ["message" => "Nieporawny typ paczki"]);

        if (strlen($name) == 0)
            AuthController::redirect('servers', ["message" => "Nazwa servera jest niepoprawna"]);

        if (!self::isValidJavaVersion($javaVersion))
            AuthController::redirect('servers', ["message" => "Niepoprawna wersja Javy"]);

        $egg = self::$pterodactyl->egg(1, $eggId);
        $serverData = [
            'name' => $name,
            'user' => $pterodactylUserId,
            'egg' => $eggId,
            'docker_image' => $egg->dockerImage,
            'startup' => $egg->startup,
            'environment' => [
                'VANILLA_VERSION' => $minecraftVersion,
                'SERVER_JARFILE' => 'server.jar',
            ],
            'limits' => [
                'memory' => $package->ram_size,
                'swap' => 0,
                'disk' => $package->disk_size,
                'io' => 500,
                'cpu' => $package->processor_power,
            ],
            'feature_limits' => [
                'databases' => 1,
                'backups' => 1,
            ],
            'allocation' => [
                'default' => self::getUnAssignedAllocationId(),
            ]
        ];

        self::$pterodactyl->createServer($serverData);
        $server = new Server($name, ServerStatus::SOLD->value, time(), time(), $package->id, $user->id);
        Repositories::$productsRepository->save($server);

        AuthController::redirect('servers', ["message" => "Zakupiono server"]);
    }

    private static function isValidEggType(string $type): bool {
        $isValid = false;
        foreach (MinecraftEggNames::cases() as $eggType)
            if ($eggType->value === $type) {
                $isValid = true;
                break;
            }

        return $isValid;
    }

    private static function isValidJavaVersion(string $version): bool {
        $isValid = false;
        foreach (JavaVersion::cases() as $caseVersion)
            if ($caseVersion->value === $version) {
                $isValid = true;
                break;
            }

        return $isValid;
    }

    private static function getUnAssignedAllocationId(int $page = 1): int {
        $allocations = self::$pterodactyl->allocations(1, $page);
        $id = null;

        foreach ($allocations['data'] as $allocation)
            if (!$allocation->assigned)
                $id = $allocation->id;

        if (!$id)
            $id = self::getUnAssignedAllocationId($page+1);

        return $id;
    }
}
