<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'full_name',
        'email',
        'password',
        'role',
        'profile_image',
        'phone',
        'birth_year',
        'aditional_info',
        'gender_id',
        'notifications',
        'is_active',
        'company_activity',
        'company_description',
        'company_video',
        'employees_number',
        'pib',
        'pdv',
        'package_id',
        'country_id',
        'city_id',
        'address',
        'zip_code',
        'deleted',
        'created_by',
        'deleted_by',
        'modified_by',
        'is_archived',
        'education_level',
        'turn_notification',
        'facebook',
        'instagram',
        'linkedin',
        'website',
        'language_id',
        'background_image',
        'google_id',
        'apple_id',
        'facebook_id',
        'linkedin_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function gender(){
        return $this->belongsTo(Gender::class);
    }

    public function ads(){
        return $this->hasMany(Ad::class);
    }

    public function activeAds(){
        return $this->hasMany(Ad::class, "user_id")->where("is_active", 1)->where("is_archived", 0)->where("end_date",">=", date('Y-m-d'));
    }

    public function ad(){
        return $this->hasOne(Ad::class)->latest();
    }

    public function applications(){
        return $this->hasMany(AdSharedInfo::class, "user_id");
    }

    public function packages(){
        return $this->belongsTo(Package::class, "package_id");
    }

    public function company_users(){
        return $this->hasMany(CompanyUser::class, "user_id");
    }

    public function company_activities(){
        return $this->belongsTo(CompanyActivity::class, "company_activity");
    }

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }

    public function uiLanguage(){
        return $this->belongsTo(UserLanguage::class, "language_id");
    }

    public function driver_licences(){
        return $this->hasMany(DriversLicence::class, "user_id");
    }

    public function additional_information(){
        return $this->hasMany(AdditionalInformation::class, "user_id");
    }

    public function notifications(){
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc')->limit(5);
    }
    public function messages(){
        return $this->hasMany(Message::class)->orderBy('created_at', 'desc')->limit(5);
    }
    public function computer_skills(){
        return $this->hasMany(ComputerSkill::class);
    }
    public function educations(){
        return $this->hasMany(Education::class);
    }
    public function languages(){
        return $this->hasMany(ForeignLanguage::class);
    }
    public function videos(){
        return $this->hasOne(CvVideo::class)->latest();
    }
    public function work_experiences(){
        return $this->hasMany(WorkExperience::class);
    }
    public function documents(){
        return $this->hasOne(UserDocument::class, "user_id")->latest();
    }

    public function desiredCities(){
        return $this->hasMany(DesireCity::class, "user_id");
    }

    public function city_notifications(){
        return $this->hasMany(CityNotification::class, "user_id");
    }

    public function type_of_work_notifications(){
        return $this->hasMany(TypeOfWorkNotification::class, "user_id");
    }

    public function desiredJobs(){
        return $this->hasMany(DesireJobs::class, "user_id");
    }

    
}
