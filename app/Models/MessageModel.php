<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageModel extends Model
{
    protected $table = "tbl_messages";
    protected $fillable = [
    						"school_id",
    						"from_user_id",
    						"to_user_id",
    						"text_message",
    						"message_date",
    						'message_time',
                            'is_read'
    						];

    public function get_form_user_details(){
    	return $this->hasOne('App\Models\UserModel','id','from_user_id');	
    }   						
}
