<?php

<<<<<<< HEAD
/**
=======
/*
>>>>>>> dev
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

<<<<<<< HEAD
/**
 * Authors:
 * - Wacław Jacek
 * - François B
 * - Tim Fish
 * - Serhan Apaydın
 * - Massimiliano Caniparoli
 * - JD Isaacks
 * - Jakub Szwacz
 * - Jan
 * - Paul
 * - damlys
 * - Marek (marast78)
 * - Peter (UnrulyNatives)
 */
return [
    'year' => ':count rok|:count lata|:count lat',
    'y' => ':countr|:countl',
    'month' => ':count miesiąc|:count miesiące|:count miesięcy',
    'm' => ':countmies',
    'week' => ':count tydzień|:count tygodnie|:count tygodni',
    'w' => ':counttyg',
    'day' => ':count dzień|:count dni|:count dni',
    'd' => ':countd',
    'hour' => ':count godzina|:count godziny|:count godzin',
    'h' => ':countg',
    'minute' => ':count minuta|:count minuty|:count minut',
    'min' => ':countm',
    'second' => ':count sekunda|:count sekundy|:count sekund',
    'a_second' => '{1}kilka sekund|:count sekunda|:count sekundy|:count sekund',
    's' => ':counts',
    'ago' => ':time temu',
    'from_now' => 'za :time',
    'after' => ':time po',
    'before' => ':time przed',
    'diff_now' => 'przed chwilą',
    'diff_yesterday' => 'wczoraj',
    'diff_tomorrow' => 'jutro',
    'diff_before_yesterday' => 'przedwczoraj',
    'diff_after_tomorrow' => 'pojutrze',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD.MM.YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd, D MMMM YYYY HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[Dziś o] LT',
        'nextDay' => '[Jutro o] LT',
        'nextWeek' => function (\Carbon\CarbonInterface $date) {
            switch ($date->dayOfWeek) {
                case 0:
                    return '[W niedzielę o] LT';
                case 2:
                    return '[We wtorek o] LT';
                case 3:
                    return '[W środę o] LT';
                case 6:
                    return '[W sobotę o] LT';
                default:
                    return '[W] dddd [o] LT';
            }
        },
        'lastDay' => '[Wczoraj o] LT',
        'lastWeek' => function (\Carbon\CarbonInterface $date) {
            switch ($date->dayOfWeek) {
                case 0:
                    return '[W zeszłą niedzielę o] LT';
                case 3:
                    return '[W zeszłą środę o] LT';
                case 6:
                    return '[W zeszłą sobotę o] LT';
                default:
                    return '[W zeszły] dddd [o] LT';
            }
        },
        'sameElse' => 'L',
    ],
    'ordinal' => ':number.',
    'months' => ['stycznia', 'lutego', 'marca', 'kwietnia', 'maja', 'czerwca', 'lipca', 'sierpnia', 'września', 'października', 'listopada', 'grudnia'],
    'months_standalone' => ['styczeń', 'luty', 'marzec', 'kwiecień', 'maj', 'czerwiec', 'lipiec', 'sierpień', 'wrzesień', 'październik', 'listopad', 'grudzień'],
    'months_short' => ['sty', 'lut', 'mar', 'kwi', 'maj', 'cze', 'lip', 'sie', 'wrz', 'paź', 'lis', 'gru'],
    'months_regexp' => '/DD?o?\.?(\[[^\[\]]*\]|\s)+MMMM?/',
    'weekdays' => ['niedziela', 'poniedziałek', 'wtorek', 'środa', 'czwartek', 'piątek', 'sobota'],
    'weekdays_short' => ['ndz', 'pon', 'wt', 'śr', 'czw', 'pt', 'sob'],
    'weekdays_min' => ['Nd', 'Pn', 'Wt', 'Śr', 'Cz', 'Pt', 'So'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
    'list' => [', ', ' i '],
    'meridiem' => ['przed południem', 'po południu'],
];
=======
return array(
    'year' => '1 rok|:count lata|:count lat',
    'y' => '1 rok|:count lata|:count lat',
    'month' => '1 miesiąc|:count miesiące|:count miesięcy',
    'm' => '1 miesiąc|:count miesiące|:count miesięcy',
    'week' => '1 tydzień|:count tygodnie|:count tygodni',
    'w' => '1 tydzień|:count tygodnie|:count tygodni',
    'day' => '1 dzień|:count dni|:count dni',
    'd' => '1 dzień|:count dni|:count dni',
    'hour' => '1 godzina|:count godziny|:count godzin',
    'h' => '1 godzina|:count godziny|:count godzin',
    'minute' => '1 minuta|:count minuty|:count minut',
    'min' => '1 minuta|:count minuty|:count minut',
    'second' => '1 sekunda|:count sekundy|:count sekund',
    's' => '1 sekunda|:count sekundy|:count sekund',
    'ago' => ':time temu',
    'from_now' => ':time od teraz',
    'after' => ':time przed',
    'before' => ':time po',
);
>>>>>>> dev
