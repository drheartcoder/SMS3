<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class ContactEnquiryModel extends Model
{    
    protected $table = "tbl_contact_enquiry";

    protected $fillable = ['sender_id','category_id','description','email','enquiry_no','contact_number','subject','comments','is_view'];

   /* public function delete()
    {	
    	parent::delete();
    }*/

    public function enquiry_category()
    {
        return $this->hasOne('App\Models\EnquiryCategoryModel','id','category_id');
    }

    public function get_user()
    {
        return $this->hasOne('App\Models\UserModel','id','sender_id');
    } 

    public function get_school_admin()
    {
        return $this->hasOne('App\Models\UserModel','email','email');
    }  

}
