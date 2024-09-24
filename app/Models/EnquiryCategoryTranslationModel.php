<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EnquiryCategoryTranslationModel extends Model
{
    protected $fillable = [
                    		'enquiry_category_id',
                            'title'
                        ];
    protected $table = 'tbl_enquiry_category_translation';
}