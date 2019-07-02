<?php namespace Logimonde\Quickpresse\Components;

use Cms\Classes\ComponentBase;
use Logimonde\Quickpresse\Models\CustomContacts as BaseContact;
use Logimonde\Quickpresse\Models\CustomList as BaseList;

class CustomContactsForm extends ComponentBase
{
    use \Logimonde\Account\Traits\AccountUtils;
    use \Logimonde\Quickpresse\Traits\CommonTraits;


    public $user;

    public function componentDetails()
    {
        return [
            'name' => 'Custom Contacts Form ',
            'description' => 'Custom Contacts Form Component'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function init()
    {
        $this->user = $this->user();
    }

    public function onRun()
    {
        $this->addJs('/plugins/logimonde/quickpresse/assets/js/lists.js');
        if ($id = $this->param('member')) {
            $this->page['customContact'] = BaseContact::whereId($id)->first();
        }
        if ($list = $this->param('list')) {
            $this->page['customList'] = $customList = BaseList::whereId($list)->first();
            $this->page['redirect'] = $this->pageUrl('app/custom-contacts', ['list' => $customList->id]);
        }
        $this->page['company'] = $this->user->company;
    }

    public function onSaveContactSettings()
    {
        $data = post();
        if (isset($data['contact_id']) && $data['contact_id'] !== '') {
            $contact = BaseContact::whereId($data['contact_id'])->first();
        } else {
            $contact = new BaseContact();
        }

        $contact->custom_list_id = $data['custom_list_id'];
        $contact->last_name = $data['last_name'];
        $contact->first_name = $data['first_name'];
        $contact->email = $data['email'];
        $contact->address = $data['address'];
        $contact->phone = $data['phone'];
        $contact->title = $data['title'];
        $contact->company = $data['company'];
        $contact->public_key = str_random(24);
        $contact->elastic_mail = $this->isHotmail($data['email']);
        $contact->active = '1';
        $contact->Save();

        \Flash::info(\Lang::get('logimonde.quickpresse::lang.app.contact_save'));
    }

    private function isHotmail($email)
    {
        $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@([lL][iI][vV][eE]|[mM][sS][nN]|[oO][uU][tT][lL][oO][oO][kK]|[hH][oO][tT][mM][aA][iI][lL])(\.[a-z]{2,3})$/';
        if (preg_match($regex, $email)) {
            return true;
        } else {
            return false;
        }
    }
}
