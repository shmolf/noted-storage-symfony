<?php

namespace App\Dto;

use App\Entity\AppToken;
use DateTimeInterface;

class AppTokenDto
{
    public ?string $name;
    public ?string $uuid;
    public ?DateTimeInterface $created;
    public ?DateTimeInterface $expires;
    public ?DateTimeInterface $lastAccess;

    public function __construct(AppToken $token) {
        $this->name = $token->getName();
        $this->uuid = $token->getUuid();
        $this->created = $token->getCreatedDate();
        $this->expires = $token->getExpirationDate();
        $this->lastAccess = $token->getLastAccessDate();
    }
}
