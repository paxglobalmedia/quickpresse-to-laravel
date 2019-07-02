<?php namespace Logimonde\Slider;

use System\Classes\PluginBase;
use Backend;

/**
 * Slider Plugin Information File
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
            'name'        => 'Slider',
            'description' => 'Show Slider',
            'author'      => 'Logimonde',
            'icon'        => 'icon-youtube-play'
        ];
    }

    public function registerComponents()
    {
        return [
            'Logimonde\Slider\Components\ShowImages' => 'ShowImages',
        ];
    }

    public function registerPermissions()
    {
        return [
            'logimonde.slider' => ['tab' => 'Logimonde', 'label' => 'Allowed to manage Sliders'],
        ];
    }

    public function registerNavigation()
    {
        return [
            'Slider' => [
                'label' => 'Slider',
                'url' => Backend::url('logimonde/slider/slider'),
                'icon' => 'icon-youtube-play',
                'permissions' => ['logimonde.slider.*'],
                'order' => 500,
            ]
        ];
    }



}
