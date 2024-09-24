<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyQuestionsAnswerModel extends Model
{
     
    protected $table    = 'tbl_survey_question_answers';
  	protected $fillable = ['survey_id','survey_question_id','from_user_id','answer','user_role'];

  	public function get_survey()
	{
		return $this->hasOne('App\Models\SurveyModel','id','survey_id');
	}
	public function get_survey_question()
	{
		return $this->hasOne('App\Models\SurveyQuestionsModel','id','survey_question_id');
	}
	public function get_form_user()
	{
		return $this->hasOne('App\Models\UserModel','id','from_user_id');
	}
}
