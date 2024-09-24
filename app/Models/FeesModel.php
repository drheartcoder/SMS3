<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use \Dimsav\Translatable\Translatable;

use Illuminate\Database\Eloquent\SoftDeletes;

class FeesModel extends Model
{
    use SoftDeletes;
    use Translatable; 

    protected $table  =    'tbl_fees';                
    public $translationModel      = 'App\Models\FeesTranslationModel';
    public $translationForeignKey = 'fees_id';
    public $translatedAttributes  = ['title'];
    
    protected $fillable = [
                            'id', 
                            'is_active'    
                          ];                    
   
}
