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
 * - Azri Jamil
 * - JD Isaacks
 * - Josh Soref
 * - Azri Jamil
 * - Hariadi Hinta
 */
return [
    'year' => '{1}setahun|]1,Inf[:count tahun',
    'y' => ':count tahun',
    'month' => '{1}sebulan|]1,Inf[:count bulan',
    'm' => ':count bulan',
    'week' => '{1}seminggu|]1,Inf[:count minggu',
    'w' => ':count minggu',
    'day' => '{1}sehari|]1,Inf[:count hari',
    'd' => ':count hari',
    'hour' => '{1}sejam|]1,Inf[:count jam',
    'h' => ':count jam',
    'minute' => '{1}seminit|]1,Inf[:count minit',
    'min' => ':count minit',
    'second' => '{1}beberapa saat|]1,Inf[:count saat',
    's' => ':count saat',
    'ago' => ':time yang lepas',
    'from_now' => 'dalam :time',
    'after' => ':time selepas',
    'before' => ':time sebelum',
    'formats' => [
        'LT' => 'HH.mm',
        'LTS' => 'HH.mm.ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY [pukul] HH.mm',
        'LLLL' => 'dddd, D MMMM YYYY [pukul] HH.mm',
    ],
    'calendar' => [
        'sameDay' => '[Hari ini pukul] LT',
        'nextDay' => '[Esok pukul] LT',
        'nextWeek' => 'dddd [pukul] LT',
        'lastDay' => '[Kelmarin pukul] LT',
        'lastWeek' => 'dddd [lepas pukul] LT',
        'sameElse' => 'L',
    ],
    'meridiem' => function ($hour) {
        if ($hour < 11) {
            return 'pagi';
        }
        if ($hour < 15) {
            return 'tengahari';
        }
        if ($hour < 19) {
            return 'petang';
        }

        return 'malam';
    },
    'months' => ['Januari', 'Februari', 'Mac', 'April', 'Mei', 'Jun', 'Julai', 'Ogos', 'September', 'Oktober', 'November', 'Disember'],
    'months_short' => ['Jan', 'Feb', 'Mac', 'Apr', 'Mei', 'Jun', 'Jul', 'Ogs', 'Sep', 'Okt', 'Nov', 'Dis'],
    'weekdays' => ['Ahad', 'Isnin', 'Selasa', 'Rabu', 'Khamis', 'Jumaat', 'Sabtu'],
    'weekdays_short' => ['Ahd', 'Isn', 'Sel', 'Rab', 'Kha', 'Jum', 'Sab'],
    'weekdays_min' => ['Ah', 'Is', 'Sl', 'Rb', 'Km', 'Jm', 'Sb'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 1,
    'list' => [', ', ' dan '],
];
=======
return array(
    'year' => ':count tahun',
    'y' => ':count tahun',
    'month' => ':count bulan',
    'm' => ':count bulan',
    'week' => ':count minggu',
    'w' => ':count minggu',
    'day' => ':count hari',
    'd' => ':count hari',
    'hour' => ':count jam',
    'h' => ':count jam',
    'minute' => ':count minit',
    'min' => ':count minit',
    'second' => ':count saat',
    's' => ':count saat',
    'ago' => ':time yang lalu',
    'from_now' => ':time dari sekarang',
    'after' => ':time selepas',
    'before' => ':time sebelum',
);
>>>>>>> dev
