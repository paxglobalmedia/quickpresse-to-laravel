<?php namespace Logimonde\Quickpresse\Components;

use Cms\Classes\ComponentBase;
use Logimonde\Quickpresse\Models\CustomList as BaseList;

class CustomListForm extends ComponentBase
{
    use \Logimonde\Account\Traits\AccountUtils;
    use \Logimonde\Quickpresse\Traits\CommonTraits;

    public $pageParam;
    public $perPage;
    public $user;

    public function componentDetails()
    {
        return [
            'name' => 'Custom List Form',
            'description' => 'Custom List Form Component'
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
        if ($id = $this->param('custom')) {
            $this->page['customList'] = BaseList::whereId($id)->first();
        }
        $this->page['redirect'] = $this->pageUrl('app/lists', []);
        $this->page['company'] = $this->user->company;
    }

    public function onSaveListSettings()
    {
        $data = post();
        if (isset($data['list_id']) && $data['list_id'] !== '') {
            $list = BaseList::whereId($data['list_id'])->first();
        } else {
            $list = new BaseList;
        }
        $list->company_id = $data['company_id'];
        $list->name = $data['name'];
        $list->email = $data['email'];
        $list->reminder = $data['reminder'];
        $list->public_key = str_random(24);
        $list->active = isset($data['active']) ? '1' : '0';
        $list->save();

        \Flash::info(\Lang::get('logimonde.quickpresse::lang.app.list_save'));
    }
}
