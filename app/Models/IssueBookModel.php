<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IssueBookModel extends Model
{	
	use SoftDeletes;

    protected $table 				= 'tbl_issue_books';

    protected $fillable 			= [
			                            'library_book_id',
			                            'user_type',
			                            'user_id',
			                            'user_no',
			                            'issue_date',
			                            'due_date',
			                            'status',
			                            'no_of_reissued',
			                            'academic_year_id',
			                            'return_date'
			    					  ];
	public function book_details()
	{
		return $this->hasOne('App\Models\BookDetailsModel','id','library_book_id');
	}

	public function user_details()
	{
		return $this->hasOne('App\Models\UserModel','id','user_id');
	}
}
