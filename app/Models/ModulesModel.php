<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModulesModel extends Model
{
    use SoftDeletes;
    protected $table	=	"tbl_modules";
    protected $fillable	=	['title','slug','is_active','is_show_in_notification'];

    
}
