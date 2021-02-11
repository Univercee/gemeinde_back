<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Service;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    // [GENA-5]
    public function getServicesByZipCode($zipcode){

        if(!preg_match("/^[1-9]\d{3}$/", $zipcode)){ // https://rgxdb.com/r/3GYNWXVR
            return response()->json(['error' => 'Bad Request'], 400);
        }
        $results = app('db')
            ->select("SELECT l.id, l.zipcode, l.region,l.name_en as name, s.id, s.name_en
                        FROM locations l
                            JOIN location_services ls
                                ON l.id = ls.location_id
                                JOIN services s
                                ON s.id = ls.service_id
                                WHERE l.zipcode = :zipcode", ['zipcode' => $zipcode]);
        $locations = [];
        $services2['services'] = [];
        foreach($results as $key)
        {
            if(!array_key_exists('location', $locations)){
                $locations = ['location' => ["id" => $key->id, "zipcode" => $key->zipcode, "name" => $key->name, "region" => $key->region]];
            }

            $services = ["id" => $key->id, "name"=>$key->name_en];
            array_push($services2['services'], $services);
        }
        $resultArray = array_merge($locations, $services2);
        return response()->json($resultArray);
    }
}
