<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Dimsav\Translatable\Translatable;


use Illuminate\Database\Eloquent\SoftDeletes;

class EnquiryCategoryModel extends Model
{
    use SoftDeletes;
    use Translatable; 
    
    protected $table = "tbl_contact_enquiry_category";

    public $translationModel      = 'App\Models\EnquiryCategoryTranslationModel';
    public $translationForeignKey = 'enquiry_category_id';
    public $translatedAttributes  = ['title'];


    protected $fillable = ['category_name'];

   /* public function delete()
    {	
    	parent::delete();
    }*/
}
