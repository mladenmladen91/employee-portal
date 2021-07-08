<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use App\Models\Ad;
use App\Models\Notification;
use App\Models\AdSharedInfo;
use App\Models\Education;
use App\Models\WorkExperience;
use App\Models\ForeignLanguage;
use App\Models\ComputerSkill;
use App\Models\UserDocument;
use App\Models\CvVideo;
use App\Models\Gender;
use App\Models\DriversLicence;
use App\Models\AdditionalInformation;
use App\Models\EducationLevel;
use App\Models\EducationArea;
use App\Models\EducationTitle;
use App\Models\JobCategory;
use App\Models\Language;
use App\Models\LanguageSpeak;
use App\Models\LanguageRead;
use App\Models\LanguageWrite;
use App\Models\ComputerSkillName;
use App\Models\ComputerSkillKnowledgeLevel;
use App\Models\DriversLicenceCategory;
use App\Models\CityNotification;
use App\Models\TypeOfWorkNotification;
use App\Models\JobType;
use App\Models\WorkTime;
use App\Models\TypeOfWork;
use App\Models\AdQuestion;
use App\Models\AdAnswer;
use Auth;
use DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class UserController extends Controller
{
    /// getting basic user Info
    public function getInfo()
    {
        $user = User::with('city')->with('country')->with('gender')->with('work_experiences')->with(['city_notifications' => function ($q) {
            $q->join('cities', 'cities.id', '=', 'city_notifications.city_id');
        }])->with(['type_of_work_notifications' => function ($q) {
            $q->join('type_of_works', 'type_of_works.id', '=', 'type_of_work_notifications.type_of_work_id');
        }])->with('company_users')->with('company_activities')->with('packages')->with('notifications')->with('documents')->with('videos')->with('messages')->with('computer_skills')->with('work_experiences')->with('additional_information')->with('driver_licences')->with('educations')->with('uiLanguage')->with('languages')->where("id", Auth::id())->leftJoinSub('select id as education_id , name as education_name from education_levels', "education_levels", "users.education_level", "=", "education_levels.education_id")->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        return response()->json(['success' => true, 'user' => $user], 200);
    }

    // function for updating client
    public function updateUser(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $client = User::find($user->id);

        if (!$client) {
            return response()->json(['success' => false, 'message' => 'Client not found'], 404);
        }
        $input = $request->all();
        $input["modified_by"] = $user->id;
        if ($request->password) {
            $input["password"] = bcrypt($request->password);
        }
        $client->update($input);
        return response()->json(['success' => true, 'message' => 'Client updated'], 200);
    }

    // function for deactivating user
    public function deactivate()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => true, 'message' => "User unauthorized"], 401);
        }

        User::find(Auth::id())->update(["is_active" => 0, "modified_by" => $user->id]);
        return response()->json(['success' => true, 'message' => "User deactivated"], 200);
    }

    // function for activating user
    public function activate()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        User::find(Auth::id())->update(["is_active" => 1, "modified_by" => $user->id]);
        return response()->json(['success' => false, 'message' => "User activated"], 200);
    }

    // function for updating profile image
    public function profileImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "image" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $userAuth = auth()->user();
        if (!$userAuth) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $user = User::find(Auth::id());

        $input = $request->all();
        if ($file = $request->file('image')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('images/profile/' . $user->id, $name);
            if ($user->profile_image) {
                unlink(public_path() . '/' . $user->profile_image);
            }
            $input['profile_image'] = 'images/profile/' . $user->id . "/" . $name;
        }
        $input["modified_by"] = $user->id;
        $user->update($input);
        return response()->json(['success' => true, 'user' => $user, "message" => "Profile image successfully updated"], 200);
    }


    // function for updating background image
    public function backgroundImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "image" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $userAuth = auth()->user();
        if (!$userAuth) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $user = User::find(Auth::id());

        $input = $request->all();
        if ($file = $request->file('image')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('images/background/' . $user->id, $name);
            if ($user->background_image) {
                unlink(public_path() . '/' . $user->background_image);
            }
            $input['background_image'] = 'images/background/' . $user->id . "/" . $name;
        }
        $input["modified_by"] = $user->id;
        $user->update($input);
        return response()->json(['success' => true, 'user' => $user, "message" => "Background image successfully updated"], 200);
    }

    // function for updating profile video
    public function profileVideo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "video" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $userAuth = auth()->user();
        if (!$userAuth) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $user = User::find(Auth::id());

        $video = CvVideo::where("user_id", $user->id);

        $input = $request->all();

        if ($video->first()) {
            $videoModel = $video->first();
            if ($file = $request->file('video')) {
                $name = time() . $file->getClientOriginalName();
                $file->move('videos/' . $user->id, $name);
                if ($videoModel->video) {
                    unlink(public_path() . '/' . $videoModel->video);
                }
                $input['video'] = 'videos/' . $user->id . "/" . $name;
            }
            $input["modified_by"] = $user->id;
            $input["description"] = "test";
            $video->update($input);
            $videoModel2 = $video->first();
        } else {
            if ($file = $request->file('video')) {
                $name = time() . $file->getClientOriginalName();
                $file->move('videos/' . $user->id, $name);
                $input['video'] = 'videos/' . $user->id . "/" . $name;
            }
            $input["created_by"] = $user->id;
            $input["user_id"] = $user->id;
            $input["description"] = "test";
            $videoModel2 = CvVideo::create($input);
        }

        return response()->json(['success' => true, 'user' => $user, "message" => "Profile image successfully updated", "video" => $videoModel2], 200);
    }


    // function for ugetting all genders
    public function getGenders(Request $request)
    {
        $genders = Gender::all();
        return response()->json(['success' => true, 'genders' => $genders], 200);
    }

    // function for ugetting all genders
    public function getEducationLevels(Request $request)
    {
        $levels = EducationLevel::all();
        return response()->json(['success' => true, 'education_levels' => $levels], 200);
    }


    // function for ugetting all genders
    public function getAllAddings(Request $request)
    {
        $educationLevels = EducationLevel::all();
        $educationAreas = EducationArea::all();
        $educationTitles = EducationTitle::all();
        $jobCategories = JobCategory::all();
        $languages = Language::all();
        $languagesSpeak = LanguageSpeak::all();
        $languagesRead = LanguageRead::all();
        $languagesWrite = LanguageWrite::all();
        $computerSkillsNames = ComputerSkillName::all();
        $computerSkillsLevels = ComputerSkillKnowledgeLevel::all();
        $driversLicenceCategories = DriversLicenceCategory::all();
        $jobTypes = JobType::all();
        $type_of_works = TypeOfWork::all();
        $workTimes = WorkTime::all();
        return response()->json(['success' => true, 'educationLevels' => $educationLevels, "jobCategories" => $jobCategories, "educationAreas" => $educationAreas, "educationTitles" => $educationTitles, "languages" => $languages, "languagesSpeak" => $languagesSpeak, "languagesRead" => $languagesRead, "languagesWrite" => $languagesWrite, "computerSkillsNames" => $computerSkillsNames, "computerSkillsLevels" => $computerSkillsLevels, "driversLicenceCategories" => $driversLicenceCategories, "jobTypes" => $jobTypes, "workTimes" => $workTimes, "type_of_works" => $type_of_works], 200);
    }


    // function for getting user list and for candidates list
    public function getUsers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'offset' => 'required|int|min:0',
            'limit' => 'required|int',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user || $user->role != "admin") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $years = $request->years;
        $city_id = $request->city_id;
        $gender_id = $request->gender_id;
        $work_experience = $request->work_experience;
        $education_level_id = $request->education_level_id;
        $role = $request->role;
        $term = $request->term;
        $candidate = $request->candidate;
        $archived = $request->archived;
        $active = $request->active;
        $company_activity = $request->company_activity;

        $users = User::with(["country", "city", "documents", "videos", "gender"])->with(['educations' => function ($q) {
            $q->join('education_titles', 'education_titles.id', '=', 'education.education_title_id');
        }])->where(function ($query) use ($years, $city_id, $gender_id, $term, $role, $archived, $company_activity, $active) {
            if ($city_id) {
                $query->where('city_id', $city_id);
            }

            if ($gender_id) {
                $query->where('gender_id', $gender_id);
            }

            if ($years && sizeof($years) > 0) {
                $query->whereBetween('birth_year', $years);
            }

            if ($role) {
                $query->where('role', "=", $role);
            }

            if ($company_activity) {
                $query->where('company_activity', "=", $company_activity);
            }

            if ($archived) {
                $query->where("is_archived", $archived);
            }

            if ($active) {
                $query->where("is_active", $active);
            }


            if ($term) {
                $query->where(function ($query) use ($term) {
                    $query
                        ->where("full_name", "like", "%" . $term . "%")
                        ->orWhere("email", "like", "%" . $term . "%")
                        ->orWhere("address", "like", "%" . $term . "%")
                        ->orWhere("phone", "like", "%" . $term . "%");
                });
            }
        });

        if ($work_experience == 2) {
            $users = $users->has('work_experiences');
        }

        if ($work_experience == 1) {
            $users = $users->has('work_experiences', "=", 0);
        }

        if ($education_level_id) {
            $users = $users->with(['educations' => function ($query) use ($education_level_id) {
                $query->where('education_level_id', $education_level_id);
            }]);
        }

        if ($candidate) {
            $users = $users->has('applications');
        }

        $count = $users->count();

        $users = $users->limit($request->get("limit"))
            ->offset($request->get("offset"))
            ->orderBy("created_at", "DESC")
            ->get();

        return response()->json(['success' => true, 'users' => $users, 'count' => $count], 200);
    }

    // function for archiving users
    public function archiveUsers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user || $user->role != "admin") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        User::whereIn("id", $request->ids)->update(["is_archived" => 1, "modified_by" => $user->id]);
        return response()->json(['success' => true, 'message' => "Users archived"], 200);
    }

    // function for deleting users
    public function deleteUsers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user || $user->role != "admin") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }
        $users = User::whereIn("id", $request->ids);
        $users->update(["modified_by" => $user->id]);
        $users->delete();
        return response()->json(['success' => true, 'message' => "Users deleted"], 200);
    }

    // function for deleting users
    public function resetUsers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user || $user->role != "admin") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }
        $users = User::whereIn("id", $request->ids);
        $users->update(["is_active" => 1, "is_archived" => 0]);
        return response()->json(['success' => true, 'message' => "Users deleted"], 200);
    }

    // function for toggling active users
    public function toggleUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user || $user->role != "admin") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }
        $user = User::find("id", $request->ids);
        $is_active = 0;
        if ($user->is_active == 0) {
            $is_active = 1;
        }
        $user->update(["is_active" => $is_active]);
        return response()->json(['success' => true, 'message' => "Users updated"], 200);
    }

    // function for getting creating users
    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'role' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:5',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $userAuth = auth()->user();
        if (!$userAuth || $userAuth->role != "admin") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'role' => $request->role,
            'is_active' => 0,
            'password' => bcrypt($request->password)
        ]);

        if (!$user) {
            return response()->json(['success' => false, "message" => "Error"], 500);
        }

        return response()->json(['success' => true, "message" => "User successfully created"], 200);
    }

    // function for creating message
    public function createMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'text' => 'required',
            'sender_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }



        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $message = Message::create([
            'title' => $request->title,
            'text' => $request->text,
            'user_id' => $request->sender_id,
            'created_by' => $user->id,
        ]);

        if (!$message) {
            return response()->json(['success' => false, "message" => "Error"], 500);
        }

        $sender = User::where("id", $message->created_by)->select("profile_image")->first();

        return response()->json(['success' => true, "message" => $message, "sender" => $sender], 200);
    }

    // send messages to many receivers
    public function sendMessages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'text' => 'required',
            'sender_ids' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }



        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $senderIds = $request->sender_ids;

        $data = [];

        foreach ($senderIds as $senderId) {
            $particularData = ["title" => $request->title, "text" => $request->text, "created_by" => $user->id, "user_id" => $senderId, "created_at" => date("Y-m-d H:i:s")];
            array_push($data, $particularData);
        }

        Message::insert($data);


        return response()->json(['success' => true, "message" => "Messaged sent"], 200);
    }

    // function for updating message
    public function updateParticularMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => "required|integer"
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();

        if ($user->id != $request->user_id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $input = $request->except(['id', 'user_id']);
        $input["modified_by"] = $user->id;
        $message = Message::where('id', $request->id);
        $message->update($input);
        return response()->json(['success' => true, 'message' => "Message updated", "message" => $message->first()], 200);
    }

    // function for creating notification
    public function createNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'text' => 'required',
            'sender_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }


        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $notification = Notification::create([
            'title' => $request->title,
            'text' => $request->text,
            'user_id' => $request->sender_id,
            'created_by' => $request->created_by,
        ]);

        if (!$notification) {
            return response()->json(['success' => false, "message" => "Error"], 500);
        }

        return response()->json(['success' => true, "message" => "Notification successfully created"], 200);
    }

    // function for updating notification
    public function updateParticularNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => "required|integer"
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();

        if ($user->id != $request->user_id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $input = $request->except(['id', 'user_id']);
        $input["modified_by"] = $user->id;
        $notification = Notification::where('id', $request->id);
        $notification->update($input);
        return response()->json(['success' => true, 'message' => "Notification updated", "message" => $notification->first()], 200);
    }

    // function for getting user info
    public function getUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $userAuth = auth()->user();
        if (!$userAuth || $userAuth->role == "employee") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }
        $user = User::with(['city', 'country', 'gender', 'videos', 'messages', 'additional_information', 'computer_skills', 'work_experiences', 'driver_licences', 'educations', 'languages', 'documents', 'company_activities', 'company_users'])->find($request->id);
        return response()->json(['success' => true, 'user' => $user], 200);
    }

    // function for updating user info from admin for companies and users
    public function updateAdminUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $userAuth = auth()->user();
        if (!$userAuth || $userAuth->role != "admin") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }
        $input = $request->except(["id"]);
        $input["modified_by"] = $userAuth->id;
        $user = User::find($request->id);
        $user->update($input);
        return response()->json(['success' => true, 'user' => $user], 200);
    }

    // function for getting dashboard info
    public function getAdminDashboard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user || $user->role != "admin") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $ads = Ad::select(DB::raw('count(id) as `data`'), DB::raw('MONTH(created_at) as month'))->whereYear('created_at', $request->year);

        $ads = $ads->groupBy('month')->orderBy("created_at", "ASC")
            ->get();

        $candidatesCount =  User::where("role", "employee")->count();
        $companiesCount =  User::where("role", "company")->count();
        $adsCount =  Ad::count();
        $topFiveCompanies = User::with(["country", "city", "company_activities"])->withCount("ads")->where("role", "company")->orderBy("ads_count", "desc")->limit(5)->get();

        $topFiveCompanies = collect($topFiveCompanies)->map(function ($item) {
            $ads = Ad::where("user_id", $item['id'])->withCount("shared_adds")->pluck("shared_adds_count")->toArray();
            $item['apply_count'] = array_sum($ads);
            return $item;
        });

        $newFiveCompanies = User::where("role", "company")->orderBy("created_at", "desc")->limit(5)->get();
        $adsApplied = AdSharedInfo::all()->groupBy("user_id")->count();


        return response()->json(['success' => true, 'ads' => $ads, 'candidatesCount' => $candidatesCount, 'companiesCount' => $companiesCount, 'adsCount' => $adsCount, 'adsAppliedCount' => $adsApplied, 'topFiveCompanies' => $topFiveCompanies, "newCompanies" => $newFiveCompanies], 200);
    }


    // function for updating particular education
    public function updateAdminParticularEducation(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "id" => "required|integer"
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }
        $user = auth()->user();
        if (!$user || $user->role != "admin") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }


        $input = $request->except(['id', 'user_id']);
        $input["modified_by"] = $user->id;
        $education = Education::where('id', $request->id);
        $education->update($input);
        return response()->json(['success' => true, 'message' => "Education updated", "education" => $education->first()], 200);
    }

    // function for updating admin particular experience
    public function updateAdminParticularExperience(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => "required|integer"
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }
        $user = auth()->user();
        if (!$user || $user->role != "admin") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }


        $input = $request->except(['id', 'user_id']);
        $input["modified_by"] = $user->id;
        $experience = WorkExperience::where('id', $request->id);
        $experience->update($input);
        return response()->json(['success' => true, 'message' => "Education updated", "experience" => $experience->first()], 200);
    }

    // function for adding particular experience
    public function addAdminExperience(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "company_name" => "required",
            "job_category_id" => "required|integer",
            "location" => "required",
            "user_id" => "required",
            "position" => "required",
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }
        $user = auth()->user();
        if (!$user || $user->role != "admin") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }


        $input = $request->all();
        $input["created_by"] = $user->id;
        $experience = WorkExperience::create($input);
        return response()->json(['success' => true, 'message' => "Education created"], 200);
    }

    // function for removing experience
    public function removeAdminExperience(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => "required",
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user || $user->role != "admin") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }
        $experience = WorkExperience::find($request->id);


        $experience->delete();
        return response()->json(['success' => true, 'message' => "Education deleted"], 200);
    }

    // function for updating foreign languages
    public function updateAdminForeignLanguages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "foreign_languages" => "required",
            "user_id" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }
        $user = auth()->user();
        if (!$user || $user->role != "admin") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $foreignLanguages = json_decode($request->get("foreign_languages"));

        ForeignLanguage::where("user_id", $request->user_id)->delete();

        foreach ($foreignLanguages as $language) {
            ForeignLanguage::create(["user_id" => $request->user_id, "languages_id" => $language->languages_id, "language_reads_id" => $language->language_reads_id, "language_writes_id" => $language->language_writes_id, "language_speaks_id" => $language->language_speaks_id, "created_by" => $user->id, "modified_by" => $user->id]);
        }

        return response()->json(['success' => true, 'message' => "Foreign languages updated"], 200);
    }

    // function for removing languages
    public function removeAdminLanguage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => "required",
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user || $user->role != "admin") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }
        $language = ForeignLanguage::find($request->id);

        $language->delete();
        return response()->json(['success' => true, 'message' => "Foreign language deleted"], 200);
    }

    // function for updating computer skills
    public function updateAdminComputerSkills(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "computer_skills" => "required",
            "user_id" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }
        $user = auth()->user();
        if (!$user || $user->role != "admin") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $computerSkills = json_decode($request->get("computer_skills"));

        ComputerSkill::where("user_id", $request->user_id)->delete();

        foreach ($computerSkills as $skill) {
            ComputerSkill::create(["user_id" => $request->user_id, "computer_skill_name_id" => $skill->computer_skill_name_id, "computer_skill_knowledge_level_id" => $skill->computer_skill_knowledge_level_id, "created_by" => $user->id, "modified_by" => $user->id]);
        }

        return response()->json(['success' => true, 'message' => "Computer skills updated"], 200);
    }

    // function for adding particular computer skill
    public function addAdminComputerSkill(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "computer_skill_name_id" => "required|integer",
            "computer_skill_knowledge_level_id" => "required|integer",
            "user_id" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }
        $user = auth()->user();
        if (!$user || $user->role != "admin") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }


        $input = $request->all();
        $input["user_id"] = $request->user_id;
        $input["created_by"] = $user->id;
        $skill = ComputerSkill::create($input);
        return response()->json(['success' => true, 'message' => "Computer skill created"], 200);
    }

    // function for updating cv
    public function updateAdminCvDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "cv" => "required",
            "user_id" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user || $user->role != "admin") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $document = UserDocument::findOrCreate(["user_id" => $request->user_id]);

        $input = $request->except(["user_id"]);
        if ($file = $request->file('cv')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('documents/cv/' . $request->user_id, $name);
            if ($document->document_link) {
                unlink(public_path() . "/" . $document->document_link);
            }
            $input['document_name'] = $name;
            $input['document_link'] = 'documents/cv/' . $request->user_id . "/" . $name;
        }

        $input["modified_by"] = $user->id;
        $document->update($input);
        return response()->json(['success' => true, 'user' => $user, "message" => "Document successfully updated"], 200);
    }

    // function for removing cv document
    public function removeAdminDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => "required",
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user || $user->role != "admin") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }
        $document = UserDocument::find($request->id);

        unlink(public_path() . "/" . $document->document_link);
        $document->delete();
        return response()->json(['success' => true, 'message' => "Document deleted"], 200);
    }

    // function for updating video
    public function updateAdminVideo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "video" => "required",
            "user_id" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user || $user->role != "admin") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $user = User::find($request->user_id);

        $video = CvVideo::where("user_id", $user->id);

        $input = $request->all();

        if ($video->first()) {
            $videoModel = $video->first();
            if ($file = $request->file('video')) {
                $name = time() . $file->getClientOriginalName();
                $file->move('videos/' . $user->id, $name);
                if ($videoModel->video) {
                    unlink(public_path() . '/' . $videoModel->video);
                }
                $input['video'] = 'videos/' . $user->id . "/" . $name;
            }
            $input["modified_by"] = $user->id;
            $input["description"] = "test";
            $video->update($input);
            $videoModel2 = $video->first();
        } else {
            if ($file = $request->file('video')) {
                $name = time() . $file->getClientOriginalName();
                $file->move('videos/' . $user->id, $name);
                $input['video'] = 'videos/' . $user->id . "/" . $name;
            }
            $input["created_by"] = $user->id;
            $input["user_id"] = $user->id;
            $input["description"] = "test";
            $videoModel2 = CvVideo::create($input);
        }
        return response()->json(['success' => true, 'user' => $user, "message" => "Video successfully created", "video" => $videoModel2], 200);
    }

    // function for removing video
    public function removeAdminVideo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => "required",
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user || $user->role != "admin") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }
        $video = CvVideo::find($request->id);

        unlink(public_path() . "/" . $video->video);
        $video->delete();
        return response()->json(['success' => true, 'message' => "Video deleted"], 200);
    }

    // function for updating drive licence
    public function updateAdminDriverLicence(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "drivers_licence_category_id" => "required|integer",
            "user_id" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }
        $licence = DriversLicence::findOrCreate(["user_id" => $request->user_id]);

        $input = $request->except(["user_id"]);
        $input["created_by"] = $user->id;
        $licence->update($input);
        return response()->json(['success' => true, 'message' => "Licence created"], 200);
    }

    // function for updating additional information
    public function updateAdminAdditionalInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "text" => "required|integer",
            "user_id" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }
        $user = auth()->user();
        if (!$user || $user->role != "admin") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }
        $info = AdditionalInformation::findOrCreate(["user_id" => $request->user_id]);

        $input = $request->except(["user_id"]);
        $input["modified_by"] = $user->id;
        $info->update($input);
        return response()->json(['success' => true, 'message' => "Information updated"], 200);
    }

    // function for updating profile image
    public function profileAdminImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "image" => "required",
            "user_id" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $userAuth = auth()->user();
        if (!$userAuth || $userAuth->role != "admin") {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $user = User::find($request->user_id);

        $input = $request->all();
        if ($file = $request->file('image')) {
            $name = time() . $file->getClientOriginalName();

            $file->move('images/profile/' . $user->id, $name);
            if ($user->profile_image) {
                unlink(public_path() . '/' . $user->profile_image);
            }
            $input['profile_image'] = 'images/profile/' . $user->id . "/" . $name;
        }
        $input["modified_by"] = $userAuth->id;
        $user->update($input);
        return response()->json(['success' => true, 'user' => $user, "message" => "Profile image successfully updated"], 200);
    }

    // function for getting all messages
    public function getMessages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'offset' => 'required|int|min:0',
            'limit' => 'required|int',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $term = $request->term;

        $messages = Message::where("user_id", $user->id)->leftJoinSub('select id as sender_id, full_name, profile_image from users', "users", "users.sender_id", "=", "messages.created_by");

        if ($term) {
            $messages = $messages->where("users.full_name", "LIKE", "%" . $term . "%");
        }

        $messages = $messages->groupBy("created_by");

        $messagesCount = $messages->count();

        $messages = $messages->limit($request->get("limit"))
            ->offset($request->get("offset"))
            ->orderBy("created_at", "DESC")
            ->get();

        return response()->json(['success' => true, 'messages' => $messages, "count" => $messagesCount], 200);
    }

    // function for getting single message
    public function getSingleMessage(Request $request)
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

        $message = Message::where("id", $request->id)->leftJoinSub('select id as owner_id , full_name from users', "users", "users.owner_id", "=", "messages.created_by")->first();

        if (!$message) {
            return response()->json(['success' => false, 'message' => "Message not found"], 404);
        }

        if ($user->id != $message->user_id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        return response()->json(['success' => true, 'message' => $message], 200);
    }

    // function for getting all notifications
    public function getNotifications(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'offset' => 'required|int|min:0',
            'limit' => 'required|int',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $messages = Notification::where("user_id", $user->id);

        $messagesCount = $messages->count();

        $messages = $messages->limit($request->get("limit"))
            ->offset($request->get("offset"))
            ->orderBy("created_at", "DESC")
            ->get();

        return response()->json(['success' => true, 'notifications' => $messages, "count" => $messagesCount], 200);
    }

    // function for getting single notification
    public function getSingleNotification(Request $request)
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

        $message = Notification::where("id", $request->id)->leftJoinSub('select id as owner_id , full_name from users', "users", "users.owner_id", "=", "notifications.created_by")->first();

        if ($user->id != $message->user_id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        return response()->json(['success' => true, 'notification' => $message], 200);
    }

    // function for viewing message
    public function viewMessage(Request $request)
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

        $message = Message::find($request->id);

        if (!$message) {
            return response()->json(['success' => false, 'message' => "Message not found"], 404);
        }

        if ($user->id != $message->user_id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $message->update(["seen" => 1]);

        return response()->json(['success' => true, 'message' => "View updated"], 200);
    }
    // function for viewing notification
    public function viewNotification(Request $request)
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

        $message = Notification::find($request->id);

        if (!$message) {
            return response()->json(['success' => false, 'message' => "Notification not found"], 404);
        }

        if ($user->id != $message->user_id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $message->update(["seen" => 1]);

        return response()->json(['success' => true, 'message' => "View updated"], 200);
    }

    // function for updating notifications on setting page
    public function updateSettingNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notifications' => 'required|int'
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $userAuth = auth()->user();
        if (!$userAuth) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $user = User::find($userAuth->id);

        $notification = 1;

        if ($request->notifications == 2) {
            $notification = 0;
        }

        $user->update(["turn_notification" => $notification]);

        $city_notifications = $request->city_notifications;
        $type_of_work_notifications = $request->type_of_work_notifications;

        if ($city_notifications) {
            $city_notifications = json_decode($request->get("city_notifications"));
            CityNotification::where("user_id", $user->id)->delete();
            foreach ($city_notifications as $notification) {
                CityNotification::create(["user_id" => $user->id, "city_id" => $notification->id]);
            }
        }

        if ($type_of_work_notifications) {
            $type_of_work_notifications = json_decode($request->get("type_of_work_notifications"));
            TypeOfWorkNotification::where("user_id", $user->id)->delete();
            foreach ($type_of_work_notifications as $notification) {
                TypeOfWorkNotification::create(["user_id" => $user->id, "type_of_work_id" => $notification->id]);
            }
        }

        return response()->json(['success' => true, 'message' => "Notifications updated"], 200);
    }


    // function for getting messages between users
    public function getConversation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'offset' => 'required|int|min:0',
            'limit' => 'required|int',
            'sender_id' => 'required|int'
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $sender_id = $request->sender_id;
        $user_id = $user->id;

        $messages = Message::where(function ($query) use ($sender_id, $user_id) {
            $query
                ->where("messages.user_id", "=", $sender_id)
                ->where("messages.created_by", "=", $user_id);
        })->orWhere(function ($query) use ($sender_id, $user_id) {
            $query
                ->where("messages.user_id", "=", $user_id)
                ->where("messages.created_by", "=", $sender_id);
        })->leftJoinSub('select id as sender_id, full_name, profile_image from users', "users", "users.sender_id", "=", "messages.created_by");

        $messagesCount = $messages->count();

        $messages = $messages->limit($request->get("limit"))
            ->offset($request->get("offset"))
            ->orderBy("messages.created_at", "DESC")
            ->get();

        return response()->json(['success' => true, 'messages' => $messages, "count" => $messagesCount], 200);
    }


    public function downloadMedia(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'route' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        return response()->download(public_path('/' . $request->route));
    }

    public function getEminentEmployers()
    {
        $users = User::where("role", "company")->where("is_active", 1)->where("is_archived", 0)->where("eminent", 1)->select("id", "full_name", "profile_image")->withCount("ads")->with('ad')->get();
        return response()->json(['success' => true, 'employers' => $users], 200);
    }

    public function getJobsData()
    {
        $users = User::where("role", "company")->where("is_active", 1)->where("is_archived", 0)->count();
        $jobs = Ad::where("is_active", 1)->where("is_archived", 0)->where('end_date', '>=', date('Y-m-d'))->count();
        return response()->json(['success' => true, 'employers' => $users, 'ads' => $jobs], 200);
    }
}
