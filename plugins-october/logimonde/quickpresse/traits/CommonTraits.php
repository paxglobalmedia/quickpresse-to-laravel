<?php namespace Logimonde\Quickpresse\Traits;

use Auth;
use Request;
use Validator;
use RainLab\Translate\Classes\Translator as languageTranslator;
use Carbon\Carbon;
use October\Rain\Database\Attach\Resizer;

trait CommonTraits
{

    protected function renderDatesLong($date, $time, $lang)
    {
        if ($lang == 'en') {
            $date_format = substr($date, 6, 4) . '-' . substr($date, 0, 2) . '-' . substr($date, 3, 2);
            return $date_format . ($time !== '' ? ' ' . date('G:i', strtotime(strtoupper($time))) : '');
        } else {
            $date_format = substr($date, 6, 4) . '-' . substr($date, 3, 2) . '-' . substr($date, 0, 2);
            return $date_format . ($time !== '' ? ' ' . trim($time) : '');
        }
    }

    protected function renderDates($date)
    {
        $translator = languageTranslator::instance();
        $lang = $translator->getLocale();

        if ($lang === 'en') {
            return  substr($date, 6, 4) . '-' . substr($date, 0, 2) . '-' . substr($date, 3, 2);
        } else {
            return substr($date, 6, 4) . '-' . substr($date, 3, 2) . '-' . substr($date, 0, 2);
        }
    }

    public function myTimeFormat($time)
    {
        if (substr($time, 11) === '00:00:00') {
            return '';
        }
        $translator = languageTranslator::instance();
        $lang = $translator->getLocale();
        $format = '';
        $timeObj = new Carbon($time);
        if ($lang === 'fr') {
            $format = 'G:i';
        } else if ($lang === 'en') {
            $format = 'g:i a';
        }
        return date($format, $timeObj->getTimestamp());

    }

    public function userRoleValidate($user)
    {
        if ($user->role_id === '1') {
            return 'admin';
        } else {
            return 'user';
        }
    }

    public function onShareEmail()
    {
        $translator = languageTranslator::instance();
        $lang = $translator->getLocale();

        $data = input();
        if (isset($data['code'])) {
            $event = Event::where('code', $data['code'])->first();
            $data['server'] = env('APP_ENV') === 'dev' ? 'http://10.1.1.24/' : 'http://173.231.105.250';
            $data['logo'] = isset($event->logo->disk_name) ? $event->logo->getPath() : '/storage/app/media/img-default/logo-default.jpg';
            $data['event'] = $event;
            if ($event->public === '1') {
                $data['linkWebinar'] = $data['server'] . '/preview/webinar/' . $event->code;
            } else {
                $data['linkWebinar'] = $data['server'] . '/attendee/register/' . $event->code;
            }

            \Mail::send('logimonde.webinar::mail.share_event_' . $lang, $data, function ($message) use ($data) {
                $message->to($data['email_to']);
                if (isset($data['copyEmail']) && $data['copyEmail'] === '1') {
                    $message->cc($data['email_from']);
                }
            });

            $params = array(
                'type' => 'success',
                'message' => 'Email sent'
            );
            return $params;
        }
    }

    public function betweenDates($dateStart, $dateEnd)
    {
        $start = new Carbon($dateStart);
        $end = new Carbon($dateEnd);
        $now = new Carbon();

        return $now->between($start, $end);
    }

    public function pageValidation()
    {
        $page = input('page');
        $lastPage = input('lastPage');
        $page = (is_array($page)) ? $page[0] : $page;
        $page = ($page > $lastPage) ? $lastPage : $page;

        return $page;
    }

    public function validateUpload($input, $file, $rules)
    {

        $data = array($input => $file);

        $validation = Validator::make($data, $rules);
        $messages = $validation->messages();
        $msgError = '';

        if ($validation->fails()) {
            foreach ($messages->get('userfile') as $message) {
                $msgError .= $message . '<br>';
            }
            $response = array('fails' => $msgError);
        } else {

            $response = array('success' => trans('app.upload1'));
        }
        return $response;
    }

    public function processingLogoFile($file, $id, $type)
    {
        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());
        $mime = $file->getMimeType();

        $name = str_random(8) . '_' . str_slug(str_replace('.', '_', $originalName), '_') . '.' . $extension;
        $size = $file->getSize();

        $path = storage_path("app/media/logo/$type/$id");
        $file->move($path, $name);

        $dataResponse = array('originalName' => $originalName, 'filename' => $name, 'extension' => $extension, 'size' => $size, 'mime' => $mime, 'path' => $path);
        return $dataResponse;
    }

    public function resizeImage($file, $height, $width)
    {
        $extension = $file['extension'];
        if (strtolower($extension) != 'pdf') {
            $fileResize = $file['path'] . '/' . $file['filename'];
            $resizer = Resizer::open($fileResize);
            $resizer->resize($width, $height);
            $resizer->save($fileResize, 95);
        }
    }

    private function checkMediaFolderUser($folder, $type)
    {
        $mediaFolder = storage_path("app/media/$type/$folder");
        if (!file_exists($mediaFolder)) {
            mkdir($mediaFolder, 0777, true);
        }
        return $mediaFolder;
    }

    public function getDimensionsPhoto($width, $height)
    {
        $percent = (350 / $width) * 100;
        return [
            'width' => $width * (intval($percent + 2) / 100),
            'height' => $height * (intval($percent + 2) / 100),
        ];

    }

    Public function insertEndOfLine($file)
    {
        $html = file_get_contents($file);
        $html = str_replace('>', '>' . PHP_EOL, $html);

        file_put_contents($file, $html . PHP_EOL, LOCK_EX);
    }
}
