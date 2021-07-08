<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CompanyUser;
use App\Models\CompanyActivity;
use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Models\Blog;
use App\Models\BlogImages;
use App\Models\SocialMedia;
use App\Models\Package;
use App\Models\PackagePurchaseHistory;
use App\Models\Ad;
use App\Models\AdSharedInfo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use DB;

class CompanyController extends Controller
{
    // function for updating company
    public function updateCompany(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $company = User::find($user->id);

        if (!$company) {
            return response()->json(['success' => false, 'message' => 'Company not found'], 404);
        }

        $input = $request->except(['company_activity']);




        $contact = CompanyUser::where("user_id", $company->id);
        $activity = CompanyActivity::where("id", $company->company_activity);

        if (!$contact->first()) {
            CompanyUser::create(["user_id" => $company->id, "contact_person" =>  $request->contact_person, "contact_person_position" => $request->contact_person_position, "contact_phone" => $request->contact_phone, "contact_mail" => $request->contact_mail]);
        } else {
            $contact->update(["contact_person" =>  $request->contact_person, "contact_person_position" => $request->contact_person_position, "contact_phone" => $request->contact_phone, "contact_mail" => $request->contact_mail]);
        }

        if ($request->company_activity) {

            $activity = CompanyActivity::find($company->company_activity);

            if (!$activity) {
                $activity = CompanyActivity::create(["name" => $request->company_activity, "created_by" => $user->id]);
                $input["company_activity"] = $activity->id;
            } else {
                $activity->update(["name" => $request->company_activity]);
            }
        }

        $company->update($input);

        return response()->json(['success' => true, 'message' => 'Company updated'], 200);
    }

    // function for adding company user
    public function addCompanyUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "contact_person" => "required",
            "contact_person_position" => "required",
            "contact_phone" => "required",
            "contact_mail" => "required",
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
        $companyUser = CompanyUser::create($input);
        return response()->json(['success' => true, 'message' => "Company user created"], 200);
    }

    // function for updating particular company user
    public function updateParticularCompanyUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => "required|integer",
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $input = $request->except(['id', 'user_id']);
        $input["modified_by"] = $user->id;
        $companyUser = CompanyUser::find($request->id);
        if (!$companyUser) {
            return response()->json(['success' => false, 'message' => "User not found"], 404);
        }
        if ($user->id != $companyUser->user_id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }
        $companyUser->update($input);
        return response()->json(['success' => true, 'message' => "Company user updated", "companyUser" => $companyUser], 200);
    }

    // function for removing company user
    public function removeCompanyUser(Request $request)
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
        $companyUser = CompanyUser::find($request->id);
        if ($companyUser->user_id != $user->id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $companyUser->delete();
        return response()->json(['success' => true, 'message' => "Company user deleted"], 200);
    }

    // function for adding company gallery
    public function addCompanyGallery(Request $request)
    {

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $input = $request->all();
        $input["user_id"] = $user->id;
        $input["created_by"] = $user->id;
        $companyGallery = Gallery::create($input);
        return response()->json(['success' => true, 'message' => "Gallery created"], 200);
    }

    // function for removing gallery
    public function removeCompanyGallery(Request $request)
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
        $companyGallery = Gallery::find($request->id);
        if ($companyGallery->user_id != $user->id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $companyGallery->delete();
        return response()->json(['success' => true, 'message' => "Company user deleted"], 200);
    }

    // function for adding image in gallery
    public function addGalleryImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "gallery_id" => "required|integer",
            "image" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $input = $request->all();
        if ($file = $request->file('image')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('images/gallery/' . $request->gallery_id, $name);
            $input['image'] = 'images/gallery/' . $request->gallery_id . "/" . $name;
        }
        $input["created_by"] = $user->id;
        $image = GalleryImage::create($input);
        return response()->json(['success' => true, 'user' => $user, "message" => "Image uploaded"], 200);
    }

    // function for removing image from gallery
    public function removeGalleryImage(Request $request)
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
        $image = GalleryImage::find($request->id);
        if ($image->user_id != $user->id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }
        unlink(public_path() . "/" . $image->image);
        $image->delete();
        return response()->json(['success' => true, 'message' => "Image deleted"], 200);
    }

    // function for adding company blog
    public function addCompanyBlog(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "title" => "required",
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
        $input["user_id"] = $user->id;
        $input["created_by"] = $user->id;
        $companyBlog = Blog::create($input);
        return response()->json(['success' => true, 'message' => "Blog created"], 200);
    }

    // function for removing blog
    public function removeCompanyBlog(Request $request)
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
        $companyBlog = Blog::find($request->id);
        if ($companyBlog->user_id != $user->id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $companyBlog->delete();
        return response()->json(['success' => true, 'message' => "Company user deleted"], 200);
    }

    // function for adding image in blog
    public function addBlogImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "blog_id" => "required|integer",
            "image" => "required"
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $input = $request->all();
        if ($file = $request->file('image')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('images/blog/' . $request->blog_id, $name);
            $input['image'] = 'images/blog' . $request->blog_id . "/" . $name;
        }
        $input["created_by"] = $user->id;
        $image = BlogImages::create($input);
        return response()->json(['success' => true, 'user' => $user, "message" => "Image uploaded"], 200);
    }

    // function for removing image from blog
    public function removeBlogImage(Request $request)
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
        $image = BlogImages::find($request->id);
        if ($image->user_id != $user->id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }
        unlink(public_path() . "/" . $image->image);
        $image->delete();
        return response()->json(['success' => true, 'message' => "Image deleted"], 200);
    }

    // function for adding social media link
    public function addSocialMedia(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "link" => "required",
            "name" => "required"
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
        $socialMedia = SocialMedia::create($input);
        return response()->json(['success' => true, 'message' => "Social media linked"], 200);
    }

    // function for updating particular social media link
    public function updateParticularSocialMedia(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => "required|integer",
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }
        if ($user->id != $request->user_id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $input = $request->except(['id', 'user_id']);
        $input["modified_by"] = $user->id;
        $socialMedia = SocialMedia::find($request->id);
        if (!$socialMedia) {
            return response()->json(['success' => false, 'message' => "Not found"], 404);
        }
        if ($user->id != $socialMedia->user_id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }
        $socialMedia->update($input);
        return response()->json(['success' => true, 'message' => "Social media updated", "social_media" => $socialMedia], 200);
    }

    // function for removing social media link
    public function removeSocialMedia(Request $request)
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
        $socialMedia = SocialMedia::find($request->id);
        if ($socialMedia->user_id != $user->id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $socialMedia->delete();
        return response()->json(['success' => true, 'message' => "Social media deleted"], 200);
    }

    // function for adding package
    public function addPackage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "description" => "required",
            "package_type" => "required|integer",
            "price" => "required|regex:/^\d*(\.\d{2})?$/",
            "duration" => "required|integer"
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
        $package = Package::create($input);
        return response()->json(['success' => true, 'message' => "Package created"], 200);
    }

    // function for updating particular package
    public function updateParticularSocialPackage(Request $request)
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
        if ($user->id != $request->user_id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $input = $request->except(['id', 'user_id']);
        $input["modified_by"] = $user->id;
        $package = Package::where('id', $request->id);
        $package->update($input);
        return response()->json(['success' => true, 'message' => "Package updated", "package" => $package->first()], 200);
    }

    // function for removing package
    public function removePackage(Request $request)
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
        $package = Package::find($request->id);
        if ($package->user_id != $user->id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $package->delete();
        return response()->json(['success' => true, 'message' => "Package deleted"], 200);
    }

    // function for adding package
    public function addPackagePurchaseHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "package_id" => "required|integer",
            "purchase_auto" => "required|boolean",
            "purchase_date" => "required|date",
            "expires_date" => "required|date"
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
        $packagePurchaseHistory = PackagePurchaseHistory::create($input);
        return response()->json(['success' => true, 'message' => "Package purchase created"], 200);
    }

    // function for removing package
    public function removePackagePurchaseHistory(Request $request)
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
        $packagePurchaseHistory = PackagePurchaseHistory::find($request->id);
        if ($packagePurchaseHistory->user_id != $user->id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $packagePurchaseHistory->delete();
        return response()->json(['success' => true, 'message' => "Package purchase deleted"], 200);
    }

    // function for getting information about particular package
    public function getParticularPackage(Request $request)
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

        $package = Package::find($request->id);
        if (!$package) {
            return response()->json(['success' => false, 'message' => "Package not found"], 404);
        }
        if ($package->user_id != $user->id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        return response()->json(['success' => true, 'package' => $package], 200);
    }

    // function for getting published ads
    public function getPublishedAds(Request $request)
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

        $ads = Ad::with(["country", "city"])->leftJoinSub('select id as typeOfWorkId , name as type_of_work from type_of_works', "type_of_works", "type_of_works.typeOfWorkId", "=", "ads.type_of_work_id")->where("is_active", 1)->where("is_archived", 0)->where("user_id", $user->id)->where(function ($query) use ($type_of_work_id, $city_id) {
            if ($city_id) {
                $query->where('city_id', $city_id);
            }

            if ($type_of_work_id) {
                $query->where('type_of_work_id', $type_of_work_id);
            }
        });

        if ($term) {
            $ads = $ads->where(function ($query) use ($term) {
                $query
                    ->where("ads.title", "like", "%" . $term . "%")
                    ->orWhere("ads.location", "like", "%" . $term . "%")
                    ->orWhere("ads.position", "like", "%" . $term . "%");
            });
        }

        $adsCount = $ads->count();

        $ads = $ads->limit($request->get("limit"))
            ->offset($request->get("offset"))
            ->orderBy("created_at", "DESC")
            ->get();

        return response()->json(['success' => true, 'ads' => $ads, 'count' => $adsCount], 200);
    }

    // function for getting arhived ads
    public function getArhivedAds(Request $request)
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

        $ads = Ad::with(["country", "city"])->leftJoinSub('select id as typeOfWorkId , name as type_of_work from type_of_works', "type_of_works", "type_of_works.typeOfWorkId", "=", "ads.type_of_work_id")->where("is_archived", 1)->where("user_id", $user->id)->where(function ($query) use ($type_of_work_id, $city_id) {
            if ($city_id) {
                $query->where('city_id', $city_id);
            }

            if ($type_of_work_id) {
                $query->where('type_of_work_id', $type_of_work_id);
            }
        });

        if ($term) {
            $ads = $ads->where(function ($query) use ($term) {
                $query
                    ->where("ads.title", "like", "%" . $term . "%")
                    ->orWhere("ads.location", "like", "%" . $term . "%")
                    ->orWhere("ads.position", "like", "%" . $term . "%");
            });
        }

        $adsCount = $ads->count();

        $ads = $ads->limit($request->get("limit"))
            ->offset($request->get("offset"))
            ->orderBy("created_at", "DESC")
            ->get();

        return response()->json(['success' => true, 'ads' => $ads, 'count' => $adsCount], 200);
    }

    // function for getting saved ads
    public function getSavedAds(Request $request)
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

        $ads = Ad::with(["country", "city"])->leftJoinSub('select id as typeOfWorkId , name as type_of_work from type_of_works', "type_of_works", "type_of_works.typeOfWorkId", "=", "ads.type_of_work_id")->where("is_active", 0)->where("is_archived", 0)->where("user_id", $user->id)->where(function ($query) use ($type_of_work_id, $city_id) {
            if ($city_id) {
                $query->where('city_id', $city_id);
            }

            if ($type_of_work_id) {
                $query->where('type_of_work_id', $type_of_work_id);
            }
        });

        if ($term) {
            $ads = $ads->where(function ($query) use ($term) {
                $query
                    ->where("ads.title", "like", "%" . $term . "%")
                    ->orWhere("ads.location", "like", "%" . $term . "%")
                    ->orWhere("ads.position", "like", "%" . $term . "%");
            });
        }

        $adsCount = $ads->count();

        $ads = $ads->limit($request->get("limit"))
            ->offset($request->get("offset"))
            ->orderBy("created_at", "DESC")
            ->get();

        return response()->json(['success' => true, 'ads' => $ads, 'count' => $adsCount], 200);
    }

    // function for getting particular ad
    public function getParticularAd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|int'
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $ad = Ad::find($request->id);

        return response()->json(['success' => true, 'ad' => $ad], 200);
    }

    // function for getting dashboard info
    public function getCompanyDashboard(Request $request)
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
        $adsStats = Ad::select(DB::raw('count(id) as `data`'), DB::raw('MONTH(created_at) as month'))->where('user_id', $user->id)->whereYear('created_at', $request->year);

        $adsStats = $adsStats->groupBy('month')->orderBy("created_at", "ASC")->get();

        $activeAdsList = Ad::where('user_id', $user->id)->where("is_active", 1)->where("is_archived", 0)->leftJoinSub('select id as owner_id , full_name as company, profile_image from users', "users", "users.owner_id", "=", "ads.user_id")->latest('created_at')->limit(5)->get();

        $activeAdsNumber = Ad::where('user_id', $user->id)->where("is_active", 1)->where("is_archived", 0)->count();

        $latestApllicationNumber = 0;

        if ($numberOfApplicationsLatest = Ad::where('user_id', $user->id)->where("is_active", 1)->where("is_archived", 0)->select('number_of_applications')->latest('created_at')->first()) {
            $latestApllicationNumber = $numberOfApplicationsLatest->number_of_applications;
        }

        $numberOfApplicationsAll = Ad::where('user_id', $user->id)->where("is_active", 1)->where("is_archived", 0)->sum('number_of_applications');

        $numberOfSelectedCandidatesForLatestAd = 0;

        $latestAdId = Ad::where('user_id', $user->id)->where("is_active", 1)->where("is_archived", 0)->select('id')->latest('created_at');

        if ($latestAdId->first()) {
            $lastAd = $latestAdId->first();
            $numberOfSelectedCandidatesForLatestAd = AdSharedInfo::where('ad_id', $lastAd->id)->where('selected', 1)->count();
        }

        $adIds = Ad::where("user_id", $user->id)->pluck('id')->toArray();
        $applyUserIds = AdSharedInfo::whereIn('ad_id', $adIds)->pluck('user_id')->toArray();

        $newCandidates = User::where('role', 'employee')->with(['educations' => function ($q) {
            $q->join('education_titles', 'education_titles.id', '=', 'education.education_title_id');
        }])->whereIn("id", $applyUserIds)
            ->latest('created_at')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'adsStats' => $adsStats,
            'activeAdsList' => $activeAdsList,
            'activeAdsNumber' => $activeAdsNumber,
            'numberOfApplicationsLatest' => $latestApllicationNumber,
            'numberOfApplicationsAll' => $numberOfApplicationsAll,
            'numberOfSelectedCandidatesForLatestAd' => $numberOfSelectedCandidatesForLatestAd,
            'newCandidates' => $newCandidates
        ], 200);
    }

    // function for getting all companies depending on term or not
    public function getCompanies(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'offset' => 'required',
            'limit' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        if ($request->term) {
            $companies = User::where("full_name", "like", "%" . $request->term . "%")->where("is_active", 1)->where("is_archived", 0)->where("role", "=", "company")->with(['country', 'city'])->withCount('activeAds');
            $companiesCount = $companies->count();
            $companies = $companies->limit($request->get("limit"))
            ->offset($request->get("offset"))
            ->orderBy("created_at", "DESC")
            ->get();
        } else {
            $companies = User::where("role", "=", "company")->where("is_active", 1)->where("is_archived", 0)->with(['country', 'city'])->withCount('activeAds');
            $companiesCount = $companies->count();
            $companies = $companies->limit($request->get("limit"))
            ->offset($request->get("offset"))
            ->orderBy("created_at", "DESC")
            ->get();
        }

        return response()->json([
            'success' => true,
            'companies' => $companies,
            'count' => $companiesCount,
        ], 200);
    }

    // function for setting ad view
    public static function updateSeen($id)
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


        $apply = AdSharedInfo::find($id);

        if (!$apply) {
            return response()->json(['success' => false, 'message' => "Not found"], 404);
        }

        if (!$user->id != $apply->user_id) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $apply->update(["viewed" => date('Y-m-d')]);
        return response()->json(['success' => true, 'message' => "Apply updated"], 200);
    }

   // function for setting reminder for apply
   public static function setReminder(Request $request)
   {

       $validator = Validator::make($request->all(), [
           'id' => 'required',
           'text' => 'required'
       ]);
       if ($validator->fails()) {
           return response()->json(["success" => false, "message" => $validator->messages()]);
       }

       $user = auth()->user();
       if (!$user) {
           return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
       }


       $apply = AdSharedInfo::find($request->id);

       if (!$apply) {
           return response()->json(['success' => false, 'message' => "Not found"], 404);
       }

       $ad = Ad::find($apply->ad_id);

       if ($user->id != $ad->user_id) {
           return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
       }

       $apply->update(["reminder" => $request->text, "modified_by" => $user->id]);
       return response()->json(['success' => true, 'message' => "Apply updated"], 200);
   }

    // function for getting adds for admin basic and companies
    public function getPersonalAds(Request $request)
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
        $is_active = $request->get("is_active");
        $is_archived = $request->get("is_archived");



        $ads = Ad::with(["country", "city", "type_of_work"])->with(['creator' => function ($query) {
            $query->select('id', 'full_name', 'profile_image');
        }])->withCount("shared_adds")->where("user_id", $user->id)->where(function ($query) use ($type_of_work_id, $city_id, $is_active, $is_archived, $userOwner) {
            if ($city_id) {
                $query->where('city_id', $city_id);
            }

            if ($type_of_work_id) {
                $query->where('type_of_work_id', $type_of_work_id);
            }

            if ($is_active == 1) {
                $query->where('ads.is_active', "=", 1);
            }

            if ($is_active == 2) {
                $query->where('ads.is_active', "=", 0);
            }

            if ($is_archived) {
                $query->where('is_archived', $is_archived);
            }
        });

        $adsCount = $ads->count();

        $ads = $ads->limit($request->get("limit"))
            ->offset($request->get("offset"))
            ->orderBy("created_at", "DESC")
            ->get();

        return response()->json(['success' => true, 'ads' => $ads, 'count' => $adsCount], 200);
    }

    // get company by public route
    public function getCompany(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|int'
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $company = User::where("id", $request->id)->where("is_active", 1)->where("is_archived", 0)->select('id', 'role', 'full_name', 'profile_image', 'country_id', 'city_id', 'company_description', 'background_image', 'phone', 'address', 'website', 'facebook', 'instagram', 'linkedin', 'pib', 'pdv', 'email', 'zip_code', 'employees_number')->with(['country', 'city', 'videos'])->first();

        if(!$company || $company->role != "company"){
            return response()->json(['success' => false, 'message' => 'Company does not exist or not active'], 404); 
        }

        return response()->json(['success' => true, 'company' => $company], 200);

    }
}
