<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class KeywordTranslationModel extends Model
{
	use Rememberable;
	
    protected $table   =  'tbl_keyword_translations';

	protected $fillable = [                    		
                            'keyword', 
                            'title',
                            'locale'
                        ];

          
}
