<?php

namespace App\Entity;

use App\Entity\User;
use DateTimeInterface;

interface Token
{
    public function getToken(): ?string;

    public function setToken(string $token): self;

    public function getCreationDate(): ?DateTimeInterface;

    public function setCreationDate(DateTimeInterface $creationDate): self;

    public function getUser(): ?User;

    public function setUser(?User $user): self;
}
