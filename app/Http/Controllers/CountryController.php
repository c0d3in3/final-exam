<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\User;

class CountryController extends Controller
{

    public function getCountries(Request $request){
        return ['results' => Country::select(['id', 'name as text'])
        ->where('name', 'LIKE', '%'.$request->input('term').'%')
        ->get()];
    }

    public function getSavedCountries(Request $request) {
        $countries = $request->user()->savedCountries()->get();
        $result = [];
        foreach($countries as $c) {
            $response = Http::get('https://disease.sh/v3/covid-19/countries/'.$c['code']);
            $data = [
                'id' => $c['id'],
                'name' => $c['name'],
                'code' => $c['code'],
                'todayCases' => $response['todayCases'],
                'todayDeaths' => $response['todayDeaths'],
                'todayRecovered' => $response['todayRecovered'],
                'flagUrl' => $response['countryInfo']['flag'],
            ];
            $result[] = $data;
        }
        return $result;
    }

    public function saveCountry(Request $request) {
        $country = Country::findOrFail($request->input('countryID'));
        $request->user()
            ->savedCountries()
            ->attach($request->input('countryID'));
        $request->user()->save();
        $response = Http::get('https://disease.sh/v3/covid-19/countries/'.$country['code']);
        $data = [
            'id' => $country['id'],
            'name' => $country['name'],
            'code' => $country['code'],
            'todayCases' => $response['todayCases'],
            'todayDeaths' => $response['todayDeaths'],
            'todayRecovered' => $response['todayRecovered'],
            'flagUrl' => $response['countryInfo']['flag'],
        ];
        return $data;
    }

    public function deleteSavedCountry(Request $request) {
        $country = Country::findOrFail($request->countryID);
        $request->user()
        ->savedCountries()
        ->detach($request->countryID);
        $request->user()->save();
        return ['status' => 'success'];
    }

    public function getWorldStats(Request $request) {
        $response = Http::get('https://disease.sh/v3/covid-19/all');
        return ['todayCases' => $response['todayCases'], 'todayDeaths' => $response['todayDeaths'], 'todayRecovered' => $response['todayRecovered']];
    }

}
