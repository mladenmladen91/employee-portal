<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkTime;
use Illuminate\Support\Facades\Validator;

class WorkTimeController extends Controller
{
    // function for updating work time
    public function updateWorkTime(Request $request){
        $validator = Validator::make($request->all(), [
            "id" => "required"
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }
        $user = auth()->user();
        if(!$user || $user->role != "admin"){
            return response()->json(['success'=> false, 'message' => "User unauthorized"], 401);
        }

        $workTime = WorkTime::find($request->id);

        $input = $request->all();
        $input["modfied_by"] = $user->id;

        $workTime->update($input);
        
        return response()->json(['success'=> true, 'message' => "WorkTime updated", "workTime" => $workTime], 200);
    }

    // function for adding work time
    public function addWorkTime(Request $request){
        $validator = Validator::make($request->all(), [
            "name" => "required"
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
        $workTime = WorkTime::create($input);
        return response()->json(['success'=> true, 'message' => "WorkTime created", "workTime" => $workTime], 200);
    }

    // function for adding work time
    public function getWorkTimes(Request $request){
        
        $workTimes = WorkTime::all();
        return response()->json(['success'=> true, "workTimes" => $workTimes], 200);
    }

    // function for removing work time
    public function removeWorkTime(Request $request){
        $validator = Validator::make($request->all(), [
            "id" => "required"
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }
        $user = auth()->user();
        if(!$user || $user->role != "admin"){
            return response()->json(['success'=> false, 'message' => "User unauthorized"], 401);
        }

        WorkTime::find($request->id)->delete();

        
        return response()->json(['success'=> true, 'message' => "WorkTime removed"], 200);
    }
}
