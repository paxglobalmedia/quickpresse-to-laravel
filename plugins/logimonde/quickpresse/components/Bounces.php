<?php namespace Logimonde\QuickPresse\Components;

use Cms\Classes\ComponentBase;
use Logimonde\QuickPresse\Models\Bounce as BaseBounce;
use Excel;
use RainLab\Translate\Classes\Translator as languageTranslator;

class Bounces extends ComponentBase
{
    use \Logimonde\Account\Traits\AccountUtils;
    use \Logimonde\Quickpresse\Traits\CommonTraits;

    public $user;
    public $lang;

    public $pageParam;
    public $sortOrder;
    public $perPage;
    public $options;
    public $search;
    public $pageNumber;
    public $sort;
    public $screen;

    public function componentDetails()
    {
        return [
            'name'        => 'Bounces',
            'description' => 'Bounces Component'
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
                'default' => '15',
            ],
            'sortOrder' => [
                'title' => 'Items order',
                'description' => 'Attribute on which the items should be ordered',
                'type' => 'dropdown',
                'default' => 'date_requested desc'
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

    public function getSortOrderOptions()
    {
        return BaseBounce::$allowedSortingOptions;
    }

    public function onRun()
    {
        $this->addJs('assets/js/bounces.js', '1.001');
        $this->page['bounces'] = $this->getBounceList();
        $this->page['sortOptions'] = BaseBounce::$allowedSortingOptions;
    }

    private function getBounceList()
    {
        $this->preparePostVariables();

        $eblasts = BaseBounce::listBounces([
            'page' => $this->pageNumber,
            'sort' => $this->sort,
            'perPage' => $this->property('itemsPerPage'),
            'company' => $this->user->role_id === '3' ? $this->user->company->id : $this->companyId,
            'search' => $this->search,
            'screen' => (input('export') or input('print')) ? false : true,
        ]);

        $this->queryStringParams($eblasts);

        return $eblasts;
    }

    protected function preparePostVariables()
    {
        $this->pageNumber = $this->pageValidation();
        $this->search = $this->page['search'] = input('search');
        $this->sort = $this->page['sort'] = input('sort') !== '' ? input('sort') : $this->property('sortOrder');
        $this->page['titleSort'] = input('sort') == 'error_count asc' ? 'error_count desc' : 'error_count asc';

        $this->pageNumber = !is_null($this->pageNumber) ? $this->pageNumber : $this->property('pageNumber');
        $this->pageNumber = $this->search !== '' ? null : $this->pageNumber;
    }

    protected function queryStringParams($model)
    {
        $options = '?search=' . $this->search;
        $options .= '&sort=' . $this->sort;

        if ($this->screen === true) {
            $options .= '&lastPage=' . (input('lastPage') !== '' ? input('lastPage') : $model->lastPage());
        }
        $this->page['options'] = $options;
    }

}
