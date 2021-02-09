<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function searchByZipCode($id){
        $var = ['location'=>["id"=>1,"zipcode"=>$id]];

        return response()->json($var);
    }
}
