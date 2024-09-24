<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyModel extends Model
{
  protected $table    = 'tbl_survey';
  protected $fillable = ['school_id','survey_title','survey_description','user_role','start_date','end_date','academic_year_id'];

  	public function get_survey_images()
	{
		return $this->hasMany('App\Models\SurveyImagesModel','survey_id','id');
	}
	public function get_questions()
	{
		return $this->hasMany('App\Models\SurveyQuestionsModel','survey_id','id');
	}
	public function get_questions_answer()
	{
		return $this->hasMany('App\Models\SurveyQuestionsAnswerModel','survey_id','id');
	}
}
