<?php namespace Logimonde\QuickPresse\Classes;

use Logimonde\QuickPresse\Models\EblastSend;

class SendRegular extends SendEblasts
{

    public static function send()
    {
        $obj = new static;
        return $obj->createEmailContent();
    }

    public function createEmailContent()
    {
        $eblast = $this->getEblatsToSend();
        if ($eblast) {
            $subcribers = $this->processListSubscribers($eblast);
            $totalSubscribers = count($subcribers);
            $this->updateSendStatus($eblast);

            $this->sendMessage($eblast, $subcribers);
            $this->updateSentTotals($eblast, $totalSubscribers);
        }
    }

    protected function sendMessage($eblast, $subscribers)
    {
        $campaign = $eblast->eblast;
        $data = $this->prepareMessageVariables($eblast);
        foreach ($subscribers as $subscriber) {
            if ($eblast->eblast->type_qp == 'custom') {
                $data['footer'] = EblastFooter::create($campaign, $subscriber->encode);
            }
            \Mail::send('logimonde.quickpresse::layout.' . $campaign->template->layout, $data, function ($message) use ($campaign, $subscriber) {
                $message->to($subscriber->email, $subscriber->name);
                if ($campaign->sender_name !== '') {
                    $message->from('logimonde@logimonde.net', $campaign->sender_name);
                } else {
                    if ($campaign->subaccount_id !== '0') {
                        $message->from('logimonde@logimonde.net', $campaign->subaccount->name);
                    } else {
                        $message->from('logimonde@logimonde.net', $campaign->company->Name);
                    }
                }
                $message->subject($campaign->title);

            });
        }
    }

    public function processListSubscribers($eblast)
    {
        if ($eblast->eblast->type_qp == 'custom') {
            $subscribers = $this->getCustomSubcribers($eblast);
        } else {
            $subscribers = $this->getSubscribers($eblast->eblast->query_string);
        }
        foreach ($subscribers as $key => $subscriber) {
            $ua = [];
            if ($subscriber->elastic === '0') {
                if ($subscriber->courriel !== '') {
                    if (filter_var($subscriber->courriel, FILTER_VALIDATE_EMAIL)) {
                        $ua['email'] = trim($subscriber->courriel);
                        $ua['name'] = $subscriber->firstname . ' ' . $subscriber->lastname;
                        $ua['encode'] = $subscriber->encode;
                        $users[] = (object)$ua;
                    }
                }
            }
        }
        return $users;
    }
}


