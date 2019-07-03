<?php namespace Logimonde\Quickpresse\Components;

use Excel;
use Input;
use Request;
use Validator;
use ValidationException;
use ApplicationException;
use Cms\Classes\ComponentBase;
use Logimonde\Quickpresse\Models\CustomList as BaseList;
use Logimonde\Quickpresse\Models\CustomContacts;

class ImportContacts extends ComponentBase
{
    use \Logimonde\Account\Traits\AccountUtils;
    use \Logimonde\Quickpresse\Traits\CommonTraits;

    public $pageParam;
    public $perPage;
    public $user;

    public function componentDetails()
    {
        return [
            'name' => 'Import Contacts',
            'description' => 'Import Contacts Component'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function init()
    {
        $this->user = $this->user();
    }

    public function onRun()
    {
        $this->loadAssets();
        if ($id = $this->param('id')) {

            if ($result = $this->checkImportFileAction()) {
                return $result;
            }
            $customList = BaseList::whereId($id)->first();
            $this->page['customList'] = $customList;
            $this->page['redirect'] = $this->pageUrl('app/custom-contacts', ['list' => $customList->id]);
            $this->page['company'] = $this->user->company;
        } else {
            return \Redirect::to($this->pageUrl('app/lists', []));
        }

    }

    protected function loadAssets()
    {
        $this->addJs('/plugins/logimonde/quickpresse/assets/vendor/file-upload/jquery.ui.widget.js');
        $this->addJs('/plugins/logimonde/quickpresse/assets/vendor/file-upload/jquery.iframe-transport.js');
        $this->addJs('/plugins/logimonde/quickpresse/assets/vendor/file-upload/jquery.fileupload.js');
        $this->addJs('/plugins/logimonde/quickpresse/assets/js/lists.js', '1.019');
    }

    protected function checkImportFileAction()
    {
        $uploadedFile = Input::file('file_upload');
        if (!Request::isMethod('POST') || !is_object($uploadedFile)) {
            return;
        }

        $validationRules[] = 'mimes:csv,txt';
        $validationRules[] = 'max:4000';

        $validation = Validator::make(
            ['file_upload' => $uploadedFile],
            ['file_upload' => $validationRules]
        );

        if ($validation->fails())
            throw new ValidationException($validation);

        if (!$uploadedFile->isValid())
            throw new ApplicationException(sprintf('File %s is not valid.', $uploadedFile->getClientOriginalName()));

        return $this->importFileProcess($uploadedFile);
    }

    private function importFileProcess($uploadedFile)
    {
        $delimiter = post('delimiter');
        if (!$delimiter)
            throw new \AjaxException([trans("logimonde.quickpresse::lang.app.imp_list_d")]);
        \Config::set('excel.csv.delimiter', $delimiter);
        $ext = $uploadedFile->getClientOriginalExtension();
        $basename = basename($uploadedFile->getClientOriginalName(), "." . $ext);
        $filename = str_random(8) . '_' . str_slug($basename) . '.' . $ext;
        $destinationPath = temp_path('public');
        $uploadedFile->move($destinationPath, $filename);
        $this->page['fields'] = $this->getFieldNamesFile($destinationPath, $filename);
        $this->page['filename'] = $filename;
        return [
            'success' => "The file has been upload",
            'fields' => [
                "element" => "#imported-fields",
                "html" => $this->renderPartial('@_fields')

            ],
            'filename' => $filename,
        ];
    }

    private function getFieldNamesFile($destinationPath, $filename)
    {
        $excelFile = Excel::load($destinationPath . '/' . $filename);
        $excelData = $excelFile->all();
        $data = $excelData->first()->keys()->toArray();
        return $data;
    }

    public function onSaveImportedData()
    {
        $filename = post('filename');
        $columns = post('columns');
        $fields = $this->checkEmptyString(post('app_fields'));
        if (is_array($fields) && count($fields) <= 0) {
            throw new \AjaxException([
                'error' => trans("logimonde.quickpresse::lang.app.imp_list"),
            ]);
        }
        $include = post('include');
        $csvData = $this->getDataImportedFile($filename);
        if (is_array($csvData) && count($csvData) > 0) {
            foreach ($csvData as $row) {
                $contact = new CustomContacts;
                foreach ($include as $key => $value) {
                    if (isset($fields[$key])) {
                        $contact->{$fields[$key]} = $row[$columns[$key]];
                    }
                }
                $contact->public_key = str_random(24);
                $contact->custom_list_id = post('list_id');
                $contact->elastic_mail = $this->isHotmail($contact->email);
                $contact->active = '1';
                $contact->save();
            }
        }
    }

    private function isHotmail($email)
    {
        $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@([lL][iI][vV][eE]|[mM][sS][nN]|[oO][uU][tT][lL][oO][oO][kK]|[hH][oO][tT][mM][aA][iI][lL])(\.[a-z]{2,3})$/';
        if (preg_match($regex, $email)) {
            return true;
        } else {
            return false;
        }
    }

    private function getDataImportedFile($file)
    {
        $delimiter = post('delimiter');
        if (!$delimiter)
            throw new \AjaxException([
                'error' => trans("logimonde.quickpresse::lang.app.imp_list_d"),
            ]);
        \Config::set('excel.csv.delimiter', $delimiter);
        $destinationPath = temp_path('public');
        $excelFile = Excel::load($destinationPath . '/' . $file);
        $excelData = $excelFile->all();
        return $excelData->toArray();
    }

    private function checkEmptyString($fields)
    {
        $newArray = array();
        foreach ($fields as $field) {
            if ($field != '') {
                $newArray[] = $field;
            }
        }
        return $newArray;
    }
}
