<?php namespace Logimonde\Partners\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Partners Back-end Controller
 */
class Partners extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Logimonde.Partners', 'partners', 'partners');
    }
}