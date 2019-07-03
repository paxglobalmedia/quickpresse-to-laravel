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
 * - Roy
 * - Stephan
 * - François B
 * - Tim Fish
 * - Kevin Huang
 * - Jacob Middag
 * - JD Isaacks
 * - Roy
 * - Stephan
 * - François B
 * - Tim Fish
 * - Jacob Middag
 * - JD Isaacks
 * - Propaganistas
 * - MegaXLR
 * - adriaanzon
 * - MonkeyPhysics
 * - JeroenG
 * - RikSomers
 * - proclame
 * - Rik de Groot (hwdegroot)
 */
return [
    'year' => ':count jaar|:count jaar',
    'a_year' => 'een jaar|:count jaar',
    'y' => ':countj',
    'month' => ':count maand|:count maanden',
    'a_month' => 'een maand|:count maanden',
    'm' => ':countmnd',
    'week' => ':count week|:count weken',
    'a_week' => 'een week|:count weken',
    'w' => ':countw',
    'day' => ':count dag|:count dagen',
    'a_day' => 'een dag|:count dagen',
    'd' => ':countd',
    'hour' => ':count uur|:count uur',
    'a_hour' => 'een uur|:count uur',
    'h' => ':countu',
    'minute' => ':count minuut|:count minuten',
    'a_minute' => 'een minuut|:count minuten',
    'min' => ':countmin',
    'second' => ':count seconde|:count seconden',
    'a_second' => 'een paar seconden|:count seconden',
    's' => ':counts',
=======
return array(
    'year' => ':count jaar',
    'y' => ':count jaar',
    'month' => '1 maand|:count maanden',
    'm' => '1 maand|:count maanden',
    'week' => '1 week|:count weken',
    'w' => '1 week|:count weken',
    'day' => '1 dag|:count dagen',
    'd' => '1 dag|:count dagen',
    'hour' => ':count uur',
    'h' => ':count uur',
    'minute' => '1 minuut|:count minuten',
    'min' => '1 minuut|:count minuten',
    'second' => '1 seconde|:count seconden',
    's' => '1 seconde|:count seconden',
>>>>>>> dev
    'ago' => ':time geleden',
    'from_now' => 'over :time',
    'after' => ':time later',
    'before' => ':time eerder',
<<<<<<< HEAD
    'diff_now' => 'nu',
    'diff_yesterday' => 'gisteren',
    'diff_tomorrow' => 'morgen',
    'diff_after_tomorrow' => 'overmorgen',
    'diff_before_yesterday' => 'eergisteren',
    'period_recurrences' => ':count keer',
    'period_interval' => function ($interval) {
        /** @var string $output */
        $output = preg_replace('/^(een|één|1)\s+/', '', $interval);

        if (preg_match('/^(een|één|1)( jaar|j| uur|u)/', $interval)) {
            return "elk $output";
        }

        return "elke $output";
    },
    'period_start_date' => 'van :date',
    'period_end_date' => 'tot :date',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD-MM-YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd D MMMM YYYY HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[vandaag om] LT',
        'nextDay' => '[morgen om] LT',
        'nextWeek' => 'dddd [om] LT',
        'lastDay' => '[gisteren om] LT',
        'lastWeek' => '[afgelopen] dddd [om] LT',
        'sameElse' => 'L',
    ],
    'ordinal' => function ($number) {
        return $number.(($number === 1 || $number === 8 || $number >= 20) ? 'ste' : 'de');
    },
    'months' => ['januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december'],
    'months_short' => ['jan', 'feb', 'mrt', 'apr', 'mei', 'jun', 'jul', 'aug', 'sep', 'okt', 'nov', 'dec'],
    'mmm_suffix' => '.',
    'weekdays' => ['zondag', 'maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag'],
    'weekdays_short' => ['zo.', 'ma.', 'di.', 'wo.', 'do.', 'vr.', 'za.'],
    'weekdays_min' => ['zo', 'ma', 'di', 'wo', 'do', 'vr', 'za'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
    'list' => [', ', ' en '],
    'meridiem' => ['\'s ochtends', '\'s middags'],
];
=======
);
>>>>>>> dev
