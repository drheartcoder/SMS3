<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessorReplacementModel extends Model
{
    protected $table 				= 'tbl_professor_replacement';
    protected $fillable 			= [
			                            'school_id',
			                            'professor_id',
                                        'professor_no',
			                            'from_date',
			                            'to_date',
			                            'academic_year_id'
			                          ];
   
   	public function professor_details()
	{
	    return $this->hasOne('App\Models\UserTranslationModel','user_id','professor_id');
	}
}
