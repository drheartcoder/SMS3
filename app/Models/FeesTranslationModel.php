<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeesTranslationModel extends Model
{
    protected $fillable = [
                    		'fees_id',
                            'title'
                        ];
    protected $table = 'tbl_fees_translation';
}