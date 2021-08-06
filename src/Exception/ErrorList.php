<?php

namespace App\Exception;

interface ErrorList
{
    /**
     * Get User-friendly list of errors
     */
    public function getErrors(): array;
    /**
     * Set User-friendly list of errors
     *
     * @param string[] $errors
     */
    public function setErrors(array $errors): self;
}
