<?php

namespace Dyln\Util;

class MoneyUtil
{
    static public function toPence($amount)
    {
        $amount = round($amount, 2);

        return (int)bcmul($amount, 100);
    }

    static public function toFloat($amount)
    {
        $amount = (float)bcdiv($amount, 100, 2);

        return number_format(round($amount, 2), 2, '.', '');
    }

    static public function formatCurrency($valueInPence, $currency = null)
    {
        $value = MoneyUtil::toFloat($valueInPence);
        if (!$currency) {
            return $value;
        }
        $locale = \Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
//        $locale = 'fr_FR';
        $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
        $return = $formatter->formatCurrency($value, $currency);
//        $format = '%.2n';
//        switch (strtoupper($currency)) {
//            case 'GBP':
//                setlocale(LC_MONETARY, 'en_GB.UTF-8');
//                $format = '£ %!n';
//                if ($hideSymbol) {
//                    $format = '%!n';
//                }
//                break;
//            case 'USD':
//                setlocale(LC_MONETARY, 'en_US.UTF-8');
//                $format = '$ %!n';
//                if ($hideSymbol) {
//                    $format = '%!n';
//                }
//                break;
//            case 'EUR':
//                setlocale(LC_MONETARY, 'fr_FR.UTF-8');
//                $format = '%!n €';
//                if ($hideSymbol) {
//                    $format = '%!n';
//                }
//                break;
//        }
//
//        $return = money_format($format, $value);

        return $return;
    }
}