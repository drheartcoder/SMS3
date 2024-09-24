<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class SchoolEmailTemplateModel extends Model
{
	use Rememberable;

    protected $table   =  'tbl_school_email_template';

    protected $fillable = [
                            'template_name',
                            'is_enabled',
                            'slug',
                            'school_id',
                            'template_from',
                            'template_from_mail',
                            'template_variables',
                            'template_subject',
                            'template_html'
                          ];           
}
