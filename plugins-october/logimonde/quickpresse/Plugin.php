<?php namespace Logimonde\QuickPresse;

use Backend;
use Illuminate\Support\Facades\Artisan;
use System\Classes\PluginBase;
use RainLab\Translate\Classes\Translator as languageTranslator;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Foundation\AliasLoader;
use Logimonde\QuickPresse\Classes\SendRegular;
use Logimonde\QuickPresse\Classes\SendElastic;
use Logimonde\QuickPresse\Classes\MailServer;
use Logimonde\QuickPresse\Classes\BounceManager;


/**
 * QuickPresse Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name' => 'Logimonde QuickPresse',
            'description' => 'No description provided yet...',
            'author' => 'Logimonde',
            'icon' => 'icon-envelope-o'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        App::register('\Maatwebsite\Excel\ExcelServiceProvider');
        App::register('\Yangqi\Htmldom\HtmldomServiceProvider');

        $alias = AliasLoader::getInstance();
        $alias->alias('Excel', 'Maatwebsite\Excel\Facades\Excel');
        $alias->alias('Htmldom', 'Yangqi\Htmldom\Htmldom');
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {

        return [
            'Logimonde\QuickPresse\Components\Library' => 'Library',
            'Logimonde\QuickPresse\Components\Manage' => 'Manage',
            'Logimonde\QuickPresse\Components\Templates' => 'Templates',
            'Logimonde\QuickPresse\Components\CampaignForm' => 'CampaignForm',
            'Logimonde\QuickPresse\Components\CampaignContent' => 'CampaignContent',
            'Logimonde\QuickPresse\Components\CampaignSchedule' => 'CampaignSchedule',
            'Logimonde\QuickPresse\Components\CustomList' => 'CustomList',
            'Logimonde\QuickPresse\Components\CustomListForm' => 'CustomListForm',
            'Logimonde\QuickPresse\Components\ImportContacts' => 'ImportContacts',
            'Logimonde\QuickPresse\Components\CustomContacts' => 'CustomContacts',
            'Logimonde\QuickPresse\Components\CustomContactsForm' => 'CustomContactsForm',
            'Logimonde\QuickPresse\Components\Reports' => 'Reports',
            'Logimonde\QuickPresse\Components\Dashboard' => 'Dashboard',
            'Logimonde\QuickPresse\Components\Archives' => 'Archives',
            'Logimonde\QuickPresse\Components\ShowQuickpresse' => 'ShowQuickpresse',
            'Logimonde\QuickPresse\Components\Statistics' => 'Statistics',
            'Logimonde\QuickPresse\Components\Unsubscribe' => 'Unsubscribe',
            'Logimonde\QuickPresse\Components\Bounces' => 'Bounces',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {

        return [
            'quickpresse' => [
                'label' => 'QuickPresse',
                'url' => Backend::url('logimonde/quickpresse/templates'),
                'icon' => 'icon-plane',
                'permissions' => ['logimonde.quickpresse.*'],
                'order' => 200,
                'sideMenu' => [
                    'templates' => [
                        'label' => 'Templates',
                        'icon' => 'icon-crop',
                        'url' => Backend::url('logimonde/quickpresse/templates'),
                        'permissions' => ['logimonde.quickpresse.access_template'],
                    ],
                ]
            ],
        ];
    }

    public function registerMarkupTags()
    {
        return [
            'filters' => [
                'number' => function ($number, $decimals = 2) {
                    $translator = languageTranslator::instance();
                    $lang = $translator->getLocale();

                    if ($lang == 'fr') {
                        $format = number_format($number, $decimals, ',', ' ');
                    } else if ($lang == 'en') {
                        $format = number_format($number, $decimals, '.', ',');
                    } else {
                        $format = number_format($number, $decimals, '.', ',');
                    }
                    return $format;
                },
                'phone' => [$this, 'phoneFormat'],
                'money' => function ($number, $decimals = 2) {
                    $translator = languageTranslator::instance();
                    $lang = $translator->getLocale();

                    if ($lang == 'fr') {
                        $money = number_format($number, $decimals, ',', ' ') . ' $';
                    } else if ($lang == 'en') {
                        $money = '$' . number_format($number, $decimals, '.', ',');
                    } else {
                        $money = '$' . number_format($number, $decimals, '.', ',');
                    }
                    return $money;
                },
                'mytime' => [$this, 'myTimeFormat'],
                'mydate' => [$this, 'myDateFormat'],
                'mydatelong' => [$this, 'myDateLongFormat'],
                'remove_empty_tags' => function ($html) {
                    $html = str_replace('&nbsp;', ' ', $html);
                    do {
                        $tmp = $html;
                        $html = preg_replace(
                            '#<([^ >]+)[^>]*>[[:space:]]*</\1>#', '', $html);
                    } while ($html !== $tmp);

                    return $html;
                }
            ]
        ];
    }

    public function phoneFormat($phoneNumber)
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        if (strlen($phoneNumber) > 10) {
            $countryCode = substr($phoneNumber, 0, strlen($phoneNumber) - 10);
            $areaCode = substr($phoneNumber, -10, 3);
            $nextThree = substr($phoneNumber, -7, 3);
            $lastFour = substr($phoneNumber, -4, 4);

            $phoneNumber = '+' . $countryCode . ' (' . $areaCode . ') ' . $nextThree . '-' . $lastFour;
        } else if (strlen($phoneNumber) === 10) {
            $areaCode = substr($phoneNumber, 0, 3);
            $nextThree = substr($phoneNumber, 3, 3);
            $lastFour = substr($phoneNumber, 6, 4);

            $phoneNumber = '(' . $areaCode . ') ' . $nextThree . '-' . $lastFour;
        } else if (strlen($phoneNumber) === 7) {
            $nextThree = substr($phoneNumber, 0, 3);
            $lastFour = substr($phoneNumber, 3, 4);

            $phoneNumber = $nextThree . '-' . $lastFour;
        }

        return $phoneNumber;
    }


    public function myDateFormat($time)
    {
        $translator = languageTranslator::instance();
        $lang = $translator->getLocale();
        $format = "";
        $timeObj = new Carbon($time);
        if ($lang == 'fr') {
            $format = 'd-m-Y';
        } else if ($lang === 'en') {
            $format = 'm-d-Y';
        }

        return date($format, $timeObj->getTimestamp());

    }

    public function myDateLongFormat($time)
    {
        $translator = languageTranslator::instance();
        $lang = $translator->getLocale();
        $format = '';
        $timeObj = new Carbon($time);
        if ($lang == 'fr') {
            setlocale(LC_TIME, 'fr_CA');
            $format = '%A, %e %B %Y';
        } else if ($lang === 'en') {
            $format = '%A %B %e, %Y';
        }

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $format = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $format);
        }

        return ucfirst(utf8_encode(strftime($format, $timeObj->getTimestamp())));

    }

    public function myTimeFormat($time)
    {
        if (substr($time, 11) == '00:00:00') {
            return '';
        }
        $translator = languageTranslator::instance();
        $lang = $translator->getLocale();
        $format = '';
        $timeObj = new Carbon($time);
        if ($lang == 'fr') {
            $format = 'G:i';
        } else if ($lang == 'en') {
            $format = 'g:i a';
        }
        return date($format, $timeObj->getTimestamp());

    }

    public function registerSettings()
    {
        return [
            'qp' => [
                'label' => 'Quick Presse',
                'description' => 'Manage application settings.',
                'icon' => 'icon-plane',
                'class' => 'Logimonde\Quickpresse\Models\Settings',
                'order' => 500,
            ]
        ];
    }

    public function registerSchedule($schedule)
    {
        $schedule->call(function () {
            SendRegular::send();

        })->cron('* * * * *');

        $schedule->call(function () {
            SendElastic::send();
        })->cron('3,8,13,18,23,28,33,38,43,48,53,58 * * * *');


        $schedule->call(function () {
            $mailServer = new MailServer;
            $mailServer->setServer();
        })->everyFiveMinutes();
    }
}
