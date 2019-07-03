<?php namespace Logimonde\Account\Components;

use Auth;
use Mail;
use Validator;
use ValidationException;
use ApplicationException;
use Cms\Classes\ComponentBase;
use RainLab\User\Models\User as UserModel;
use RainLab\User\Components\ResetPassword;

class UserPasswordReset extends ResetPassword
{

    public function componentDetails()
    {
        return [
            'name' => 'User Password Reset ',
            'description' => 'User Password Reset Component'
        ];
    }

    /**
     * Trigger the password reset email
     */
    public function onRestorePassword()
    {
        $rules = [
            'email' => 'required|email|between:6,255'
        ];

        $validation = Validator::make(post(), $rules);
        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        if (!$user = UserModel::findByEmail(post('email'))) {
            throw new ApplicationException(trans('rainlab.user::lang.account.invalid_user'));
        }

        $code = implode('!', [$user->id, $user->getResetPasswordCode()]);
        $link = $this->controller->currentPageUrl([
            $this->property('paramCode') => $code
        ]);

        $data = [
            'name' => $user->name,
            'link' => $link,
            'code' => $code,
            'logo' => 'http://media.logimonde.net/image/quickpresse/quickpresse.png'
        ];

        Mail::send('logimonde.account::mail.restore_password_' . $user->language, $data, function ($message) use ($user) {
            $message->to($user->email, $user->full_name);
        });
    }

    /**
     * Perform the password reset
     */
    public function onResetPassword()
    {
        $rules = [
            'code' => 'required',
            'password' => 'required|between:4,255'
        ];

        $validation = Validator::make(post(), $rules);
        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        /*
         * Break up the code parts
         */
        $parts = explode('!', post('code'));
        if (count($parts) != 2) {
            throw new ValidationException(['code' => trans('rainlab.user::lang.account.invalid_activation_code')]);
        }

        list($userId, $code) = $parts;

        if (!strlen(trim($userId)) || !($user = Auth::findUserById($userId))) {
            throw new ApplicationException(trans('rainlab.user::lang.account.invalid_user'));
        }

        if (!$user->attemptResetPassword($code, post('password'))) {
            throw new ValidationException(['code' => trans('rainlab.user::lang.account.invalid_activation_code')]);
        }
    }

}
