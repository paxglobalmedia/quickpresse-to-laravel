<?php namespace Logimonde\Quickpresse\Components;

use Cms\Classes\ComponentBase;
use Logimonde\Quickpresse\Models\Template as BaseTemplate;

class Templates extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Templates',
            'description' => 'Templates Component'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {

        $this->page['templates'] = BaseTemplate::isPublished()
            ->where('admin', '0')
            ->orderBy('order')->get();
    }
}
