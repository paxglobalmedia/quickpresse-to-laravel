<?php

use RainLab\Translate\Classes\Translator as languageTranslator;
use Logimonde\Quickpresse\Models\Province;
use Logimonde\Quickpresse\Models\City;
use Logimonde\Crm\Models\Company;
use Logimonde\Quickpresse\Models\EblastSend;
use Logimonde\Quickpresse\Models\Click as BaseClick;
use Logimonde\Quickpresse\Models\Eblast;
use Logimonde\Quickpresse\Models\EblastContent;
use Logimonde\Quickpresse\Models\Stats;
use RainLab\User\Models\User;


Route::get('search/city', function () {
    $city = \Input::get('city');
    $translator = languageTranslator::instance();
    $lang = $translator->getLocale();

    $cities = \Db::connection('logimonde')->table('city');

    $cities->leftJoin('province', 'province.idProvince', '=', 'city.idProvince');
    $cities->leftJoin('country', 'country.idPays', '=', 'province.idPays');
    $cities->select(
        "city.idVille as city_id",
        "city.nom_$lang as city_name",
        "province.idProvince as state_id",
        "province.nom_$lang as state_name",
        "country.idPays as country_id",
        "country.nom_$lang as country_name"
    );
    $cities->where("city.nom_$lang", 'LIKE', "%$city%");
    $cities->orderBy("country.nom_$lang", 'asc');
    $cities->orderBy("province.nom_$lang", 'asc');
    $cities->orderBy("city.nom_$lang", 'asc');
    $data = $cities->take(10)->get();
    return \Response::json($data);
});

Route::get('search/company', function () {
    $search = \Input::get('company');

    $data = array();
    $companies = Company::where('Name', 'LIKE', "%$search%")
        ->orderBy('Name')
        ->get();
    foreach ($companies as $company) {
        $item['id'] = $company->id;
        $item['name'] = $company->Name;
        $data[] = $item;
    }
    return \Response::json($data);
});

Route::get('update/location/{type}', function ($type) {
    $items = null;
    $model = null;
    if ($type == 'user') {
        $items = User::get();
    } else if ($type == 'account') {
        $items = Account::isActive()->get();
    } else if ($type == 'company') {
        $items = Company::isActive()->get();
    }
    foreach ($items as $item) {
        if ($type == 'user') {
            $model = User::whereId($item->id)->first();
        } else if ($type == 'account') {
            $model = Account::whereId($item->id)->first();
        } else if ($type == 'company') {
            $model = Company::whereId($item->id)->first();
        }

        $city = City::where('idVille', $item->city_id)->first();
        $state = Province::where('idProvince', $city->idProvince)->first();
        $model->country_id = $state->idPays;
        $model->state_id = $state->idProvince;
        $model->save();
    }
});


Route::get('stats/show/{id}', function ($id) {
    $qp = EblastSend::whereId($id)->first();
    if ($qp) {
        $qp->statistics();

        $pixel = base64_decode(
            'R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw=='
        );
        return \Response::make($pixel)
            ->header('Content-Type', 'image/gif')
            ->header('Cache-Control', 'must-revalidate, no-cache, no-store, private');
    } else {
        return \Redirect::to('404');
    }
});

Route::get('stats/newsletter/{id}', function ($id) {
    $qp = EblastSend::whereId($id)->first();
    if ($qp) {
        $stats = new Stats;
        $stats->eblast_send_id = $qp->id;
        $stats->view_time = date('Y-m-d H:i:s');
        $stats->save();

        $pixel = base64_decode(
            'R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw=='
        );
        return \Response::make($pixel)
            ->header('Content-Type', 'image/gif')
            ->header('Cache-Control', 'must-revalidate, no-cache, no-store, private');
    } else {
        return \Redirect::to('404');
    }
});

Route::get('click-tracking/html/{code}', function ($code) {
    $click = BaseClick::where('code', $code)->first();
    if ($click) {
        $click->increment('actions');
        $content = EblastContent::whereId($click->eblast_content_id)->first();
        if ($content) {
            $content->increment('clicks');
        }
        return \Redirect::to($click->url);
    } else {
        return \Redirect::to('404');
    }


});

Route::get('click-tracking/image/{code}', function ($code) {
    $content = EblastContent::where('code', $code)->first();
    if ($content) {
        $content->increment('clicks');

        return \Redirect::to($content->link_content);
    } else {
        return \Redirect::to('404');
    }
});
