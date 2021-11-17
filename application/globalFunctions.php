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
 * @param int $maxDecimals
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

    // number_format will remove a '-' in front of only zeros, so we have to add it again afterwards
    $isNegative = false;
    if (str_contains($parts[0], '-'))
        $isNegative = true;

    $parts[0] = number_format($parts[0], 0, ',', '.');
    if ($isNegative && !str_contains($parts[0], '-')) {
        $parts[0] = '-' . $parts[0];
    }

    return $parts[0] . (strlen($parts[1]) !== 0 ? ',' . $parts[1] : '');
}

/**
 * Print the given string to the output stream if in debug mode
 * @param string $string
 */
function debug(string $string): void
{
    if (APPLICATION_DEBUG === true) {
        echo $string;
    }
}

/**
 * Round function for BCMath numbers. Result will be returned formatted with format_number()
 * @param string $number
 * @param int $decimalCount
 * @return string
 */
function bcround(string $number, int $decimalCount = 2): string
{
    if (str_contains($number, '.') === false) {
        return $number;
    }

    $decimalCount = max($decimalCount, 0);
    list($ints, $decimals) = explode('.', $number, 2);
    $isNegative = str_contains($ints, '-');
    $ints = str_replace('-', '', $ints);

    if ($decimalCount > strlen($decimals)) {
        return format_number($number, min($decimalCount, 2), $decimalCount);
    }

    $carry = 0;
    for ($i = strlen($decimals) - 1; $i >= $decimalCount; --$i) {
        $decimal = intval($decimals[$i]);
        $decimal += $carry;
        if ($decimal >= 5) {
            $carry = 1;
        } else {
            $carry = 0;
        }
        $decimals[$i] = 0;
    }

    if ($decimalCount - 1 >= 0) {
        if (intval($decimals[$decimalCount - 1]) + $carry === 10) {
            $decimals[$decimalCount - 1] = '0';
            for ($i = 2; $decimalCount - $i >= 0; $i++) {
                if (intval($decimals[$decimalCount - $i]) + $carry === 10) {
                    $decimals[$decimalCount - $i] = '0';
                } else {
                    $decimals[$decimalCount - $i] = intval($decimals[$decimalCount - $i]) + $carry;
                    $carry = 0;
                }
            }
        } else {
            $decimals[$decimalCount - 1] = intval($decimals[$decimalCount - 1]) + $carry;
            $carry = 0;
        }
    }

    if ($carry === 1) {
        for ($i = strlen($ints) - 1; $i >= 0; --$i) {
            $int = intval($ints[$i]);
            $int += $carry;
            if ($int === 10) {
                $ints[$i] = 0;
                $carry = 1;
            } else {
                $ints[$i] = $int;
                $carry = 0;
                break;
            }
        }

        if ($carry === 1) {
            $ints = '1' . $ints;
        }
    }

    return format_number(($isNegative ? '-' : '') . $ints . '.' . $decimals, min($decimalCount, 2), $decimalCount);
}
