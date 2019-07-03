<?php namespace Logimonde\Quickpresse\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Templates Back-end Controller
 */
class Templates extends Controller
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

        BackendMenu::setContext('Logimonde.Quickpresse', 'quickpresse', 'templates');
    }
}
