<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyImagesModel extends Model
{
    protected $table    = 'tbl_survey_images';
  	protected $fillable = ['survey_id','survey_image'];
}
