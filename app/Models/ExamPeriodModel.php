<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use \Dimsav\Translatable\Translatable;

use Illuminate\Database\Eloquent\SoftDeletes;

class ExamPeriodModel extends Model
{
    use SoftDeletes;
    use Translatable; 

    protected $table  =    'tbl_exam_period';                
    public $translationModel      = 'App\Models\ExamPeriodTranslationModel';
    public $translationForeignKey = 'exam_id';
    public $translatedAttributes  = ['exam_name'];
    
    protected $fillable = [
                            'id', 
                            'is_active',
                            'school_id'    
                          ]; 

    public function get_exam_period(){

        return $this->hasOne('App\Models\ExamPeriodTranslationModel','exam_id','id')->where('locale',\Session::get('locale'));
    }

}
