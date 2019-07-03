<?php namespace Logimonde\QuickPresse\Classes;

use Logimonde\Quickpresse\Models\Settings;
use Illuminate\Mail\TransportManager;

class MailServer
{
    public $mailPort = '2525';
    public $mailEncryption = '';
    public $mailUsername = 'abuse@logimonde.net';
    public $mailPassword = '4ffd07e5-2ba5-44e6-8f2e-ee570091ce8c';
    private $mailServer = 'smtp.elasticemail.com';

    public function setServer()
    {
        Settings::set('last_email_server', 'smtp.elasticemail.com');
        $this->updateAppConfig();
        $this->updateMailSettings();
    }

    public function getLastServer()
    {
        return Settings::get('last_email_server');
    }

    protected function updateAppConfig()
    {
        $config = \App::make('config');
        $config->set('mail.host', $this->mailServer);
        $config->set('mail.encryption', $this->mailEncryption);
        $config->set('mail.username', $this->mailUsername);
        $config->set('mail.password', $this->mailPassword);
    }

    protected function overrideMailerConfig()
    {
        $config = \App::make('config');
        $config->set('mail.host', $this->mailServer);
        $config->set('mail.encryption', $this->mailEncryption);
        $config->set('mail.username', $this->mailUsername);
        $config->set('mail.password', $this->mailPassword);

        $app = \App::getInstance();

        $app->register('Illuminate\Mail\MailServiceProvider');
    }

    protected function updateMailSettings()
    {
        $settings = $this->getMailSettings();
        $mailSettings = (array)json_decode($settings->value);

        $mailSettings['smtp_address'] = $this->mailServer;
        $mailSettings['smtp_authorization'] = '1';
        $mailSettings['smtp_encryption'] = $this->mailEncryption;
        \Db::table('system_settings')
            ->where('item', 'system_mail_settings')
            ->update(['value' => json_encode($mailSettings)]);
    }

    public function rollbackServer()
    {
        $config = \App::make('config');
        $config->set('mail.host', 'smtp.elasticemail.com');
        $config->set('mail.encryption', $this->mailEncryption);
        $config->set('mail.username', 'abuse@logimonde.net');
        $config->set('mail.password', '4ffd07e5-2ba5-44e6-8f2e-ee570091ce8c');

        $settings = $this->getMailSettings();
        $mailSettings = (array)json_decode($settings->value);

        $mailSettings['smtp_address'] = 'smtp.elasticemail.com';
        $mailSettings['smtp_authorization'] = '1';
        $mailSettings['smtp_encryption'] = '';
        \Db::table('system_settings')
            ->where('item', 'system_mail_settings')
            ->update(['value' => json_encode($mailSettings)]);

    }

    private function getMailSettings()
    {
        return \Db::table('system_settings')->where('item', 'system_mail_settings')->first();
    }


}