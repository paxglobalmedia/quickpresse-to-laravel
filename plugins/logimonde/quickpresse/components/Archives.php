<?php namespace Logimonde\Quickpresse\Components;

use Cms\Classes\ComponentBase;
use Logimonde\Quickpresse\Models\Eblast;
use Logimonde\Quickpresse\Models\EblastSend;
use Logimonde\Quickpresse\Models\EblastContent;
use Logimonde\Quickpresse\Models\Dashboard as BaseDashboard;
use RainLab\Translate\Classes\Translator as languageTranslator;
use Carbon\Carbon;

class Archives extends ComponentBase
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
        'all' => 'All',
    ];

    public function componentDetails()
    {
        return [
            'name' => 'Archives',
            'description' => 'Archives Component'
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
                'default' => 'date_required desc'
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
        $this->addJs('assets/js/dashboard.js', '1.012');
        $this->page['qps'] = $this->getEblastList();
    }

    private function getEblastList()
    {
        $page = $this->pageValidation();
        $search = $this->page['search'] = input('search');
        $filter = $this->page['filter'] = input('filter') !== '' ? input('filter') : 'active';
        $company_id = $this->page['company_id'] = input('company_id') !== '' ? input('company_id') : null;
        $this->page['name_company'] = input('name_company') !== '' ? input('name_company') : '';
        $sort = $this->page['sort'] = input('sort') !== '' ? input('sort') : 'date_requested desc';
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
            'status' => true,
        ]);

        $lastPage = $this->page['lastPage'] = $eblast->lastPage();
        $this->page['filterOptions'] = $this->filterOptions;
        $this->page['sortOptions'] = BaseDashboard::$allowedSortingOptions;
        $options = '?search=' . $search;
        $options .= '&filter=' . $filter;
        $options .= '&sort=' . $sort;
        $options .= '&lastPage=' . (input('lastPage') !== '' ? input('lastPage') : $lastPage);
        $this->page['options'] = $options;

        return $eblast;
    }

    public function onIncludeCampaignSection()
    {
        if ($id = post('campaign_id')) {
            $campaign = $this->getCampaign($id);
            $campaign->show_qp_section = post('qp_section');
            $campaign->period_show = post('qp_section') === 1 ? '5' : '0';
            $campaign->save();
        }
    }

    public function onCopyCampaign()
    {
        if ($id = post('id')) {
            $campaign = $this->getCampaign($id);
            $copy_campaign = $campaign->replicate();
            $copy_campaign->title = $campaign->title . ' - ' . ($this->lang == 'en' ? 'Copy' : 'Copie');
            $copy_campaign->active = '0';
            $copy_campaign->total_cost = '0';
            $copy_campaign->save();

            $content = EblastContent::where('eblast_id', $campaign->id)->first();
            $copy_content = $content->replicate();
            $copy_content->eblast_id = $copy_campaign->id;
            $copy_content->clicks = 0;
            $copy_content->code = str_random(48);
            $copy_content->save();

            return \Redirect::to($this->pageUrl('app/manage', ['campaign' => $copy_campaign->id]));
        }

    }

    public function getSentStatus($id)
    {
        $send = EblastSend::whereId($id)->first();
        $diff = $this->getDifferenceTime($send->date_send, $send->sent_final_at);

        if ($diff > 100) {
            return true;
        } else {
            return false;
        }
    }

    public function showDifferenceTime($date_start, $date_end)
    {
        return $this->getDifferenceTime($date_start, $date_end);
    }

    private function getDifferenceTime($date_start, $date_end)
    {
        $start = Carbon::parse($date_start);
        $end = Carbon::parse($date_end);
        return $start->diffInMinutes($end);
    }

    private function getCampaign($id)
    {
        return Eblast::whereId($id)->first();
    }

    public function onResendEblast()
    {
        if ($id = post('eblast')) {
            $send = EblastSend::whereId($id)->first();
            $send->status = '0';
            $send->date_send = null;
            $send->save();
            return \Redirect::to($this->pageUrl('app/archives'));
        }
    }
}
