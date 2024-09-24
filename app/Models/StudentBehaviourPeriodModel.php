<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use \Dimsav\Translatable\Translatable;


class StudentBehaviourPeriodModel extends Model
{

    protected $table  =    'tbl_student_behaviour_period';         
    protected $fillable = [
                            'period',
                            'school_id',
                            'academic_year_id'  
                          ]; 


}
