<?php


namespace App\Http\Controllers\Quickpresse;

use App\Http\Controllers\Controller;

class QuickpresseController extends Controller
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
