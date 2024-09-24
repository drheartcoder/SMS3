<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeesTransactionModel extends Model
{
    protected $fillable = [
    						'order_no',
    						'transaction_id',
                            'school_fees_id',
                            'student_id',
                            'fees_type',
                            'payment_type',
                            'months',
                            'amount',
                            'academic_year_id',
                            'school_id'
                        ];
    protected $table = 'tbl_fees_transaction';

    public function get_transaction_details(){
        return $this->hasOne('App\Models\TransactionDetailsModel','id','transaction_id');
    }

    public function get_main_fees(){
        return $this->hasOne('App\Models\FeesSchoolModel','id','school_fees_id');
    }

    public function get_bus_fees(){
        return $this->hasOne('App\Models\BusStudentsModel','id','school_fees_id');
    }

    public function get_club_fees(){
        return $this->hasOne('App\Models\ClubStudentsModel','id','school_fees_id');
    }
}
