<?php namespace Logimonde\Quickpresse\Components;

use Cms\Classes\ComponentBase;
use Excel;
use RainLab\Translate\Classes\Translator as languageTranslator;
use Db;

class Reports extends ComponentBase
{
    use \Logimonde\Account\Traits\AccountUtils;
    use \Logimonde\Quickpresse\Traits\CommonTraits;

    public $lang;

    public $user;

    public $pageParam;

    public $perPage;
    public $options;

    public function componentDetails()
    {
        return [
            'name' => 'Reports',
            'description' => 'Reports Component'
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
                'default' => '15',
            ],
        ];
    }

    public function init()
    {
        $this->user = $this->user();
        $this->perPage = $this->property('itemsPerPage');
        $translator = languageTranslator::instance();
        $this->lang = $translator->getLocale();
    }

    public function onRun()
    {
        $this->addJs('assets/js/reports.js', '1.009');
        $this->page['totals'] = $dataTotals = $this->getTotalDay();

        $this->page['printRedirect'] = $this->pageUrl('app-print/total-day');
        if (input('print')) {
            $this->page['print'] = true;
        }

        if (input('export')) {
            $this->exportTotals($dataTotals);
        }
    }

    private function getTotalDay()
    {
        $startDate = $this->page['startDate'] = input('start_date') != '' ? input('start_date') : null;
        $endDate = $this->page['endDate'] = input('end_date') != '' ? input('end_date') : null;

        if ($startDate != '') {
            $total = Db::table('logimonde_quickpresse_total_day')
                ->where('date_sent', '>=', $this->renderDates($startDate))
                ->where('date_sent', '<=', $this->renderDates($endDate))
                ->orderBy('date_sent', 'asc')->paginate($this->perPage);
        } else {
            $total = Db::table('logimonde_quickpresse_total_day')->orderBy('date_sent', 'desc')->paginate($this->perPage);
        }

        return $total;
    }

    private function setDataInArray($dataTotals)
    {
        $headers = $this->exportHeaders();
        foreach ($dataTotals as $dataStat) {
            $item[$headers['date']] = $dataStat->date_sent;
            $item[$headers['qp_en']] = $dataStat->en;
            $item[$headers['total_en']] = $dataStat->en > 0 ? $dataStat->total_en : 0;
            $item[$headers['qp_fr']] = $dataStat->fr;
            $item[$headers['total_fr']] = $dataStat->fr > 0 ? $dataStat->total_fr : 0;

            $data[] = $item;
        }
        return $data;
    }

    protected function exportTotals($dataTotals)
    {
        $data = $this->setDataInArray($dataTotals);

        Excel::create('totals', function ($excel) use ($data) {
            $excel->sheet('Totals', function ($sheet) use ($data) {
                $sheet->fromArray($data);
            });
        })->export('xls');
    }

    private function exportHeaders()
    {
        $headers = [
            'en' => [
                'date' => 'Date',
                'qp_en' => 'QP - English',
                'total_en' => 'Total ($) - English',
                'qp_fr' => 'QP - French',
                'total_fr' => 'Total ($) - French',
            ],
            'fr' => [
                'date' => 'Date',
                'qp_en' => 'QP - Anglais',
                'total_en' => 'Total ($) - Anglais',
                'qp_fr' => 'QP - Français',
                'total_fr' => 'Total ($) - Français',
            ],
        ];
        return $headers[$this->lang];
    }
}
