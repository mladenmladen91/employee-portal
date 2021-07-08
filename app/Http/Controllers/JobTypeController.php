<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobType;
use Illuminate\Support\Facades\Validator;

class JobTypeController extends Controller
{
    // function for updating work time
    public function updateJobType(Request $request){
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

        $jobType = JobType::find($request->id);

        if(!$jobType){
            return response()->json(['success'=> false, 'message' => "JobType not found"], 404);
        }

        $input = $request->all();
        $input["modfied_by"] = $user->id;

        $jobType->update($input);
        
        return response()->json(['success'=> true, 'message' => "JobType updated", "jobType" => $jobType], 200);
    }

    // function for adding work time
    public function addJobType(Request $request){
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
        $jobType = JobType::create($input);
        return response()->json(['success'=> true, 'message' => "JobType created", "jobType" => $jobType], 200);
    }

    // function for getting job types
    public function getJobTypes(Request $request){
        
        $jobTypes = JobType::all();
        return response()->json(['success'=> true, "jobTyped" => $jobTypes], 200);
    }

    // function for removing job types
    public function removeJobType(Request $request){
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

        $jobType = JobType::find($request->id);

        if(!$jobType){
            return response()->json(['success'=> false, 'message' => "JobType not found"], 404);
        }

        $jobType->delete();

        
        return response()->json(['success'=> true, 'message' => "JobType removed"], 200);
    }

    // function for getting jobType based on type of work id
    public function getJobTypesByTypeOfWork(Request $request){
        $validator = Validator::make($request->all(), [
            "id" => "required"
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }
        

        $jobTypes = JobType::where("type_of_work_id", $request->id)->get();

        
        return response()->json(['success'=> true, 'jobTypes' => $jobTypes], 200);
    }
}
