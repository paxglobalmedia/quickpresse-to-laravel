<?php namespace Logimonde\QuickPresse\Classes;

use Logimonde\QuickPresse\Models\EblastSend;
use Logimonde\Quickpresse\Models\CustomContacts;
use Illuminate\Support\Facades\Artisan;

class SendEblasts
{
    protected function prepareMessageVariables($eblast)
    {
        $campaign = $eblast->eblast;
        $data['qp'] = $eblast;
        $data['host'] = config('app.url');
        $data['campaign'] = $campaign;
        $data['fileHtml'] = $this->readHtmlFile($campaign->contents[0]->source_path);
        $data['footer'] = EblastFooter::create($campaign);
        $data['template'] = \View::make('logimonde.quickpresse::template.' . $campaign->template->email_file, $data)->render();
        return $data;
    }

    protected function mailServer()
    {
        $mailServer = new MailServer;
        $mailServer->setServer();
    }

    protected function getSubscribers($query)
    {
        return \Db::connection('circulation')->select($query);
    }

    /**
     * @param $eblast
     * @return mixed
     * The table crm_en is used from database circulation
     */
    protected function getCustomSubcribers($eblast)
    {
        return CustomContacts::select('email as courriel', 'first_name as firstname', 'last_name as lastname', 'public_key as encode', 'elastic_mail as elastic')
            ->where('custom_list_id', $eblast->eblast->custom_list_id)
            ->where('active', '1')->get();
    }

    protected function getEblatsToSend()
    {
        return EblastSend::where('approved', '1')
            ->where('status', '0')
            ->where(\Db::raw("CONCAT(date_required, ' ', time_required)"), '<=', date('Y-m-d H:i:s'))
            ->orderBy('date_required', 'asc')
            ->first();
    }

    protected function updateSendStatus($eblast)
    {
        $server = $this->getServer();
        EblastSend::whereId($eblast->id)
            ->update([
                'status' => '1',
                'date_send' => date('Y-m-d H:i:s'),
                'server' => $server,
                ]);
    }

    private function getServer()
    {
        $mailServer = new MailServer;
        $server = $mailServer->getLastServer();

        if ($server == 'smtp.elasticemail.com') {
            return 5;
        } else if ($server == 'smtp.elasticemail.com') {
            return 6;
        } else if ($server == 'smtp.elasticemail.com') {
            return 4;
        } else {
            return 0;
        }
    }

    protected function updateSentTotals($eblast, $total)
    {
        EblastSend::whereId($eblast->id)
            ->update(['total_subs_normal' => $total, 'sent_final_at' => date('Y-m-d H:i:s')]);
    }


    protected function testVarDumpData($data)
    {
        file_put_contents(storage_path('/temp/' . str_random(12) . '.txt'), print_r($data, true));
    }

    protected function testSubcribers()
    {
        return [
            [
                'courriel' => 'francener@paxglobalmedia.com',
                'firstname' => 'Juan Carlos',
                'lastname' => 'Lora',
                'elastic' => '1',
                'encode' => '0',
            ],
            [
                'courriel' => 'web@logimonde.com',
                'firstname' => 'Web',
                'lastname' => 'Programmer',
                'elastic' => '0',
                'encode' => '0',
            ],
            [
                'courriel' => 'tizpeople@gmail.com',
                'firstname' => 'FA Gmail',
                'lastname' => 'Alezy',
                'elastic' => '1',
                'encode' => '0',
            ],
            [
                'courriel' => 'it@paxglobalmedia.com',
                'firstname' => 'Karine',
                'lastname' => 'Lefebvre',
                'elastic' => '0',
                'encode' => '0',
            ],

        ];
    }

    protected function testElasticSubcribers()
    {
        return [
            [
                'courriel' => 'alezyy@hotmail.com',
                'firstname' => 'Juan Carlos',
                'lastname' => 'Lora',
                'elastic' => '1',
                'encode' => '0',
            ],

        ];
    }

    private function readHtmlFile($file)
    {
        return file_get_contents(base_path($file));
    }
}
