<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubadminServicesModel extends Model
{
    protected $table = 'subadmin_services';
    protected $fillable = ['user_id','service_id'];
}
