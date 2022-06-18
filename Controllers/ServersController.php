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
    private static int $expireDays = 31;
    private static array $mcVersions = [];

    public static final function init(Pterodactyl $pterodactyl): void {
        self::$pterodactyl = $pterodactyl;
        self::$mcVersions = json_decode(file_get_contents("Utils/mc_versions.json"));
    }

    public static final function suspendServer(object $server): void {
        $pterodactylId = $server->getPterodactylId() ?? null;
        $serverId = $server->getId();

        if (!$pterodactylId || $pterodactylId == 0)
            AuthController::redirect('server-list', ["message" => "Wystąpił nieoczekiwany błąd. Skontaktuj się z administratorem domeny."]);

        self::$pterodactyl->suspendServer($pterodactylId);
        Repositories::$productsRepository->updateOneById(["status" => "expired"], $serverId);
    }

    public static final function unSuspendServer(AvocadoRequest $req): void {
        AuthController::authenticationMiddleware();
        $serverId = intval($req->params['id']) ?? null;

        if (!$serverId)
            AuthController::redirect('server-list', ["message" => "Wystąpił nieoczekiwany błąd. Skontaktuj się z administratorem domeny."]);

        $server = Repositories::$productsRepository->findOneById($serverId);

        $pterodactylId = $server->getPterodactylId() ?? null;

        $user = Repositories::$userRepository->findOneById($_SESSION['id']);
        $userMoney = $user->getWallet();

        $packageCost = Repositories::$packagesRepository->findOneById(intval($server->getPackage()))->getCost();

        if ($userMoney < $packageCost)
            AuthController::redirect('server-list', ["message" => "Nie masz wystarczających pieniędzy do dokonania akcji."]);

        Repositories::$userRepository->updateOneById([
            'wallet' => $userMoney - $packageCost
        ], $user->getId());

        if (!$serverId || $serverId == 0)
            AuthController::redirect('server-list', ["message" => "Wystąpił nieoczekiwany błąd. Skontaktuj się z administratorem domeny."]);

        self::$pterodactyl->unsuspendServer($pterodactylId);
        Repositories::$productsRepository->updateOneById(["status" => "sold", "expireDate" => time() + 24 * 60 * 60 * self::$expireDays], $serverId);
        echo "<script>localStorage.setItem('user-panel-actual-visible', 'bought-servers')</script>";
        AuthController::redirect('');
    }

    private static function getVanillaEnvironmentData($minecraftVersion) {
        return [
            'VANILLA_VERSION' => $minecraftVersion,
            'SERVER_JARFILE' => 'server.jar',
        ];
    }

    private static function getForgeEnvironmentData($minecraftVersion, $forgeVersion) {
        return [
            'MC_VERSION' => $minecraftVersion,
            'BUILD_TYPE' => 'recommended',
            'FORGE_VERSION' => $forgeVersion,
            'SERVER_JARFILE' => 'server.jar',
        ];
    }

    public static final function create(AvocadoRequest $req): void {
        $eggType            = $req->body['egg_type'] ?? null;
        $eggId              = intval($req->body['egg_id']) ?? null;
        $packageId          = intval($req->body['package_id']) ?? null;
        $name               = $req->body['server_name'] ?? null;
        $minecraftVersion   = trim($req->body['mc_version']) ?? null;
        $javaVersion        = $req->body['java_version'] ?? null;
        $forgeVersion       = $req->body['forge_version'] ?? null;
        $pterodactylUserId  = $_SESSION['pterodactyl_user_id'];
        $user               = Repositories::$userRepository->findOneById($_SESSION['id']);
        $isForge            = $eggId == 2;

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

        // is forge
        if ($isForge && !$forgeVersion)
            AuthController::redirect('servers', ["message" => "Niepoprawna wersja Forge"]);

        $egg = self::$pterodactyl->egg(1, $eggId);

        $serverData = [
            'name' => $name,
            'user' => $pterodactylUserId,
            'egg' => $eggId,
            'docker_image' => $egg->dockerImage,
            'startup' => $egg->startup,
            'limits' => [
                'memory' => $package->getRamSize(),
                'swap' => 0,
                'disk' => $package->getDiskSize(),
                'io' => 500,
                'cpu' => $package->getProcessorPower(),
            ],
            'feature_limits' => [
                'databases' => 1,
                'backups' => 1,
            ],
            'allocation' => [
                'default' => self::getUnAssignedAllocationId(),
            ]
        ];

        $serverData['environment'] = match ($eggId) {
            5 => self::getVanillaEnvironmentData($minecraftVersion),
            2 => self::getForgeEnvironmentData($minecraftVersion, $forgeVersion)
        };

        $userCash = $user->getWallet();
        $serverPrice = $package->getCost();

        if ($userCash < $serverPrice)
            AuthController::redirect('servers', ["message" => "Nie masz wystarczającej ilości pieniędzy"]);

        Repositories::$userRepository->updateOneById([
            "wallet" => $userCash - $serverPrice
        ], $user->getId());

        try {
            $pterodactylServer = self::$pterodactyl->createServer($serverData);
            $createDate = time();
            $expireDate = time() + 24 * 60 * 60 * self::$expireDays;

            $userId = $user->getId();
            $serverId = $pterodactylServer->id;

            $server = new Server($name, ServerStatus::SOLD->value, $createDate, $expireDate, $package->getId(), $userId, $serverId);
            Repositories::$productsRepository->save($server);

            AuthController::redirect('server-list');
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            AuthController::redirect('servers', ["message" => "Wystąpił nieoczekiwany błąd skontaktuj się z administratorem domeny."]);
        }
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
