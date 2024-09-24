<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsMediaModel extends Model
{
  protected $table    = 'tbl_news_media';
  protected $fillable = ['news_id','media_name','media_type'];

}
