<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSmsTemplateTranslationModel extends Model
{
    protected $table    = 'tbl_school_sms_template_translation';
    protected $fillable = ['school_sms_template_id','template_subject', 'template_html', 'locale'];
}
