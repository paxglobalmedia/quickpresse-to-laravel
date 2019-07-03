<?php namespace Logimonde\Quickpresse\Components;

use Input;
use Request;
use Validator;
use ValidationException;
use ApplicationException;
use Cms\Classes\ComponentBase;
use Logimonde\Quickpresse\Models\Template as BaseTemplate;
use Logimonde\Quickpresse\Models\Library as BaseLibrary;
use Logimonde\Quickpresse\Models\Click;
use Logimonde\Quickpresse\Models\Eblast;
use Logimonde\Quickpresse\Models\EblastContent;
use RainLab\Translate\Classes\Translator as languageTranslator;

class CampaignContent extends ComponentBase
{
    use \Logimonde\Account\Traits\AccountUtils;
    use \Logimonde\Quickpresse\Traits\CommonTraits;
    use \Logimonde\Quickpresse\Traits\CampaignContentTrait;

    public $user;
    public $library;
    public $lang;
    public $server;


    public function componentDetails()
    {
        return [
            'name' => 'Campaign Content',
            'description' => 'Campaign Content Component'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function init()
    {
        $this->user = $this->user();
        $this->library = $this->addComponent('Logimonde\QuickPresse\Components\Library', 'library', []);
        $translator = languageTranslator::instance();
        $this->lang = $translator->getLocale();
        $this->server = config('app.url');
    }

    public function onRun()
    {
        $this->loadAssets();
        if ($result = $this->checkUploadFileAction()) {
            return $result;
        }
        $this->page['templates'] = $this->getTemplates();
        if ($id = $this->param('campaign')) {
            $campaign = $this->getCampaign($id);
            $this->page['campaign'] = $campaign;
            $this->getTemplatePreview($id);
        }

    }

    protected function loadAssets()
    {
        $this->addCss('assets/vendor/flex-images/jquery.flex-images.css');
        $this->addCss('assets/vendor/redactor/redactor.css');
        $this->addCss('assets/vendor/spectrum/spectrum.css');
        $this->addJs('assets/vendor/redactor/redactor.js');
        $this->addJs('assets/vendor/jquery/jquery-migrate-1.4.1.min.js');
        $this->addJs('assets/vendor/spectrum/spectrum.js');
        if ($this->lang == 'fr') {
            $this->addJs('assets/vendor/spectrum/jquery.spectrum-fr.js');
            $this->addJs('assets/vendor/redactor/fr.js');
        }
        $this->addJs('/plugins/logimonde/quickpresse/assets/vendor/file-upload/jquery.ui.widget.js');
        $this->addJs('/plugins/logimonde/quickpresse/assets/vendor/file-upload/jquery.iframe-transport.js');
        $this->addJs('/plugins/logimonde/quickpresse/assets/vendor/file-upload/jquery.fileupload.js');
        $this->addJs('assets/vendor/flex-images/jquery.flex-images.min.js');
        $this->addJs('assets/vendor/tabSlideOut/js/jquery.tabSlideOut.v1.3.js');
        $this->addJs('/plugins/logimonde/quickpresse/assets/js/campaign_content.js', '1.045');
        $this->addJs('assets/js/editor.js');
    }

    public function onLoadTemplatePreview()
    {
        if ($id = post('template_id')) {
            $campaign = $this->getCampaign(post('campaign_id'));
            $template = $this->getTemplate($id);
            if (!$template)
                throw new \AjaxException(['error' => 'Template not found']);
            $this->page['template'] = $template;
            if ($campaign && $campaign->template_id === $id) {
                $this->getTemplatePreview(post('campaign_id'));
                return [
                    "#content-preview" => $this->renderPartial("@$template->partial_file")
                ];
            } else {
                if ($campaign->contents_count === 0) {
                    return [
                        "#content-preview" => $this->renderPartial("@$template->partial_file")
                    ];
                } else {
                    throw new \AjaxException([
                        'error' => 'Different Template, lost data. You must remove all content objects before',
                    ]);
                }
            }
        } else {
            throw new \AjaxException([
                'error' => 'Template not found',
            ]);
        }
    }

    public function onAddObject()
    {
        $images = $this->getImagesFromLibrary();
        $this->page['pictures'] = $images;
        $this->page['campaign'] = $this->getCampaign(post('campaign_id'));
        $this->page['block'] = post('block');
        $this->page['blockType'] = post('block_type');
        $this->page['imageWidth'] = post('image_width');
    }

    public function onLoadContentSettings()
    {
        if ($id = post('id')) {
            $content = $this->getEblastContent($id);
            $this->page['block'] = post('block');
            $this->page['blockType'] = post('block');
            $this->page['imageWidth'] = post('image_width');
            $this->page['contentData'] = $content;
            $this->fileDataSettings($content);
        }
    }

    public function onUpdateProperties()
    {
        $data = post();
        if (isset($data['content_id']) && $data['content_id'] !== '') {
            $content = $this->getEblastContent($data['content_id']);
            if (isset($data['link'])) {
                $content->link_content = $this->parseUrl($data['link']);
            }

        } else {
            $content = new EblastContent;
        }
        if (isset($data['text_content'])) {
            $content->text_content = $data['text_content'];
            $content->style_block = [
                'background' => $data['background_color'],
                'text' => $data['text_color']
            ];
            $content->type_content = 'text';
            $content->block = $data['called_block'];
            $content->mime = 'text/plain';
            $content->eblast_id = $data['campaign_id'];
        }
        $content->save();
        $this->fileDataSettings($content);

        $this->page['template'] = $template = $this->getTemplate($data['template_id']);
        return [
            'template' => $template
        ];

    }

    private function parseUrl($url)
    {
        if ($url !== '') {
            $parsed = parse_url($url);
            if (empty($parsed['scheme'])) {
                return 'http://' . ltrim($url, '/');
            } else {
                return $url;
            }
        } else {
            return '';
        }
    }

    public function onRemoveFileContent()
    {
        if ($id = post('template_id')) {
            EblastContent::whereId(post('content_id'))->delete();
            $template = BaseTemplate::whereId($id)->first();
            Click::where('eblast_content_id', post('content_id'))->delete();
            $campaign = $this->getCampaign(post('campaign_id'));
            $this->page['template'] = $template;
            $this->page['campaign'] = $campaign;
            return [
                'template' => $template,
                'contents' => count($campaign->contents()->get()),
                'message' => \Lang::get('logimonde.quickpresse::lang.content.deleted')
            ];
        }
    }

    public function onUpdateCampaignContent()
    {
        $campaign = $this->getCampaign(post('campaign_id'));
        $campaign->template_id = post('template_id');
        $campaign->save();
        $this->parseClickTracking($campaign);
        if (post('redirect')) {
            \Flash::info(trans('logimonde.quickpresse::lang.content.qp_upd'));
        } else {
            return [
                'message' => trans('logimonde.quickpresse::lang.content.qp_upd')
            ];
        }
    }

    public function onSelectImageFromLibrary()
    {
        $copiedFile = $this->copyImageFromLibrary(post('image_id'));
        if (!is_array($copiedFile)) {
            throw new \AjaxException([
                'error' => 'Error image',
            ]);
        }
        $content = $this->saveFileContent($copiedFile, '');
        $this->page['campaign'] = $this->getCampaign(post('campaign_id'));
        $this->page['template'] = $template = BaseTemplate::whereId(post('template_id'))->first();
        $partial = $template ? $template->partial_file : '_preview';
        return [
            'template' => $template,
            'html' => $this->renderPartial("@" . $partial),
            'content' => $content,
            'imageWidth' => post('set_img_width'),
        ];
    }

    public function onAddNewPageImage()
    {
        if ($id = post('template_id')) {
            $campaign = $this->getCampaign(post('campaign_id'));
            $template = BaseTemplate::whereId($id)->first();
            $this->page['campaign'] = $campaign;
            $this->page['template'] = $template;
            $this->page['counter'] = post('counter');

            return [
                'template' => $template,
                'html' => $this->renderPartial("@templates/__new_page_image")
            ];
        }
    }

    private function getCampaign($campaign_id)
    {
        return Eblast::whereId($campaign_id)->first();
    }

    private function getEblastContent($content_id)
    {
        return EblastContent::whereId($content_id)->first();
    }

    private function getTemplate($template)
    {
        return BaseTemplate::whereId($template)->first();
    }

    private function fileDataSettings($content)
    {
        if ($content) {
            $this->page['campaign'] = $this->getCampaign($content->eblast_id);
        }
    }
}
