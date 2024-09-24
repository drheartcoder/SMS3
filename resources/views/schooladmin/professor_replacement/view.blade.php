@extends('schooladmin.layout.master')                
@section('main_content')


<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="{{url('/')}}/school_admin/dashboard">{{translation('dashboard')}}</a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right"></i>
    </span>
    <li>
      <i class="{{$module_icon}}"></i>
      <a href="{{$module_url_path}}">{{$module_title}}</a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right"></i>
    </span>
    <i class=""></i>
    <li class="active">{{$page_title}}</li>
  </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
  <div>
    <h1><i class="{{$module_icon}}"></i>{{str_plural($module_title)}}</h1>

  </div>
</div>
<!-- END Page Title -->


<!-- BEGIN Tiles -->
  <div class="row">
                <div class="col-md-6 col-lg-6 col-md-offset-3 col-lg-offset-3">
                     <div class="pet-stories">
                       @if(!empty($arr_images))  
                       <div id="myCarousel" class="carousel slide" data-ride="carousel">
                          <!-- Indicators -->
                          <ol class="carousel-indicators">
                             @foreach($arr_images as $key2 => $images)
                            <li data-target="#myCarousel" data-slide-to="{{$key2}}" @if($key2==0) class="active" @endif></li>
                            @endforeach
                            <!-- <li data-target="#myCarousel" data-slide-to="1"></li>
                            <li data-target="#myCarousel" data-slide-to="2"></li> -->
                          </ol>

                          <!-- Wrapper for slides -->
                          <div class="carousel-inner">

                            @foreach($arr_images as $key => $images)

                            <?php

                            if(isset($images['media_name']) && ($images['media_name'])!='') 
                            {
                                $fileURL = '';
                                $fileURL = $newsUploadImagePath.'/'.$images['media_name'];

                                if(file_exists($fileURL))
                                {
                                    $survey_img = resize_images_new('uploads/news_media/',$images['media_name'],'880','414');
                                }
                                else
                                {
                                    $survey_img  = url('/').'/uploads/default.png'; 
                                }
                            }else{
                                 $survey_img  = url('/').'/uploads/default.png'; 
                            } ?>
                          <div class="item @if($key==0) active @endif">
                            <img src="{{ $survey_img }}" alt="News Image" class="img-responsive" />
                          </div>
                          @endforeach
 
                          </div>
                          <!-- Left and right controls -->
                          <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                            <span class="icon-img-arw slider-icon-chevron-left"></span>
                            <span class="sr-only">Previous</span>
                          </a>
                          <a class="right carousel-control" href="#myCarousel" data-slide="next">
                            <span class="icon-img-arw slider-icon-chevron-right"></span>
                            <span class="sr-only">Next</span>
                          </a>
                        </div>
                        @endif
                       


                        <div class="social-list-main">
                            <div class="user-list-blo detils">
                                <span><i class="fa fa-calendar"></i></span><span><?php echo getDateFormat($arr_news_data['publish_date']).''.translation('to').' '.getDateFormat($arr_news_data['end_date']);  ?></span>
                            </div>
                            <div class="lable-list-blo border-right-news">
                                <span><i class="fa fa-clock-o"></i></span><span><?php echo getTimeFormat($arr_news_data['start_time']).' - '. getTimeFormat($arr_news_data['end_time']); ?></span>
                            </div>
                           
                        </div>
                        <div class="story-detail more-text">
                           <div class="news-details-title"> {{ $arr_news_data['news_title'] or '' }} </div>
                            <p> {{ $arr_news_data['description'] or '' }} </p>

                            @if(!empty($arr_other))

                            @foreach($arr_other as $links)
                            <?php

                            $document_name = '';
                            if(isset($links['media_name']) && ($links['media_name'])!='') 
                              {
                                  $fileURL = '';
                                  $fileURL = $newsUploadImageBasePath.'/'.$links['media_name'];

                                  if(file_exists($fileURL))
                                  {
                                      $document_name = $newsUploadImagePublicPath.'/'.$links['media_name'];
                                  }
                                  
                              } 
                            if($document_name!=''){  
                              $ext = getFileExtenion($links['media_name']);
                              ?>
                              @if($ext!='mp4' )
                              <div class="icon-links-docs">
                                <div class="title-video-news">{{translation('document')}}</div>
                                <a href="{{$module_url_path.'/download_document/'.base64_encode($links['id'])}}"   title="{{translation('download_document')}}" class="pdf-news-details-link">

                                     @if($ext== 'doc' || $ext=='docx')
                                       <img src="{{url('/')}}/images/news-details-doc.png" alt="" />
                                      @elseif($ext=='pdf' )
                                        <img src="{{url('/')}}/images/pdf-school-news-details.png" alt="" />
                                      @endif
                                  </a>
                            </div>
                            @endif

                         <?php if($ext=='mp4' || $ext == "MP4"){?>

                            <div class="youtube-video-link">
                               <video   controls="controls" preload="none">
                                          <!-- MP4 for Safari, IE9, iPhone, iPad, Android, and Windows Phone 7 -->
                                          <source type="video/mp4" src="{{$document_name }}" />
                                          <!-- Flash fallback for non-HTML5 browsers without JavaScript -->
                                          <object width="217" height="240" type="application/x-shockwave-flash" data="flashmediaelement.swf">
                                              <param name="movie" value="flashmediaelement.swf" />
                                              <param name="flashvars" value="controls=true&file=videos/wellclick1.mp4"/>
                                              <!-- Image as a last resort -->
                                          </object>

                                      </video>
                           </div>
                            <?php } ?>
                           
                           
                            <?php } ?>
                                @endforeach
                            @endif

                           

                          @if($arr_news_data['video_url']!='')
                           <div class="youtube-video-link">
                              <div class="title-video-news">{{translation('youtube_video')}}</div>
                               <iframe src="{{$arr_news_data['video_url']}}" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                           </div>
                          @endif
                           
                          
                        </div>
                    </div>
                </div>              

                 <div class="clearfix"></div>
                              <div class="col-sm-8 col-sm-offset-4 col-lg-9 col-lg-offset-3">
                                  <a href="{{ $module_url_path }}" class="btn btn-primary" style="float: right;margin-top: 20px;" > <i class="fa fa-arrow-left"></i> {{translation('back')}} </a>
                            </div>
                            <div class="clearfix"></div>

                <!-- END Main Content -->

                
            </div>
@endsection
