<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookCategoryTranslationModel extends Model
{
    protected $table    = 'tbl_book_category_translation';
    protected $fillable = ['book_category_id','category_name','slug','locale'];
}
