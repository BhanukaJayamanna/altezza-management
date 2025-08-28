<?php

namespace App\Helpers;

class NumberToWords
{
    private static $ones = [
        0 => '', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four',
        5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve', 13 => 'thirteen',
        14 => 'fourteen', 15 => 'fifteen', 16 => 'sixteen', 17 => 'seventeen',
        18 => 'eighteen', 19 => 'nineteen'
    ];

    private static $tens = [
        2 => 'twenty', 3 => 'thirty', 4 => 'forty', 5 => 'fifty',
        6 => 'sixty', 7 => 'seventy', 8 => 'eighty', 9 => 'ninety'
    ];

    private static $thousands = [
        '', 'thousand', 'million', 'billion', 'trillion'
    ];

    public static function convert($number)
    {
        if ($number == 0) {
            return 'zero';
        }

        $parts = explode('.', number_format($number, 2, '.', ''));
        $integer = intval($parts[0]);
        $decimal = intval($parts[1] ?? 0);

        $words = self::convertInteger($integer);

        if ($decimal > 0) {
            $words .= ' and ' . self::convertInteger($decimal) . ' cents';
        }

        return $words;
    }

    private static function convertInteger($number)
    {
        if ($number == 0) {
            return '';
        }

        $groups = [];
        $groupIndex = 0;

        while ($number > 0) {
            $group = $number % 1000;
            if ($group != 0) {
                $groups[] = self::convertGroup($group) . ' ' . self::$thousands[$groupIndex];
            }
            $number = intval($number / 1000);
            $groupIndex++;
        }

        return trim(implode(' ', array_reverse($groups)));
    }

    private static function convertGroup($number)
    {
        $result = '';

        $hundreds = intval($number / 100);
        if ($hundreds > 0) {
            $result .= self::$ones[$hundreds] . ' hundred ';
        }

        $remainder = $number % 100;
        if ($remainder >= 20) {
            $tens = intval($remainder / 10);
            $ones = $remainder % 10;
            $result .= self::$tens[$tens];
            if ($ones > 0) {
                $result .= ' ' . self::$ones[$ones];
            }
        } elseif ($remainder > 0) {
            $result .= self::$ones[$remainder];
        }

        return trim($result);
    }
}
