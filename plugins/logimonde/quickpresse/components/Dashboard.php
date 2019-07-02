<?php namespace Logimonde\Quickpresse\Components;

use Cms\Classes\ComponentBase;
use Logimonde\Quickpresse\Models\Eblast;
use Logimonde\Quickpresse\Models\EblastSend;
use Logimonde\Quickpresse\Models\Dashboard as BaseDashboard;
use Logimonde\Crm\Models\Contract;
use RainLab\Translate\Classes\Translator as languageTranslator;

class Dashboard extends ComponentBase
{
    use \Logimonde\Account\Traits\AccountUtils;
    use \Logimonde\Quickpresse\Traits\CommonTraits;

    public $lang;

    public $user;

    public $pageParam;
    public $sortOrder;
    public $perPage;

    public $filterOptions = [
        'active' => 'Active',
        'today' => 'Today',
        'new' => 'New',
        'draft' => 'Draft',
        'div1' => '-',
        'contract' => 'Contract',
        'qp_list' => 'Qp List',
        'custom' => 'Custom list',
        'div2' => '-',
        'all' => 'All',
    ];

    public function componentDetails()
    {
        return [
            'name' => 'Dashboard',
            'description' => 'Dashboard Component'
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
                'default' => 'date_requested asc'
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
        return EblastSend::$allowedSortingOptions;
    }

    public function onRun()
    {
        $this->addJs('assets/js/dashboard.js', '1.016');
        $this->page['qps'] = $this->getEblastList();
        $this->page['incomplete'] = $this->getIncompleteCampaigns();
        $this->page['contracts'] = $this->getContractList();
    }

    private function getEblastList()
    {
        $page = $this->pageValidation();
        $search = $this->page['search'] = input('search');
        if ($this->user->role_id === '3') {
            $filter = $this->page['filter'] = input('filter') !== '' ? input('filter') : 'all';
        } else {
            $filter = $this->page['filter'] = input('filter') !== '' ? input('filter') : 'active';
        }
        $company_id = $this->page['company_id'] = input('company_id') !== '' ? input('company_id') : null;
        $this->page['name_company'] = input('name_company') !== '' ? input('name_company') : '';
        $sort = $this->page['sort'] = input('sort') !== '' ? input('sort') : $this->property('sortOrder');
        $this->page['titleSort'] = input('sort') == 'date_requested asc' ? 'date_requested desc' : 'date_requested asc';
        $page = !is_null($page) ? $page : $this->property('pageNumber');
        $page = $search !== '' ? null : $page;

        $eblast = BaseDashboard::listEblasts([
            'page' => $page,
            'sort' => $sort,
            'perPage' => $this->property('itemsPerPage'),
            'company' => $this->user->role_id === '3' ? $this->user->company->id : $company_id,
            'filter' => $filter,
            'search' => $search,
            'status' => false,
        ]);

        $lastPage = $this->page['lastPage'] = $eblast->lastPage();
        $this->page['filterOptions'] = $this->filterOptions;
        $this->page['sortOptions'] = BaseDashboard::$allowedSortingOptions;
        $options = '?search=' . $search;
        $options .= '&filter=' . $filter;
        $options .= '&sort=' . $sort;
        $options .= '&lastPage=' . (input('lastPage') !== '' ? input('lastPage') : $lastPage);
        $this->page['options'] = $options;
        \Session::put('modelFetch', date('Y-m-d H:i:s'));

        return $eblast;
    }

    private function getIncompleteCampaigns()
    {
        if ($this->user->role_id === '3') {
            return Eblast::doesntHave('sends')->where('company_id', $this->user->company->id)->get();
        } else {
            return Eblast::doesntHave('sends')->get();
        }
    }

    private function getContractList()
    {
        if ($this->user->role_id === '3') {
            return Contract::where('company_id', $this->user->company->id)->get();
        } else {
            return false;
        }
    }

    public function onShowContractList()
    {
        $this->page['contracts'] = $this->getContractList();
    }

    public function onGetIncompleteList()
    {
        $this->page['user'] = $this->user;
        $this->page['incomplete'] = $this->getIncompleteCampaigns();
    }

    public function onUpdateStatus()
    {
        $qp = EblastSend::whereId(post('id'))->first();
        if ($qp->approved === '0') {
            $qp->approved = '1';
            $qp->save();
        } else {
            $qp->approved = '0';
            $qp->save();
        }
        return ['status' => $qp->approved];
    }

    public function onSetServer()
    {
        if (post('id')) {
            $qp = EblastSend::whereId(post('id'))->first();
            $qp->server = post('server');
            $qp->save();
        }
    }

    public function onDetectNewEblasts()
    {
        if ($this->user->role_id !== '3') {
            try {
                $modelFetch = \Session::get('modelFetch');
                $recentlyCreated = EblastSend::where('created_at', '>', "$modelFetch")->get();
                if (count($recentlyCreated) <= 0)
                    return false;

                $newQp = count($recentlyCreated);
                return [
                    'new' => ($newQp > 0 ? true : false),
                    'message' => trans('logimonde.quickpresse::lang.submit.new_qp', ['number' => $newQp]),
                ];
            } catch (\Exception $ex) {
                return \Redirect::to($this->pageUrl('account/connection'));
            }
        }
    }

    public function onShowAdminQp()
    {
        if (post('id')) {
            $qp = EblastSend::whereId(post('id'))->first();
            $qp->displayed = '1';
            $qp->save();
        }
    }

    public function onLoadEblastData()
    {
        if (post('id')) {
            $this->page['send'] = EblastSend::whereId(post('id'))->first();
            $this->page['lang'] = $this->lang;
        }
    }

    public function onUpdateEblastDate()
    {
        $data = post();
        if ($id = post('send_id')) {
            $eblast = EblastSend::whereId($id)->first();
            $eblast->date_required = $this->renderDates($data['date']);
            $eblast->time_required = $data['time'];
            $eblast->save();
            \Flash::success(trans('logimonde.quickpresse::lang.submit.msgsubupd'));
            $url = $this->pageUrl('app/dashboard', []);
            return \Redirect::to($url);
        }
    }

    public function getTypeNameEblast($type)
    {
        return trans("logimonde.quickpresse::lang.submit.$type");
    }
}
