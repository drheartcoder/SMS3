<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use \Dimsav\Translatable\Translatable;
use Cartalyst\Sentinel\Users\EloquentUser as CartalystUser;
use Watson\Rememberable\Rememberable;
use Illuminate\Database\Eloquent\SoftDeletes;


class UserModel extends CartalystUser
{
    use SoftDeletes;
    use Rememberable;
    use Translatable;
    protected $table                = 'users';
    public $translationModel      = 'App\Models\UserTranslationModel';
    public $translationForeignKey = 'user_id';
    public $translatedAttributes  = ['first_name',
                                     'last_name',
                                     'special_note',
                                     'locale'];
                                     
    protected $fillable = [
                    		'email',
                            'password',
                            'last_name',
                            'first_name',
                            'gender',
                            'permissions',
                            'profile_image',
                            'is_active',
                            'mobile_no',
                            'address',
                            'latitude',
                            'longitude',
                            'post_code',
                            'national_id',
                            'telephone_no',
                            'nationality_id',
                            'last_login',
                            'birth_date',
                            'first_time_login',
                            'city',
                            'country'
                        ];
                        
    public function user_details()
    {
        /*return $this->hasOne('App\Models\UserTranslationModel','user_id','id')->where('locale',\Session::get('locale'));*/
        return $this->hasOne('App\Models\UserTranslationModel','user_id','id');
    }
    public function employee_details()
    {
        return $this->hasOne('App\Models\EmployeeModel','user_id','id');
    }
    public function professor_details()
    {
        return $this->hasOne('App\Models\ProfessorModel','user_id','id');
    }
    public function school_admin_details()
    {
        return $this->hasOne('App\Models\SchoolAdminModel','user_id','id');
    }

    public function student_details()
    {
        return $this->hasOne('App\Models\StudentModel','user_id','id');
    }

    public function get_student_details()
    {
       return $this->hasOne('App\Models\StudentModel','user_id','id');
    }

    public function get_parent_details()
    {
       return $this->hasOne('App\Models\ParentModel','user_id','id');
    }
    public function user_role()
    {
       return $this->hasOne('App\Models\UserRoleModel','user_id','id');
    }

    public function school_admin()
    {
       return $this->hasOne('App\Models\SchoolAdminModel','user_id','id');
    }
    
     
}