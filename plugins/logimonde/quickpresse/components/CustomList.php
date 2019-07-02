<?php namespace Logimonde\Quickpresse\Components;

use Cms\Classes\ComponentBase;
use Logimonde\Quickpresse\Models\CustomList as BaseList;
use Logimonde\Quickpresse\Models\CustomContacts;
use Excel;
use RainLab\Translate\Classes\Translator as languageTranslator;

class CustomList extends ComponentBase
{

    use \Logimonde\Account\Traits\AccountUtils;
    use \Logimonde\Quickpresse\Traits\CommonTraits;

    public $pageParam;
    public $perPage;
    public $user;
    public $lang;

    public function componentDetails()
    {
        return [
            'name' => 'Custom List',
            'description' => 'Custom List Component'
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
                'default' => 'date_begin asc'
            ],
        ];
    }

    public function init()
    {
        $this->user = $this->user();
        $this->perPage = $this->property('itemsPerPage');
        $translator = languageTranslator::instance();
        $this->lang = $translator->getLocale();
    }

    public function onRun()
    {
        $this->page['url'] = $this->pageUrl('app/lists', []);
        $this->page['customLists'] = $this->getCustomList();
        $this->page['company'] = $this->user->company;
        if ($export = $this->param('export')) {
            $this->exportLists($export);
        }

    }

    private function getCustomList()
    {
        $page = $this->pageValidation();
        $search = $this->page['search'] = input('search');
        $company_id = $this->page['company_id'] = input('company_id') != '' ? input('company_id') : null;
        $this->page['name_company'] = input('name_company') != '' ? input('name_company') : '';
        $sort = $this->page['sort'] = input('sort') != '' ? input('sort') : $this->property('sortOrder');
        $page = !is_null($page) ? $page : $this->property('pageNumber');
        $page = $search != '' ? null : $page;

        $library = BaseList::listCustomLists([
            'page' => $page,
            'sort' => $sort,
            'perPage' => $this->property('itemsPerPage'),
            'search' => $search,
            'company' => ($this->user->role_id == '3') ? $this->user->company->id : $company_id,
            'active' => false,
        ]);

        $this->page['sortOptions'] = BaseList::$allowedSortingOptions;
        $options = '?search=' . $search;
        $options .= '&sort=' . $sort;
        $options .= '&lastPage=' . (input('lastPage') != '' ? input('lastPage') : $library->lastPage());
        $this->page['options'] = $options;

        return $library;
    }

    private function setDataInArray($code)
    {
        $data = [];
        $headers = $this->exportHeaders();
        $customList = $this->getCustomListByCode($code);
        $contacts = CustomContacts::where('custom_list_id', $customList->id)->get();

        if (count($contacts) <= 0)
            return false;

        foreach ($contacts as $contact) {
            $item[$headers['first_name']] = $contact->first_name;
            $item[$headers['last_name']] = $contact->last_name;
            $item[$headers['email']] = $contact->email;
            $item[$headers['address']] = $contact->address;
            $item[$headers['phone']] = $contact->phone;
            $item[$headers['title']] = $contact->title;
            $item[$headers['company']] = $contact->company;
            $item[$headers['active']] = $contact->active;
            $data[] = $item;
        }
        return [
            'data' => $data,
            'name' => str_slug($customList->name)
        ];

    }

    protected function exportLists($code)
    {
        $list = $this->setDataInArray($code);
        $data = $list['data'];
        $name = $list['name'];

        Excel::create($name, function ($excel) use ($data, $name) {
            $excel->sheet($name, function ($sheet) use ($data) {
                $sheet->fromArray($data);
            });
        })->export('xls');
    }

    private function exportHeaders()
    {
        $headers = [
            'en' => [
                'first_name' => 'First name',
                'last_name' => 'Last name',
                'email' => 'Email',
                'address' => 'Address',
                'phone' => 'Phone',
                'title' => 'Title',
                'company' => 'Company',
                'active' => 'Active',
            ],
            'fr' => [
                'first_name' => 'Prenom',
                'last_name' => 'Nom',
                'email' => 'Courriel',
                'address' => 'Adresse',
                'phone' => 'Téléphone',
                'title' => 'Titre',
                'company' => 'Compagnie',
                'active' => 'Actif',
            ],
        ];
        return $headers[$this->lang];
    }

    private function getCustomListByCode($code)
    {
        return BaseList::where('public_key', $code)->first();
    }

    public function onDeleteList()
    {
        if ($id = post('id')) {
            BaseList::whereId($id)->delete();

            $url = $this->pageUrl('app/lists');

            return \Redirect::to($url);
        }
    }
}
