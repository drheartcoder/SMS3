@extends('admin.layout.master')                
@section('main_content')




<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home">
      </i>
      <a href="{{ url($admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}}
      </a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa fa-users faa-vertical animated-hover">
      </i>
      
      <a href="{{ url($module_url_path) }}" class="call_loader">{{ $module_title or ''}}
      </a>
    </span> 
    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa fa-eye">
      </i>
    </span> 
    <li class="active">   {{ $page_title or '' }}
    </li>
  </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
  <div>
      <h1><i class="fa fa-list"></i> {{translation('personal_details')}}</h1>
  </div>
</div>
<!-- END Page Title -->

<!-- BEGIN Main Content -->
{{-- <div class="row"> --}}

   <div class="box {{ $theme_color }}">
      <div class="box-title">
         <h3>
            <i class="fa fa-list"></i>
           {{translation('personal_details')}} 
         </h3>
         <div class="box-tool">
            <a data-action="collapse" href="#"></a>
            <a data-action="close" href="#"></a>
         </div>
      </div>
      
        <?php
     
        $no   = $role.'no';
        $base_url = url('/').config('app.project.img_path.user_profile_images');

          
          if(isset($arr_data['users']['profile_image']) && ($arr_data['users']['profile_image']!='') && file_exists($base_url.$arr_data['users']['profile_image']))
          {
            $profile_image = isset($arr_data['users']['profile_image'])?$arr_data['users']['profile_image']:'';
          }
          else
          {
            $profile_image = "default.png"; 
          }
      
          $special_note    = isset($arr_data['users']['special_note']) ?$arr_data['users']['special_note']:"-";
          $parent_national_id    = isset($arr_data[$role]['parent_national_id']) ?$arr_data[$role]['parent_national_id']:"-";
          $first_name    = isset($arr_data['users']['first_name']) ?$arr_data['users']['first_name']:"-";
          $last_name     = isset($arr_data['users']['last_name']) ?$arr_data['users']['last_name']:"-";
          $name = $first_name.' '.$last_name;
          $email         = isset($arr_data['users']['email']) ?$arr_data['users']['email']:"-";
          $birth_date    = isset($arr_data['users']['birth_date']) && $arr_data['users']['birth_date']!='0000-00-00'?getDateFormat($arr_data['users']['birth_date']):"-";
          $gender        = isset($arr_data['users']['gender']) ?$arr_data['users']['gender']:"-";
          $mobile_no     = isset($arr_data['users']['mobile_no']) ?$arr_data['users']['mobile_no']:"-";
          $alt_mobile_no = isset($arr_data[$role]['alternate_mobile_no']) ?$arr_data[$role]['alternate_mobile_no']:"-";
          $telephone_no  = isset($arr_data['users']['telephone_no']) ?$arr_data['users']['telephone_no']:"-";
          $address       = isset($arr_data['users']['address']) ?$arr_data['users']['address']:"-";
          $city          = isset($arr_data['users']['city']) ?$arr_data['users']['city']:"-";
          $national_id   = isset($arr_data['users']['national_id']) ?$arr_data['users']['national_id']:"-";
          $occupation    = isset($arr_data[$role]['occupation']) ?$arr_data[$role]['occupation']:"-";
          $qualification_degree    = isset($arr_data[$role]['qualification_degree']) ?$arr_data[$role]['qualification_degree']:"-";
          $relation    = isset($arr_data[$role]['relation']) ?$arr_data[$role]['relation']:"-";
          $marital_status    = isset($arr_data[$role]['marital_status']) ?$arr_data[$role]['marital_status']:"-";
          $yr_of_exp    = isset($arr_data[$role]['year_of_experience']) ?$arr_data[$role]['year_of_experience']:"-";
          $joining_data    = isset($arr_data[$role]['joining_date']) ?$arr_data[$role]['joining_date']:"-";
          $no    = isset($arr_data[$role][$role.'_no']) ?$arr_data[$role][$role.'_no']:"-";
          $joining_date = explode(' ',$joining_data);
          $subject    = isset($arr_data[$role]['subject_id']) ?$arr_data[$role]['subject_id']:"-";
          
        ?>
          <div class="clearfix"></div>
          <div class="box-content view-details-seciton-main details-section-main-block">
            <div class="row">
              <div class="col-md-12" >
                  <img src="{{url('/')}}/uploads/profile_image/{{$profile_image}}" height="150px" width="150px" style="margin: 20px 0;">
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
                                        {{$name or ''}}
                                     </div>
                                     <div class="clearfix"></div>
                                  </div>

                                  <div class="form-group">
                                     <label class="control-label"><b> {{translation($role.'_number')}} </b>: </label>
                                     <div class="controls">
                                        {{$no or ''}}
                                     </div>
                                     <div class="clearfix"></div>
                                  </div>

                                  <div class="form-group">
                                     <label class="control-label"><b> {{translation('email')}}  </b>: </label>
                                     <div class="controls">
                                        {{$email or ''}}
                                     </div>
                                     <div class="clearfix"></div>
                                  </div>

                                  <div class="form-group">
                                     <label class="control-label"><b> {{translation('birth_date')}}  </b>: </label>
                                     <div class="controls">
                                        {{$birth_date or ''}}
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
                                        {{$mobile_no or ''}}
                                     </div>
                                     <div class="clearfix"></div>
                                  </div>

                                  @if($role == 'parent')
                                   <div class="form-group">
                                     <label class="control-label"> <b>{{translation('alternate_mobile_no')}}</b> :  </label>
                                     <div class="controls">
                                        {{$alt_mobile_no or ''}}
                                     </div>
                                     <div class="clearfix"></div>
                                  </div>
                                  @endif

                                   @if($role == 'student')
                                   <div class="form-group">
                                     <label class="control-label"> <b>{{translation('special_note')}}</b> : </label>
                                     <div class="controls">
                                        {{$special_note or ''}}
                                     </div>
                                     <div class="clearfix"></div>
                                  </div>
                                  @endif

                                  <div class="form-group">
                                     <label class="control-label"> <b>{{translation('telephone_number')}}</b> :</label>
                                     <div class="controls">
                                        {{$telephone_no or ''}}
                                     </div>
                                     <div class="clearfix"></div>
                                  </div>

                                  <div class="form-group">
                                     <label class="control-label"> <b>{{translation('address')}}</b> :</label>
                                     <div class="controls">
                                        {{$address or ''}}
                                     </div>
                                     <div class="clearfix"></div>
                                  </div>

                                  <div class="form-group">
                                     <label class="control-label"> <b>{{translation('city')}}</b> :</label>
                                     <div class="controls">
                                        {{$city or ''}}
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
                                     <label class="control-label"> <b>{{translation('national_id')}}</b> :</label>
                                     <div class="controls">
                                        {{$national_id or ''}}
                                     </div>
                                     <div class="clearfix"></div>
                                  </div>

                                  @if($role == 'student')
                                    <div class="form-group">
                                       <label class="control-label"> <b>{{translation('parent_national_id')}}</b> :</label>
                                       <div class="controls">
                                          {{$parent_national_id or ''}}
                                       </div>
                                       <div class="clearfix"></div>
                                    </div>
                                  @endif


                                    @if($role == 'parent')
                                      <div class="form-group">
                                       <label class="control-label"><b> {{translation('occupation')}} </b> : </label>
                                       <div class="controls">
                                          {{$occupation or ''}}
                                       </div>
                                       <div class="clearfix"></div>
                                      </div>
                                    @endif
                {{-- 
                                    @if($role == 'professor')
                                      <div class="form-group">
                                       <label class="control-label"><b> {{translation('subject_teach')}} </b>: </label>
                                       <div class="controls">
                                          {{$subject or ''}}
                                       </div>
                                       <div class="clearfix"></div>
                                      </div>
                                    @endif
                 --}}
                                    @if($role == 'parent' || $role == 'professor')
                                    <div class="form-group">
                                       <label class="control-label"><b> {{translation('qualification_degree')}}  </b>: </label>
                                       <div class="controls">
                                          {{$qualification_degree or ''}}
                                       </div>
                                       <div class="clearfix"></div>
                                    </div>
                                    @endif

                                    @if($role == 'parent')
                                    <div class="form-group">
                                       <label class="control-label"><b> {{translation('relation')}}  </b>: </label>
                                       <div class="controls">
                                          {{$relation or ''}}
                                       </div>
                                       <div class="clearfix"></div>
                                    </div>
                                    @endif

                                    @if($role == 'parent')
                                    <div class="form-group">
                                       <label class="control-label"><b> {{translation('marital_status')}}  </b>: </label>
                                       <div class="controls">
                                          {{$marital_status or ''}}
                                       </div>
                                       <div class="clearfix"></div>
                                    </div>
                                    @endif

                                    @if($role == 'professor')
                                    <div class="form-group">
                                       <label class="control-label"><b> {{translation('year_of_experience')}}  </b>: </label>
                                       <div class="controls">
                                          {{$yr_of_exp or ''}}
                                       </div>
                                       <div class="clearfix"></div>
                                    </div>
                                    @endif

                                    @if($role == 'professor')
                                    <div class="form-group">
                                       <label class="control-label"><b> {{translation('joining_date')}}  </b>: </label>
                                       <div class="controls">
                                          {{getDateFormat($joining_date[0]) or ''}}
                                       </div>
                                       <div class="clearfix"></div>
                                    </div>
                                    @endif
                            </div>
                        </div>
                    </div>                  
                  </div>
                   <div class="col-sm-9 col-lg-12 controls">
                        <a href="{{ $module_url_path }}" class="btn btn-primary" style="float: right;" > <i class="fa fa-arrow-left"></i> {{translation('back')}} </a>
                     </div>
                  </div>
                </div>
            </div>

      </div>
      </div>
            

      <div class="form-group">
    
  </div><div class="clearfix"></div>
    
{{-- </div> --}}
<!-- END Main Content -->
@stop

