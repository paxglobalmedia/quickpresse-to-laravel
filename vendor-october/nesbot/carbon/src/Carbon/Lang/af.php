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
 * - François B
 * - JD Isaacks
 * - Pierre du Plessis
 */
return [
    'year' => ':count jaar|:count jare',
    'a_year' => '\'n jaar|:count jare',
    'y' => ':count j.',
    'month' => ':count maand|:count maande',
    'a_month' => '\'n maand|:count maande',
    'm' => ':count maa.',
    'week' => ':count week|:count weke',
    'a_week' => '\'n week|:count weke',
    'w' => ':count w.',
    'day' => ':count dag|:count dae',
    'a_day' => '\'n dag|:count dae',
    'd' => ':count d.',
    'hour' => ':count uur|:count ure',
    'a_hour' => '\'n uur|:count ure',
    'h' => ':count u.',
    'minute' => ':count minuut|:count minute',
    'a_minute' => '\'n minuut|:count minute',
    'min' => ':count min.',
    'second' => ':count sekond|:count sekondes',
    'a_second' => '\'n paar sekondes|:count sekondes',
    's' => ':count s.',
    'ago' => ':time gelede',
    'from_now' => 'oor :time',
    'after' => ':time na',
    'before' => ':time voor',
    'diff_yesterday' => 'Gister',
    'diff_tomorrow' => 'Môre',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd, D MMMM YYYY HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[Vandag om] LT',
        'nextDay' => '[Môre om] LT',
        'nextWeek' => 'dddd [om] LT',
        'lastDay' => '[Gister om] LT',
        'lastWeek' => '[Laas] dddd [om] LT',
        'sameElse' => 'L',
    ],
    'ordinal' => function ($number) {
        return $number.(($number === 1 || $number === 8 || $number >= 20) ? 'ste' : 'de');
    },
    'meridiem' => ['VM', 'NM'],
    'months' => ['Januarie', 'Februarie', 'Maart', 'April', 'Mei', 'Junie', 'Julie', 'Augustus', 'September', 'Oktober', 'November', 'Desember'],
    'months_short' => ['Jan', 'Feb', 'Mrt', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Des'],
    'weekdays' => ['Sondag', 'Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrydag', 'Saterdag'],
    'weekdays_short' => ['Son', 'Maa', 'Din', 'Woe', 'Don', 'Vry', 'Sat'],
    'weekdays_min' => ['So', 'Ma', 'Di', 'Wo', 'Do', 'Vr', 'Sa'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
    'list' => [', ', ' en '],
];
=======
return array(
    'year' => '1 jaar|:count jare',
    'y' => '1 jaar|:count jare',
    'month' => '1 maand|:count maande',
    'm' => '1 maand|:count maande',
    'week' => '1 week|:count weke',
    'w' => '1 week|:count weke',
    'day' => '1 dag|:count dae',
    'd' => '1 dag|:count dae',
    'hour' => '1 uur|:count ure',
    'h' => '1 uur|:count ure',
    'minute' => '1 minuut|:count minute',
    'min' => '1 minuut|:count minute',
    'second' => '1 sekond|:count sekondes',
    's' => '1 sekond|:count sekondes',
    'ago' => ':time terug',
    'from_now' => ':time van nou af',
    'after' => ':time na',
    'before' => ':time voor',
);
>>>>>>> dev
