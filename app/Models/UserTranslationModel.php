<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTranslationModel extends Model
{
    protected $table    = 'user_translation';
    protected $fillable = ['user_id','first_name','last_name','locale','city'];
}
