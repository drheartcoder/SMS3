<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model  as Eloquent;


class NotificationModulesModel extends Eloquent
{
    protected $table	 = 'tbl_notification_modules'; 

	protected $fillable  = ['module_title','role','is_active'];   
}
