<?php namespace Logimonde\Account\Components;

use Cms\Classes\ComponentBase;
use Logimonde\Quickpresse\Models\Account;

class SubaccountForm extends ComponentBase
{
    use \Logimonde\Account\Traits\AccountUtils;
    use \Logimonde\Quickpresse\Traits\CommonTraits;

    public function componentDetails()
    {
        return [
            'name' => 'SubAccount Form ',
            'description' => 'SubAccount Form Component'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        if (post('submit_form') == 'form-sub-account-ok') {
            $this->saveSubAccount();
        }
        $this->addJs('/plugins/logimonde/account/assets/js/account.js');

        $this->page['user_id'] = input('user_id');


        if ($id = $this->param('account')) {
            $account = Account::whereId($id)->first();
            $this->page['account'] = $account;
            $this->page['redirect'] = $this->pageUrl('account/subaccount-form', ['account' => $account->id]);
        }

    }

    public function saveSubAccount()
    {
        $data = post();
        if (is_array($data) && $data['account_id'] != '') {
            $account = Account::whereId($data['account_id'])->first();
        } else {
            $account = new Account;
        }
        $account->name = $data['name'];
        $account->address = $data['address'];
        $account->zip = $data['zip'];
        $account->city_id = $data['city'];
        $account->state_id = $data['state'];
        $account->country_id = $data['country'];
        $account->phone = $data['phone'];
        $account->active = isset($data['active']) ? '1' : '0';
        $account->save();
        if (\Request::hasFile('logo_file_en')) {
            if (!$logo = $this->uploadLogo('logo_file_en', $account->id, 'subaccount')) {
                return false;
            }
        }
        if (\Request::hasFile('logo_file_fr')) {
            if ($this->uploadLogo('logo_file_fr', $account->id, 'subaccount')) {
                return false;
            }
        }

        \Flash::success(\Lang::get('logimonde.account::lang.company.update'));

    }

    public function onDeleteSubAccountLogo()
    {
        if ($id = post('account_id')) {
            $account = Account::whereId($id)->first();
            if (post('logo_lang') == 'en') {
                $account->logo_file_en = '';
            } else if (post('logo_lang') == 'fr') {
                $account->logo_file_fr = '';
            }
            $account->save();
        }
    }
}
