<?php namespace Logimonde\Quickpresse\Components;

use Cms\Classes\ComponentBase;
use Logimonde\Quickpresse\Models\Eblast;
use Logimonde\Quickpresse\Models\EblastSend;
use Logimonde\Crm\Models\ItemContract;
use Logimonde\Crm\Models\ItemAgreement;
use RainLab\Translate\Classes\Translator as languageTranslator;

class CampaignSchedule extends ComponentBase
{
    use \Logimonde\Account\Traits\AccountUtils;
    use \Logimonde\Quickpresse\Traits\CommonTraits;

    public $user;
    public $lang;

    public function componentDetails()
    {
        return [
            'name' => 'Campaign Schedule',
            'description' => 'Campaign Schedule Component'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function init()
    {
        $this->user = $this->user();
        $translator = languageTranslator::instance();
        $this->lang = $translator->getLocale();
    }

    public function onRun()
    {
        $this->addJs('/plugins/logimonde/quickpresse/assets/js/campaign_schedule.js', '1.011');

        if ($id = $this->param('campaign')) {
            $campaign = $this->getCampaign($id);
            $this->page['campaign'] = $campaign;
            $this->page['balance'] = $this->getQuickPresseBalance($campaign);
        }
    }

    private function getCampaign($campaign_id)
    {
        return Eblast::whereId($campaign_id)->first();
    }

    public function onScheduleCampaign()
    {
        $data = post();
        if (isset($data['campaign_id']) && $data['campaign_id'] !== '') {
            $campaign = $this->getCampaign($data['campaign_id']);
            if (isset($data['send_id']) && $data['send_id'] !== '') {
                $schedule = EblastSend::whereId($data['send_id'])->first();
            } else {
                $schedule = new EblastSend;
            }
            $schedule->eblast_id = $data['campaign_id'];
            $schedule->date_required = $this->renderDates($data['date'], $this->lang);
            $schedule->time_required = $data['time'];
            $schedule->recipients = $this->getTotalRecipients($campaign);
            $schedule->slug = $schedule->date_required . '-' . str_slug($campaign->title, "-") . '-' . str_random(3);
            $schedule->pages = $this->getPagesFromContent($campaign);
            $schedule->unit_price = $this->getUnitPrice($campaign);
            $schedule->save();

            $this->page['campaign'] = $campaign;
            $this->page['balance'] = $balance = $this->getQuickPresseBalance($campaign);
            return ['balance' => $balance];
        }
    }

    public function onDeleteDate()
    {
        if ($id = post('id')) {
            EblastSend::whereId($id)->delete();

            $campaign = $this->getCampaign(post('campaign_id'));
            $this->page['campaign'] = $campaign;
            $this->page['balance'] = $this->getQuickPresseBalance($campaign);
        }
    }

    public function onEditDate()
    {
        if ($id = post('id')) {
            $this->page['send'] = EblastSend::whereId($id)->first();
        }
        $campaign = $this->getCampaign(post('campaign_id'));
        $this->page['campaign'] = $campaign;
        $this->page['balance'] = $this->getQuickPresseBalance($campaign);
    }

    private function getPagesFromContent($campaign)
    {
        $pages = $campaign->contents()->where('block', 'like', '%block-flyer%')->get();
        return count($pages) > 1 ? count($pages) : 1;
    }

    private function getItemContract($campaign)
    {
        return ItemContract::where('product_id', $campaign->product_id)
            ->where('contract_id', $campaign->contract_id)->first();
    }

    private function getCampaignDates($campaign)
    {
        return EblastSend::where('eblast_id', $campaign->id)->count();
    }

    private function getQuickPresseBalance($campaign)
    {
        $itemContract = $this->getItemContract($campaign);
        $pages = $this->getPagesFromContent($campaign);
        $dates = $this->getCampaignDates($campaign);
        if ($campaign->type_qp == 'contract') {
            $balance = $itemContract->qp_balance - ($pages * $dates);
        } else {
            $balance = 1;
        }
        return $balance;
    }

    private function getQueryListCount($campaign)
    {
        if ($campaign->product->qp_audition > 0) {
            return $campaign->product->qp_audition;
        } else {
            $sql = $campaign->query_string;
            $query = \Db::connection('circulation');
            if (!empty($sql)) {
                $results = $query->select($sql);
                return count($results);
            } else {
                return 0;
            }
        }
    }

    private function getTotalRecipients($campaign)
    {
        if ($campaign->type_qp == 'custom') {
            return $campaign->custom_list->list_count;
        } else {
            return $this->getQueryListCount($campaign);
        }
    }

    private function getUnitPrice($campaign)
    {
        if ($campaign->type_qp == 'contract') {
            $contract = ItemContract::where('contract_id', $campaign->contract_id)
                ->where('product_id', $campaign->product_id)->first();
            if ($contract) {
                return ($contract->price_sale > 0 ? $contract->price_sale : $contract->price_list);
            } else {
                return $campaign->product->price ;
            }
        } else if ($campaign->type_qp == 'qp_list') {
            if (isset($campaign->agreement)) {
                $agreement = ItemAgreement::where('agreement_id', $campaign->agreement_id)
                    ->where('product_id', $campaign->product_id)->first();
                return ($agreement->price_sale > 0 ? $agreement->price_sale : $agreement->price_list);
            } else {
                return $campaign->product->price ;
            }
        } else if ($campaign->type_qp == 'custom') {
            return $campaign->product->price;
        }
    }

}
