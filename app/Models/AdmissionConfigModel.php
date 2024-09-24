<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmissionConfigModel extends Model
{
    protected $table = "tbl_admission_config";

    protected $fillable = ['school_id','academic_year','educational_board','application_fee','no_of_seats','admission_close','admission_open','level_id'];

    public function get_academic_year()
    {
    	return $this->hasOne('App\Models\AcademicYearModel','id','academic_year');
    }
    public function get_level()
    {
    	return $this->hasOne('App\Models\LevelModel','id','level_id');
    }
    public function get_education_board()
    {
    	return $this->hasOne('App\Models\EducationalBoardModel','id','educational_board');
    }
}
