<?php
namespace App\Http\Controllers;

class LocationController extends Controller
{
    public function __construct()
    {
      $this->middleware('enforceJson');
    }

    // [GENA-5]
    public function getServicesByZipCode($zipcode)
    {
        if(!preg_match("/^[1-9]\d{3}$/", $zipcode)) // https://rgxdb.com/r/3GYNWXVR
        {
            return response()->json(['error' => 'Bad Request'], 400);
        }

        $results = app('db')
            ->select("SELECT l.id as location_id, l.zipcode, l.region, l.name_de as location_name,
                        s.id as service_id, s.name_en as service_name
                        FROM locations l
                        LEFT JOIN location_services ls ON l.id = ls.location_id
                        LEFT JOIN services s ON s.id = ls.service_id
                        WHERE l.zipcode = :zipcode", ['zipcode' => $zipcode]);
        if(empty($results)){
            return response()->json(['error' => 'Not found'], 404);
        }
        $l = $results[0];
        $location_services['location'] = ['id' => $l->location_id, 'zipcode' => $l->zipcode,
        'name' => $l->location_name, 'region' => $l->region];
        $location_services['services'] = [];

        foreach($results as $key)
        {
            if($key->service_id && $key->service_name)
            {
                array_push($location_services['services'], ['id' => $key->service_id, 'name'=>$key->service_name]);
            }
        }
        return response()->json($location_services);
    }

    // [GENA-4]
    public function getLocationsHaveServices(){
        $results = app('db')
                ->select("SELECT DISTINCT locations.id, zipcode, name_de AS 'name', region, lat, lng
                        FROM locations
                        JOIN location_services ON location_services.location_id = locations.id");
        return response()->json($results);
    }

    public function getAllLocations(){
        $results = app('db')
                ->select("SELECT id, CONCAT(zipcode, ' ', name_de, ' ',region) as display_name FROM locations");
        return response()->json($results);
    }
}
