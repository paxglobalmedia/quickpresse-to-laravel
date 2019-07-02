<?php namespace Logimonde\QuickPresse\Components;

use Input;
use Request;
use Validator;
use ValidationException;
use ApplicationException;
use Cms\Classes\ComponentBase;
use Logimonde\Quickpresse\Models\Library as BaseLibrary;

class Library extends ComponentBase
{

    use \Logimonde\Account\Traits\AccountUtils;
    use \Logimonde\Quickpresse\Traits\CommonTraits;

    public $pageParam;
    public $perPage;
    public $user;

    public function componentDetails()
    {
        return [
            'name' => 'Library',
            'description' => 'Library Component'
        ];
    }

    public function defineProperties()
    {
        return [
            'pageNumber' => [
                'title' => 'Page number',
                'description' => 'This value is used to determine what page the user is on.',
                'type' => 'string',
                'default' => '{{ :page }}',
            ],
            'itemsPerPage' => [
                'title' => 'Items per page',
                'type' => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'Invalid format of the posts per page value',
                'default' => '25',
            ],
            'sortOrder' => [
                'title' => 'Items order',
                'description' => 'Attribute on which the items should be ordered',
                'type' => 'dropdown',
                'default' => 'updated_at desc'
            ],
        ];
    }

    public function init()
    {
        $this->user = $this->user();
        $this->perPage = $this->property('itemsPerPage');

    }

    public function onRun()
    {
        $this->loadAssets();
        if ($result = $this->checkUploadImageAction()) {
            return $result;
        }

        $this->page['url'] = $this->pageUrl('app/library', []);
        $this->page['pictures'] = $this->getLibrary();
        $this->page['company'] = $this->user->company;
    }

    public function loadAssets()
    {
        $this->addCss('assets/vendor/flex-images/jquery.flex-images.css');
        $this->addJs('/plugins/logimonde/quickpresse/assets/vendor/file-upload/jquery.ui.widget.js');
        $this->addJs('/plugins/logimonde/quickpresse/assets/vendor/file-upload/jquery.iframe-transport.js');
        $this->addJs('/plugins/logimonde/quickpresse/assets/vendor/file-upload/jquery.fileupload.js');
        $this->addJs('assets/vendor/flex-images/jquery.flex-images.min.js');
        $this->addJs('/plugins/logimonde/quickpresse/assets/js/library.js', '1.016');
    }

    private function getLibrary()
    {
        $page = $this->pageValidation();

        $search = $this->page['search'] = input('search');
        $sort = $this->page['sort'] = input('sort') !== '' ? input('sort') : $this->property('sortOrder');
        $page = !is_null($page) ? $page : $this->property('pageNumber');
        $page = $search !== '' ? null : $page;

        $library = BaseLibrary::listImages([
            'page' => $page,
            'sort' => $sort,
            'perPage' => $this->property('itemsPerPage'),
            'search' => $search,
            'company' => $this->user->company->id
        ]);

        $this->page['typeView'] = input('view_type') !== '' ? input('view_type') : 'grid-view';
        $this->page['sortOptions'] = BaseLibrary::$allowedSortingOptions;
        $options = '?search=' . $search;
        $options .= '&sort=' . $sort;
        $options .= '&lastPage=' . (input('lastPage') !== '' ? input('lastPage') : $library->lastPage());
        $this->page['options'] = $options;

        return $library;
    }

    public function onLoadImageList()
    {
        $pictures = $this->getLibrary();
        $this->page['pictures'] = $pictures;
        $partial = post('view_type') == 'list-view' ? '_image_list' : '_image_grid';
        return [
            '#image-list' => $this->renderPartial("@$partial"),
            '#pag-library' => $this->renderPartial("@_pagination_library"),
        ];

    }


    public function onDeleteImage()
    {
        BaseLibrary::whereId(post('id'))->delete();

        $this->page['pictures'] = $this->getLibrary();
        $partial = post('view_type') == 'list-view' ? '_image_list' : '_image_grid';
        return [
            '#image-list' => $this->renderPartial("@$partial"),
            '#pag-library' => $this->renderPartial("@_pagination_library"),
        ];


    }

    public function checkUploadImageAction()
    {
        $uploadedFile = Input::file('item_image');
        if (!Request::isMethod('POST') || !is_object($uploadedFile)) {
            return;
        }
        $addLibrary = post('add_library');
        if (!isset($addLibrary) && $addLibrary !== '1') {
            return;
        }

        /*
         * Validate file types
         */

        $validationRules[] = 'mimes:png,jpg,jpeg';

        $validation = Validator::make(
            ['item_image' => $uploadedFile],
            ['item_image' => $validationRules]
        );

        if ($validation->fails())
            throw new ValidationException($validation);

        if (!$uploadedFile->isValid())
            throw new AjaxException(sprintf('File %s is not valid.', $uploadedFile->getClientOriginalName()));

        $uploadedData = $this->storeUploadFile($uploadedFile);
        $this->insertImage($uploadedData);

        return $uploadedData;
    }

    public function storeUploadFile($uploadedFile)
    {
        $ext = $uploadedFile->getClientOriginalExtension();
        $basename = basename($uploadedFile->getClientOriginalName(), "." . $ext);
        $filename = str_random(8) . '_' . str_slug($basename) . '.' . $ext;
        $folder = '/library/' . $this->user->company->media_folder;
        $destinationPath = $this->checkMediaFolderUser($this->user->company->media_folder, 'library');
        $fileSize = $uploadedFile->getSize();
        $mime = $uploadedFile->getMimeType();
        $uploadedFile->move($destinationPath, $filename);

        $dimensions = getimagesize($destinationPath . '/' . $filename);

        return [
            'source' => $folder . '/' . $filename,
            'size' => $fileSize,
            'mime' => $mime,
            'original' => $uploadedFile->getClientOriginalName(),
            'filename' => $filename,
            'height' => $dimensions[1],
            'width' => $dimensions[0],
            'view_type' => post('view_type'),
            'success' => trans('logimonde.quickpresse::lang.app.upload1'),
        ];
    }

    public function insertImage($uploadedFile)
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

}
