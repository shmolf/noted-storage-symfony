<?php

namespace App\Utility;

use Exception;

class Random {
    public const LOWERCASE = 1;
    public const UPPERCASE = 2;
    public const NUMERICAL = 4;
    public const SYMBOLS = 8;
    public const ALPHA_NUM = self::LOWERCASE | self::UPPERCASE | self::NUMERICAL;

    public static function createString(int $length = 1, array $flags = []){
        $enabledOptions = array_reduce($flags, fn(int $options, int $flag) => $options | $flag, 0);

        if ($enabledOptions === 0) {
            $enabledOptions = self::LOWERCASE | self::UPPERCASE | self::NUMERICAL | self::SYMBOLS;
        }

        $charSets = array_values(array_filter(
            [
                ['flag' => self::LOWERCASE, 'start' => 97, 'end' => 122],
                ['flag' => self::UPPERCASE, 'start' => 65, 'end' => 90],
                ['flag' => self::NUMERICAL, 'start' => 48, 'end' => 57],
                ['flag' => self::SYMBOLS, 'start' => 33, 'end' => 47],
            ],
            fn(array $option) => $option['flag'] & $enabledOptions
        ));

        if (empty($charSets)) {
            throw new Exception('Invalid options provided');
        }

        $length = max($length, 0);
        $output = '';

        while (strlen($output) < $length){
            $index = rand(0, count($charSets) - 1);
            $charSet = $charSets[$index];
            $output .= chr(rand($charSet['start'], $charSet['end']));
        }

        return $output;
    }
}
