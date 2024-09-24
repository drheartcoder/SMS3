<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSettingModel extends Model
{
    protected $table = 'tbl_payment_setting';
        
    protected $fillable = [ 
    						'enable_wire_transfer',
    						'beneficiary_bank_name',
                            'beneficiary_bank_address',
    						'account_name',
    						'account_number',
    						'swift_address',
    						'bank_code',
    						'comment',
    						'enable_paypal',
    						'transfer_sort_order_of_display',
                            'paypal_sort_order_of_display',
                            'cheque_sort_order_of_display',
                            'cash_sort_order_of_display',
    						'email',
    						'debug_email',
                            'mid',
                            'merchant_key',
    						'beneficiary_bank_address',
                            'user_id',
                            'enable_cheque_transfer',
                            'cheque_payee_name',
                            'enable_cash_transfer'

    					];
}
