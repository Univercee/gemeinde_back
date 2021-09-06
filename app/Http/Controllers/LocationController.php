<?php
namespace App\Http\Controllers;
use App\Managers\LocationManager;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct()
    {
      $this->middleware('enforceJson');
    }

    public function getNearestLocation(Request $request){
        $lat = $request->query('lat');
        $lng = $request->query('lng');
        if(!is_numeric($lat) || !is_numeric($lng) || $lat<-90 || $lat>90 || $lng<-180 || $lng>180){
            abort(response()->json(['error' => 'Bad Request'], 400));
        } 
        $results = app('db')->select("SELECT id, name_de AS name, CONCAT(zipcode, ' ', name_de, ' ',region) as display_name, 
        region, lat, lng, elevation, language, round(ST_Distance_Sphere(position, ST_GeomFromText('POINT($lat $lng)', 4326))) AS distance
        FROM locations ORDER BY distance LIMIT 10");
        return response()->json($results);
    }

    // [GENA-5]
    public function getServicesByZipCode($zipcode)
    {
        if(!preg_match("/^[1-9]\d{3}$/", $zipcode)) // https://rgxdb.com/r/3GYNWXVR
        {
            abort(response()->json(['error' => 'Bad Request'], 400));
        }

        $results = app('db')
            ->select("SELECT l.id as location_id, l.zipcode, l.region, l.name_de as location_name,
                        s.id as service_id, s.name_en as service_name
                        FROM locations l
                        LEFT JOIN location_services ls ON l.id = ls.location_id
                        LEFT JOIN services s ON s.id = ls.service_id
                        WHERE l.zipcode = :zipcode", ['zipcode' => $zipcode]);
        if(empty($results)){
          abort(response()->json(['error' => 'Not found'], 404));
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
                ->select("SELECT DISTINCT locations.id, zipcode, name_de AS 'name', CONCAT(zipcode, ' ', name_de, ' ',region) as display_name, region, lat, lng, elevation, language
                        FROM locations
                        JOIN location_services ON location_services.location_id = locations.id");
        return response()->json($results);
    }

    public function getAllLocations(){
        $results = app('db')
                ->select("SELECT id, CONCAT(zipcode, ' ', name_de, ' ',region) as display_name, region, lat, lng, elevation, language FROM locations");
        return response()->json($results);
    }
}
