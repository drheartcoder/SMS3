<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuggestionCategoriesModel extends Model
{
    protected $table = "tbl_suggestion_categories";

    protected $fillable = ['category'];
}
