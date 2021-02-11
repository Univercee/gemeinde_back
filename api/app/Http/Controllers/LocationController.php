<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Service;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    // [GENA-5]
    public function getServicesByZipCode($zipcode){

        $results = app('db')
            ->select("SELECT l.id, l.zipcode, l.region,l.name_en, s.id, s.name_en
                        FROM locations l
                            JOIN location_services ls
                                ON l.id = ls.location_id
                                JOIN services s
                                ON s.id = ls.service_id
                                WHERE l.zipcode = :zipcode", ['zipcode' => $zipcode]);

        return response()->json($results);
    }



    // [GENA-4]
    public function getLocationsHaveServices(){
        $results = app('db')
                ->select("SELECT DISTINCT locations.id, zipcode, name_en AS 'name', region, lat, lng
                        FROM locations
                        JOIN location_services ON location_services.location_id = locations.id
                        ");
        return response()->json(['locations'=>$results]);
    }
}
