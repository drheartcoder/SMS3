<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolProfileTranslationModel extends Model
{
    protected $fillable = [
                    		'school_profile_id',
                            'value',
                            'locale' ];
                            
    protected $table = 'tbl_school_profile_translation';
}