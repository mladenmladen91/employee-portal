<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ad;
use App\Models\AdQuestion;
use App\Models\AdAnswer;
use App\Models\AdSharedInfo;
use App\Models\Favorite;
use App\Models\User;
use App\Models\TypeOfWorkNotification;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class AddControler extends Controller
{
    // function for getting adds
    public function getAds(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'offset' => 'required|int|min:0',
            'limit' => 'required|int',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $type_of_work_id = $request->get("type_of_work_id");
        $type_of_work_ids = json_decode($request->get("type_of_work_ids"));
        $city_id = $request->get("city_id");
        $country_id = $request->get("country_id");
        $term = $request->get("term");
        $userId = $request->get("user_id");
        $before = $request->get("before");
        $work_time_id = $request->get("work_time_id");
        $education_level_id = $request->get("education_level_id");
        $job_type_id = $request->get("job_type_id");
        $employers = json_decode($request->get("employers"));
        $employer_term = $request->get("employer_term");
        $position_term = $request->get("position_term");
        //$city_country = $request->get("city_country");

        $ads = Ad::with(["country", "city", "type_of_work"])->leftJoinSub('select id as owner_id , full_name as company, profile_image from users', "users", "users.owner_id", "=", "ads.user_id")->leftJoinSub('select id as typeOfWorkId , name as type_of_work from type_of_works', "type_of_works", "type_of_works.typeOfWorkId", "=", "ads.type_of_work_id")->leftJoinSub('select id as workTimeId , name as work_time from work_times', "work_times", "work_times.workTimeId", "=", "ads.work_time_id")->where("ads.end_date", ">=", date("Y-m-d"))->where("ads.is_active", 1)->where("ads.is_archived", 0)->where(function ($query) use ($type_of_work_id, $city_id, $country_id, $before, $type_of_work_ids, $work_time_id, $education_level_id, $employers, $job_type_id) {
            if ($city_id) {
                $query->where('ads.city_id', $city_id);
            }

            if ($country_id) {
                $query->where('ads.country_id', $city_id);
            }

            if ($type_of_work_id) {
                $query->where('ads.type_of_work_id', $type_of_work_id);
            }

            if ($job_type_id) {
                $query->where('ads.job_type_id', $job_type_id);
            }

            if ($type_of_work_ids) {
                $query->whereIn('ads.type_of_work_id', $type_of_work_ids);
            }

            if ($employers) {
                $query->whereIn('ads.user_id', $employers);
            }

            if ($before) {
                $query->where('ads.created_at', '>', Carbon::now()->subDays($before));
            }

            if ($work_time_id) {
                $query->where('work_times.workTimeId', $work_time_id);
            }

            if ($education_level_id) {
                $query->where('ads.education_level_id', $education_level_id);
            }
        });

        if ($term) {
            $ads = $ads->where(function ($query) use ($term) {
                $query
                    ->where("ads.title", "like", "%" . $term . "%")
                    ->orWhere("ads.description", "like", "%" . $term . "%")
                    ->orWhere("users.company", "like", "%" . $term . "%")
                    ->orWhere("ads.location", "like", "%" . $term . "%")
                    ->orWhere("ads.position", "like", "%" . $term . "%");
            });
        }

        /*if ($city_country) {
            $ads = $ads->where(function ($query) use ($city_country) {
                $query
                    ->where("ads.city_id", $city_country)
                    ->orWhere("ads.country_id", $city_country);
             });
        }*/

        if ($employer_term) {
            $ads = $ads->where("users.company", "like", "%" . $employer_term. "%");
        }

        if ($position_term) {
            $ads = $ads->where("ads.title", "like", "%" . $position_term . "%");
        }

        $adsCount = $ads->count();

        $ads = $ads->limit($request->get("limit"))
            ->offset($request->get("offset"))
            ->orderBy("ads.created_at", "DESC")
            ->get();

        $favorites = null;
        if ($userId) {
            $favorites = Favorite::where("user_id", $userId)->get();
        }
        if ($favorites) {
            $ads = collect($ads)->map(function ($item) use ($favorites) {

                $item['favorite'] = $favorites->where('ad_id', $item['id'])->first() ? 1 : 0;
                return $item;
            });
        }

        return response()->json(['success' => true, 'ads' => $ads, 'count' => $adsCount], 200);
    }

    // function for getting archiving ads
    public function archiveAds(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        Ad::whereIn("id", $request->ids)->where("user_id", $user->id)->update(["is_archived" => 1, "modified_by" => $user->id]);
        return response()->json(['success' => true, 'message' => "Ads archived"], 200);
    }

    // function for creating ads
    public function createAd(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'end_date' => 'required',
            'type_of_work_id' => 'required|int'
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user || $user->role == "employee") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }


        $input = $request->except(['ads_questions']);

        // document upload
        if ($file = $request->file('document')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('documents/ads/' . $user->id, $name);
            $input['document'] = 'documents/ads/' . $user->id . "/" . $name;
        }

        if ($file = $request->file('video')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('videos/ads/' . $user->id, $name);
            $input['video'] = 'videos/ads/' . $user->id . "/" . $name;
        }

        $input['created_by'] = $user->id;
        $input['user_id'] = $user->id;

        $ad = Ad::create($input);

        if ($request->ads_questions) {

            $ad_questions = json_decode($request->ads_questions);
            foreach ($ad_questions as $question) {
                AdQuestion::create(["ad_id" => $ad->id, "text_question" => $question->text_question, "created_by" => $user->id]);
            }
        }

        self::sendNewAdNotification($ad->type_of_work_id, $ad->id, $user->id);

        return response()->json(['success' => true, 'message' => "Ad added", "add" => $ad->with('questions')], 200);
    }

    // function for sending create ad notification
    public static function sendNewAdNotification($typeOfWorkId, $id, $userId)
    {
        $userIds = TypeOfWorkNotification::where("type_of_work_id", $typeOfWorkId)->pluck("user_id")->toArray();
        $turnOnUserIds = User::whereIn("id", $userIds)->where("turn_notification", 1)->pluck("id")->toArray();
        $data = [];
        foreach ($turnOnUserIds as $senderId) {
            $particularData = ["title" => "Novi oglas", "text" => "Imamo novi oglas za Vas", "created_by" => $userId, "user_id" => $senderId, "type" => "single_ad", "particular_id" => $id, "created_at" => date("Y-m-d H:i:s")];
            array_push($data, $particularData);
        }

        Notification::insert($data);

        return true;
    }

    // function for creating ads
    public function setAd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|int',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user || $user->role == "employee") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $ad = Ad::find($request->id);

        if (!$ad) {
            return response()->json(['success' => false, 'message' => "Ad not found"], 404);
        }

        if ($user->id != $ad->user_id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $ad->update(["is_active" => 1]);
        return response()->json(['success' => true, 'message' => "Ad set"], 200);
    }

    // function for getting adds for companies admin part
    public function getAdminCompaniesAds(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'offset' => 'required|int|min:0',
            'limit' => 'required|int'
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user || $user->role != "admin") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $type_of_work_id = $request->get("type_of_work_id");
        $city_id = $request->get("city_id");
        $is_active = $request->get("is_active");
        $is_archived = $request->get("is_archived");
        $userOwner = $request->user_id;
        $term = $request->term;


        $ads = Ad::with(["country", "city", "type_of_work"])->with(['creator' => function ($query) {
            $query->select('id', 'full_name', 'profile_image');
        }])->where(function ($query) use ($type_of_work_id, $city_id, $is_active, $is_archived, $userOwner) {
            if ($city_id) {
                $query->where('city_id', $city_id);
            }

            if ($type_of_work_id) {
                $query->where('type_of_work_id', $type_of_work_id);
            }

            if ($is_active == 1) {
                $query->where('ads.is_active', "=", 1)->where('ads.is_archived', "=", 0);
            }

            if ($is_active == 2) {
                $query->where('ads.is_active', "=", 0);
            }

            if ($is_archived) {
                $query->where('ads.is_archived', $is_archived);
            }

            if ($userOwner) {
                $query->where('ads.user_id', $userOwner);
            }
        });

        if ($term) {
            $ads = $ads->where(function ($query) use ($term) {
                $query
                    ->where("ads.title", "like", "%" . $term . "%")
                    ->orWhere("ads.location", "like", "%" . $term . "%");
            });
        }

        $adsCount = $ads->count();

        $ads = $ads->limit($request->get("limit"))
            ->offset($request->get("offset"))
            ->orderBy("created_at", "DESC")
            ->get();

        return response()->json(['success' => true, 'ads' => $ads, 'count' => $adsCount], 200);
    }


    // function for getting adds for companies public part
    public function getCompaniesAds(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'offset' => 'required|int|min:0',
            'limit' => 'required|int',
            'company_id' => 'required|int'
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $ads = Ad::with(["country", "city", "type_of_work"])->with(['creator' => function ($query) {
            $query->select('id', 'full_name', 'profile_image');
        }])->where("user_id", $request->company_id)->where("is_active", 1)->where("is_archived", 0)->where("end_date", ">=", date("Y-m-d"));

        $adsCount = $ads->count();

        $ads = $ads->limit($request->get("limit"))
            ->offset($request->get("offset"))
            ->orderBy("created_at", "DESC")
            ->get();

        return response()->json(['success' => true, 'ads' => $ads, 'count' => $adsCount], 200);
    }

    // function for getting ad for all
    public function getAd(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|int',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }


        $ad = Ad::with(["country", "city", "type_of_work", "questions"])
            ->leftJoinSub('select id as publisher_id, full_name, facebook, instagram, linkedin, profile_image, company_description from users', "users", "users.publisher_id", "=", "ads.user_id")
            ->leftJoinSub('select id as video_id, video, user_id from cv_videos', "cv_videos", "cv_videos.user_id", "=", "users.publisher_id")
            ->where("id", $request->id)->first();



        return response()->json(['success' => true, 'ad' => $ad], 200);
    }

    // function for getting ad for logged user
    public function getAuthAd(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|int'
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $favorite = 0;


        $user = auth()->user();
        if ($user) {
            $favorite = Favorite::where("user_id", $user->id)->where("ad_id", $request->id)->count();
        }
        $ad = Ad::with(["country", "city", "type_of_work", "questions"])
            ->leftJoinSub('select id as publisher_id, full_name, profile_image from users', "users", "users.publisher_id", "=", "ads.user_id")
            ->leftJoinSub('select id as video_id, video, user_id from cv_videos', "cv_videos", "cv_videos.user_id", "=", "users.publisher_id")
            ->where("id", $request->id)->first();

        if (!$ad) {
            return response()->json(['success' => false, 'message' => "Ad not found"], 404);
        }

        $ad->favorite = $favorite;

        return response()->json(['success' => true, 'ad' => $ad], 200);
    }

    // function for updating ad
    public function updateAd(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|int'
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user || $user->role == "employee") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $ad = Ad::find($request->id);

        if (!$ad) {
            return response()->json(['success' => false, 'message' => "Ad not found"], 404);
        }

        if ($user->role == "company" && $user->id != $ad->user_id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }


        $input = $request->except(['ads_questions', 'id']);

        // document upload
        if ($file = $request->file('document')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('documents/ads/' . $user->id, $name);
            $input['document'] = 'documents/ads/' . $user->id . "/" . $name;
            unlink(public_path() . "/" . $ad->document);
        }

        if ($file = $request->file('video')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('videos/ads/' . $user->id, $name);
            $input['video'] = 'videos/ads/' . $user->id . "/" . $name;
            unlink(public_path() . "/" . $ad->video);
        }

        $input['modified_by'] = $user->id;

        $ad->update($input);

        if ($request->ads_questions) {

            $ad_questions = json_decode($request->ads_questions);

            AdQuestion::where("ad_id", $ad->id)->delete();
            foreach ($ad_questions as $question) {
                AdQuestion::create(["ad_id" => $ad->id, "text_question" => $question->text_question, "created_by" => $user->id]);
            }
        }

        return response()->json(['success' => true, 'message' => "Ad added", "add" => $ad->with('questions')], 200);
    }

    // function for answering ad question
    public function answerQuestion(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'ad_question_id' => 'required|int'
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $input = $request->all();
        $input["user_id"] = $user->id;
        // video answer
        if ($file = $request->file('video_anwser')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('videos/answers/' . $request->ad_question_id, $name);
            $input['video_anwser'] = 'videos/answers/' . $request->ad_question_id . "/" . $name;
        }

        $input['created_by'] = $user->id;

        $answer = AdAnswer::create($input);

        return response()->json(['success' => true, 'message' => "Ad added", "answer" => $answer], 200);
    }


    // function for answering ad question
    public function removeAnswer(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|int'
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }


        $answer = AdAnswer::find($request->id);

        if (!$answer) {
            return response()->json(['success' => false, 'message' => "Answer not found"], 404);
        }

        if ($user->id != $answer->user_id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        if ($answer->video_anwser) {
            unlink(public_path() . "/" . $answer->video_anwser);
        }

        $answer->delete();

        return response()->json(['success' => true, 'message' => "Answer deleted"], 200);
    }

    /// function for applying to an ad
    public function adApply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ad_id' => 'required|int',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $input = $request->all();
        $input["user_id"] = $user->id;
        $input["created_by"] = $user->id;


        $input["applied"] = 1;

        $alreadyApply = AdSharedInfo::where("user_id", $user->id)->where("ad_id", $request->ad_id);

        if ($alreadyApply->first()) {
            $alreadyApplyModel =  $alreadyApply->first();
            if ($alreadyApplyModel->applied == 1) {
                return response()->json(['success' => false, 'message' => "User already applied on ad", 'send' => 0], 200);
            } else {
                $alreadyApply->update(["applied" => 1]);
                return response()->json(['success' => true, 'message' => "User successfully applied", 'send' => 1], 200);
            }
        }

        AdSharedInfo::create($input);
        return response()->json(['success' => true, 'message' => "User successfully applied", 'send' => 1], 200);
    }

    // function for saving an ad
    public function adSavedApply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ad_id' => 'required|int',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $input = $request->all();
        $input["user_id"] = $user->id;
        $input["created_by"] = $user->id;
        $input["applied"] = 0;

        $alreadyApply = AdSharedInfo::where("user_id", $user->id)->where("ad_id", $request->ad_id)->first();

        if ($alreadyApply) {
            return response()->json(['success' => false, 'message' => "User already applied on ad", 'send' => 0], 200);
        }

        AdSharedInfo::create($input);
        return response()->json(['success' => true, 'message' => "User successfully applied", 'send' => 1], 200);
    }

    // function for applying to an ad
    public function viewApplicant(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|int',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $apply =  AdSharedInfo::where('id', $request->id)
            ->leftJoinSub('select id as idA, user_id as publisher_id , title, description, city_id, country_id, position from ads', "ads", "ads.idA", "=", "ad_shared_infos.ad_id")->first();

        if (!$apply) {
            return response()->json(['success' => false, 'message' => "Application not found"], 404);
        }

        if ($apply->publisher_id != $user->id && $user->role = "company" || $apply->user_id != $user->id && $user->role = "employee") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $questionsModel =  AdQuestion::where("ad_id", $apply->ad_id);

        $questions = $questionsModel->get();

        $questionIds = $questionsModel->pluck("id")->toArray();

        $answers = AdAnswer::where("user_id", $apply->user_id)->whereIn("ad_question_id", $questionIds)->leftJoinSub('select id as idQ, text_question , video_question from ad_questions', "ad_questions", "ad_questions.idQ", "=", "ad_answers.ad_question_id")->get();

        $user = $user = User::with('city')->with('country')->with('gender')->with('additional_information')->with('work_experiences')->with('documents')->with('videos')->with('computer_skills')->with('driver_licences')->with('educations')->with('languages')->where("id", $apply->user_id)->first();

        return response()->json(['success' => true, 'apply' => $apply, 'user' => $user, 'answer' => $answers, 'questions' => $questions], 200);
    }

    // function for archiving ads
    public function toggleActiveAd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $ad = Ad::find($request->id);

        if (!$ad) {
            return response()->json(['success' => false, 'message' => "Ad not found"], 404);
        }

        if ($user->id != $ad->user_id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $is_active = 0;

        if ($ad->is_active == 0) {
            $is_active = 1;
        }

        $ad->update(["is_active" => $is_active]);

        return response()->json(['success' => true, 'message' => "Ad archived", "ad" => $ad], 200);
    }

    // function for getting adeleting ads
    public function deleteAds(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        Ad::whereIn("id", $request->ids)->where("user_id", $user->id)->delete();
        return response()->json(['success' => true, 'message' => "Ads deleted"], 200);
    }

    // function for getting adeleting ads
    public function deleteAd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        if ($user->role == "admin") {
            Ad::find($request->id)->delete();
            return response()->json(['success' => true, 'message' => "Ad deleted"], 200);
        }
        $ad = Ad::find($request->id);
        if ($ad->user_id != $user->id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }
        Ad::find($request->id)->delete();
        return response()->json(['success' => true, 'message' => "Ad deleted"], 200);
    }

    // function for getting applicant infos
    public function viewApplicants(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'offset' => 'required|int|min:0',
            'limit' => 'required|int',
            'ad_id' => 'required|int',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $ad = Ad::find($request->ad_id);

        if (!$ad) {
            return response()->json(['success' => false, 'message' => "Ad not found"], 404);
        }

        $user = auth()->user();
        if (!$user || $user->role == "employee" || $user->role == "company" && $user->id != $ad->user_id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $selected = $request->selected;
        $city_id = $request->city_id;
        $gender_id = $request->gender_id;
        $work_experience = $request->work_experience;
        $education_level = $request->education_level;
        $birth_from = $request->birth_from;
        $birth_to = $request->birth_to;
        $term = $request->term;

        $applications = AdSharedInfo::with(['user' => function ($q) {
            $q->with('city')->with('country')->with('gender')->with('work_experiences')->with('documents')->with('videos')->with('computer_skills')->with('work_experiences')->with('educations')->leftJoinSub('select id as education_id , name as education_name from education_levels', "education_levels", "users.education_level", "=", "education_levels.education_id");
        }])->where("ad_id", "=", $request->ad_id)->joinSub('select id as idUser, full_name as applicant_name, email as applicant_email, phone as applicant_phone, city_id as cityIdUser, gender_id as genderIdUser, birth_year from users', "users", "users.idUser", "=", "ad_shared_infos.user_id")->leftJoinSub('select id as idEducation, user_id as userIdEducation, education_level_id from education', "education", "users.idUser", "=", "education.userIdEducation")->where("applied", 1)->where(function ($query) use ($selected, $city_id, $gender_id, $education_level, $birth_from, $birth_to) {
            if ($selected) {
                $query->where('selected', 1);
            }
            if ($city_id) {
                $query->where('users.cityIdUser', $city_id);
            }
            if ($gender_id) {
                $query->where('users.genderIdUser', $gender_id);
            }
            if ($education_level) {
                $query->where('education.education_level_id', $education_level);
            }
            if ($birth_from) {
                $query->where('users.birth_year', '>', $birth_from);
            }
            if ($birth_to) {
                $query->where('users.birth_year', '<', $birth_to);
            }
        });

        if ($term) {
            $applications = $applications->where(function ($query) use ($term) {
                $query
                    ->where("users.applicant_name", "like", "%" . $term . "%")
                    ->orWhere("users.applicant_email", "like", "%" . $term . "%")
                    ->orWhere("users.applicant_phone", "like", "%" . $term . "%");
            });
        }

        if ($work_experience) {
            $applications = $applications->joinSub('select id as idWorkExperience, user_id as userIdWorkExperience from work_experiences', "work_experiences", "users.idUser", "=", "work_experiences.userIdWorkExperience");
        }

        $total = $applications->count();

        $applications = $applications->limit($request->get("limit"))->offset($request->get("offset"))
            ->orderBy("created_at", "DESC")
            ->get();

        return response()->json(['success' => true, 'applicants' => $applications, "total" => $total], 200);
    }


    // function for getting selected applicant infos
    public function viewSelectedApplicants(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'offset' => 'required|int|min:0',
            'limit' => 'required|int'
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }



        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $adIds = Ad::where("user_id", $user->id)->pluck("id")->toArray();

        $selected = $request->selected;
        $city_id = $request->city_id;
        $gender_id = $request->gender_id;
        $work_experience = $request->work_experience;
        $education_level = $request->education_level;
        $birth_from = $request->birth_from;
        $birth_to = $request->birth_to;
        $term = $request->term;

        $applications = AdSharedInfo::with(['user' => function ($q) {
            $q->with('city')->with('country')->with('gender')->with('work_experiences')->with('documents')->with('videos')->with('computer_skills')->with('work_experiences')->with('educations')->leftJoinSub('select id as education_id , name as education_name from education_levels', "education_levels", "users.education_level", "=", "education_levels.education_id");
        }])->joinSub('select id as idUser, full_name as applicant_name, email as applicant_email, phone as applicant_phone, city_id as cityIdUser, gender_id as genderIdUser, birth_year from users', "users", "users.idUser", "=", "ad_shared_infos.user_id")->leftJoinSub('select id as idEducation, user_id as userIdEducation, education_level_id from education', "education", "users.idUser", "=", "education.userIdEducation")->whereIn("ad_id", $adIds)->where("selected", 1)->where(function ($query) use ($city_id, $gender_id, $education_level, $birth_from, $birth_to) {
            if ($city_id) {
                $query->where('users.cityIdUser', $city_id);
            }
            if ($gender_id) {
                $query->where('users.genderIdUser', $gender_id);
            }
            if ($education_level) {
                $query->where('education.education_level_id', $education_level);
            }
            if ($birth_from) {
                $query->where('users.birth_year', '>', $birth_from);
            }
            if ($birth_to) {
                $query->where('users.birth_year', '<', $birth_to);
            }
        });

        if ($term) {
            $applications = $applications->where(function ($query) use ($term) {
                $query
                    ->where("users.applicant_name", "like", "%" . $term . "%")
                    ->orWhere("users.applicant_email", "like", "%" . $term . "%")
                    ->orWhere("users.applicant_phone", "like", "%" . $term . "%");
            });
        }

        if ($work_experience) {
            $applications = $applications->joinSub('select id as idWorkExperience, user_id as userIdWorkExperience from work_experiences', "work_experiences", "users.idUser", "=", "work_experiences.userIdWorkExperience");
        }

        $total = $applications->count();

        $applications = $applications->limit($request->get("limit"))->offset($request->get("offset"))
            ->orderBy("created_at", "DESC")
            ->get();

        return response()->json(['success' => true, 'applicants' => $applications, "total" => $total], 200);
    }


    // za ubacivanje više kandidata u izdvojene
    public function addSelected(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'application_ids' => 'required',
            'ad_id' => 'required|int',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }


        $ad = Ad::find($request->ad_id);

        if (!$ad) {
            return response()->json(['success' => false, 'message' => "Ad not found"], 404);
        }


        $user = auth()->user();
        if (!$user || $user->role == "employee" || $user->role == "company" && $user->id != $ad->user_id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $application = AdSharedInfo::whereIn("id", $request->application_ids)->update(["selected" => 1]);

        return response()->json(['success' => true, 'message' => "Users selected"], 200);
    }

    // za uklanjanje više kandidata u izdvojene
    public function removeSelected(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'application_ids' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user || $user->role == "employee") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $application = AdSharedInfo::whereIn("id", $request->application_ids)->update(["selected" => 0]);

        return response()->json(['success' => true, 'message' => "Users selected"], 200);
    }

    // function for resetting ads
    public function resetAd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $ad = Ad::find($request->id);

        if (!$ad) {
            return response()->json(['success' => false, 'message' => "Ad not found"], 404);
        }

        if ($user->id != $ad->user_id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }


        $ad->update(["is_active" => 0, "is_archived" => 0]);

        return response()->json(['success' => true, 'message' => "Ad resetted", "ad" => $ad], 200);
    }

    // function for getting keywords ads
    public function keywordAutoComplete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'keyword' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $employerAds = User::select("id", "full_name as title")->withCount("activeAds")->where("is_active", 1)->where("is_archived", 0)->where("full_name", "like", "%" . $request->keyword . "%")->get();
        $positionAds = Ad::select("id", "title")->where("is_active", 1)->where("is_archived", 0)->where("title", "like", "%" . $request->keyword . "%")->get();
    
        return response()->json(['success' => true, 'employerAds' => $employerAds, 'positionAds' => $positionAds], 200);
    }
}
