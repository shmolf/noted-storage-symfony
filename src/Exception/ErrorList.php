<?php

namespace App\Exception;

interface ErrorList
{
    public function getErrors(): array;
    public function setErrors(array $errors): self;
}
