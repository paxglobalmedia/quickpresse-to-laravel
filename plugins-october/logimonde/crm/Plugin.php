<?php namespace Logimonde\Crm;

use Backend;
use System\Classes\PluginBase;

/**
 * Crm Plugin Information File
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
            'name'        => 'Crm',
            'description' => 'No description provided yet...',
            'author'      => 'Logimonde',
            'icon'        => 'icon-leaf'
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

    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'Logimonde\Crm\Components\MyComponent' => 'myComponent',
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

        return [
            'logimonde.crm.some_permission' => [
                'tab' => 'Crm',
                'label' => 'Some permission'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return []; // Remove this line to activate

        return [
            'crm' => [
                'label'       => 'Crm',
                'url'         => Backend::url('logimonde/crm/mycontroller'),
                'icon'        => 'icon-leaf',
                'permissions' => ['logimonde.crm.*'],
                'order'       => 500,
            ],
        ];
    }
}
