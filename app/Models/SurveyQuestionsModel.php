<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyQuestionsModel extends Model
{
  protected $table    = 'tbl_survey_questions';
  protected $fillable = ['survey_id','question_category_id','survey_question','question_options'];

  	public function get_question_type()
	{
		return $this->hasOne('App\Models\QuestionCategoryModel','id','question_category_id');
	}
}
