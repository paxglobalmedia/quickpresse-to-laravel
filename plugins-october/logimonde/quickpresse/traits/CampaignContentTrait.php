<?php namespace Logimonde\Quickpresse\Traits;

use Input;
use Request;
use Validator;
use ValidationException;
use ApplicationException;
use AjaxException;
use Cms\Classes\ComponentBase;
use Logimonde\Quickpresse\Models\Template as BaseTemplate;
use Logimonde\Quickpresse\Models\Library as BaseLibrary;
use Logimonde\Quickpresse\Models\EblastContent;
use Logimonde\Quickpresse\Models\Click as BaseClick;

trait CampaignContentTrait
{
    private function getTemplatePreview($campaignId)
    {
        $content = EblastContent::where('eblast_id', $campaignId)->get();
        if ($content->count() === 1) {
            $this->page['contentData'] = $content[0];
            $this->fileDataSettings($content[0]);
        } else {
            $this->page['campaign'] = $this->getCampaign($campaignId);
        }
    }

    private function getTemplates()
    {
        if ($this->user->role_id === '3') {
            $template = BaseTemplate::isPublished()
                ->where('admin', '0')
                ->orderBy('order', 'asc')->get();
        } else {
            $template = BaseTemplate::isPublished()
                ->orderBy('order', 'asc')->get();
        }
        return $template;
    }

    private function getImagesFromLibrary()
    {
        if ($this->user->role_id === '3') {
            return BaseLibrary::isPublished()
                ->where('company_id', $this->user->company_id)
                ->orderBy('created_at', 'desc')->get();
        } else {
            return BaseLibrary::isPublished()
                ->orderBy('created_at', 'desc')->get();
        }
    }

    protected function checkUploadFileAction()
    {
        $uploadedFile = Input::file('file_upload');
        if (!Request::isMethod('POST') || !is_object($uploadedFile)) {
            return;
        }

        $allowed = array('gif', 'png', 'jpg', 'jpeg');
        $compressed = array('html', 'rar', 'zip');
        $ext = $uploadedFile->getClientOriginalExtension();
        if (in_array($ext, $allowed)) {
            $validationRules[] = 'mimes:png,jpg,jpeg,gif';
            $validationRules[] = 'max:4000';
            $validationRules[] = 'dimensions:max_width=2430,max_height=4320';
        } else if (in_array($ext, $compressed)) {
            $validationRules[] = 'mimes:html,rar,zip';
            $validationRules[] = 'max:4000';
        } else {
            throw new AjaxException(trans('logimonde.quickpresse::lang.content.format'));
        }

        $validation = Validator::make(
            ['file_upload' => $uploadedFile],
            ['file_upload' => $validationRules],
            trans('logimonde.quickpresse::validation')
        );

        if ($validation->fails())
            throw new ValidationException($validation);

        $uploadedData = $this->addUploadContent($uploadedFile, $ext);
        $this->page['fileData'] = $uploadedData;

        return $this->selectPartialToReturn($uploadedData);

    }

    public function addUploadContent($uploadedFile, $ext)
    {

        $basename = basename($uploadedFile->getClientOriginalName(), '.' . $ext);
        $filename = str_random(8) . '_' . str_slug($basename) . '.' . $ext;
        $folder = '/eblast/' . $this->user->company->media_folder;
        $destinationPath = $this->checkMediaFolderUser($this->user->company->media_folder, 'eblast');

        $uploadedFile->move($destinationPath, $filename);
        $localFile = storage_path("/app/media$folder/$filename");
        $mime = mime_content_type($localFile);
        if ($ext === 'zip' || $ext === 'rar') {
            $contentHtml = $this->processCompressedFile($localFile, $ext, $folder);
            $filename = $contentHtml->filename_content;
            $mime = $contentHtml->mime;
        } else {
            $this->validateUploadContent($localFile, $ext, $uploadedFile, $destinationPath, $filename);
        }

        $uploadedData = [
            'source' => "/storage/app/media$folder/$filename",
            'mime' => $mime,
            'original' => $uploadedFile->getClientOriginalName(),
            'filename' => $filename,
            'imageWidth' => post('set_img_width'),
            'block' => post('called_block'),
        ];
        $content = $this->saveFileContent($uploadedData, $ext);
        $uploadedData['content_id'] = $content->id;
        $uploadedData['link'] = $content->link_content;

        return $uploadedData;
    }

    private function validateUploadContent($localFile, $ext, $uploadedFile, $destinationPath, $filename)
    {
        $blockType = post('block_type');
        if ($ext == 'html') {
            if ($blockType != 'html')
                throw new AjaxException(trans('logimonde.quickpresse::lang.submit.file_image'));

            $this->parseHtmlFile($localFile);
        } else {
            if ($blockType != 'image')
                throw new AjaxException(trans('logimonde.quickpresse::lang.submit.file_html'));

            $library = post('add_library');
            if (isset($library) && $library === '1') {
                $uploadedData = $this->addImageToLibrary($localFile, $uploadedFile);
                $this->insertLibraryImage($uploadedData);
            }
            $imageWidth = post('set_img_width');
            $this->resizeImage(
                [
                    'extension' => $ext,
                    'path' => $destinationPath,
                    'filename' => $filename
                ], 0, $imageWidth !== '' ? $imageWidth : 750
            );
        }
    }

    private function addImageToLibrary($localFile, $uploadedFile)
    {
        $ext = $uploadedFile->getClientOriginalExtension();
        $basename = basename($uploadedFile->getClientOriginalName(), "." . $ext);
        $filename = str_random(8) . '_' . str_slug($basename) . '.' . $ext;
        $folder = '/library/' . $this->user->company->media_folder;
        $destinationPath = $this->checkMediaFolderUser($this->user->company->media_folder, 'library');

        $fileSize = filesize($localFile);
        $mime = mime_content_type($localFile);
        $dimensions = getimagesize($localFile);

        if (!copy($localFile, $destinationPath . '/' . $filename)) {
            return false;
        }

        return [
            'source' => $folder . '/' . $filename,
            'size' => $fileSize,
            'mime' => $mime,
            'original' => $uploadedFile->getClientOriginalName(),
            'filename' => $filename,
            'height' => $dimensions[1],
            'width' => $dimensions[0],
        ];
    }

    private function insertLibraryImage($uploadedFile)
    {
        $company = post('company_id');

        $image = new BaseLibrary;
        $image->company_id = $company;
        $image->user_id = $this->user->id;
        $image->source = $uploadedFile['source'];
        $image->original_name = $uploadedFile['original'];
        $image->filename = $uploadedFile['filename'];
        $image->size = $uploadedFile['size'];
        $image->mime = $uploadedFile['mime'];
        $image->height = $uploadedFile['height'];
        $image->width = $uploadedFile['width'];
        $image->public_key = str_random(32);
        $image->published = '1';
        $image->save();
    }

    public function saveFileContent($uploadedFile, $ext)
    {
        $data = post();
        if (isset($data['content_id']) && $data['content_id'] !== '') {
            $content = $this->getEblastContent($data['content_id']);
        } else {
            $content = new EblastContent();
            $content->code = str_random(48);
        }
        if ($ext === 'zip' || $ext === 'rar') {
            $content->host_images = '1';
        } else {
            $content->eblast_id = $data['campaign_id'];
            $content->original_name = $uploadedFile['original'];
            $content->filename_content = $uploadedFile['filename'];
            $content->mime = $uploadedFile['mime'];
            if ($uploadedFile['mime'] === 'text/html') {
                $content->type_content = 'html';
            } else {
                $content->type_content = 'image';
            }
            $content->source_path = $uploadedFile['source'];
            $content->block = post('called_block');
        }
        $content->save();
        return $content;
    }

    private function selectPartialToReturn($uploadedData)
    {
        if ($uploadedData['mime'] === 'text/html') {
            $settings = '@settings/_html_setup';
        } else if ($uploadedData['mime'] === 'image/jpeg' || $uploadedData['mime'] === 'image/png' || $uploadedData['mime'] === 'image/gif') {
            $settings = '@settings/_image_setup';
        } else {
            $settings = '@settings/_library';
        }
        $this->page['campaign'] = $this->getCampaign(post('campaign_id'));
        $this->page['template'] = $template = BaseTemplate::whereId(post('template_id'))->first();
        $partial = $template ? $template->partial_file : '_preview';
        return [
            'success' => 'The file has been upload',
            'preview' => [
                'element' => '#content-preview',
                'html' => $this->renderPartial('@' . $partial)
            ],
            'settings' => [
                'element' => '#content-settings',
                'html' => $this->renderPartial($settings)
            ],
            'template' => $template,
            'file' => $uploadedData,
        ];
    }

    public function readHtmlFile($file)
    {
        return base_path($file);
    }

    private function parseHtmlFile($fileHtml)
    {
        $html = new \Htmldom($fileHtml);
        if (!$html)
            throw new AjaxException('Error file HTML -> ' . $fileHtml);
        $meta = $html->find('meta');
        if ($meta) {
            foreach ($meta as $element) {
                $element->outertext = '';
            }
        }
        $title = $html->find('title');
        if ($title) {
            foreach ($title as $element) {
                $element->outertext = '';
            }
        }
        $script = $html->find('script');
        if ($script) {
            foreach ($script as $element) {
                $element->outertext = '';
            }
        }
        $link = $html->find('link');
        if ($link) {
            foreach ($link as $element) {
                $element->outertext = '';
            }
        }
        $html->save($fileHtml);
        $this->insertEndOfLine($fileHtml);

        return $fileHtml;
    }

    private function processCompressedFile($folder)
    {
        $content = $this->getEblastContent(post('content_id'));
        if (!$content)
            throw new AjaxException(trans('logimonde.quickpresse::lang.content.error_zip'));

        $localPath = storage_path("/app/media$folder/html/$content->id/");
        if (!file_exists($localPath)) {
            mkdir($localPath, 0777, true);
            chmod($localPath, 0777);
        }
        $this->parseImagesHtmlFile($content, $folder);
        return $content;
    }

    private function extractZipFile($filename, $localPath)
    {

        $zip = new \ZipArchive;
        if ($zip->open($filename) === TRUE) {
            $zip->extractTo($localPath);
            $zip->close();
            return array_diff(scandir($localPath), array('..', '.'));
        } else {
            return false;
        }
    }

    private function extractRarFile($filename, $localPath)
    {

        $rar_file = rar_open($filename);
        $list = rar_list($rar_file);
        foreach ($list as $file) {
            $entry = rar_entry_get($rar_file, $file->getName());
            $entry->extract($localPath);
        }
        rar_close($rar_file);
        return array_diff(scandir($localPath), array('..', '.'));
    }


    private function parseImagesHtmlFile($content, $folder)
    {
        $hostServer = config('app.url');
        $url = $hostServer . "/storage/app/media$folder/html/$content->id/";
        $file = storage_path("/app/media$folder/") . $content->filename_content;

        $html = new \Htmldom($file);
        $img = $html->find('img');
        if ($img) {
            foreach ($img as $element) {

                $element->src = $url . $element->src;
                $style = $element->style;
                $width = $element->width;
                $element->outertext = '<img src="' . $element->src . '" width="' . $width . '" style="' . $style . '">';
            }
        }
        $html->save($file);
        $this->insertEndOfLine($file);
    }

    private function copyImageFromLibrary($id)
    {
        $image = BaseLibrary::where('id', $id)->first();
        $libraryFile = storage_path("/app/media$image->source");

        $folder = '/eblast/' . $this->user->company->media_folder;
        $destinationPath = $this->checkMediaFolderUser($this->user->company->media_folder, 'eblast');

        if (!copy($libraryFile, $destinationPath . '/' . $image->filename)) {
            return false;
        }
        $ext = pathinfo($libraryFile, PATHINFO_EXTENSION);
        $imageWidth = post('set_img_width');
        $this->resizeImage(
            [
                'extension' => $ext,
                'path' => $destinationPath,
                'filename' => $image->filename
            ], 0, $imageWidth !== '' ? $imageWidth : 750
        );

        return [
            'source' => "/storage/app/media$folder/$image->filename",
            'mime' => $image->mime,
            'original' => $image->original_name,
            'filename' => $image->filename,
        ];
    }

    public function parseClickTracking($campaign)
    {
        $content = $campaign->contents()->where('mime', 'text/html')->first();
        if ($content) {
            if ($content->parsed_href === '0') {
                $file = base_path($content->source_path);
                $html = new \Htmldom($file);
                $aHref = $html->find('a');
                if ($aHref) {
                    foreach ($aHref as $element) {
                        $innerText = $element->innertext;
                        $style = $element->style;
                        $class = $element->class;
                        $title = $element->title;
                        $alt = $element->alt;
                        $src = $element->src;
                        $href = $element->href;
                        $href = $this->addClickThroughUrl($content, $href, $title, $alt, $innerText);
                        $element->outertext = '<a href="' . $href . '" class="' . $class . '" style="' . $style .
                            '" title="' . $title . '" alt="' . $alt . '" src="' . $src . '" target="_blank">' .
                            $innerText . '</a>';

                    }
                }
                $html->save($file);
                $this->insertEndOfLine($file);
            }
        }
    }

    private function addClickThroughUrl($content, $href, $title, $alt, $innerText)
    {
        $hostServer = config('app.url');
        $attrTitle = ($alt !== '' ? $alt : ($title !== '' ? $title : ''));

        $click = new BaseClick;
        $click->eblast_content_id = $content->id;
        $click->code = str_random(48);
        $click->url = $href;
        $click->title = (($attrTitle !== '') ? $attrTitle : strip_tags($innerText));
        $click->save();

        $this->updateContentParsed($content->id);

        $link = $hostServer . 'click-tracking/html/' . $click->code;
        return $link;
    }

    private function updateContentParsed($id)
    {
        $content = EblastContent::whereId($id)->first();
        $content->parsed_href = '1';
        $content->save();
    }
}
