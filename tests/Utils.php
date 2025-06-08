<?php

namespace Alibe\GeoCodes\Tests;

class Utils
{
    /**
     * Checks if an array is a list (numerically indexed, sequential).
     * @param array<string> $array
     * @return bool
     */
    public static function isList(array $array): bool
    {
        return array_keys($array) === range(0, count($array) - 1);
    }
}
