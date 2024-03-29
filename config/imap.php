<?php
/*
* File:     imap.php
* Category: config
* Author:   M. Goldenbaum
* Created:  24.09.16 22:36
* Updated:  -
*
* Description:
*  -
*/

return [

    /*
    |--------------------------------------------------------------------------
    | IMAP Host Address
    |--------------------------------------------------------------------------
    |
    | By default this the imap handler will use this given account.
    |
    */
    'default' => env('IMAP_DEFAULT_ACCOUNT', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Available IMAP accounts
    |--------------------------------------------------------------------------
    |
    | Please list all IMAP accounts which you are planning to use within the
    | array below.
    |
    */
    'accounts' => [

        /*'default' => [
            'host'  => env('IMAP_HOST', '173.231.105.254'),
            'port'  => env('IMAP_PORT', 143),
            'encryption'    => env('IMAP_ENCRYPTION', false), // Supported: false, 'ssl', 'tls'
            'validate_cert' => env('IMAP_VALIDATE_CERT', false),
            'username' => env('IMAP_USERNAME', 'logimonde@logimonde.net'),
            'password' => env('IMAP_PASSWORD', '!!Logi224..!!'),
        ],*/

        'default' => [
            'host'  => 'smtp.elasticemail.com',
            'port'  => 2525,
            'encryption'    => '', // Supported: false, 'ssl', 'tls'
            'username' => 'abuse@logimonde.net',
            'password' => '4ffd07e5-2ba5-44e6-8f2e-ee570091ce8c',
        ],

        /*
        'gmail' => [
            'host' => 'imap.gmail.com',
            'port' => 993,
            'encryption' => 'ssl', // Supported: false, 'ssl', 'tls'
            'validate_cert' => true,
            'username' => 'example@gmail.com',
            'password' => 'PASSWORD',
        ],

        'another' => [
            'host' => '',
            'port' => 993,
            'encryption' => false, // Supported: false, 'ssl', 'tls'
            'validate_cert' => true,
            'username' => '',
            'password' => '',
        ]
        */
    ],

    /*
    |--------------------------------------------------------------------------
    | Available IMAP options
    |--------------------------------------------------------------------------
    |
    | Available php imap config parameters are listed below
    |   -Fetch option:
    |       FT_UID  - Message marked as read by fetching the message
    |       FT_PEEK - Fetch the message without setting the "read" flag
    |   -Open IMAP options:
    |       DISABLE_AUTHENTICATOR - Disable authentication properties.
    |                               Use 'GSSAPI' if you encounter the following
    |                               error: "Kerberos error: No credentials cache
    |                               file found (try running kinit) (...)"
    |
    */
    'options' => [
        'fetch' => FT_UID,
        'open' => [
            // 'DISABLE_AUTHENTICATOR' => 'GSSAPI'
        ]
    ]
];
