<?php

if (!function_exists('currency')) {
    /**
     * Format amount with currency settings
     */
    function currency($amount, $decimals = 2) {
        $symbol = \App\Models\Setting::getValue('currency_symbol', 'Rs.');
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
        return \App\Models\Setting::getValue('currency_symbol', 'Rs.');
    }
}
