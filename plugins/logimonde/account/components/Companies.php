<?php namespace Logimonde\Account\Components;

use Cms\Classes\ComponentBase;
use Logimonde\Quickpresse\Models\Company;

class Companies extends ComponentBase
{
    use \Logimonde\Account\Traits\AccountUtils;
    use \Logimonde\Quickpresse\Traits\CommonTraits;

    public $pageParam;
    public $perPage;
    public $user;

    public function componentDetails()
    {
        return [
            'name' => 'Companies',
            'description' => 'Companies Component'
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
                'default' => '20',
            ],
            'sortOrder' => [
                'title' => 'Items order',
                'description' => 'Attribute on which the items should be ordered',
                'type' => 'dropdown',
                'default' => 'name asc'
            ],
        ];
    }

    public function init()
    {
        $this->user = $this->user();
        $this->perPage = $this->property('itemsPerPage');
    }

    public function onRun()
    {
        $this->page['companies'] = $this->getCompanies();
    }

    private function getCompanies()
    {
        $page = $this->pageValidation();

        $search = $this->page['search'] = input('search');
        $sort = isset($params['sort']) ? $params['sort'] : $this->property('sortOrder');
        $page = !is_null($page) ? $page : $this->property('pageNumber');
        $page = $search != '' ? null : $page;

        $companies = Company::listCompanies([
            'page' => $page,
            'sort' => $sort,
            'perPage' => $this->property('itemsPerPage'),
            'search' => $search
        ]);

        $options = '?search=' . $search;
        $options .= '&sort=' . $sort;
        $options .= '&lastPage=' . (input('lastPage') != '' ? input('lastPage') : $companies->lastPage());
        $this->page['options'] = $options;

        return $companies;
    }
}
