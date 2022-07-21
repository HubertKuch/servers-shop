<?php

namespace Servers\Controllers;

use Avocado\Router\AvocadoRequest;
use Carbon\Carbon;
use HCGCloud\Pterodactyl\Pterodactyl;
use Servers\Models\enumerations\JavaVersion;
use Servers\Models\enumerations\LogType;
use Servers\Models\enumerations\MinecraftEggNames;
use Servers\Models\enumerations\PaymentStatus;
use Servers\Models\enumerations\PaymentType;
use Servers\Models\enumerations\ServerStatus;
use Servers\Models\Log;
use Servers\Models\Notification;
use Servers\Models\Payment;
use Servers\Models\Server;
use Servers\Models\User;
use Servers\Repositories;
use Servers\Services\PaymentsService;

class ServersController {
    private static Pterodactyl $pterodactyl;
    private static array $mcVersions = [];
    private static int $expiresTime = 31;
    private static string $expiresType = 'DAYS';

    public static final function init(Pterodactyl $pterodactyl): void {
        self::$pterodactyl = $pterodactyl;
        self::$mcVersions = json_decode(file_get_contents("Utils/mc_versions.json"));
        self::$expiresTime = intval($_ENV['EXPIRES_IN']);
        self::$expiresType = $_ENV['EXPIRES_TYPE'];
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
        $serverId = intval($req->query['id']) ?? null;

        if (!$serverId)
            AuthController::redirect('server-list', ["message" => "Wystąpił nieoczekiwany błąd. Skontaktuj się z administratorem domeny."]);

        /** @var $server Server */
        $server = Repositories::$productsRepository->findOneById($serverId);

        $pterodactylId = $server->getPterodactylId() ?? null;

        /** @var $user User */
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

        $payment = new Payment(
            time(),
            time(),
            PaymentsService::getIPAddress(),
            PaymentStatus::RESOLVED->value,
            $packageCost,
            $userMoney - $packageCost,
            null,
            $user->getId(),
            "",
            null,
            PaymentType::RENEW_SERVER
        );

        Repositories::$paymentsRepository->save($payment);
        self::$pterodactyl->unsuspendServer($pterodactylId);

        $expireDate = Carbon::now();
        $expireDate = match (self::$expiresType) {
            'DAYS'      => $expireDate->addDays(self::$expiresTime),
            'HOURS'     => $expireDate->addHours(self::$expiresTime),
            'MINUTES'   => $expireDate->addMinutes(self::$expiresTime),
            default     => $expireDate->addDays(self::$expiresTime)
        };

        $humanParsedExpireDateType = match (self::$expiresType) {
            "DAYS" => "dni",
            "HOURS" => "godzin",
            "MINUTES" => "minut"
        };

        $expireDate = $expireDate->getTimestamp();
        $expireTime = self::$expiresTime;

        $log = new Log(
            LogType::PRODUCT->value,
            $user->getId(),
            $server->getId(),
            null,
            "Uzytkownik {$user->getUsername()} odnowil server `{$server->getTitle()}` na {$expireTime} {$humanParsedExpireDateType}"
        );

        Repositories::$logsRepository->save($log);
        Repositories::$productsRepository->updateOneById(["status" => "sold", "expireDate" => $expireDate], $serverId);
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
        AuthController::authenticationMiddleware();

        $eggType            = $req->body['egg_type'] ?? null;
        $eggId              = intval($req->body['egg_id']) ?? null;
        $packageId          = intval($req->body['package_id']) ?? null;
        $name               = $req->body['server_name'] ?? null;
        $minecraftVersion   = trim($req->body['mc_version']) ?? null;
        $javaVersion        = $req->body['java_version'] ?? null;
        $forgeVersion       = $req->body['forge_version'] ?? null;
        $pterodactylUserId  = $_SESSION['pterodactyl_user_id'];
        /** @var $user User */
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

        try {
            $createDate = Carbon::now()->getTimestamp();
            $expireDate = Carbon::now();

            $expireDate = match (self::$expiresType) {
                'DAYS'      => $expireDate->addDays(self::$expiresTime),
                'HOURS'     => $expireDate->addHours(self::$expiresTime),
                'MINUTES'   => $expireDate->addMinutes(self::$expiresTime),
                default     => $expireDate->addDays(self::$expiresTime)
            };

            $expireDate = $expireDate->getTimestamp();

            $userId = $user->getId();

            $userCash = $user->getWallet();
            $serverPrice = $package->getCost();

            if ($userCash < $serverPrice)
                AuthController::redirect('servers', ["message" => "Nie masz wystarczającej ilości pieniędzy"]);

            $pterodactylServer = self::$pterodactyl->createServer($serverData);
            $serverId = $pterodactylServer->id;

            $serversWithTheSameName = count(Repositories::$productsRepository->findMany(["title" => "$name%", "user_id" => $_SESSION['id']]));

            if ($serversWithTheSameName > 0) $name .= " #$serversWithTheSameName";

            $server = new Server($name, ServerStatus::SOLD->value, $createDate, $expireDate, $package->getId(), $userId, $serverId);

            Repositories::$productsRepository->save($server);
            Repositories::$userRepository->updateOneById([
                "wallet" => $userCash - $serverPrice
            ], $user->getId());

            $now = time();

            $payment = new Payment(
                $now,
                $now,
                PaymentsService::getIPAddress(),
                PaymentStatus::RESOLVED->value,
                $serverPrice,
                $user->getWallet() - $serverPrice,
                null,
                 $userId,
                "",
                 null,
                 PaymentType::BOUGHT_SERVER
            );

            Repositories::$paymentsRepository->save($payment);

            AuthController::redirect('server-list');
        } catch (\Exception $e) {
            AuthController::redirect('servers', ["message" => "Wystąpił nieoczekiwany błąd skontaktuj się z administratorem domeny."]);
        }
    }

    public static function getReleasedMinecraftVersions(): array {
        $mcVersions = json_decode(file_get_contents('Utils/mc_versions.json'));

        // is match {D.DD.D} patten
        return array_filter(
            $mcVersions,
            fn($version) => strlen($version) <= 6 && is_numeric(substr($version, -1, 1))
        );
    }

    private static function deleteServerIfExpiredSince(Server $server) {
        $deleteAfterTimeType = $_ENV['DELETE_SERVER_SINCE_TYPE'];
        $deleteAfterTime = $_ENV['DELETE_SERVER_SINCE'];

        $serverExpiredAt = Carbon::createFromTimestamp($server->getExpireDate());
        $now = Carbon::now();

        $diff = match ($deleteAfterTimeType) {
            "MONTHS" => $serverExpiredAt->diffInMonths($now),
            "HOURS" => $serverExpiredAt->diffInHours($now),
            "MINUTES" => $serverExpiredAt->diffInMinutes($now),
            default => $serverExpiredAt->diffInDays($now)
        };

        if ($diff >= intval($deleteAfterTime)) {
            Repositories::$productsRepository->deleteOneById($server->getId());

            $timeToNotification = match ($deleteAfterTimeType) {
                "MONTHS" => "miesiecy",
                "MINUTES" => "minut",
                default => "dni"
            };

            /** @var $user User */
            $user = Repositories::$userRepository->findOneById($server->getUserId());

            Repositories::$notificationsRepository->save(new Notification(
                "Twoj server (`{$server->getTitle()}`) zostal usuniety z powodu braku uisczenia platnosci w terminie {$deleteAfterTime}{$timeToNotification}",
                $now->getTimestamp(),
                $user
            ));

            Repositories::$logsRepository->save(new Log(
                LogType::PRODUCT->value,
                $server->getUserId(),
                null,
                null,
                "Server `{$server->getTitle()}` uzytkownika {$user->getUsername()} o id uzytkownika {$user->getId()} zostal usuniety z powodu przedawnienia ({$deleteAfterTime}{$timeToNotification})."
            ));

            self::$pterodactyl->forceDeleteServer($server->getPterodactylId());
        }
    }

    public static function checkServers(AvocadoRequest $req): void {
        $servers = Repositories::$productsRepository->findMany();

        /** @var $server Server */
        foreach ($servers as $server) {
            if ($server->getExpireDate() < time()) {
                if ($server->getStatus() !== ServerStatus::EXPIRED->value) {
                    echo "EXPIRED ".$server->getTitle();
                    $deleteAfterTimeType = $_ENV['DELETE_SERVER_SINCE_TYPE'];
                    $deleteAfterTime = $_ENV['DELETE_SERVER_SINCE'];

                    $timeToNotification = match ($deleteAfterTimeType) {
                        "MONTHS" => "miesiecy",
                        "MINUTES" => "minut",
                        default => "dni"
                    };

                    /** @var $user User */
                    $user = Repositories::$userRepository->findOneById($server->getUserId());
                    ServersController::suspendServer($server);

                    $notification = new Notification("Twoj server `{$server->getTitle()}` zostal przedawniony. Jesli w ciagu {$deleteAfterTime} {$timeToNotification} nie zostanie oplacony zostanie usuniety.", time(), $user);
                    $log = new Log(LogType::PRODUCT->value, $user->getId(), $server->getId(), null, "Server uzytkownika {$user->getUsername()} o id {$user->getId()} zostal przedawniony.");

                    Repositories::$logsRepository->save($log);
                    Repositories::$notificationsRepository->save($notification);
                    break;
                }

                self::deleteServerIfExpiredSince($server);
            }
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
