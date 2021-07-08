<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Education;
use App\Models\WorkExperience;
use App\Models\ForeignLanguage;
use App\Models\ComputerSkill;
use App\Models\UserDocument;
use App\Models\CvVideo;
use App\Models\DriversLicence;
use App\Models\DriversLicenceCategory;
use App\Models\AdditionalInformation;
use App\Models\DesireJob;
use App\Models\DesireCity;
use App\Models\Favorite;
use App\Models\CityNotification;
use App\Models\TypeOfWorkNotification;
use App\Models\Ad;
use App\Models\EducationTitle;
use DB;
use App\Models\AdSharedInfo;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{

    // function for updating particular education
    public function updateParticularEducation(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "id" => "required",
            "education_area_id" => "required",
            "education_title_id" => "required",
            "institution" => "required",
            "course" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $input = $request->except(['id']);
        $input["modified_by"] = $user->id;
        $education = Education::find($request->id);
        //return response()->json(['success' => false, 'message' => $education], 401);
        if ($education->user_id != $user->id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }
        $education->update($input);
        $educationTitle = EducationTitle::find($education->education_title_id);
        $education->title = $educationTitle->name;
        return response()->json(['success' => true, 'message' => "Education updated", "education" => $education], 200);
    }

    // function for add particular education
    public function addEducation(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "education_area_id" => "required|integer",
            "education_title_id" => "required",
            "institution" => "required",
            "course" => "required"
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
        $education = Education::create($input);
        $educationTitle = EducationTitle::find($request->education_title_id);
        $education->title = $educationTitle->name;
        return response()->json(['success' => true, 'message' => "Education created", "education" => $education], 200);
    }

     // function for removing particular education
     public function removeEducation(Request $request)
     {
 
         $validator = Validator::make($request->all(), [
             "id" => "required|integer"
         ]);
         if ($validator->fails()) {
             return response()->json(["success" => false, "message" => $validator->messages()]);
         }
         $user = auth()->user();
         if (!$user) {
             return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
         }
 
 
         $education = Education::find($request->id);

          if($user->id != $education->user_id){
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401); 
          }

          $education->delete();

         return response()->json(['success' => true, 'message' => "Education deleted"], 200);
     }

    // function for updating particular experience
    public function updateParticularExperience(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => "required|integer",
            "company_name" => "required",
            "job_category_id" => "required|integer",
            "location" => "required",
            "position" => "required",
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $input = $request->except(['id']);
        $input["modified_by"] = $user->id;
        $experience = WorkExperience::find($request->id);
        if ($experience->user_id != $user->id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }
        $experience->update($input);
        return response()->json(['success' => true, 'message' => "Experience updated", "experience" => $experience], 200);
    }

    // function for adding particular experience
    public function addExperience(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "company_name" => "required",
            "job_category_id" => "required|integer",
            "location" => "required",
            "position" => "required",
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
        $experience = WorkExperience::create($input);
        return response()->json(['success' => true, 'message' => "Education created"], 200);
    }

    // function for removing experience
    public function removeExperience(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => "required",
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }
        $experience = WorkExperience::find($request->id);
        if ($experience->user_id != $user->id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $experience->delete();
        return response()->json(['success' => true, 'message' => "Education deleted"], 200);
    }

    // function for updating foreign languages
    public function updateForeignLanguages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "foreign_languages" => "required",
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $foreignLanguages = json_decode($request->get("foreign_languages"));

        ForeignLanguage::where("user_id", $user->id)->delete();

        foreach ($foreignLanguages as $language) {
            ForeignLanguage::create(["user_id" => $user->id, "languages_id" => $language->languages_id, "language_reads_id" => $language->language_reads_id, "language_writes_id" => $language->language_writes_id, "language_speaks_id" => $language->language_speaks_id, "created_by" => $user->id, "modified_by" => $user->id]);
        }

        return response()->json(['success' => true, 'message' => "Foreign languages updated"], 200);
    }

    // function for removing languages
    public function removeLanguage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => "required",
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }
        $language = ForeignLanguage::find($request->id);
        if ($language->user_id != $user->id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $language->delete();
        return response()->json(['success' => true, 'message' => "Foreign language deleted"], 200);
    }

    // function for updating computer skills
    public function updateComputerSkills(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "computer_skills" => "required",
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $computerSkills = json_decode($request->get("computer_skills"));

        ComputerSkill::where("user_id", $user->id)->delete();

        foreach ($computerSkills as $skill) {
            ComputerSkill::create(["user_id" => $user->id, "computer_skill_name_id" => $skill->computer_skill_name_id, "computer_skill_knowledge_level_id" => $skill->computer_skill_knowledge_level_id, "created_by" => $user->id, "modified_by" => $user->id]);
        }

        $computerSkills = ComputerSkill::leftJoinSub('select id as idS, name as skill_name from computer_skill_names', "computer_skill_names", "computer_skill_names.idS", "=", "computer_skills.computer_skill_name_id")->where("user_id", $user->id)->get();

        return response()->json(['success' => true, 'message' => "Computer skills updated", "computerSkills" => $computerSkills], 200);
    }

    // function for adding particular computer skill
    public function addComputerSkill(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "computer_skill_name_id" => "required|integer",
            "computer_skill_knowledge_level_id" => "required|integer",
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
        $skill = ComputerSkill::create($input);
        return response()->json(['success' => true, 'message' => "Computer skill created"], 200);
    }

    // function for removing particular computer skill
    public function removeComputerSkill(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => "required|integer"
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $skill = ComputerSkill::find($request->id);

        if (!$skill) {
            return response()->json(['success' => false, 'message' => "Skill not found"], 401);
        }

        if ($user->id != $skill->user_id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $skill->delete();

        $computerSkills = ComputerSkill::leftJoinSub('select id as idS, name as skill_name from computer_skill_names', "computer_skill_names", "computer_skill_names.idS", "=", "computer_skills.computer_skill_name_id")->where("user_id", $user->id)->get();

        return response()->json(['success' => true, 'message' => "Computer skills updated", "computerSkills" => $computerSkills], 200);
    }

    // function for updating cv
    public function updateCvDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "cv" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $document = UserDocument::where(["user_id" => $user->id])->first();

        if ($document) {
            if ($document->document_link) {
                unlink(public_path() . "/" . $document->document_link);
            }
            $document->delete();
        }


        $input = $request->all();
        if ($file = $request->file('cv')) {
            $name = time() . $file->getClientOriginalName();
            $name = str_replace(" ","", $name);
            $file->move('documents/cv/' . $user->id, $name);
            $input['document_name'] = $name;
            $input['document_link'] = 'documents/cv/' . $user->id . "/" . $name;
        }

        $input["user_id"] = $user->id;
        $input["modified_by"] = $user->id;
        $input["created_by"] = $user->id;
        $cv = UserDocument::create($input);
        return response()->json(['success' => true, 'user' => $user, "message" => "Document successfully updated", "cv" => $cv], 200);
    }

    // function for removing cv document
    public function removeDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => "required",
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }
        $document = UserDocument::find($request->id);
        if ($document->user_id != $user->id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }
        unlink(public_path() . "/" . $document->document_link);
        $document->delete();
        return response()->json(['success' => true, 'message' => "Document deleted"], 200);
    }

    // function for adding video
    public function addVideo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "video" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }



        $input = $request->all();
        if ($file = $request->file('video')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('videos/' . $user->id, $name);
            $input['description'] = 'test';
            $input['video'] = 'videos/' . $user->id . "/" . $name;
        }
        $input["created_by"] = $user->id;
        $input["user_id"] = $user->id;
        $video = CvVideo::create($input);
        return response()->json(['success' => true, 'user' => $user, "message" => "Video successfully created", "video" => $video], 200);
    }

    // function for removing video
    public function removeVideo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => "required",
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }
        $video = CvVideo::find($request->id);
        if ($video->user_id != $user->id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }
        unlink(public_path() . "/" . $video->video);
        $video->delete();
        return response()->json(['success' => true, 'message' => "Video deleted"], 200);
    }


    // function for updating drive licence
    public function updateDriverLicence(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "drivers_licence_category_id" => "required|integer",
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }
        $licence = DriversLicence::where(["user_id" => $user->id]);

        $input = $request->all();
        $input["modified_by"] = $user->id;

        if (!$licence->first()) {
            $input["user_id"] = $user->id;
            $input["created_by"] = $user->id;
            $licence = DriversLicence::create($input);
            $licenceNew = $licence->first();
            $category = DriversLicenceCategory::find($licenceNew->drivers_licence_category_id);
            $licenceNew->category = $category->name;
            return response()->json(['success' => true, 'message' => "Licence updated", "licence" => $licenceNew], 200);
        }


        $licence->update($input);
        $licenceNew = $licence->first();
        $category = DriversLicenceCategory::find($licenceNew->drivers_licence_category_id);
        $licenceNew->category = $category->name;
        return response()->json(['success' => true, 'message' => "Licence updated", "licence" => $licenceNew], 200);
    }

    // function for updating additional information
    public function updateAdditionalInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "text" => "required",
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }
        $input = $request->all();
        $input["modified_by"] = $user->id;
        $info = AdditionalInformation::where(["user_id" => $user->id]);

        if (!$info->first()) {
            $input["user_id"] = $user->id;
            $input["created_by"] = $user->id;
            $info = AdditionalInformation::create($input);
            return response()->json(['success' => true, 'message' => "Information updated", "info" => $info], 200);
        }


        $info->update($input);
        return response()->json(['success' => true, 'message' => "Information updated", "info" => $info->first()], 200);
    }

    // function for getting client adds
    public function getClientAdds(Request $request)
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

        $type_of_work_id = $request->get("type_of_work_id");
        $city_id = $request->get("city_id");
        $term = $request->get("term");
        $company_id = $request->get("company_id");
        $applied = $request->get("applied");
        $active = $request->get("active");

        $ads = AdSharedInfo::where('user_id', $user->id)
            ->leftJoinSub('select id as adM, user_id as publisher_id , title, description, city_id, country_id, position, is_active, is_archived, location, type_of_work_id, end_date from ads', "ads", "ads.adM", "=", "ad_shared_infos.ad_id")
            ->leftJoinSub('select id as owner_id, full_name as owner_name, profile_image from users', "users", "users.owner_id", "=", "ads.publisher_id")
            ->leftJoinSub('select id as typeOfWorkId, name as type_of_work_name from type_of_works', "type_of_works", "type_of_works.typeOfWorkId", "=", "ads.type_of_work_id");

        if ($applied == 1) {
            $ads = $ads->where('ad_shared_infos.applied', "=", 1);
        }

        if ($applied == 2) {
            $ads = $ads->where('ad_shared_infos.applied', "=", 0);
        }

        if ($city_id) {
            $ads = $ads->where('ads.city_id', $city_id);
        }

        if ($active == 1) {
            $ads = $ads->where('ads.is_active', "=", 1);
        }

        if ($active == 2) {
            $ads = $ads->where('ads.is_active', "=", 0);
        }

        if ($company_id) {
            $ads = $ads->where('ads.publisher_id', $company_id);
        }

        if ($type_of_work_id) {
            $ads = $ads->where('ads.type_of_work_id', $type_of_work_id);
        }

        if ($term) {
            $ads = $ads->where(function ($query) use ($term) {
                $query
                    ->where("ads.title", "like", "%" . $term . "%")
                    ->orWhere("users.owner_name", "like", "%" . $term . "%")
                    ->orWhere("ads.location", "like", "%" . $term . "%")
                    ->orWhere("ads.position", "like", "%" . $term . "%");
            });
        }

        $adsCount = $ads->count();

        $ads = $ads->limit($request->get("limit"))
            ->offset($request->get("offset"))
            ->orderBy("ad_shared_infos.created_at", "DESC")
            ->get();

        $favorites = null;
        $favorites = Favorite::where("user_id", $user->id)->get();
        if ($favorites) {
            $ads = collect($ads)->map(function ($item) use ($favorites) {

                $item['favorite'] = $favorites->where('ad_id', $item['ad_id'])->first() ? 1 : 0;
                return $item;
            });
        }

        return response()->json(['success' => true, 'ads' => $ads, 'count' => $adsCount], 200);
    }

    /* public function getFavoriteClientAds(Request $request)
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


        $ads = AdSharedInfo::where('user_id', $user->id)
            ->leftJoinSub('select id, user_id as publisher_id , title, description, city_id, country_id from ads', "ads", "ads.id", "=", "ad_shared_infos.ad_id");

        $adsCount = $ads->count();

        $ads = $ads->limit($request->get("limit"))
            ->offset($request->get("offset"))
            ->orderBy("created_at", "DESC")
            ->get();

        return response()->json(['success' => true, 'ads' => $ads, 'count' => $adsCount], 200);
    } */



    // function for getting dashboard info
    public function getDashboard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $ads = AdSharedInfo::select(DB::raw('count(id) as `data`'), DB::raw('MONTH(created_at) as month'))->where('user_id', $user->id)->whereYear('created_at', $request->year);

        $ads = $ads->groupBy('month')->orderBy("created_at", "ASC")
            ->get();

        $adLast = AdSharedInfo::where('user_id', $user->id)
            ->leftJoinSub('select id, user_id as publisher_id , title, description, city_id, country_id, position from ads', "ads", "ads.id", "=", "ad_shared_infos.ad_id")
            ->leftJoinSub('select id as cityId, name as city, name_short as city_short from cities', "cities", "ads.city_id", "=", "cities.cityId")
            ->leftJoinSub('select id as countryId, name as country, name_short as country_short from countries', "countries", "ads.country_id", "=", "countries.countryId")
            ->leftJoinSub('select id as owner_id , profile_image from users', "users", "users.owner_id", "=", "ads.publisher_id");

        $adLast = $adLast->limit(10)
            ->offset(0)
            ->orderBy("created_at", "DESC")
            ->get();

            $cityCriteria = CityNotification::where("user_id", $user->id)->pluck("city_id")->toArray();
            $typeCriteria = TypeOfWorkNotification::where("user_id", $user->id)->pluck("type_of_work_id")->toArray();
    
            $latestAds = [];
            if (sizeof($typeCriteria) > 0) {
                $latestAds = Ad::with(["country", "city", "type_of_work"])->with(['creator' => function ($query) {
                    $query->select('id', 'full_name', 'profile_image');
                }])->where("is_active", 1)->where("is_archived", 0)->where(function ($query) use ($cityCriteria, $typeCriteria) {
                    if ($cityCriteria) {
                        $query->whereIn('city_id', $cityCriteria);
                    }
    
                    if ($typeCriteria) {
                        $query->whereIn('type_of_work_id', $typeCriteria);
                    }
                });
    
                $latestAds = $latestAds->take(5)
                    ->orderBy("created_at", "DESC")
                    ->get();
            }    

        $documentsCount =  UserDocument::where("user_id", $user->id)->count();
        $applyCount =  AdSharedInfo::where("user_id", $user->id)->count();
        $cvVideos = CvVideo::where("user_id", $user->id)->get();

        return response()->json(['success' => true, 'ads' => $ads, "cvCount" => $documentsCount, "applicationNumber" => $applyCount, "videos" => $cvVideos, "lastAds" => $adLast, "newAds" => $latestAds], 200);
    }

    // function for filtering special ads
    public function filterSpecialAds(Request $request)
    {

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $cityCriteria = CityNotification::where("user_id", $user->id)->pluck("city_id")->toArray();
        $typeCriteria = TypeOfWorkNotification::where("user_id", $user->id)->pluck("type_of_work_id")->toArray();
        $type_of_work_id = $request->get("type_of_work_id");
        $city_id = $request->get("city_id");
        $userOwner = $request->user_id;

        $latestAds = [];
        if (sizeof($typeCriteria) > 0) {
            $latestAds = Ad::with(["country", "city", "type_of_work"])->with(['creator' => function ($query) {
                $query->select('id', 'full_name', 'profile_image');
            }])->where("is_active", 1)->where("is_archived", 0)->where(function ($query) use ($cityCriteria, $typeCriteria, $type_of_work_id, $city_id, $userOwner) {
                if ($cityCriteria) {
                    $query->whereIn('ads.city_id', $cityCriteria);
                }

                if ($typeCriteria) {
                    $query->whereIn('ads.type_of_work_id', $typeCriteria);
                }

                if ($city_id) {
                    $query->where('ads.city_id', $city_id);
                }

                if ($type_of_work_id) {
                    $query->where('ads.type_of_work_id', $type_of_work_id);
                }


                if ($userOwner) {
                    $query->where('ads.user_id', $userOwner);
                }
            });

            $latestAds = $latestAds->take(5)
                ->orderBy("created_at", "DESC")
                ->get();
        }

        return response()->json(['success' => true, "newAds" => $latestAds], 200);
    }

    public function addSeen(Request $request)
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

        AdSharedInfo::where("user_id", $user->id)->where("id", $request->id)->update(["seen" => 1]);
        return response()->json(['success' => true, 'message' => "Ad viewed"], 200);
    }

    public function updateDesireJobs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type_of_work_ids' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        DesireJob::where("user_id", $user->id)->delete();
        $jobs = $request->type_of_work_ids;
        foreach ($jobs as $id) {
            DesireJob::create(["user_id" => $user->id, "type_of_work_id" => $id]);
        }

        return response()->json(['success' => true, 'message' => "Desire jobs added"], 200);
    }

    public function updateDesireCities(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city_ids' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        DesireCity::where("user_id", $user->id)->delete();
        $cities = $request->type_of_work_ids;
        foreach ($cities as $id) {
            DesireCity::create(["user_id" => $user->id, "city_id" => $id]);
        }

        return response()->json(['success' => true, 'message' => "Desire cities added"], 200);
    }

    public function toggleFavorite(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ad_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $userAuth = auth()->user();
        if (!$userAuth) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $ad_id = $request->ad_id;
        $favorite = Favorite::where("ad_id", $ad_id)->where("user_id", $userAuth->id)->first();

        if ($favorite) {
            $favorite->delete();
        } else {
            Favorite::create(["ad_id" => $ad_id, "user_id" => $userAuth->id]);
        }

        return response()->json(['success' => true, 'message' => "Favorite syncronised"], 200);
    }

    public function getFavorites(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'offset' => 'required|int|min:0',
            'limit' => 'required|int',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $userAuth = auth()->user();
        if (!$userAuth) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }


        $favorites = Favorite::where("user_id", "=", $userAuth->id)->leftJoinSub('select id as job_id, user_id as publisher_id , title, description, city_id, country_id, position,  location, type_of_work_id, is_active from ads', "ads", "ads.job_id", "=", "favorites.ad_id")
            ->leftJoinSub('select id as owner_id , full_name, profile_image from users', "users", "users.owner_id", "=", "ads.publisher_id")
            ->leftJoinSub('select id as type_id, name as type_of_work_name from type_of_works', "type_of_works", "type_of_works.type_id", "=", "ads.type_of_work_id");

        $favoritesCount = $favorites->count();

        $favorites = $favorites->limit($request->get("limit"))
            ->offset($request->get("offset"))
            ->orderBy("favorites.created_at", "DESC")
            ->get();

        return response()->json(['success' => true, 'favorites' => $favorites, "count" => $favoritesCount], 200);
    }

    // function for getting client adds
    // function for getting client adds
    public function getAdsForMe(Request $request)
    {

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $city_id = $request->city_id;
        $owner_id = $request->company_id;

        $typeIds = TypeOfWorkNotification::where("user_id", $user->id)->pluck('type_of_work_id')->toArray();
        $cityIds = CityNotification::where("user_id", $user->id)->pluck('city_id')->toArray();

        $ads = null;
        if (sizeof($typeIds) > 0 || sizeof($cityIds) > 0) {
            $ads = Ad::with(["country", "city", "type_of_work"])->leftJoinSub('select id as owner_id , full_name as company, profile_image from users', "users", "users.owner_id", "=", "ads.user_id")->where("ads.is_active", 1)->where("ads.is_archived", 0)->where(function ($query) use ($typeIds, $cityIds, $city_id, $owner_id) {

                if (sizeof($typeIds) > 0) {
                    $query->whereIn("type_of_work_id", $typeIds);
                }

                if (sizeof($cityIds) > 0) {
                    $query->whereIn("city_id", $cityIds);
                }

                if ($city_id) {
                    $query->where("ads.city_id", $city_id);
                }
                if ($owner_id) {
                    $query->where("users.owner_id", $owner_id);
                }
            });


            $ads = $ads->limit(10)
                ->offset(0)
                ->orderBy("ads.created_at", "DESC")
                ->get();
        }


        return response()->json(['success' => true, 'ads' => $ads, "typeIds" => $typeIds, "cityIds" => $cityIds], 200);
    }
}
