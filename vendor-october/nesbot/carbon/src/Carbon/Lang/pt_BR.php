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
 * - Cassiano Montanari
 * - Eduardo Dalla Vecchia
 * - David Rodrigues
 * - Matt Pope
 * - François B
 * - Prodis
 * - Marlon Maxwel
 * - JD Isaacks
 * - Raphael Amorim
 * - Rafael Raupp
 * - felipeleite1
 * - swalker
 * - Lucas Macedo
 * - Paulo Freitas
 * - Sebastian Thierer
 */
return array_replace_recursive(require __DIR__.'/pt.php', [
    'period_recurrences' => 'uma|:count vez',
    'period_interval' => 'toda :interval',
    'formats' => [
        'LLL' => 'D [de] MMMM [de] YYYY [às] HH:mm',
        'LLLL' => 'dddd, D [de] MMMM [de] YYYY [às] HH:mm',
    ],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,
]);
=======
return array(
    'year' => '1 ano|:count anos',
    'y' => '1 ano|:count anos',
    'month' => '1 mês|:count meses',
    'm' => '1 mês|:count meses',
    'week' => '1 semana|:count semanas',
    'w' => '1 semana|:count semanas',
    'day' => '1 dia|:count dias',
    'd' => '1 dia|:count dias',
    'hour' => '1 hora|:count horas',
    'h' => '1 hora|:count horas',
    'minute' => '1 minuto|:count minutos',
    'min' => '1 minuto|:count minutos',
    'second' => '1 segundo|:count segundos',
    's' => '1 segundo|:count segundos',
    'ago' => 'há :time',
    'from_now' => 'em :time',
    'after' => 'após :time',
    'before' => ':time atrás',
);
>>>>>>> dev
