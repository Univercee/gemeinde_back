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

        $location_query = app('db')->select("SELECT id FROM locations WHERE zipcode = :zipcode", ['zipcode'=> $zipcode]);
        if(empty($location_query)){
            return response()->json(['error' => 'Not found'], 404);
        }

        $results = app('db')
            ->select("SELECT l.id as location_id, l.zipcode, l.region, l.name_en as location_name, s.id as service_id, s.name_en as service_name
                        FROM locations l
                        JOIN location_services ls ON l.id = ls.location_id
                        JOIN services s ON s.id = ls.service_id
                        WHERE l.zipcode = :zipcode", ['zipcode' => $zipcode]);
        $locations = [];
        $location_services['services'] = [];
        foreach($results as $key)
        {
            if(!array_key_exists('location', $locations)){
                $locations = ['location' => ['id' => $key->location_id, 'zipcode' => $key->zipcode,
                    'name' => $key->location_name, 'region' => $key->region]];
            }
            array_push($location_services['services'], ['id' => $key->service_id, 'name'=>$key->service_name]);
        }
        $resultArray = array_merge($locations, $location_services);
        return response()->json($resultArray);
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


    // [GENA-7]
    public function generateKeyFor($email){
        app('db')->update("UPDATE users
                        SET users.key = :k, users.key_at = NOW()
                        WHERE users.email = :email",['k'=>uniqid(), 'email'=>$email]);
    }

    // [GENA-7]
    public function checkEmail(Request $request){
        $email = $request['email'];
        $data = [
            'data'=>null,
            'code'=>null
        ];
        if(!preg_match("/^[-!#-'*+\/-9=?^-~]+(?:\.[-!#-'*+\/-9=?^-~]+)*@[-!#-'*+\/-9=?^-~]+(?:\.[-!#-'*+\/-9=?^-~]+)+$/i", $email)){ // https://rgxdb.com/r/1JWKZ0PW
            $results['data'] = ['error' => 'Bad Request'];
            $results['code'] = 400;
        }
        else{
            $responce = app('db')
                        ->select("SELECT * FROM users
                                WHERE email = :email", ['email' => $email]);
            $user = $responce[0];
            if(empty($responce)){

                //registration here
                
                $results['data'] = ['error' => 'Not found'];
                $results['code'] = 404;
            }
            else{

                //login here

                $results['data'] = ['user' => $user];
                $results['code'] = 200;
                LocationController::generateKeyFor($user->email);
            }
        }
        return response()->json($results['data'], $results['code']);
    }

    // [GENA-7]
    public function confirmRegistration($key){
        app('db')->update("UPDATE users
                        SET registered_at = NOW()
                        WHERE users.key = :k",['k'=>$key]);
    }

    

    // [GENA-7]
    public function emailForm(){
        return view('api/emailForm');
    }
}
