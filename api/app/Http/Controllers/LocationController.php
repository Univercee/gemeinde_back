<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Service;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function searchByZipCode($id){
        $var = ['location'=>["id"=>1,"zipcode"=>$id]];
        $results = app('db')
            ->select("SELECT l.id, l.zipcode, l.name_en, s.id, s.name_en
                        FROM locations l
                            JOIN location_services ls
                                ON l.id = ls.location_id
                                JOIN services s
                                ON s.id = ls.service_id
                                WHERE l.zipcode = '$id'");

        return response()->json($results);
    }
}
