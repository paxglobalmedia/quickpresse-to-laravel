<?php namespace Logimonde\Quickpresse\Components;

use Cms\Classes\ComponentBase;
use Logimonde\Quickpresse\Models\Eblast;
use Logimonde\Quickpresse\Models\EblastSend;
use RainLab\Translate\Classes\Translator as languageTranslator;

class ShowQuickpresse extends ComponentBase
{

    use \Logimonde\Quickpresse\Traits\TemplateTrait;
    public $host;
    public $lang;

    public function componentDetails()
    {
        return [
            'name' => 'Show Quick Presse',
            'description' => 'Show Quick Presse Component'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function init()
    {
        $translator = languageTranslator::instance();
        $this->lang = $translator->getLocale();
        $this->host = $this->page['host'] = config('app.url');
    }

    public function onRun()
    {
        $this->addJs('/plugins/logimonde/quickpresse/assets/js/show.js', '1.002');
        if ($id = $this->param('qp')) {
            $qp = $this->getQuickPresse($id);
            if ($qp) {
                $campaign = $this->getCampaign($qp->eblast_id);
                if ($campaign->template) {
                    $this->{$campaign->template->email_file}($campaign, $qp);
                } else {
                    return \Redirect::to('404');
                }
                $this->page->title = $campaign->title;
            } else {
                return \Redirect::to('http://old.quickpresse.com/show/' . $id);
            }
        }
    }

    private function getQuickPresse($id)
    {
        return EblastSend::whereId($id)->orWhere('slug', $id)->first();
    }

    private function getCampaign($id)
    {
        return Eblast::whereId($id)->first();
    }

    public function readHtmlFile($file)
    {
        return file_get_contents(base_path($file));
    }

    public function onShareEmail()
    {
        $data = post();
        $send = EblastSend::whereId(post('eblast_id'))->first();
        if ($send) {
            $data['host'] = $this->host;
            $data['logo'] = $this->host . '/themes/logimonde/assets/images/logo.png';

            \Mail::send('logimonde.quickpresse::mail.share_' . $send->eblast->language, $data, function ($message) use ($data) {
                $message->to($data['email_to']);
                if (isset($data['copyEmail']) && $data['copyEmail'] === '1') {
                    $message->cc($data['email_from']);
                }
            });

            $send->shared();
            return ['message' => ($send->eblast->language == 'en' ? 'The message has been sent' : 'Le message a été envoyé'),];
        } else {
            throw new \AjaxException(['error' => 'Eblast not found']);
        }
    }

}
