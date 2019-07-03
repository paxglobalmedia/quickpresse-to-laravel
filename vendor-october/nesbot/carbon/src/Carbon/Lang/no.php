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
/*
 * Authors:
 * - Daniel S. Billing
 * - Paul
 * - Jimmie Johansson
 * - Jens Herlevsen
 */
return array_replace_recursive(require __DIR__.'/nb.php', [
    'formats' => [
        'LLL' => 'D. MMMM YYYY HH:mm',
        'LLLL' => 'dddd, D. MMMM YYYY [kl.] HH:mm',
    ],
    'calendar' => [
        'nextWeek' => 'på dddd [kl.] LT',
        'lastWeek' => '[i] dddd[s kl.] LT',
    ],
]);
=======
return array(
    'year' => '1 år|:count år',
    'y' => '1 år|:count år',
    'month' => '1 måned|:count måneder',
    'm' => '1 måned|:count måneder',
    'week' => '1 uke|:count uker',
    'w' => '1 uke|:count uker',
    'day' => '1 dag|:count dager',
    'd' => '1 dag|:count dager',
    'hour' => '1 time|:count timer',
    'h' => '1 time|:count timer',
    'minute' => '1 minutt|:count minutter',
    'min' => '1 minutt|:count minutter',
    'second' => '1 sekund|:count sekunder',
    's' => '1 sekund|:count sekunder',
    'ago' => ':time siden',
    'from_now' => 'om :time',
    'after' => ':time etter',
    'before' => ':time før',
);
>>>>>>> dev
