<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;

class ProfileController extends Controller
{
 public function userInfo(Request $request){
        return response()->json(['Message'=>'Go to profile']);
    }
}
