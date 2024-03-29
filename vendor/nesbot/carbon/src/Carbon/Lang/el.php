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
 * - Alessandro Di Felice
 * - François B
 * - Tim Fish
 * - Gabriel Monteagudo
 * - JD Isaacks
 * - yiannisdesp
 */
return [
    'year' => ':count χρόνος|:count χρόνια',
    'a_year' => 'ένας χρόνος|:count χρόνια',
    'y' => ':count χρό.',
    'month' => ':count μήνας|:count μήνες',
    'a_month' => 'ένας μήνας|:count μήνες',
    'm' => ':count μήν.',
    'week' => ':count εβδομάδα|:count εβδομάδες',
    'a_week' => 'μια εβδομάδα|:count εβδομάδες',
    'w' => ':count εβδ.',
    'day' => ':count μέρα|:count μέρες',
    'a_day' => 'μία μέρα|:count μέρες',
    'd' => ':count μέρ.',
    'hour' => ':count ώρα|:count ώρες',
    'a_hour' => 'μία ώρα|:count ώρες',
    'h' => ':count ώρα|:count ώρες',
    'minute' => ':count λεπτό|:count λεπτά',
    'a_minute' => 'ένα λεπτό|:count λεπτά',
    'min' => ':count λεπ.',
    'second' => ':count δευτερόλεπτο|:count δευτερόλεπτα',
    'a_second' => 'λίγα δευτερόλεπτα|:count δευτερόλεπτα',
    's' => ':count δευ.',
    'ago' => ':time πριν',
    'from_now' => 'σε :time',
    'after' => ':time μετά',
    'before' => ':time πριν',
    'formats' => [
        'LT' => 'h:mm A',
        'LTS' => 'h:mm:ss A',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY h:mm A',
        'LLLL' => 'dddd, D MMMM YYYY h:mm A',
    ],
    'calendar' => [
        'sameDay' => '[Σήμερα {}] LT',
        'nextDay' => '[Αύριο {}] LT',
        'nextWeek' => 'dddd [{}] LT',
        'lastDay' => '[Χθες {}] LT',
        'lastWeek' => function (\Carbon\CarbonInterface $current) {
            switch ($current->dayOfWeek) {
                case 6:
                    return '[το προηγούμενο] dddd [{}] LT';
                default:
                    return '[την προηγούμενη] dddd [{}] LT';
            }
        },
        'sameElse' => 'L',
    ],
    'ordinal' => ':numberη',
    'meridiem' => ['ΠΜ', 'ΜΜ', 'πμ', 'μμ'],
    'months' => ['Ιανουαρίου', 'Φεβρουαρίου', 'Μαρτίου', 'Απριλίου', 'Μαΐου', 'Ιουνίου', 'Ιουλίου', 'Αυγούστου', 'Σεπτεμβρίου', 'Οκτωβρίου', 'Νοεμβρίου', 'Δεκεμβρίου'],
    'months_standalone' => ['Ιανουάριος', 'Φεβρουάριος', 'Μάρτιος', 'Απρίλιος', 'Μάιος', 'Ιούνιος', 'Ιούλιος', 'Αύγουστος', 'Σεπτέμβριος', 'Οκτώβριος', 'Νοέμβριος', 'Δεκέμβριος'],
    'months_regexp' => '/D[oD]?[\s,]+MMMM/',
    'months_short' => ['Ιαν', 'Φεβ', 'Μαρ', 'Απρ', 'Μαϊ', 'Ιουν', 'Ιουλ', 'Αυγ', 'Σεπ', 'Οκτ', 'Νοε', 'Δεκ'],
    'weekdays' => ['Κυριακή', 'Δευτέρα', 'Τρίτη', 'Τετάρτη', 'Πέμπτη', 'Παρασκευή', 'Σάββατο'],
    'weekdays_short' => ['Κυρ', 'Δευ', 'Τρι', 'Τετ', 'Πεμ', 'Παρ', 'Σαβ'],
    'weekdays_min' => ['Κυ', 'Δε', 'Τρ', 'Τε', 'Πε', 'Πα', 'Σα'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
    'list' => [', ', ' και '],
];
=======
return array(
    'year' => '1 χρόνος|:count χρόνια',
    'y' => '1 χρόνος|:count χρόνια',
    'month' => '1 μήνας|:count μήνες',
    'm' => '1 μήνας|:count μήνες',
    'week' => '1 εβδομάδα|:count εβδομάδες',
    'w' => '1 εβδομάδα|:count εβδομάδες',
    'day' => '1 μέρα|:count μέρες',
    'd' => '1 μέρα|:count μέρες',
    'hour' => '1 ώρα|:count ώρες',
    'h' => '1 ώρα|:count ώρες',
    'minute' => '1 λεπτό|:count λεπτά',
    'min' => '1 λεπτό|:count λεπτά',
    'second' => '1 δευτερόλεπτο|:count δευτερόλεπτα',
    's' => '1 δευτερόλεπτο|:count δευτερόλεπτα',
    'ago' => 'πρίν απο :time',
    'from_now' => 'σε :time απο τώρα',
    'after' => ':time μετά',
    'before' => ':time πρίν',
);
>>>>>>> dev
