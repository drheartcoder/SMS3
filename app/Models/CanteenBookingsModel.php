<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CanteenBookingsModel extends Model
{
    protected $table = "tbl_canteen_booking";

    protected $fillable = ['school_id',
                            'customer_id',
                            'staff_id',
                            'order_no',
                            'user_role',
                            'order_type',
                            'booking_date',
                            'total_price',
                            'delivery_status',
                            'payment_status',
                            'customer_national_id',
                            'academic_year_id'];


    public function get_user_details()
    {
        return $this->hasOne('App\Models\UserModel','id','customer_id');
    }

    public function get_bookings_details()
    {
        return $this->hasMany('App\Models\CanteenBookingDetailModel','booking_id','id');
    }


}
