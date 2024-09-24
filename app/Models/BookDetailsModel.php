<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookDetailsModel extends Model
{	
	use SoftDeletes;

    protected $table 				= 'tbl_book_details';

    protected $fillable 			= [
			                            'library_content_id',
			                            'book_no',
			                            'title',
			                            'author',
			                            'edition',
			                            'publisher',
			                            'no_of_books',
			                            'available_books',
			                            'shelf_no',
			                            'book_position',
			                            'cost',
			                            'ISBN_no',
			                            'cd_type',
			                            'academic_year_id',

			    					  ];

	public function library_content()
	{
		return $this->hasOne('App\Models\LibraryContentModel','id','library_content_id')->where('school_id',\Session::get('school_id'));
	}

	public function academic_year()
	{
		return $this->hasOne('App\Models\AcademicYearModel','id','academic_year_id');
	}

	
}
