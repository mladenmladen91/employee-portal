<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\City;
use Illuminate\Support\Facades\Validator;

class CountryController extends Controller
{
    // function for adding country
    public function addCountry(Request $request){
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "name_short" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }
        $user = auth()->user();
        if(!$user){
            return response()->json(['success'=> false, 'message' => "User unauthorized"], 401);
        }

        $input = $request->all();
        $input["created_by"] = $user->id;
        $country = Country::create($input);
        return response()->json(['success'=> true, 'message' => "Country created"], 200);
    }

    // function for updating particular country
    public function updateParticularCountry(Request $request){
        $validator = Validator::make($request->all(), [
            "id" => "required|integer"
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }
        $user = auth()->user();
        if(!$user){
            return response()->json(['success'=> false, 'message' => "User unauthorized"], 401);
        }
        if($user->id != $request->user_id){
            return response()->json(['success'=> false, 'message' => "User unauthorized"], 401);
        }

        $input = $request->except(['id', 'user_id']);
        $input["modified_by"] = $user->id;
        $country = Country::where('id', $request->id);
        $country->update($input);
        return response()->json(['success'=> true, 'message' => "Company user updated", "experience" => $country->first()], 200);
    }

    // function for removing country
    public function removeCountry(Request $request){
        $validator = Validator::make($request->all(), [
            "id" => "required|integer"
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        } 

        $user = auth()->user();
        if(!$user){
            return response()->json(['success'=> false, 'message' => "User unauthorized"], 401);
        }
        $country = Country::find($request->id);
        if($country->user_id != $user->id){
            return response()->json(['success'=> false, 'message' => "User unauthorized"], 401);
        }

        $country->delete();
        return response()->json(['success'=> true, 'message' => "Company user deleted"], 200);
    }
  //  function for getting countries
    public function getCountries(Request $request){
        
        $countries = Country::all();
        
        return response()->json(['success'=> true, 'countries' => $countries], 200);
    }

    //  function for getting countries
    public function getCountryCity(Request $request){
        $validator = Validator::make($request->all(), [
            "id" => "required|integer"
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        } 

        $id = $request->id;
        
        $cities = City::where(function ($query) use ($id) {
           if($id){ 
            $query
            ->where("country_id", "=", $id);
           }
        })->get();
        
        return response()->json(['success'=> true, 'cities' => $cities], 200);
    }

   //  function for getting countries or cities
   public function autoCompleteCountryCity(Request $request)
   {

       $all = [];

       if ($request->keyword) {
           $cities = City::select("id", "name")->withCount("activeAds")->where("name", "like", "%" . $request->keyword . "%")->get();
           $cities = collect($cities)->map(function ($item) {
               $item['field'] = 'city_id';
               return $item;
           });
           $cities = $cities->toArray();
           $countries = Country::select("id", "name")->withCount("activeAds")->where("name", "like", "%" . $request->keyword . "%")->get();
           $countries = collect($countries)->map(function ($item) {
               $item['field'] = 'country_id';
               return $item;
           });
           $countries = $countries->toArray();
           $all = array_merge($cities, $countries);
       } else {
           $all = Country::select("id", "name")->withCount("activeAds")->get();
           $all = collect($all)->map(function ($item) {
               $item['field'] = 'country_id';
               return $item;
           });
           $all = $all->toArray();
       }

       return response()->json(['success' => true, 'all' => $all], 200);
   }
}
