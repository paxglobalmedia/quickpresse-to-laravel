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
 * - Josh Soref
 * - François B
 * - Tim Fish
 * - shaishavgandhi05
 * - Serhan Apaydın
 * - JD Isaacks
 * - tomhorvat
 * - Josh Soref
 * - François B
 * - shaishavgandhi05
 * - Serhan Apaydın
 * - JD Isaacks
 * - tomhorvat
 * - Stjepan
 */
return [
    'year' => ':count godinu|:count godine|:count godina',
    'y' => ':count god.|:count god.|:count god.',
    'month' => ':count mjesec|:count mjeseca|:count mjeseci',
    'm' => ':count mj.|:count mj.|:count mj.',
    'week' => ':count tjedan|:count tjedna|:count tjedana',
    'w' => ':count tj.|:count tj.|:count tj.',
    'day' => ':count dan|:count dana|:count dana',
    'd' => ':count d.|:count d.|:count d.',
    'hour' => ':count sat|:count sata|:count sati',
    'h' => ':count sat|:count sata|:count sati',
    'minute' => ':count minutu|:count minute|:count minuta',
    'min' => ':count min.|:count min.|:count min.',
    'second' => ':count sekundu|:count sekunde|:count sekundi',
    's' => ':count sek.|:count sek.|:count sek.',
=======
return array(
    'year' => ':count godinu|:count godine|:count godina',
    'y' => ':count godinu|:count godine|:count godina',
    'month' => ':count mjesec|:count mjeseca|:count mjeseci',
    'm' => ':count mjesec|:count mjeseca|:count mjeseci',
    'week' => ':count tjedan|:count tjedna|:count tjedana',
    'w' => ':count tjedan|:count tjedna|:count tjedana',
    'day' => ':count dan|:count dana|:count dana',
    'd' => ':count dan|:count dana|:count dana',
    'hour' => ':count sat|:count sata|:count sati',
    'h' => ':count sat|:count sata|:count sati',
    'minute' => ':count minutu|:count minute |:count minuta',
    'min' => ':count minutu|:count minute |:count minuta',
    'second' => ':count sekundu|:count sekunde|:count sekundi',
    's' => ':count sekundu|:count sekunde|:count sekundi',
>>>>>>> dev
    'ago' => 'prije :time',
    'from_now' => 'za :time',
    'after' => 'za :time',
    'before' => 'prije :time',
<<<<<<< HEAD
    'diff_yesterday' => 'jučer',
    'diff_tomorrow' => 'sutra',
    'diff_before_yesterday' => 'prekjučer',
    'diff_after_tomorrow' => 'prekosutra',
    'formats' => [
        'LT' => 'H:mm',
        'LTS' => 'H:mm:ss',
        'L' => 'DD.MM.YYYY',
        'LL' => 'D. MMMM YYYY',
        'LLL' => 'D. MMMM YYYY H:mm',
        'LLLL' => 'dddd, D. MMMM YYYY H:mm',
    ],
    'calendar' => [
        'sameDay' => '[danas u] LT',
        'nextDay' => '[sutra u] LT',
        'nextWeek' => function (\Carbon\CarbonInterface $date) {
            switch ($date->dayOfWeek) {
                case 0:
                    return '[u] [nedjelju] [u] LT';
                case 3:
                    return '[u] [srijedu] [u] LT';
                case 6:
                    return '[u] [subotu] [u] LT';
                default:
                    return '[u] dddd [u] LT';
            }
        },
        'lastDay' => '[jučer u] LT',
        'lastWeek' => function (\Carbon\CarbonInterface $date) {
            switch ($date->dayOfWeek) {
                case 0:
                case 3:
                    return '[prošlu] dddd [u] LT';
                case 6:
                    return '[prošle] [subote] [u] LT';
                default:
                    return '[prošli] dddd [u] LT';
            }
        },
        'sameElse' => 'L',
    ],
    'ordinal' => ':number.',
    'months' => ['siječnja', 'veljače', 'ožujka', 'travnja', 'svibnja', 'lipnja', 'srpnja', 'kolovoza', 'rujna', 'listopada', 'studenoga', 'prosinca'],
    'months_standalone' => ['siječanj', 'veljača', 'ožujak', 'travanj', 'svibanj', 'lipanj', 'srpanj', 'kolovoz', 'rujan', 'listopad', 'studeni', 'prosinac'],
    'months_short' => ['sij.', 'velj.', 'ožu.', 'tra.', 'svi.', 'lip.', 'srp.', 'kol.', 'ruj.', 'lis.', 'stu.', 'pro.'],
    'months_regexp' => '/D[oD]?(\[[^\[\]]*\]|\s)+MMMM?/',
    'weekdays' => ['nedjelju', 'ponedjeljak', 'utorak', 'srijedu', 'četvrtak', 'petak', 'subotu'],
    'weekdays_standalone' => ['nedjelja', 'ponedjeljak', 'utorak', 'srijeda', 'četvrtak', 'petak', 'subota'],
    'weekdays_short' => ['ned.', 'pon.', 'uto.', 'sri.', 'čet.', 'pet.', 'sub.'],
    'weekdays_min' => ['ne', 'po', 'ut', 'sr', 'če', 'pe', 'su'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 1,
    'list' => [', ', ' i '],
];
=======
);
>>>>>>> dev
