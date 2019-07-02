<?php namespace Logimonde\Partners\Components;

use Cms\Classes\ComponentBase;
use Logimonde\Partners\Models\Partner;

class PartnerOverview extends ComponentBase
{

    public $partners;

    public function componentDetails()
    {
        return [
            'name' => 'Partner Overview',
            'description' => 'Shows an overview of partners'
        ];
    }

    public function defineProperties()
    {
        $options = array();
        for ($i = 0; $i < 11; $i++) {
            $options[$i] = $i;
        }
        $orderByOptions = array("name" => "Name", "weight" => "Weight", "created_at" => "Created At");
        $orderOptions = array("asc" => "Ascending", "desc" => "Descending");
        return [
            'maxItems' => [
                'title' => 'Amount of Partners',
                'type' => 'dropdown',
                'default' => '0',
                'placeholder' => 'Select amount of partners (0 = all partners)',
                'options' => $options
            ],
            'orderBy' => [
                'title' => 'Order By',
                'type' => 'dropdown',
                'default' => 'created_at',
                'placeholder' => 'Order partners by',
                'options' => $orderByOptions
            ],
            'order' => [
                'title' => 'Order',
                'type' => 'dropdown',
                'default' => 'desc',
                'placeholder' => 'Order partners',
                'options' => $orderOptions
            ]
        ];
    }

    public function onRun()
    {
        $this->addJs('assets/js/partners.js', '1.03');
        $maxItems = $this->property('maxItems');
        $orderBy = $this->property('orderBy');
        $order = $this->property('order');
        if (!$orderBy) {
            $orderBy = 'created_at';
        }
        if (!$order) {
            $order = 'desc';
        }
        if ($maxItems != 0) {
            $this->partners = Partner::orderBy($orderBy, $order)->take($maxItems)->get();
        } else {
            $this->partners = Partner::all();
        }
        $this->page['total'] = count($this->partners);
        $this->page['iterate'] = ceil(count($this->partners) / 6);
    }

    public function onDetectScreenSize()
    {

        if (post('width') > '768') {
            return false;
        } else {
            $this->page['partners'] = $this->partners = Partner::all();
            return [
                '#carousel-partners' => $this->renderPartial('@_carousel_mobile')
            ];
        }

    }
}
