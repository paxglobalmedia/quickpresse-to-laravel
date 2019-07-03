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
use Logimonde\QuickPresse\Models\Company;
use Logimonde\QuickPresse\Models\Settings as QpSettings;
use RainLab\User\Models\User as BaseUser;
use RainLab\User\Models\Settings as UserSettings;
use Exception;
use October\Rain\Database\Attach\Resizer;

class UserRegister extends ComponentBase
{
    public $host;
    public function componentDetails()
    {
        return [
            'name' => 'User Register',
            'description' => 'User Register Component'
        ];
    }

    public function defineProperties()
    {
        return [
            'redirect' => [
                'title' => 'rainlab.user::lang.account.redirect_to',
                'description' => 'rainlab.user::lang.account.redirect_to_desc',
                'type' => 'dropdown',
                'default' => ''
            ],

        ];
    }

    public function getRedirectOptions()
    {
        return ['' => '- none -'] + Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function init()
    {
        $this->host = $this->page['host'] = config('app.url');
    }

    public function onRun()
    {
        if ($result = $this->checkUploadLogoAction()) {
            return $result;
        }
        $this->loadAssets();

    }

    protected function loadAssets()
    {
        $this->addJs('/plugins/logimonde/quickpresse/assets/vendor/file-upload/jquery.ui.widget.js');
        $this->addJs('/plugins/logimonde/quickpresse/assets/vendor/file-upload/jquery.iframe-transport.js');
        $this->addJs('/plugins/logimonde/quickpresse/assets/vendor/file-upload/jquery.fileupload.js');
        $this->addJs('/plugins/logimonde/account/assets/js/register.js', '1.012');
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

            if (!array_key_exists('password_confirmation', $data)) {
                $data['password_confirmation'] = post('password');
            }

            $rules = [
                'email' => 'required|email|between:6,255',
                'password' => 'required|between:4,255',
            ];

            if ($this->loginAttribute() == UserSettings::LOGIN_USERNAME) {
                $rules['username'] = 'required|between:2,255';
            }

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
            $this->page['userPwd'] = $data['password'];

        } catch (Exception $ex) {
            if (Request::ajax()) throw $ex;
            else Flash::error($ex->getMessage());
        }
    }

    public function onUpdateUserData()
    {
        if ($id = post('user_id')) {
            $data = post();
            $user = BaseUser::whereId($id)->first();
            $user->first_name = $data['first_name'];
            $user->last_name = $data['last_name'];
            $user->name = $data['first_name'] . ' ' . $data['last_name'];
            $user->title = $data['title'];
            $user->phone = $data['phone'];
            $user->extension = $data['extension'];
            $user->save();
            $this->page['user'] = $user;
            $this->page['userPwd'] = $data['user_pwd'];
        }
    }

    /**
     * Returns the login model attribute.
     */
    public function loginAttribute()
    {
        return UserSettings::get('login_attribute', UserSettings::LOGIN_EMAIL);
    }

    protected function checkUploadLogoAction()
    {
        $uploadedFile = Input::file('file_upload');
        if (!Request::isMethod('POST') || !is_object($uploadedFile)) {
            return;
        }

        $validationRules = [];

        /*
         * Validate file types
         */

        $validationRules[] = 'mimes:png,jpg,jpeg,gif';


        $validation = Validator::make(
            ['file_upload' => $uploadedFile],
            ['file_upload' => $validationRules]
        );

        if ($validation->fails())
            throw new ValidationException($validation);

        if (!$uploadedFile->isValid())
            throw new ApplicationException(sprintf('File %s is not valid.', $uploadedFile->getClientOriginalName()));

        $ext = $uploadedFile->getClientOriginalExtension();
        $basename = basename($uploadedFile->getClientOriginalName(), "." . $ext);
        $filename = str_random(8) . '_' . str_slug($basename) . '_'. post('logo_lang') . '.' . $ext;
        $folder = temp_path('company/');
        $uploadedFile->move($folder, $filename);


        $fileResize = $folder . $filename;
        $resizer = Resizer::open($fileResize);
        $resizer->resize(200, 200);
        $resizer->save($fileResize, 99);


        return [
            'path' => $folder,
            'lang' => post('logo_lang'),
            'filename' => $filename,
            'success' => trans('logimonde.account::lang.company.upload', ['file' => $basename . '.' . $ext]),
        ];
    }

    /**
     * The last step new user form registration to quickpresse
     */
    public function onCompanyData()
    {
        $data = post();
        $company = new Company;
        $company->name = $data['company'];
        $company->address = $data['address'];
        $company->zip = $data['zip'];
        $company->city_id = $data['city'];
        $company->state_id = $data['state'];
        $company->country_id = $data['country'];
        $company->save();
        if ($id = $data['user_id']) {
            $user = BaseUser::whereId($id)->first();
            $this->sendEmailConfirmation($data, $user);
            $this->sendAdminData($data, $user);
        }
    }

    private function sendEmailConfirmation($data, $user)
    {
        $data['logo'] = 'http://media.logimonde.net/image/quickpresse/quickpresse.png';
        $data['user'] = $user;
        $data['host'] = $this->host;
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
        $data['host'] = $this->host;
        $data['adminEmails'] = (env('APP_ENV') == 'dev') ? 'juancarlos1@logimonde.com' : explode(';', trim($adminEmail));

        \Mail::send('logimonde.account::mail.new_user', $data, function ($message) use ($data, $user) {
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
