<?php

namespace App\Dto;

use App\Entity\RefreshToken;
use DateTimeInterface;

class OauthTokenDto
{
    public ?string $name;
    public ?string $uuid;
    public ?string $host;
    public ?bool $isValid;
    public ?DateTimeInterface $created;
    public ?DateTimeInterface $expires;
    public ?DateTimeInterface $lastAccess;

    public function __construct(RefreshToken $token) {
        $this->host = $token->getHost();
        $this->isValid = $token->getIsValid();
        $this->created = $token->getCreationDate();
        $this->expires = $token->getExpirationDate();
    }
}
