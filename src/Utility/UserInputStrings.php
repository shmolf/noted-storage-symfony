<?php

namespace App\Utility;

class UserInputStrings {
    /* \p{Ll} - Lowercase Letter
     * \p{Lu} - Uppercase Letter
     * (\pP|\pS - \pP Punctuation, or \pS Symbol (which should include emojis)
     * \p{Nd} - Number
     */
    public const REGEX_PASSWORD = "/^(?=.*\p{Ll}+)(?=.*\p{Lu}+)(?=.*(\pP|\pS)+)(?=.*\p{Nd}+).*$/u";
    public const PASSWORD_DESCRIPTION =
        'Requires at least one of each: uppercase, lowercase, numeric, and symbolic characters.';

    public const REGEX_EMAIL = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

    public static function trimMb4String(string $string, string $trim_chars = '\s'): string
    {
        return preg_replace("/^[{$trim_chars}]*(?U)(.*)[{$trim_chars}]*$/u", '\\1', $string);
    }
}
