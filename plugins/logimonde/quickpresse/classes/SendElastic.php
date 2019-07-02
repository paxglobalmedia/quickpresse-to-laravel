<?php namespace Logimonde\QuickPresse\Classes;

use Logimonde\QuickPresse\Models\EblastSend;

class SendElastic extends SendEblasts
{
    public static function send()
    {
        $obj = new static;
        return $obj->processData();
    }

    public function processData()
    {
        $eblast = $this->getEblatsElasticToSend();
        if ($eblast) {
            $subscribers = $this->processListSubscribers($eblast);
            $totalSubscribers = count($subscribers);

            $this->updateSendElasticStatus($eblast);
            if ($totalSubscribers > 0) {
                $this->createMessageContent($eblast, $subscribers);
            }
            $this->updateSentElasticTotals($eblast, $totalSubscribers);
        }
    }

    private function createMessageContent($eblast, $subscribers)
    {

        $campaign = $eblast->eblast;
        $data = $this->prepareMessageVariables($eblast);
        $body = \View::make('logimonde.quickpresse::layout.' . $campaign->template->layout_elastic, $data)->render();
        foreach ($subscribers as $subscriber) {
            if ($eblast->eblast->type_qp == 'custom') {
                if ($campaign->template_id !== '3') {
                    $data['footer'] = EblastFooter::create($campaign, $subscriber->encode);
                }
                $body = \View::make('logimonde.quickpresse::layout.' . $campaign->template->layout_elastic, $data)->render();
            }

            $this->sendMessageToElastic($campaign, $body, $subscriber);
        }
    }

    private function sendMessageToElastic($campaign, $body, $subscriber)
    {
        $res = '';

        $params = 'username=' . urlencode('abuse@logimonde.net');
        $params .= '&api_key=' . urlencode('4ffd07e5-2ba5-44e6-8f2e-ee570091ce8c');
        $params .= '&from=' . urlencode('logimonde@logimonde.net');
        if ($campaign->sender_name !== '') {
            $params .= '&from_name=' . urlencode($campaign->sender_name);
        } else {
            if ($campaign->subaccount_id !== '0') {
                $params .= '&from_name=' . urlencode($campaign->subaccount->name);
            } else {
                $params .= '&from_name=' . urlencode($campaign->company->Name);
            }
        }

        $params .= '&to=' . urlencode($subscriber->email);
        $params .= '&subject=' . urlencode($campaign->title);
        $params .= '&body_html=' . urlencode($body);

        $header = "POST /mailer/send HTTP/1.0\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= 'Content-Length: ' . strlen($params) . "\r\n\r\n";
        $fp = fsockopen('ssl://api.elasticemail.com', 443, $errno, $errstr, 30);

        if (!$fp)
            return 'ERROR. Could not open connection';
        else {
            fputs($fp, $header . $params);
            while (!feof($fp)) {
                $res .= fread($fp, 1024);
            }
            fclose($fp);
        }
    }

    public function processListSubscribers($eblast)
    {
        $users = array();
        if ($eblast->eblast->type_qp == 'custom') {
            $subscribers = $this->getCustomSubcribers($eblast);
        } else {
            $subscribers = $this->getSubscribers($eblast->eblast->query_string);
        }

        foreach ($subscribers as $key => $subscriber) {
            $ua = [];
            if ($subscriber->elastic === '1') {
                if ($subscriber->courriel !== '') {
                    if (filter_var($subscriber->courriel, FILTER_VALIDATE_EMAIL)) {
                        $ua['email'] = trim($subscriber->courriel);
                        $ua['name'] = $subscriber->firstname . ' ' . $subscriber->lastname;
                        $ua['encode'] = $subscriber->encode;
                        $users[$key] = (object)$ua;
                    }
                }
            }
        }
        return $users;
    }

    protected function getEblatsElasticToSend()
    {
        return EblastSend::where('approved', '1')
            ->where('elastic', '0')
            ->where(\Db::raw("CONCAT(date_required, ' ', time_required)"), '<=', date('Y-m-d H:i:s'))
            ->orderBy('date_required', 'asc')
            ->first();
    }

    protected function updateSendElasticStatus($eblast)
    {
        EblastSend::whereId($eblast->id)
            ->update(['elastic' => '1', 'date_elastic_send' => date('Y-m-d H:i:s')]);
    }

    protected function updateSentElasticTotals($eblast, $total)
    {
        EblastSend::whereId($eblast->id)
            ->update(['total_subs_elastic' => $total, 'sent_final_elastic_at' => date('Y-m-d H:i:s')]);
    }

}
