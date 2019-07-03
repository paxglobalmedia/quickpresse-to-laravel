<?php namespace Logimonde\Quickpresse\Traits;

use Input;
use Request;
use Validator;
use ValidationException;
use ApplicationException;
use System\Models\File;
use October\Rain\Database\Attach\Resizer;

trait ComponentUtils
{

    public $model;
    public $attribute;
    protected $populated;

    public function bindModel($attribute, $model)
    {
        if (is_callable($model))
            $model = $model();

        $this->model = $model;
        $this->attribute = $attribute;

        if ($this->model) {
            $relationType = $this->model->getRelationType($attribute);
            $this->isMulti = ($relationType == 'attachMany' || $relationType == 'morphMany');
            $this->isBound = true;
        }
    }

    public function isPopulated()
    {
        if ($this->isMulti) {
            return $this->getPopulated()->count() > 0;
        } else {
            return !!$this->getPopulated();
        }
    }

    public function getPopulated()
    {
        if ($this->populated !== null)
            return $this->populated;

        /*
         * Use deferred bindings
         */
        if ($sessionKey = $this->getSessionKey()) {
            $deferredQuery = $this->model
                ->{$this->attribute}()
                ->withDeferred($sessionKey)
                ->orderBy('id', 'desc');

            return $this->isMulti ? $deferredQuery->get() : $deferredQuery->first();
        }

        return $this->model->{$this->attribute};
    }

    protected function checkUploadAction()
    {
        $uploadedFile = Input::file('file_data');
        if (!Request::isMethod('POST') || !is_object($uploadedFile)) {
            return;
        }

        $validationRules = [];

        /*
         * Validate file types
         */
        if (count($this->fileTypes)) {
            $mimes = trim(implode(',', (array)$this->fileTypes));
            $mimes = str_replace('.', '', $mimes);
            if ($mimes !== '*') {
                $validationRules[] = 'mimes:' . $mimes;
            }
        }

        $validation = Validator::make(
            ['file_data' => $uploadedFile],
            ['file_data' => $validationRules]
        );

        if ($validation->fails())
            throw new ValidationException($validation);

        if (!$uploadedFile->isValid())
            throw new ApplicationException(sprintf('File %s is not valid.', $uploadedFile->getClientOriginalName()));

        $file = new File;
        $file->data = $uploadedFile;
        $file->is_public = true;
        $file->save();

        $this->model->{$this->attribute}()->add($file, $this->getSessionKey());

        return [
            'id' => $file->id,
            'path' => $file->getPath()
        ];
    }

    public function getSessionKey()
    {
        return !!$this->property('deferredBinding')
            ? post('_session_key')
            : null;
    }

    public function getFileList()
    {
        /*
         * Use deferred bindings
         */
        if ($sessionKey = $this->getSessionKey()) {
            $list = $this->model
                ->{$this->attribute}()
                ->withDeferred($sessionKey)
                ->orderBy('id', 'desc')
                ->get();
        } else {
            $list = $this->model
                ->{$this->attribute}()
                ->orderBy('id', 'desc')
                ->get();
        }
        if (!$list) {
            $list = new Collection;
        }
        /*
         * Decorate each file with thumb
         */
        $list->each(function ($file) {
            $this->decorateFileAttributes($file);
        });
        return $list;
    }

    /**
     * Returns the specified accepted file types, or the default
     * based on the mode. Image mode will return:
     * - jpg,jpeg,bmp,png,gif,svg
     * @return string
     */

    protected function processFileTypes()
    {
        $fileTypes = $this->property('fileTypes', '*');
        $result = [];

        if ($fileTypes !== '*') {
            foreach (explode(',', $fileTypes) as $type) {
                $type = trim($type);

                if (substr($type, 0, 1) !== '.') {
                    $type = '.' . $type;
                }

                $result[] = $type;
            }
        } else {
            $result[] = '*';
        }

        return $result;
    }

    /**
     * Returns the specified accepted file types, or the default
     * based on the mode. Image mode will return:
     * - jpg,jpeg,bmp,png,gif,svg
     * @return string
     */
    protected function processFileTypes2($includeDot = false)
    {
        $types = $this->property('fileTypes', '*');
        if (!$types || $types == '*') {
            $types = implode(',', File::getDefaultFileTypes());
        }
        if (!is_array($types)) {
            $types = explode(',', $types);
        }
        $types = array_map(function ($value) use ($includeDot) {
            $value = trim($value);
            if (substr($value, 0, 1) === '.') {
                $value = substr($value, 1);
            }
            if ($includeDot) {
                $value = '.' . $value;
            }
            return $value;
        }, $types);
        return implode(',', $types);
    }

    // function to make slug (URL string)
    public function slugify($text, $separator)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', $separator, $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}
