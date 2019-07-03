<?php namespace Logimonde\QuickPresse\Components;

use Cms\Classes\ComponentBase;
use Logimonde\QuickPresse\Models\CustomList as BaseList;
use Logimonde\QuickPresse\Models\CustomContacts as BaseContacts;

class Unsubscribe extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'Unsubscribe',
            'description' => 'Unsubscribe Custom List'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $listCode = $this->param('list');
        $list = $this->getCustomListData($listCode);
        if ($list) {
            $this->page['list'] = $list;
            if ($code = $this->param('contact')) {
                $this->page['contact'] = BaseContacts::where('public_key', $code)->isActive()->first();
            }
        } else {
            return \Redirect::to('404');
        }
    }

    private function getCustomListData($list)
    {
        return BaseList::where('public_key', $list)->first();
    }

    public function onUnsubscribeContact()
    {
        if ($code = post('code')) {
            $contact = BaseContacts::where('public_key', $code)->first();
            $contact->active = '0';
            $contact->save();
        }
    }
}
