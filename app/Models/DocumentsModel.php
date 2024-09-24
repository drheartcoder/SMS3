<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentsModel extends Model
{
    protected $table 				= 'tbl_documents';
    protected $fillable 			= [
			                            'school_id',
			                            'parent_id',
			                            'student_id',
			                            'level_class_id',
			                            'academic_year_id',
			                            'document_title',
                                        'document_name'
			                          ];

    public function get_student_details()
    {
    	return $this->hasOne('App\Models\UserModel','id','student_id');
    }
 	public function get_level_class_details()
    {
    	return $this->hasOne('App\Models\LevelClassModel','id','level_class_id');
    }
       
}
