<?php namespace Logimonde\QuickPresse\Classes;

use Logimonde\Quickpresse\Models\Settings;


class EblastFooter
{
    public static function create($campaign, $encode = null)
    {
        $obj = new static;
        return $obj->createEblastFooter($campaign, $encode);
    }

    public function createEblastFooter($campaign, $encode)
    {
        if ($campaign->type_qp == 'custom') {
            $footer = $this->getFooterCustomEblast($campaign, $encode);
        } else {
            $footer = $campaign->language == 'en' ? Settings::get('qp_footer_en') : Settings::get('qp_footer_fr');
        }
        return $footer;
    }

    private function getFooterCustomEblast($campaign, $encode)
    {
        $link = config('app.url') . 'unsubscribe/custom-list/' .
            $campaign->custom_list->public_key;

        if (!is_null($encode)) {
            $link .= '/' . $encode;
        }

        if ($campaign->language == 'en') {
            $footer = $campaign->custom_list->reminder .
                '<br><br>Unsubscribe:&nbsp;' .
                '<a href="' . $link . '" target="_blank">Click here</a>';
        } else {
            $footer = $campaign->custom_list->reminder .
                '<br><br>Se désabonner:&nbsp;' .
                '<a href="' . $link . '" target="_blank">Cliquez ici</a>';
        }
        return $footer;
    }
}
