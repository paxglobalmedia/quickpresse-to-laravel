<?php namespace Logimonde\Account\Components;

use Lang;
use Auth;
use Mail;
use Event;
use Flash;
use Input;
use Request;
use Redirect;
use Validator;
use ValidationException;
use ApplicationException;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Logimonde\QuickPresse\Models\Settings as QpSettings;
use RainLab\User\Models\Settings as UserSettings;
use Exception;


class BasicRegister extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'BasicRegister Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $this->addJs('/plugins/logimonde/account/assets/js/basic.js', '1.002');
    }

    /**
     * Register the user
     */
    public function onRegisterUser()
    {
        try {
            if (!UserSettings::get('allow_registration', true)) {
                throw new ApplicationException(Lang::get('rainlab.user::lang.account.registration_disabled'));
            }

            /*
             * Validate input
             */
            $data = post();

            $data['name'] = $data['first_name'] . ' ' . $data['last_name'];
            $data['user_pwd'] = $data['password'];

            if (!array_key_exists('password_confirmation', $data)) {
                $data['password_confirmation'] = post('password');
            }

            $rules = [
                'email' => 'required|email|between:6,255',
                'password' => 'required|between:4,255',
            ];

            $validation = Validator::make($data, $rules);
            if ($validation->fails()) {
                throw new ValidationException($validation);
            }

            /*
             * Register user
             */

            $automaticActivation = UserSettings::get('activate_mode') === UserSettings::ACTIVATE_AUTO;
            $user = Auth::register($data, $automaticActivation);

            $this->page['user'] = $user;

            $this->sendEmailConfirmation($data, $user);
            $this->sendAdminData($data, $user);

        } catch (Exception $ex) {
            if (Request::ajax()) throw $ex;
            else Flash::error($ex->getMessage());
        }
    }

    private function sendEmailConfirmation($data, $user)
    {
        $data['logo'] = 'http://media.logimonde.net/image/quickpresse/quickpresse.png';
        $data['user'] = $user;
        \Mail::send('logimonde.account::mail.welcome_user_' . $user->language, $data, function ($message) use ($data, $user) {
            $message->to($user->email, $user->name);

        });
    }

    /*
     * Send mail notify admin
     * New user signed up
     */
    private function sendAdminData($data, $user)
    {
        $adminEmail = QpSettings::get('email_contact');
        $data['logo'] = 'http://media.logimonde.net/image/quickpresse/quickpresse.png';
        $data['user'] = $user;
        $data['adminEmails'] = (env('APP_ENV') == 'dev') ? 'juancarlos1@logimonde.com' : explode(';', trim($adminEmail));

        \Mail::send('logimonde.account::mail.new_user_basic', $data, function ($message) use ($data, $user) {
            $message->to($data['adminEmails']);
            if (isset($data['logo_name_en']) && $data['logo_name_en'] != '') {
                $message->attach($data['logo_name_en']);
            }
            if (isset($data['logo_name_fr']) && $data['logo_name_fr'] != '') {
                $message->attach($data['logo_name_fr']);
            }
        });
    }
}
