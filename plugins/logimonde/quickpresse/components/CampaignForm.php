<?php namespace Logimonde\Quickpresse\Components;

use Cms\Classes\ComponentBase;
use Logimonde\Crm\Models\Company;
use Logimonde\Crm\Models\Contract;
use Logimonde\Crm\Models\Agreement;
use Logimonde\Crm\Models\ItemAgreement;
use Logimonde\Crm\Models\ItemContract;
use Logimonde\Crm\Models\Product;
use Logimonde\Quickpresse\Models\Eblast;
use Logimonde\Quickpresse\Models\CustomList;
use RainLab\Translate\Classes\Translator as languageTranslator;

class CampaignForm extends ComponentBase
{
    use \Logimonde\Account\Traits\AccountUtils;

    public $user;
    public $lang;

    public function componentDetails()
    {
        return [
            'name' => 'Campaign Form',
            'description' => 'Campaign Form Component'
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
        $this->addJs('/plugins/logimonde/quickpresse/assets/js/campaign.js', '1.021');
        if ($this->user->role_id == '3') {
            $this->getDataCompany($this->user->company_id);
        }
        if ($id = $this->param('campaign')) {
            $campaign = $this->getCampaign($id);
            $this->page['campaign'] = $campaign;
            $this->page['qpType'] = $campaign->type_qp;
        }
    }

    public function onCompanyIntro()
    {
        if ($id = post('company_id')) {
            $this->getDataCompany($id);
        } else {
            throw new \AjaxException(['error' => 'Company Error']);
        }
    }

    public function onCompanyOptions()
    {
        if ($id = post('company_id')) {
            $type = post('type');
            $this->page['qpType'] = $type;
            $this->getDataCompany($id, $type);
        } else {
            throw new \AjaxException(['error' => 'Company Error']);
        }
    }

    private function getDataCompany($id, $type = null)
    {
        $company = $this->getCompany($id);
        $this->page['company'] = $company;
        $this->page['customLists'] = $this->getCustomLists($id);
        $this->page['user'] = $this->user;
        $contracts = $this->getContracts($company->id);
        $this->page['contracts'] = $contracts;
        $this->page['subaccounts'] = $this->getCompanySubAccounts($company);

        if ($type == 'qp_list') {
            $this->page['agreements'] = $this->getAgreements($id);
            $this->page['products'] = $this->getProducts();
        }
    }

    public function onValidateContract()
    {
        $message = null;

        if (post('contract_id') !== '') {
            $contract = Contract::whereId(post('contract_id'))->isActive()->first();
            $this->page['contract'] = $contract;
            $status = $this->page['status'] = true;
            if ($contract->balance === '0') {
                $message = trans('logimonde.quickpresse::lang.submit.errCtrc3');
                $status = $this->page['status'] = false;
            } else if ($contract->balance <= '5') {
                $message = trans('logimonde.quickpresse::lang.submit.infoctrc1') . ' ' .
                    $contract->balance . ' ' .
                    trans('logimonde.quickpresse::lang.submit.infoctrc2');
                $status = $this->page['status'] = true;
            }
            $this->page['subaccounts'] = $contract->subaccounts()->where('active', '1')->get();
            $this->page['products'] = $contract->items()->get();
            return [
                'message' => $message,
                'status' => $status,
            ];
        } else {
            throw new \AjaxException([
                'error' => trans('logimonde.quickpresse::lang.submit.sel_cont'),
            ]);
        }
    }

    public function onGetProductBalance()
    {
        if (post('product_id') !== '') {
            $product = ItemContract::where('product_id', post('product_id'))
                ->where('contract_id', post('contract_id'))->first();
            if ($product->qp_balance > 0) {
                $this->page['product'] = $product;
                return ['product' => $product];
            } else {
                throw new \AjaxException([
                    'error' => trans('logimonde.quickpresse::lang.submit.errCtrc3'),
                ]);
            }
        }
    }

    public function onValidateCustomList()
    {

        if (!$list = $this->getOneList(post('list_id')))
            throw new \AjaxException([
                'error' => trans('logimonde.quickpresse::lang.submit.no_list'),
            ]);

        if ($list->list_count > 0) {
            return [
                'message' => trans('logimonde.quickpresse::lang.submit.list_ok',
                    [
                        'name' => $list->name,
                        'number' => $list->list_count
                    ]
                )];
        } else {
            throw new \AjaxException([
                'error' => trans('logimonde.quickpresse::lang.submit.empty_list'),
            ]);
        }

    }

    private function getCampaign($id)
    {
        return Eblast::whereId($id)->first();
    }

    private function getCompany($company_id)
    {
        return Company::whereId($company_id)->isActive()->first();
    }

    private function getCompanySubAccounts($company)
    {
        return $company->subaccounts()->where('active', '1')->get();
    }

    private function getContracts($company_id)
    {
        return Contract::where('company_id', $company_id)
            ->isActive()
            ->hasBalance()
            ->isQuickPresse()
            ->isApproved()
            ->isPublished()->get();
    }

    private function getCustomLists($company_id)
    {
        return CustomList::where('company_id', $company_id)
            ->isActive()
            ->get();
    }

    private function getOneList($id)
    {
        return CustomList::whereId($id)->first();
    }

    private function getProducts()
    {
        if ($this->user->role_id === '3') {
            $products = Product::isPublished()
                ->where('family_id', '6')
                ->where('section_id', '6')
                ->orderBy("name_$this->lang", 'asc')
                ->get();
        } else {
            $products = Product::isPublished()
                ->where('family_id', '6')
                ->where('location_id', '7')
                // ->orderBy("name_$this->lang", 'asc')
                ->orderBy("section_id", 'desc')
                ->orderBy("name_$this->lang", 'asc')
                ->get();
        }

        return $products;
    }

    private function getAgreements($company_id)
    {
        return ItemAgreement::whereHas('agreement', function ($query) use ($company_id) {
            $query->where('company_id', $company_id);
            $query->where('status', '1');
            $query->isPublished();
        })->get();

    }


    public function onSelectDistributionList()
    {
        $data = post();
        if ($data['switch_type'] == 'agreement') {
            $this->page['agreements'] = $this->getAgreements($data['company_id']);
        } else if ($data['switch_type'] == 'regular') {
            $this->page['products'] = $this->getProducts();
        }
    }

    public function onCreateCampaign()
    {
        $data = post();

        $campaign = new Eblast;
        $campaign->user_id = $this->user->id;
        $campaign->contract_id = isset($data['contract_id']) ? $data['contract_id'] : 0;
        $campaign->agreement_id = isset($data['agreement_id']) ? $data['agreement_id'] : 0;
        $campaign->custom_list_id = isset($data['list_id']) ? $data['list_id'] : 0;
        $campaign->product_id = isset($data['product_id']) ? $data['product_id'] : 0;
        $campaign->subaccount_id = isset($data['subaccount_id']) ? $data['subaccount_id'] : 0;
        $campaign->company_id = $data['company_id'];
        $campaign->title = $data['title'];
        $campaign->notes = $data['notes'];
        $campaign->admin_notes = isset($data['admin_notes']) ? $data['admin_notes'] : '';
        $campaign->sender_name = $data['sender_name'];
        $campaign->show_qp_section = '1';
        $campaign->period_show = '5';
        $campaign->type_qp = $data['type_qp'];

        if ($data['type_qp'] == 'contract') {
            $contractData = $this->getContractProductData($data['contract_id'], $data['product_id']);
            $campaign->language = $contractData['lang'];
            $campaign->query_string = $contractData['query'];
            $campaign->list_name = $contractData['name'];
        } else if ($data['type_qp'] == 'qp_list') {
            $agreementId = isset($data['agreement_id']) ? $data['agreement_id'] : null;
            $productData = $this->getAgreementProductData($agreementId, $data);
            $campaign->language = $productData['lang'];
            $campaign->query_string = $productData['query'];
            $campaign->list_name = $productData['name'];
            $campaign->agreement_id = isset($productData['agreement']) ? $productData['agreement'] : 0;
        } else if ($data['type_qp'] == 'custom') {
            $customId = isset($data['list_id']) ? $data['list_id'] : null;
            $productData = $this->getCustomListProductData($customId, $data);
            $campaign->product_id = $productData['product'] ? $productData['product'] : 0;
            $campaign->agreement_id = isset($productData['agreement']) ? $productData['agreement'] : 0;
            $campaign->language = isset($data['language']) ? $data['language'] : '';
        }
        $campaign->save();

        \Flash::info(trans('logimonde.quickpresse::lang.content.qp_add'));

        return \Redirect::to($this->pageUrl('app/manage', ['campaign' => $campaign->id]));
    }

    public function getContractProductData($contract, $product)
    {
        $item = ItemContract::where('contract_id', $contract)
            ->where('product_id', $product)->first();
        return [
            'lang' => $item->qp_language,
            'query' => $item->qp_query_string,
            'name' => $item->qp_list_name,
        ];
    }

    public function getAgreementProductData($agreement, $data)
    {
        if (!is_null($agreement)) {
            $item = ItemAgreement::where('agreement_id', $agreement)
                ->where('product_id', $data['product_id'])->first();
            $agreementId = $agreement;
        } else {
            $item = Product::where('id', $data['product_id'])->first();
            $agreementId = $this->createAutomaticAgreement($data, $item);
        }

        return [
            'lang' => $item->qp_language,
            'query' => $item->qp_query_string,
            'name' => isset($item->qp_list_name) ? $item->qp_list_name :
                ($this->lang == 'en' ? $item->name_en : $item->name_fr),
            'agreement' => isset($agreementId) ? $agreementId : '0',
        ];
    }

    public function getCustomListProductData($customId, $data)
    {
        if (is_null($customId))
            return false;

        $list = CustomList::whereId($customId)->first();
        if ($list->list_count === 0)
            return false;

        $product = Product::where('family_id', '6')
            ->where('qp_list_min', '<=', $list->list_count)
            ->where('qp_list_max', '>', $list->list_count)
            ->where('section_id', '7')
            ->first();

        $agreementId = $this->createAutomaticAgreement($data, $product);
        return [
            'product' => $product->id,
            'agreement' => $agreementId
        ];
    }

    public function onCheckProductContract()
    {
        if (post('product_id') !== '') {
            $company = post('company_id');
            $item = ItemContract::where('product_id', post('product_id'))
                ->whereHas('contract', function ($query) use ($company) {
                    $query->where('company_id', $company);
                    $query->where('balance', '>', '0');
                })->first();
            return ['item' => $item ];
        }
    }

    public function onConfirmProductContract()
    {
        return \Redirect::to($this->pageUrl('app/campaign-settings'));
    }

    private function createAutomaticAgreement($data, $product)
    {
        $agreement = new Agreement;
        $agreement->title = $data['title'];
        $agreement->user_id = $this->user->id;
        $agreement->company_id = $data['company_id'];
        $agreement->status = '2';
        $agreement->published = '1';
        $agreement->code = str_random(48);
        $agreement->save();

        $item = new ItemAgreement;
        $item->agreement_id = $agreement->id;
        $item->product_id = $product->id;
        $item->quantity = '1';
        $item->price_list = $product->price;
        $item->save();
        return $agreement->id;
    }


    public function onUpdateCampaign()
    {
        $data = post();

        $campaign = Eblast::whereId($data['campaign_id'])->first();
        $campaign->title = $data['title'];
        $campaign->notes = isset($data['notes']) ? $data['notes'] : '';
        $campaign->sender_name = isset($data['sender_name']) ? $data['sender_name'] : '';
        $campaign->admin_notes = isset($data['admin_notes']) ? $data['admin_notes'] : '';
        if ($campaign->type_qp == 'custom') {
            $campaign->language = isset($data['language']) ? $data['language'] : '';
        }
        $campaign->save();

        \Flash::info(trans('logimonde.quickpresse::lang.content.qp_upd'));

        return \Redirect::to($this->pageUrl('app/manage', ['campaign' => $campaign->id]));
    }
}
