<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsTemplateModel extends Model
{

    protected $table   =  'tbl_sms_template';                                
    protected $fillable = [
                            'school_id',
                            'is_enabled',
                            'template_slug',
                            'template_variables',
                            'template_subject',
                            'template_html'
                          ];
                                  
}
