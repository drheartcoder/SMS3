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
          <a href="{{$module_url_path}}">{{$page_title}}</a>
        </li>

        <span class="divider">
          <i class="fa fa-angle-right"></i>
        </span>
            <i class="fa fa-eye"></i>
            <li class="active">{{$module_title}}</li>
    </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="{{$module_icon}}"></i>{{$page_title}}</h1>

    </div>
</div>
<!-- END Page Title -->

<!-- BEGIN Main Content -->
{{-- <div class="row"> --}}

   <div class="box {{ $theme_color }}">
      <div class="box-title">
         <h3>
            <i class="fa fa-list"></i>
           {{translation('student_details')}}
         </h3>
         <div class="box-tool">
         </div>
      </div>
      
        <?php
    
          $first_name                 = isset($arr_data['get_user_details']['first_name']) ?$arr_data['get_user_details']['first_name']:"-";
          $last_name                  = isset($arr_data['get_user_details']['last_name']) ?$arr_data['get_user_details']['last_name']:"-";
          $name                       = ucfirst($first_name).' '.ucfirst($last_name);
          $level                      = isset($arr_data['get_level_class']['get_level']['level_name']) ?$arr_data['get_level_class']['get_level']['level_name']:"-";
          $class                      = isset($arr_data['get_level_class']['get_class']['class_name']) ?$arr_data['get_level_class']['get_class']['class_name']:"-";
          $email                      = isset($arr_data['get_user_details']['email']) ?$arr_data['get_user_details']['email']:"-";
          $birth_date                 = isset($arr_data['get_user_details']['birth_date']) ?$arr_data['get_user_details']['birth_date']:"-";
          if($birth_date == '-')
          {
            $date                       = '-';
          }
          else
          {
            $date                       = getDateFormat($birth_date);  
          }
          $gender                     = isset($arr_data['get_user_details']['gender']) ?$arr_data['get_user_details']['gender']:"-";
          $mobile_no                  = isset($arr_data['get_user_details']['mobile_no']) ?$arr_data['get_user_details']['mobile_no']:"-";
          $telephone_no               = isset($arr_data['get_user_details']['telephone_no']) ?$arr_data['get_user_details']['telephone_no']:"-";
          $address                    = isset($arr_data['get_user_details']['address']) ?$arr_data['get_user_details']['address']:"-";
          $city                       = isset($arr_data['get_user_details']['city']) ?$arr_data['get_user_details']['city']:"-";
          $country                    = isset($arr_data['get_user_details']['country']) ?$arr_data['get_user_details']['country']:"-";
          $national_id                = isset($arr_data['get_user_details']['national_id']) ?$arr_data['get_user_details']['national_id']:"-";
            
          $no                         = isset($arr_data['student_no']) ?$arr_data['student_no']:"-";
          $role                       = isset($arr_data['user_role']) ?$arr_data['user_role']:"-";

          $parent_first_name          = isset($arr_data['get_parent_details']['first_name']) ?$arr_data['get_parent_details']['first_name']:"-";
          $parent_last_name           = isset($arr_data['get_parent_details']['last_name']) ?$arr_data['get_parent_details']['last_name']:"-";

          $parent_name                = ucfirst($parent_first_name).' '.ucfirst($parent_last_name);
          $parent_email               = isset($arr_data['get_parent_details']['email']) ?$arr_data['get_parent_details']['email']:"-";
          $parent_national_id         = isset($arr_data['get_parent_details']['national_id']) ?$arr_data['get_parent_details']['national_id']:"-";
          $relation                   = isset($arr_data['relation']) ?$arr_data['relation']:"-";
          
        ?>
          <div class="clearfix"></div>
          <div class="box-content view-details-seciton-main details-section-main-block">
            @include('schooladmin.layout._operation_status')
            <div class="row">
                <div class="col-md-12" >
                  @if(isset($arr_data['get_user_details']['profile_image']) && ($arr_data['get_user_details']['profile_image'] == "" || !file_exists($base_url.$arr_data['get_user_details']['profile_image'] )))
                    <img src="{{$image_path}}/default.png"  height="150px" width="150px" style="margin: 20px 0 20px 0;">
                  @else
                    <input type="hidden" name="oldimage" value="{{$arr_data['get_user_details']['profile_image']}}">
                    <img src="{{$image_path.'/'.$arr_data['get_user_details']['profile_image']}}"  height="150px" width="150px" style="margin: 20px 0 20px 0;">
                  @endif                
                </div>

                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="user-details-section-main">
                                <div class="details-infor-section-block">
                                    {{translation('personal_details')}}
                                </div>
                                <div class="form-group">
                                     <label class="control-label"><b> {{translation('name')}} </b>: </label>
                                     <div class="controls">
                                        {{$name}}
                                     </div>
                                     <div class="clearfix"></div>
                                  </div>

                                  <div class="form-group">
                                     <label class="control-label"><b> {{translation('student_number')}} </b>: </label>
                                     <div class="controls">
                                        {{$no}}
                                     </div>
                                     <div class="clearfix"></div>
                                  </div>

                                  <div class="form-group">
                                     <label class="control-label"><b> {{translation('level')}} </b>: </label>
                                     <div class="controls">
                                        {{$level}}
                                     </div>
                                     <div class="clearfix"></div>
                                  </div>

                                  <div class="form-group">
                                     <label class="control-label"><b> {{translation('class')}} </b>: </label>
                                     <div class="controls">
                                        {{$class}}
                                     </div>
                                     <div class="clearfix"></div>
                                  </div>


                                  <div class="form-group">
                                     <label class="control-label"><b> {{translation('email')}}  </b>: </label>
                                     <div class="controls">
                                        {{$email}}
                                     </div>
                                     <div class="clearfix"></div>
                                  </div>

                                  <div class="form-group">
                                     <label class="control-label"><b> {{translation('birth_date')}}  </b>: </label>
                                     <div class="controls">
                                        {{$date}}
                                     </div>
                                     <div class="clearfix"></div>
                                  </div>

                                  <div class="form-group">
                                     <label class="control-label"><b> {{translation('gender')}}  </b>: </label>
                                     <div class="controls">
                                        {{$gender or ''}}
                                     </div>
                                     <div class="clearfix"></div>
                                  </div>                                  
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="user-details-section-main">
                                <div class="details-infor-section-block">
                                    {{translation('contact_details')}}
                                </div>
                                <div class="form-group">
                                     <label class="control-label"><b> {{translation('mobile_no')}}  </b>: </label>
                                     <div class="controls">
                                        {{$mobile_no}}
                                     </div>
                                     <div class="clearfix"></div>
                                  </div>

                                  <div class="form-group">
                                     <label class="control-label"> <b>{{translation('telephone_number')}}</b> :</label>
                                     <div class="controls">
                                        {{$telephone_no}}
                                     </div>
                                     <div class="clearfix"></div>
                                  </div>

                                  <div class="form-group">
                                     <label class="control-label"> <b>{{translation('address')}}</b> :</label>
                                     <div class="controls">
                                        {{$address}}
                                     </div>
                                     <div class="clearfix"></div>
                                  </div>

                                  <div class="form-group">
                                     <label class="control-label"> <b>{{translation('city')}}</b> :</label>
                                     <div class="controls">
                                        {{$city}}
                                     </div>
                                     <div class="clearfix"></div>
                                  </div>

                                  <div class="form-group">
                                     <label class="control-label"> <b>{{translation('country')}}</b> :</label>
                                     <div class="controls">
                                        {{$country }}
                                     </div>
                                     <div class="clearfix"></div>
                                  </div>

                                  <div class="form-group">
                                     <label class="control-label"> <b>{{translation('masar_code')}}</b> :</label>
                                     <div class="controls">
                                        {{$national_id }}
                                     </div>
                                     <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="user-details-section-main">
                                <div class="details-infor-section-block">
                                    {{translation('other_details')}}
                                </div>

                                <div class="form-group">
                                     <label class="control-label"> <b>{{translation('parent_national_id')}}</b> :</label>
                                     <div class="controls">
                                        {{$parent_national_id }}
                                     </div>
                                     <div class="clearfix"></div>
                                </div>

                                <div class="form-group">
                                     <label class="control-label"> <b>{{translation('parent_name')}}</b> :</label>
                                     <div class="controls">
                                        {{$parent_name }}
                                     </div>
                                     <div class="clearfix"></div>
                                </div>

                                <div class="form-group">
                                     <label class="control-label"> <b>{{translation('parent_email')}}</b> :</label>
                                     <div class="controls">
                                        {{$parent_email }}
                                     </div>
                                     <div class="clearfix"></div>
                                </div>

                                <div class="form-group">
                                     <label class="control-label"> <b>{{translation('relation')}}</b> :</label>
                                     <div class="controls">
                                        {{translation(strtolower($relation))}}
                                     </div>
                                     <div class="clearfix"></div>
                                </div>

                                  @if(!empty($arr_data['get_documents']))
                                  <div class="form-group">
                                     <label class="control-label"> <b>{{translation('document')}}</b> :</label>
                                     <div class="controls">
                                        @foreach($arr_data['get_documents'] as $key => $documentRs)

                                          <?php
                                            $document_name = '';
                                            if(isset($documentRs['document_name']) && ($documentRs['document_name'])!='') 
                                              {
                                                  $fileURL = '';
                                                  $fileURL = $student_document_base_img_path.'/'.$documentRs['document_name'];

                                                  if(file_exists($fileURL))
                                                  {
                                                      $document_name = $documentRs['document_name'];
                                                  }
                                                  
                                              } 

                                            ?>
                                            @if(!empty($document_name))
                                              @if($key > 0)
                                                ,&nbsp;
                                              @endif
                                              <a href='{{$module_url_path.'/download_document/'.base64_encode($documentRs['id'])}}'


                                                  title="{{translation('download_document')}}"  > {{ $documentRs['document_title'] or ''}}</a>
                                            @endif
                                         @endforeach
                                     </div>
                                     <div class="clearfix"></div>
                                  </div>
                                  @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                          <div class="form-group back-btn-form-block">
                                <div class="controls">
                                    <div class="map-section-block">
                                         <iframe src="https://www.google.com/maps/embed/v1/place?q={{$address}}&amp;key={{config('constants.GOOGLE_API_KEY')}}" width="100%" height="642" frameborder="0" style="border:0" allowfullscreen></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>                        
                        <div class="col-md-12">
                            <div class="form-group back-btn-form-block">
                               <div class="controls view-btns-edts">
                                  <a href="{{ $module_url_path }}" class="btn btn-primary" style="float: right;" > <i class="fa fa-arrow-left"></i>{{translation('back')}}</a>
                               </div>
                            </div>
                        </div>
                    </div>
                  </div>
                </div>
            </div>
          </div>
      </div>
      </div>
    <script>
  $(document).ready()
  {
    $(".map-section-block").show();
  }
</script>
{{-- </div> --}}
<!-- END Main Content -->
@stop

