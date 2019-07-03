<?php namespace Logimonde\Quickpresse\Components;

use Cms\Classes\ComponentBase;
use Logimonde\Crm\Models\Agreement;
use Logimonde\Crm\Models\ItemContract;
use Logimonde\Quickpresse\Models\Eblast;
use Logimonde\Quickpresse\Models\EblastContent;
use Logimonde\Quickpresse\Models\EblastSend;
use Logimonde\Crm\Models\ItemAgreement;
use Logimonde\Crm\Models\Contract;
use Logimonde\Crm\Models\Product;
use Logimonde\Quickpresse\Models\Settings;
use Logimonde\QuickPresse\Classes\EblastFooter;
use Logimonde\Quickpresse\Models\Click;

class Manage extends ComponentBase
{
    use \Logimonde\Account\Traits\AccountUtils;

    public $user;
    public $kind;

    public function componentDetails()
    {
        return [
            'name' => 'Manage QP',
            'description' => 'Manage QP Component'
        ];
    }

    public function defineProperties()
    {
        return [
            'kind' => [
                'title' => 'Kind of ths form',
                'type' => 'dropdown',
                'default' => 'normal',
                'placeholder' => 'Select kind of the form',
                'options' => ['normal' => 'Normal', 'clone' => 'Clone']
            ]
        ];
    }

    public function init()
    {
        $this->user = $this->user();
        $this->kind = $this->property('kind');
    }

    public function onRun()
    {
        $this->addJs('/plugins/logimonde/quickpresse/assets/js/campaign.js', '1.028');
        if ($id = $this->param('campaign')) {
            $campaign = $this->getCampaign($id);
            if ($campaign) {
                if ($campaign->contents_count > 0) {
                    $this->validateTemplateContent($campaign);
                }
                $this->showCampaignData($campaign);
            } else {
                \Flash::error(trans('logimonde.quickpresse::lang.content.not_qp'));
                return \Redirect::to($this->pageUrl('app/dashboard'));
            }
        }
    }

    private function showCampaignData($campaign)
    {
        $this->page['campaign'] = $campaign;
        $pages = $this->getPagesFromContent($campaign);
        $this->page['pages'] = $pages;
        $this->page['total_cost'] = $this->getTotalCostCampaign($campaign, $pages);
        if ($campaign->type_qp == 'custom') {
            $this->page['reached'] = $campaign->custom_list->list_count;
        } else {
            $this->page['reached'] = $this->getQueryListCount($campaign);
        }

        if ($campaign->type_qp == 'contract') {
            $item = $this->getItemContract($campaign);
            $this->page['qp_balance'] = $item->qp_balance;
        }
        $this->sentEblastsCampaign($campaign);
        $this->page['eblastApproved'] = $this->validateApprovedEblast($campaign);
    }

    private function validateApprovedEblast($campaign)
    {
        return $campaign->sends()->where('approved', '1')->where('status', '0')->get();
    }

    private function sentEblastsCampaign($campaign)
    {
        $this->page['sentEblats'] = $campaign->sends()->where('status', '1')->get();
    }

    private function getCampaign($id)
    {
        return Eblast::whereId($id)->first();
    }

    private function validateTemplateContent($campaign)
    {
        if ($campaign->template_id === '0') {
            EblastContent::where('eblast_id', $campaign->id)->delete();
        }
    }

    public function onChangeStatusCampaign()
    {
        if ($id = post('campaign_id')) {
            $campaign = $this->getCampaign($id);
            $campaign->active = post('active');
            $campaign->save();
            $pages = $this->getPagesFromContent($campaign);
            if ($campaign->active === '1') {
                $totalCost = $this->getTotalCostCampaign($campaign, $pages);

                $campaign = $this->updateCampaignSummary($campaign, $totalCost, $pages);
                $this->updateCrmTotalCostCampaign($campaign, $totalCost);
            }
            $this->updateContractBalance($campaign, $pages);
            $this->showCampaignData($campaign);
            if (post('redirect')) {
                \Flash::info(trans('logimonde.quickpresse::lang.content.qp_active'));
                return \Redirect::to($this->pageUrl('app/manage', ['campaign' => $id]));
            }
        }
    }

    private function updateCampaignSummary($campaign, $total, $pages)
    {
        $totalQp = $campaign->total_mailing * $pages;
        $campaign = Eblast::whereId($campaign->id)->first();
        $campaign->total_cost = $total;
        $campaign->total_qp = $totalQp;
        $campaign->save();
        return $campaign;
    }

    private function updateCrmTotalCostCampaign($campaign, $total)
    {
        if ($campaign->type_qp != 'contract') {
            $agreement = Agreement::whereId($campaign->agreement_id)->first();
            if ($agreement) {
                if ($agreement->status === '2') {
                    $agreement->amount = $total;
                    $agreement->save();
                }
            }
        }
    }

    private function updateContractBalance($campaign, $pages)
    {

        if ($campaign->type_qp == 'contract') {

            $total = $campaign->total_mailing * $pages;
            $contract = Contract::where('id', $campaign->contract_id)->first();
            if ($campaign->active === '1') {
                $contract->decrement('balance', $total);
            } else {
                $contract->increment('balance', $total);
            }
            $this->updateItemContractBalance($campaign, $total);
        }
    }

    private function updateItemContractBalance($campaign, $total)
    {
        $item = $this->getItemContract($campaign);
        if ($campaign->active === '1') {
            $item->decrement('qp_balance', $total);
        } else {
            $item->increment('qp_balance', $total);
        }

    }

    private function getItemContract($campaign)
    {
        return ItemContract::where('product_id', $campaign->product_id)
            ->where('contract_id', $campaign->contract_id)->first();
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

    public function onSetDaysPaxSection()
    {
        if ($id = post('campaign_id')) {
            $campaign = $this->getCampaign($id);
            $campaign->period_show = post('days_pax');
            $campaign->save();
            return post();
        }
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

    public function onDeleteCampaign()
    {
        if ($id = post('campaign_id')) {
            $campaign = Eblast::whereId($id)->first();
            $contents = $campaign->contents()->get();
            if (count($contents) > 0) {
                $ids = array();
                foreach ($contents as $content) {
                    $ids[] = $content->id;
                }
                EblastContent::where('eblast_id', $id)->delete();
                Click::whereIn('eblast_content_id', $ids)->delete();
            }
            Eblast::whereId($id)->delete();
        }
    }

    private function getPagesFromContent($campaign)
    {
        $pages = $campaign->contents()->where('block', 'like', '%block-flyer%')->get();
        return count($pages) > 1 ? count($pages) : 1;
    }

    private function getTotalCostCampaign($campaign, $pages)
    {
        $price = 0;
        if ($campaign->type_qp == 'contract') {
            $contract = ItemContract::where('contract_id', $campaign->contract_id)
                ->where('product_id', $campaign->product_id)->first();
            if ($contract) {
                $price = ($contract->price_sale > 0 ? $contract->price_sale : $contract->price_list) * $pages;
            }
        } else if ($campaign->type_qp == 'qp_list') {
            if (isset($campaign->agreement)) {
                $agreement = ItemAgreement::where('agreement_id', $campaign->agreement_id)
                    ->where('product_id', $campaign->product_id)->first();
                $price = ($agreement->price_sale > 0 ? $agreement->price_sale : $agreement->price_list) * $pages;
            } else {
                $price = $campaign->product->price * $pages;
            }
        } else if ($campaign->type_qp == 'custom') {
            $reached = $campaign->custom_list->list_count;
            $price = $campaign->product->price * $pages * $reached;
        }
        $sends = $campaign->total_mailing;
        if ($sends > 0) {
            $price = $price * $sends;
        }
        return $price;
    }


    public function onSendTestByEmail()
    {
        if ($id = post('sendId')) {
            $qp = $this->getQuickPresse($id);
            $data['qp'] = $qp;

            if (!$campaign = $this->getCampaign(post('campaign_id')))
                return false;

            $data['host'] = config('app.url');
            $data['campaign'] = $campaign;
            $data['fileHtml'] = $this->readHtmlFile($campaign->contents[0]->source_path);
            $data['footer'] = EblastFooter::create($campaign);
            $data['template'] = \View::make('logimonde.quickpresse::template.' . $campaign->template->email_file, $data)->render();

            \Mail::send('logimonde.quickpresse::layout.' . $campaign->template->layout, $data, function ($message) use ($campaign) {
                $message->to($campaign->user->email, $campaign->user->name);
                $message->replyTo('logimonde@logimonde.com');
                if ($campaign->sender_name !== '') {
                    $message->from('noreply@logimonde.com', $campaign->sender_name);
                } else {
                    $message->from('noreply@logimonde.com', $campaign->company->Name);
                }

                $message->subject('Test Quick Presse: ' . $campaign->title);
            });

            return [
                'email' => $campaign->user->email,
            ];
        }
    }

    public function onSendAdminTestByEmail()
    {
        if ($id = post('sendId')) {
            $qp = $this->getQuickPresse($id);
            $data['qp'] = $qp;
            if (!$campaign = $this->getCampaign(post('campaign_id')))
                return false;

            $data['host'] = config('app.url');
            $data['campaign'] = $campaign;
            $data['fileHtml'] = $this->readHtmlFile($campaign->contents[0]->source_path);
            $data['footer'] = EblastFooter::create($campaign);
            $data['template'] = \View::make('logimonde.quickpresse::template.' . $campaign->template->email_file, $data)->render();

            $admin_emails = explode(';', Settings::get('email_test'));
            foreach ($admin_emails as $email) {
                if ($email !== '')
                    \Mail::send('logimonde.quickpresse::layout.' . $campaign->template->layout, $data, function ($message) use ($campaign, $email) {
                        $message->to($email);
                        if ($campaign->sender_name !== '') {
                            $message->from('noreply@logimonde.com', $campaign->sender_name);
                        } else {
                            $message->from('noreply@logimonde.com', $campaign->company->Name);
                        }
                        $message->subject('Test Quick Presse: ' . $campaign->title);
                    });
            }
        }
    }

    private function getQuickPresse($id)
    {
        return EblastSend::whereId($id)->first();
    }

    public function readHtmlFile($file)
    {
        return file_get_contents(base_path($file));
    }
}
