<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Session;

class TransactionDetailsModel extends Model
{
    protected $table = "tbl_transaction_details";

    protected $fillable = ['order_no',
    						'transaction_type',
    						'bank_name',
    						'cheque_no',
    						'account_holder_name',
    						'receipt_image',
    						'approval_status',
    						'txn_id',
    						'txn_status',
    						'tx_date',
                            'student_id',
                            'payment_done_by',
                            'payment_date',
                            'rejection_reason',
    						'amount',
                            'school_id',
                            'academic_year_id',
                            'user_no',
                            'type'];

    public function get_transactions(){
        return  $this->hasMany('App\Models\FeesTransactionModel','order_no','order_no');
    }

    public function canteen_transactions(){
        return  $this->hasMany('App\Models\CanteenTransactionsModel','order_no','order_no');
    }

    public function get_student(){
        return  $this->hasOne('App\Models\UserTranslationModel','user_id','student_id')
                                                                ->where('locale',Session::get('locale'));
                                                                
    }

    public function get_parent(){
        return  $this->hasOne('App\Models\UserTranslationModel','user_id','payment_done_by')
                                                                ->where('locale',Session::get('locale'));
    }                        
}
