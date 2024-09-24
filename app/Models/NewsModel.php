<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsModel extends Model
{
  protected $table    = 'tbl_news';
  protected $fillable = ['news_title','school_id','description','added_date_time',
  'publish_date','end_date','start_time','video_url','end_time','is_published','academic_year_id'];


  public function get_news_media(){
  	return $this->hasMany('App\Models\NewsMediaModel','news_id','id');
  }

}
