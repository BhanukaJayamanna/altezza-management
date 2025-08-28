<?php

if (!function_exists('currency')) {
    /**
     * Format amount with currency settings
     */
    function currency($amount, $decimals = 2) {
        $symbol = \App\Models\Setting::getValue('currency_symbol', 'LKR ');
        return $symbol . ' ' . number_format($amount, $decimals);
    }
}

if (!function_exists('currency_code')) {
    /**
     * Get currency code from settings
     */
    function currency_code() {
        return \App\Models\Setting::getValue('currency', 'LKR');
    }
}

if (!function_exists('currency_symbol')) {
    /**
     * Get currency symbol from settings
     */
    function currency_symbol() {
        return \App\Models\Setting::getValue('currency_symbol', 'LKR ');
    }
}

if (!function_exists('setting')) {
    /**
     * Get setting value with default
     */
    function setting($key, $default = null) {
        return \App\Models\Setting::getValue($key, $default);
    }
}

if (!function_exists('number_to_words')) {
    /**
     * Convert number to words
     */
    function number_to_words($number) {
        $ones = [
            0 => '', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four',
            5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine',
            10 => 'ten', 11 => 'eleven', 12 => 'twelve', 13 => 'thirteen',
            14 => 'fourteen', 15 => 'fifteen', 16 => 'sixteen', 17 => 'seventeen',
            18 => 'eighteen', 19 => 'nineteen'
        ];

        $tens = [
            2 => 'twenty', 3 => 'thirty', 4 => 'forty', 5 => 'fifty',
            6 => 'sixty', 7 => 'seventy', 8 => 'eighty', 9 => 'ninety'
        ];

        $thousands = ['', 'thousand', 'million', 'billion'];

        if ($number == 0) {
            return 'zero';
        }

        $parts = explode('.', number_format($number, 2, '.', ''));
        $integer = intval($parts[0]);
        $decimal = intval($parts[1] ?? 0);

        $words = convertInteger($integer, $ones, $tens, $thousands);

        if ($decimal > 0) {
            $words .= ' and ' . convertInteger($decimal, $ones, $tens, $thousands) . ' cents';
        }

        return $words;
    }
}

if (!function_exists('convertInteger')) {
    function convertInteger($number, $ones, $tens, $thousands) {
        if ($number == 0) {
            return '';
        }

        $groups = [];
        $groupIndex = 0;

        while ($number > 0) {
            $group = $number % 1000;
            if ($group != 0) {
                $groups[] = convertGroup($group, $ones, $tens) . ' ' . $thousands[$groupIndex];
            }
            $number = intval($number / 1000);
            $groupIndex++;
        }

        return trim(implode(' ', array_reverse($groups)));
    }
}

if (!function_exists('convertGroup')) {
    function convertGroup($number, $ones, $tens) {
        $result = '';

        $hundreds = intval($number / 100);
        if ($hundreds > 0) {
            $result .= $ones[$hundreds] . ' hundred ';
        }

        $remainder = $number % 100;
        if ($remainder >= 20) {
            $tensDigit = intval($remainder / 10);
            $onesDigit = $remainder % 10;
            $result .= $tens[$tensDigit];
            if ($onesDigit > 0) {
                $result .= ' ' . $ones[$onesDigit];
            }
        } elseif ($remainder > 0) {
            $result .= $ones[$remainder];
        }

        return trim($result);
    }
}