<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationsModel extends Model
{
    protected $table 	= 'tbl_notifications';
    protected $fillable = ['user_id','is_read','is_show','user_type','notification_type','title','view_url'];
}
