<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSettingsModel extends Model
{
    protected $table = 'tbl_notification_setting';
 	
    protected $fillable = [
                            'user_id',
                            'role_id',
                            'notification_permission',
                            'school_id'
    					  ];
}
