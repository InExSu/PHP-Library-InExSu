<?php

declare(strict_types=1);

function a1_Strings_Trim(array $a1_S, int $length = 70): array
{
    /**
     * обрезать строки массива 1-мерного
     */

    return array_map(fn($string) => mb_substr(
        $string,
        0,
        $length,
        'UTF-8'
    ), $a1_S);
}
