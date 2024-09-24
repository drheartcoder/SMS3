<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CanteenTransactionsModel extends Model
{
    protected $table = "tbl_canteen_transaction";

    protected $fillable = [ 'cust_id',
    						'staff_id',
                            'transaction_id',
                            'order_no',
                            'paid_date',
                        	'amount'];


}
