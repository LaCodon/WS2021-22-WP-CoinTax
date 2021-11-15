<?php

/**
 * Surrounds var_dump with HTML pre tags for better readability
 * @param $obj
 */
function var_dump_pre($obj): void
{
    echo '<pre>';
    var_dump($obj);
    echo '</pre>';
}

/**
 * Formats a number with the minimum given decimals, cuts trailing zeros.
 * @param string $number
 * @param int $minDecimals
 * @return string
 */
function format_number(string $number, int $minDecimals = 2, int $maxDecimals = 18): string
{
    $number = rtrim($number, '0');
    $parts = explode('.', $number, 2);

    if (strlen($parts[1]) > $maxDecimals) {
        $parts[1] = substr($parts[1], 0, $maxDecimals);
    }
    $parts[1] = str_pad($parts[1], $minDecimals, '0');

    $parts[0] = number_format($parts[0], 0, ',', '.');

    return $parts[0] . ',' . $parts[1];
}