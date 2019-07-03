<?php namespace Logimonde\Quickpresse\Components;

use Cms\Classes\ComponentBase;
use Logimonde\Quickpresse\Models\Dashboard as BaseDashboard;
use Logimonde\Quickpresse\Models\Click;
use Logimonde\Quickpresse\Models\Eblast;
use Excel;
use RainLab\Translate\Classes\Translator as languageTranslator;
use Logimonde\Crm\Models\Subaccount;

class Statistics extends ComponentBase
{
    use \Logimonde\Account\Traits\AccountUtils;
    use \Logimonde\Quickpresse\Traits\CommonTraits;

    public $lang;

    public $user;

    public $pageParam;
    public $sortOrder;
    public $perPage;
    public $options;
    public $search;
    public $filter;
    public $startDate;
    public $endDate;
    public $companyId;
    public $subAccountId;
    public $pageNumber;
    public $sort;
    public $screen;


    public function componentDetails()
    {
        return [
            'name' => 'Statistics',
            'description' => 'Statistics Component'
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
        $this->screen = true;
    }

    public function getSortOrderOptions()
    {
        return BaseDashboard::$allowedStatsSorting;
    }

    public function onRun()
    {
        $this->addJs('assets/js/reports.js', '1.009');


        $this->page['printRedirect'] = $this->pageUrl('app-print/statistics');
        if ($print = input('print')) {
            $this->page['print'] = true;
            $this->page['landscape'] = true;
            $this->screen = false;
        }
        if ($this->user->role_id === '3') {
            $this->page['subaccounts'] = $this->getSubAccountsUser();
        }
        if ($export = input('export')) {
            $this->screen = false;
            $this->exportStats();
        }
        $this->page['qps'] = $this->getEblastList();
        $this->page['sortOptions'] = BaseDashboard::$allowedStatsSorting;
    }

    private function getEblastList()
    {
        $this->preparePostVariables();

        $eblasts = BaseDashboard::listEblasts([
            'page' => $this->pageNumber,
            'sort' => $this->sort,
            'perPage' => $this->property('itemsPerPage'),
            'company' => $this->user->role_id === '3' ? $this->user->company->id : $this->companyId,
            'subaccount' => $this->subAccountId,
            'filter' => $this->filter,
            'search' => $this->search,
            'startDate' => ($this->startDate !== null) ? $this->renderDates($this->startDate) : '',
            'endDate' => ($this->endDate !== null) ? $this->renderDates($this->endDate) : '',
            'status' => true,
            'screen' => (input('export') or input('print')) ? false : true,
        ]);

        $this->queryStringParams($eblasts);

        return $eblasts;
    }

    protected function preparePostVariables()
    {
        $this->pageNumber = $this->pageValidation();
        $this->search = $this->page['search'] = input('search');
        $this->filter = $this->page['filter'] = input('filter') != '' ? input('filter') : "active";
        $this->startDate = $this->page['startDate'] = input('start_date') != '' ? input('start_date') : null;
        $this->endDate = $this->page['endDate'] = input('end_date') != '' ? input('end_date') : null;
        $this->companyId = $this->page['company_id'] = input('company_id') != '' ? input('company_id') : null;
        $this->subAccountId = $this->page['subaccount_id'] = input('subaccount_id') != '' ? input('subaccount_id') : null;
        $this->sort = $this->page['sort'] = input('sort') != '' ? input('sort') : $this->property('sortOrder');

        $this->page['name_company'] = input('name_company') != '' ? input('name_company') : '';
        $this->page['titleSort'] = input('sort') == 'date_requested asc' ? 'date_requested desc' : 'date_requested asc';

        $this->pageNumber = !is_null($this->pageNumber) ? $this->pageNumber : $this->property('pageNumber');
        $this->pageNumber = $this->search !== '' ? null : $this->pageNumber;
    }

    protected function queryStringParams($model)
    {
        $options = '?search=' . $this->search;
        $options .= '&sort=' . $this->sort;
        $options .= '&start_date=' . $this->startDate;
        $options .= '&end_date=' . $this->endDate;
        $options .= '&filter=' . $this->filter;
        $options .= '&company_id=' . (($this->user->role_id === '3') ? $this->user->company->id : $this->companyId);
        $options .= '&subaccount_id=' . $this->subAccountId;

        if ($this->screen === true) {
            $options .= '&lastPage=' . (input('lastPage') !== '' ? input('lastPage') : $model->lastPage());
        }
        $this->page['options'] = $options;
    }

    private function setDataInArray()
    {
        $data = array();
        $dataStats = $this->getEblastList();
        $headers = $this->exportHeaders();
        foreach ($dataStats as $dataStat) {
            $item[$headers['send_date']] = $dataStat->date_required . ' ' . $dataStat->time_required;
            $item[$headers['title']] = $dataStat->title;
            if ($this->user->role_id !== '3') {
                $item[$headers['company']] = $dataStat->company_name;
            }
            if ($dataStat->list_name !== '') {
                $item[$headers['destination']] = $dataStat->list_name;
            } else {
                $item[$headers['destination']] = $this->lang == 'en' ? $dataStat->eblast->product->name_en : $dataStat->eblast->product->name_fr;
            }
            $item[$headers['recipients']] = $dataStat->recipients;
            if ($dataStat->recipients > 0) {
                $item[$headers['opened']] = ($dataStat->views / $dataStat->recipients) * 100;
                $item[$headers['clicked']] = ($dataStat->eblast->clicks_total / $dataStat->recipients) * 100;
            } else {
                $item[$headers['opened']] = 0;
                $item[$headers['clicked']] = 0;
            }

            $data[] = $item;
        }
        return $data;
    }

    protected function exportStats()
    {
        $data = $this->setDataInArray();

        Excel::create('statistics', function ($excel) use ($data) {
            $excel->sheet('Statistics', function ($sheet) use ($data) {
                $sheet->fromArray($data);
            });
        })->export('xls');
    }

    private function exportHeaders()
    {
        $headers = [
            'en' => [
                'send_date' => 'Send date',
                'title' => 'Title',
                'destination' => 'Destination',
                'company' => 'Company',
                'recipients' => 'Recipients',
                'opened' => 'Opened',
                'clicked' => 'Clicked',
            ],
            'fr' => [
                'send_date' => "Date d'envoi",
                'title' => 'Titre',
                'destination' => 'Destination',
                'company' => 'Compagnie',
                'recipients' => 'Destinataires',
                'opened' => 'Ouvert',
                'clicked' => 'Cliqué',
            ],
        ];
        return $headers[$this->lang];
    }

    private function getSubAccountsUser()
    {
        return Subaccount::where('company_id', $this->user->company_id)->get();
    }

    public function onLoadClicksContent()
    {
        $this->page['user'] = $this->user;
        if ($id = post('eblast_id')) {
            $campaign = Eblast::whereId($id)->first();

            $this->page['campaign'] = $campaign;
            $clicks = Click::whereIn('eblast_content_id', $this->getContentIds($campaign))
                ->orderBy('actions', 'desc')
                ->get();
            if (count($clicks) > 0) {
                $this->page['clicks'] = $clicks;
            } else {
                $contentLink = array();
                $contents = $campaign->contents()->get();
                foreach ($contents as $key => $content) {
                    if ($content->link_content != '') {
                        $contentLink[$key]['url'] = $content->link_content;
                        $contentLink[$key]['title'] = '';
                        $contentLink[$key]['actions'] = $content->clicks;
                    }
                }
                $this->page['clicks'] = $contentLink;
            }
        }

    }

    private function getContentIds($campaign)
    {
        $ids = array();
        $contents = $campaign->contents()->get();
        foreach ($contents as $content) {
            $ids[] = $content->id;
        }
        return $ids;
    }

}
