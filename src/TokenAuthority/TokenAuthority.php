<?php

namespace App\TokenAuthority;

use App\Entity\Token;

interface TokenAuthority
{
    public function validateToken(string $tokenString): ?Token;
}
