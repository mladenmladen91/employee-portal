<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller
{
    // function for adding city
    public function addCity(Request $request){
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "name_short" => "required",
            "zip_code" => "required",
            "country_id" => "required|integer",
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if(!$user || $user->role != "admin"){
            return response()->json(['success'=> false, 'message' => "User unauthorized"], 401);
        }

        $input = $request->all();
        $input["created_by"] = $user->id;
        $city = City::create($input);
        return response()->json(['success'=> true, 'message' => "City created"], 200);
    }

    // function for updating particular city
    public function updateParticularCity(Request $request){
        $validator = Validator::make($request->all(), [
            "id" => "required|integer"
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if(!$user || $user->role != "admin"){
            return response()->json(['success'=> false, 'message' => "User unauthorized"], 401);
        }
        /*if($user->id != $request->user_id){
            return response()->json(['success'=> false, 'message' => "User unauthorized"], 401);
        }*/

        $input = $request->except(['id', 'user_id']);
        $input["modified_by"] = $user->id;
        $city = City::where('id', $request->id);
        $city->update($input);
        return response()->json(['success'=> true, 'message' => "Company user updated", "experience" => $city->first()], 200);
    }

    // function for removing city
    public function removeCity(Request $request){
        $validator = Validator::make($request->all(), [
            "id" => "required|integer"
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if(!$user || $user->role != "admin"){
            return response()->json(['success'=> false, 'message' => "User unauthorized"], 401);
        }
        $city = City::find($request->id);
        

        $city->delete();
        return response()->json(['success'=> true, 'message' => "Company user deleted"], 200);
    }

    //  function for getting cities
    public function getCities(Request $request){
        
        $cities = City::all();
        
        return response()->json(['success'=> true, 'cities' => $cities], 200);
    }

    //  function for getting cities
    public function searchCities(Request $request){

        if($request->term){
            $cities = City::where("name", "like", "%" . $request->term . "%")->get();
        }else{
            $cities = City::all();
        }    
        
        return response()->json(['success'=> true, 'cities' => $cities], 200);
    }
}
