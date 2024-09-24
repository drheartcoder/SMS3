<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryContentModel extends Model
{
    protected $table = "tbl_library_contents";

    protected $fillable = [
                            'school_id',
                            'category_id',
                            'type',
                            'purchase_date',
                            'bill_no'
                          ];


}
