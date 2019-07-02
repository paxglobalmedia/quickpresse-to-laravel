<?php namespace Logimonde\Account\Components;

use Logimonde\QuickPresse\Models\Account;
use Redirect;
use System\Models\File;
use ApplicationException;
use Cms\Classes\ComponentBase;
use Logimonde\Quickpresse\Models\Company;


class UserUpdate extends ComponentBase
{

    use \Logimonde\Account\Traits\AccountUtils;

    public $user;

    public function componentDetails()
    {
        return [
            'name' => 'User Update',
            'description' => 'User Update Component'
        ];
    }

    public function defineProperties()
    {
        return [

        ];
    }

    public function init()
    {
        $id = $this->param('user');
        $this->user = $this->user($id);
    }

    public function onRun()
    {
        $this->loadAssets();


        $this->page['currentUser'] = $this->user;
    }


    private function loadAssets()
    {
        $this->addCss('/plugins/logimonde/account/assets/css/profile.css');

        $this->addJs('/plugins/logimonde/account/assets/js/account.js', '1.04');
    }


    /**
     * Update the user
     */
    public function onUpdate()
    {
        $data = post();

        if ($this->user->role_id == '3') {
            $user = $this->user;
        } else {
            $user = $this->user($data['user']);
        }

        $data['name'] = post('first_name') . ' ' . post('last_name');
        $data['city_id'] = post('city');
        $data['state_id'] = post('state');
        $data['country_id'] = post('country');
        $user->fill($data);
        $user->save(null, post('_session_key'));
        if ($this->user->role_id == '3') {
            $redirect = $this->pageUrl('account/profile', ['user' => $this->user->id]);
        } else {
            $redirect = $this->pageUrl('account/users');
        }
        \Flash::success(\Lang::get('logimonde.account::lang.user.update'));
        return \Redirect::to($redirect);
    }

    public function onPasswordUpdate()
    {
        $data = post();

        if ($this->user->role_id == '3') {
            $user = $this->user;
        } else {
            $user = $this->user($data['user']);
        }

        $user->fill($data);
        $user->save(null, post('_session_key'));
        if ($this->user->role_id == '3') {
            $redirect = $this->pageUrl('account/profile', ['user' => $this->user->id]);
        } else {
            $redirect = $this->pageUrl('account/users');
        }
        \Flash::success(\Lang::get('logimonde.account::lang.user.update'));
        return \Redirect::to($redirect);
    }

}