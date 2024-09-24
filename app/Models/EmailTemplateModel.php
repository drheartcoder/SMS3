<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class EmailTemplateModel extends Model
{
	use Rememberable;

    protected $table   =  'tbl_email_template';

    protected $fillable = [
                            'template_name',
                            'template_from',
                            'template_from_mail',
                            'template_variables',
                            'template_subject',
                            'template_html'
                          ];
                
    public function template_details()
    {
        return $this->hasMany('App\Models\EmailTemplateTranslationModel','email_template_id','id');
    }                      
}
