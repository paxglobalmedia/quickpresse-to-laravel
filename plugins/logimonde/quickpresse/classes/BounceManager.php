<?php namespace Logimonde\QuickPresse\Classes;

use Logimonde\Quickpresse\Models\EblastSend;
use Webklex\IMAP\Facades\Client;
use Logimonde\QuickPresse\Models\Bounce;
use Logimonde\QuickPresse\Models\Code;

class BounceManager
{
    protected $mailClient;
    public $mailBoxes;

    public function check()
    {
        $this->connect();
        $this->getMailBoxes();
        $this->readMessages();
    }

    private function connect()
    {
        $this->mailClient = Client::account('default');

        $this->mailClient->connect();
    }

    protected function getMailBoxes()
    {
        $this->mailBoxes = $this->mailClient->getFolders();
    }

    private function readMessages()
    {
        $messages = $this->mailBoxes[0]->messages()->limit(200)->get();
        foreach ($messages as $key => $message) {
            $data['date'] = $message->getDate();
            $attachments = $message->getAttachments();
            if (isset($attachments[0])) {
                $content = $attachments[0]->getContent();
                if ($email = $this->parseBounceEmail($content)) {
                    $data['status'] = $this->parseStatusCode($content);
                    $data['code'] = $this->parseBounceCode($content);
                    $data['email'] = $email;
                    $data['attachment_content'] = $content;
                    $eblast = isset($attachments[1]) ? $attachments[1]->getContent() : null;

                    $this->insertBounce($data);

                    $this->parseBounceEblast($eblast, $data);
                }

            }

            $message->delete();
        }

    }

    private function parseBounceCode($string)
    {
        $pattern = '/Diagnostic-Code: smtp; (.*)/';
        $matches = Array();
        preg_match($pattern, $string, $matches);
        return (isset($matches[1]) ? $matches[1] : '');
    }

    private function parseStatusCode($string)
    {
        $pattern = '/Status: (.*)/';
        $matches = Array();
        preg_match($pattern, $string, $matches);
        return trim($matches[1]);
    }

    private function parseBounceEmail($string)
    {
        $pattern = '/Final-Recipient: rfc822; (.*)/';
        $matches = Array();
        preg_match($pattern, $string, $matches);
        return (isset($matches[1]) ? trim($matches[1]) : false);
    }

    private function parseBounceEblast($string, $data)
    {
        if (!is_null($string)) {
            $pattern = '/<html (.*)/';
            $matches = Array();
            preg_match($pattern, $string, $matches);
            $eblast = isset($matches[1]) ? trim(substr($matches[1], 6, 6)) : false;
            if ($eblast) {
                $this->updateEblastBounces($eblast, $data);
            }
        }
    }

    private function insertBounce($data)
    {
        $bounce = Bounce::where('email', $data['email'])->first();
        if ($bounce) {
            $bounce->increment('error_count');
        } else {
            $bounce = new Bounce;
            $bounce->email = $data['email'];
            $bounce->code_id = $data['status'];
            $bounce->code_error = $data['code'];
            $bounce->error_count = 1;
        }
        $bounce->bounce_date = $data['date'];
        $bounce->save();
    }

    private function updateEblastBounces($eblast, $data)
    {
        $send = EblastSend::whereId($eblast)->first();
        if ($send) {
            if ($bounceType = $this->getBounceStatusCode($data['status'])) {
                if ($bounceType == 'Hard') {
                    $send->increment('bounces_hard');
                } else {
                    $send->increment('bounces_soft');
                }
            }
        }
    }

    private function getBounceStatusCode($status)
    {
        $bounce = Code::where('id', $status)->first();
        if ($bounce) {
            return $bounce->type;
        } else {
            return false;
        }
    }

}

