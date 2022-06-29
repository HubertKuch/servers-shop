<?php

namespace Servers\Models;

use Avocado\ORM\Attributes\Field;
use Avocado\ORM\Attributes\Id;
use Avocado\ORM\Attributes\Table;

#[Table("notification")]
class Notification {
    #[Id]
    private int $id;
    #[Field]
    private string $message;
    #[Field]
    private int $isRead;
    #[Field]
    private int $date;
    #[Field('user_id')]
    private int $userId;

    public function __construct(string $message, int $date, User $user) {
        $this->message = $message;
        $this->isRead = false;
        $this->date = $date;
        $this->userId = $user->getId();
    }

    public function getMessage(): string {
        return $this->message;
    }

    public function isRead(): bool {
        return $this->isRead === 1;
    }

    public function getDate(): int {
        return $this->date;
    }

    public function getUserId(): int {
        return $this->userId;
    }
}
