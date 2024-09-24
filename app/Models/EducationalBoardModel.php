<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationalBoardModel extends Model
{
    protected $table = "tbl_educational_board";

    protected $fillable = ['board','school_id','professor','employee','school_admin'];
}
