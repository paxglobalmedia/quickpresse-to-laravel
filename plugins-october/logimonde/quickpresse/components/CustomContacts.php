<?php namespace Logimonde\Quickpresse\Components;

use Cms\Classes\ComponentBase;
use Logimonde\Quickpresse\Models\CustomList as BaseList;
use Logimonde\Quickpresse\Models\CustomContacts as BaseContacts;

class CustomContacts extends ComponentBase
{
    use \Logimonde\Account\Traits\AccountUtils;
    use \Logimonde\Quickpresse\Traits\CommonTraits;

    public $pageParam;
    public $perPage;
    public $user;

    public function componentDetails()
    {
        return [
            'name'        => 'Custom Contacts',
            'description' => 'Custom Contacts Component'
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
                'default' => 'updated_at desc'
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
        $this->addJs('/plugins/logimonde/quickpresse/assets/js/lists.js');
        if ($id = $this->param('list')) {
            $customList = BaseList::whereId($id)->first();
            $this->page['url'] = $this->pageUrl('app/list', []);
            $this->page['customList'] = $customList;
            $this->page['customContacts'] = $this->getCustomContacts($id);
            $this->page['company'] = $this->user->company;
        }
    }

    private function getCustomContacts($list_id)
    {
        $page = $this->pageValidation();
        $search = $this->page['search'] = input('search');
        $this->page['company_id'] = input('company_id') != '' ? input('company_id') : null;
        $this->page['name_company'] = input('name_company') != '' ? input('name_company') : '';
        $sort = $this->page['sort'] = input('sort') != '' ? input('sort') : $this->property('sortOrder');
        $page = !is_null($page) ? $page : $this->property('pageNumber');
        $page = $search !== '' ? null : $page;

        $library = BaseContacts::listCustomContacts([
            'page' => $page,
            'sort' => $sort,
            'perPage' => $this->property('itemsPerPage'),
            'search' => $search,
            'list' => $list_id,
        ]);

        $this->page['sortOptions'] = BaseContacts::$allowedSortingOptions;
        $options = '?search=' . $search;
        $options .= '&sort=' . $sort;
        $options .= '&lastPage=' . (input('lastPage') !== '' ? input('lastPage') : $library->lastPage());
        $this->page['options'] = $options;

        return $library;
    }

    public function onDeleteContact()
    {
        if ($id = post('id')) {
            BaseContacts::whereId($id)->delete();
        }
    }
}
