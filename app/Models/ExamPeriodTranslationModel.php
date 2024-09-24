<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamPeriodTranslationModel extends Model
{
    protected $fillable = [
                    		'exam_id',
                            'exam_name',
                            'locale'
                        ];
    protected $table = 'tbl_exam_period_translation';
}