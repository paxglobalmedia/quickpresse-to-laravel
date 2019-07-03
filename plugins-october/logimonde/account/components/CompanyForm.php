<?php namespace Logimonde\Account\Components;

use Cms\Classes\ComponentBase;
use Logimonde\Quickpresse\Models\Company;
use RainLab\User\Models\User;

class CompanyForm extends ComponentBase
{
    use \Logimonde\Account\Traits\AccountUtils;
    use \Logimonde\Quickpresse\Traits\CommonTraits;

    public $pageParam;
    public $perPage;

    public function componentDetails()
    {
        return [
            'name'        => 'Company Form',
            'description' => 'Company Form Component'
        ];
    }

    public function defineProperties()
    {
        return [
            'pageNumber' => [
                'title' => 'Page number',
                'description' => 'This value is used to determine what page the user is on.',
                'type' => 'string',
                'default' => '{{ :page }}',
            ],
            'itemsPerPage' => [
                'title' => 'Items per page',
                'type' => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'Invalid format of the posts per page value',
                'default' => '10',
            ],
            'sortOrder' => [
                'title' => 'Items order',
                'description' => 'Attribute on which the items should be ordered',
                'type' => 'dropdown',
                'default' => 'created_at desc'
            ],
        ];
    }

    public function init()
    {
        $this->perPage = $this->property('itemsPerPage');

    }

    public function onRun()
    {
        if (post('submit_form') == 'form-company-ok') {
            $this->saveCompany();
        }
        $this->loadAssets();
        $company_id = $this->param('company');
        $company = Company::whereId($company_id)->first();
        if (isset($company->id)) {
            $this->page['accounts'] = $this->getAccounts($company->id);
            $this->page['users'] = $this->getUsers($company->id);
        }
        $this->page['company'] = $company;
        $this->page['redirect'] = $this->pageUrl('account/company', ['company' => $company->id]);
    }


    private function loadAssets()
    {
        $this->addCss('/plugins/logimonde/account/assets/css/profile.css');

        $this->addJs('/plugins/logimonde/account/assets/js/account.js');
    }
}
