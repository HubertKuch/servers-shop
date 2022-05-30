<?php

namespace Servers\Controllers;

use Avocado\Router\AvocadoRequest;
use HCGCloud\Pterodactyl\Pterodactyl;
use Servers\Models\JavaVersion;
use Servers\Models\MinecraftEggNames;
use Servers\Models\PaymentMethods;
use Servers\Models\Server;
use Servers\Models\ServerStatus;
use Servers\Repositories;

class ServersController {
    private static Pterodactyl $pterodactyl;
    private static int $expireDays = 31;
    private static array $mcVersions = [];

    public static final function init(Pterodactyl $pterodactyl): void {
        self::$pterodactyl = $pterodactyl;
        self::$mcVersions = json_decode(file_get_contents("Utils/mc_versions.json"));
    }

    public static final function suspendServer(object $server): void {
        $pterodactylId = $server->pterodactyl_id ?? null;
        $serverId = $server->id;

        if (!$pterodactylId || $pterodactylId == 0)
            AuthController::redirect('servers', ["message" => "Wystąpił nieoczekiwany błąd. Skontaktuj się z administratorem domeny."]);

        self::$pterodactyl->suspendServer($pterodactylId);
        Repositories::$productsRepository->updateOneById(["status" => "expired"], $serverId);
    }

    public static final function unSuspendServer(AvocadoRequest $req): void {
        AuthController::authenticationMiddleware();
        $serverId = intval($req->params['id']) ?? null;

        if (!$serverId)
            AuthController::redirect('servers', ["message" => "Wystąpił nieoczekiwany błąd. Skontaktuj się z administratorem domeny."]);

        $server = Repositories::$productsRepository->findOneById($serverId);

        $pterodactylId = $server->pterodactyl_id ?? null;

        if (!$serverId || $serverId == 0)
            AuthController::redirect('servers', ["message" => "Wystąpił nieoczekiwany błąd. Skontaktuj się z administratorem domeny."]);

        self::$pterodactyl->unsuspendServer($pterodactylId);
        Repositories::$productsRepository->updateOneById(["status" => "sold", "expireDate" => time() + 24 * 60 * 60 * self::$expireDays], $serverId);
        echo "<script>localStorage.setItem('user-panel-actual-visible', 'bought-servers')</script>";
        AuthController::redirect('panel');
    }

    public static final function create(AvocadoRequest $req): void {
        $eggType            = $req->body['egg_type'] ?? null;
        $eggId              = intval($req->body['egg_id']) ?? null;
        $packageId          = intval($req->body['package_id']) ?? null;
        $name               = $req->body['server_name'] ?? null;
        $minecraftVersion   = trim($req->body['mc_version']) ?? null;
        $javaVersion        = $req->body['java_version'] ?? null;
        $pterodactylUserId  = $_SESSION['pterodactyl_user_id'];
        $user               = Repositories::$userRepository->findOneById($_SESSION['id']);

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

        if (!in_array($minecraftVersion, self::$mcVersions))
            AuthController::redirect('servers', ["message" => "Niepoprawna wersja Minecraft"]);

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

        $pterodactylServer = self::$pterodactyl->createServer($serverData);
        $createDate = time();
        $expireDate = time() + 24 * 60 * 60 * self::$expireDays;

        $userId = $user->id;
        $serverId = $pterodactylServer->id;

        $server = new Server($name, ServerStatus::EXPIRED->value, $createDate, $expireDate, $package->id, $userId, $serverId);
        Repositories::$productsRepository->save($server);

        AuthController::redirect('panel');
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
