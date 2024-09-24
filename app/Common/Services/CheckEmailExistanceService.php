<?php
namespace App\Common\Services;

use App\Models\ModulesModel;
use App\Models\RoleModel;
use App\Models\UserRoleModel;
use App\Models\UserModel;
use App\Models\UserTranslationModel;
use App\Models\ModuleUserModel;
use App\Models\AcademicYearModel;
use App\Models\ProfessorModel;
use App\Models\EmployeeModel;
use App\Models\StudentModel;
use App\Models\ParentModel;
use App\Models\SchoolAdminModel;

use Session;
use DB;
class CheckEmailExistanceService
{
    public function __construct()
    {
        $this->UserModel             = new UserModel();
        $this->UserRoleModel         = new UserRoleModel();
        $this->RoleModel             = new RoleModel();
        $this->ModulesModel          = new ModulesModel();
        $this->ModuleUserModel       = new ModuleUserModel();
        $this->AcademicYearModel     = new AcademicYearModel();
        $this->UserTranslationModel  = new UserTranslationModel();
        $this->ProfessorModel        = new ProfessorModel();
        $this->StudentModel          = new StudentModel();
        $this->EmployeeModel         = new EmployeeModel();
        $this->ParentModel           = new ParentModel();
        $this->SchoolAdminModel      = new SchoolAdminModel();
         /*Local Section*/

        $this->user_profile_base_img_path     = public_path().config('app.project.img_path.user_profile_images');
        $this->user_profile_public_img_path   = url('/').config('app.project.img_path.user_profile_images');

        if(Session::has('locale'))
        {
            $this->locale = Session::get('locale');
        }
        else
        {
            $this->locale = 'en';
        }

        $this->school_id = Session::get('school_id');
    }

    public function check_existence_while_registration($email){

        $user = $this->UserModel->where('email',$email)->first();
        if(isset($user->id)){
            $school_id = \Session::get('school_id');
            $professor =    $this->ProfessorModel
                                 ->where('user_id',$user->id)
                                 ->where('has_left',0)
                                 ->count();
                                 
            if($professor==0)
            {
                $professor =   $this->ProfessorModel
                                 ->where('user_id',$user->id)
                                 ->where('has_left',1)
                                 ->first();

                if(count($professor)>0){
                    return $professor->user_id;
                }
                $school_admin =     $this->SchoolAdminModel
                                        ->where('user_id',$user->id)
                                        ->count();
                if($school_admin<=0)
                {
                    $employee   =    $this->EmployeeModel
                                            ->where('user_id',$user->id)
                                            ->where('has_left',0)
                                            ->count();
                    
                    if($employee<=0)
                    {
                        $employee   =    $this->EmployeeModel
                                            ->where('user_id',$user->id)
                                            ->where('has_left',1)
                                            ->first();
                        if(isset($employee->user_id)){
                            return $employee->user_id;
                        }

                        $student        =   $this->StudentModel
                                                 ->where('user_id',$user->id)
                                                 ->where('has_left',0)
                                                 ->count();

                        if($student<=0)
                        {   
                            $student        =   $this->StudentModel
                                                 ->where('user_id',$user->id)
                                                 ->where('has_left',1)
                                                 ->first();
                            if(isset($student->user_id)){
                                return $student->user_id; 
                            }                     
                            $parent    =   $this->ParentModel
                                                ->where('user_id',$user->id)
                                                ->first();
                            
                            if(count($parent)>0){

                                return isset($parent->user_id) ? $parent->user_id : 0;

                            }   
                            else{
                                return 'not_exist';
                            }            
                        }
                    }
                }
            }
            return 'exist';       
        }
        else{
            return 'not_exist';          
        }
        
    }
    public function check_existence_while_registration_of_parent($email){
        $result = $this->UserModel
                                  ->where('email',$email)
                                  ->first();

        $flag='not_exist';
        if($result)
        {
            $result = $result->toArray();
            if(count($result)>0)
            {
                if($this->StudentModel->where(['school_id'=>$this->school_id,'user_id'=>$result['id']])->count() >0)
                {
                    if($this->SchoolParentModel->where(['school_id'=>$this->school_id,'parent_id'=>$result['id']])->count() >0)
                    {
                        $flag='exist';
                    }
                }
            }
        }
        return $flag;
    }        
}