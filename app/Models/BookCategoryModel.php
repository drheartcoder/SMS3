<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Dimsav\Translatable\Translatable;

class BookCategoryModel extends Model
{
	use SoftDeletes;
    use Rememberable;
    use Translatable;
    protected $table = "tbl_book_categories";

    public $translationModel      = 'App\Models\BookCategoryTranslationModel';
    public $translationForeignKey = 'book_category_id';
    public $translatedAttributes  = ['category_name',
    								 'slug',
                                     'locale'];

    protected $fillable = ['school_id'];


    function get_book_contents(){
        return $this->hasOne('App\Models\LibraryContentModel','category_id','id');
    }
}
