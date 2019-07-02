<?php namespace Logimonde\Partners;

use Backend;
use System\Classes\PluginBase;

/**
 * Partners Plugin Information File
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
            'name'        => 'Partners',
            'description' => 'Adds the ability to add and show your partners.',
            'author'      => 'Logimonde',
            'icon'        => 'icon-bookmark'
        ];
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            'Logimonde\Partners\Components\PartnerOverview' => 'partnerOverview',
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return [
            'partners' => [
                'label'       => 'Partners',
                'url'         => Backend::url('logimonde/partners/partners'),
                'icon'        => 'icon-bookmark',
                'permissions' => ['logimonde.partners.*'],
                'order'       => 500,
            ],
        ];
    }

}
