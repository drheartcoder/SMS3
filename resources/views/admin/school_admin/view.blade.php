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



<div class="page-title new-agetitle">
    <div>
        <h1><i class="fa fa-user"></i> {{ isset($module_title)?$module_title:"" }}</h1>
    </div>
</div>
<!-- END Page Title -->

<!-- BEGIN Main Content -->
{{-- <div class="row"> --}}

   <div class="box {{ $theme_color }}">
      <div class="box-title">
         <h3>
            <i class="fa fa-list"></i>
           {{translation('personal_details')  }}
         </h3>
         <div class="box-tool">
            <a data-action="collapse" href="#"></a>
            <a data-action="close" href="#"></a>
         </div>
      </div>
      
        <?php

          $special_note           = isset($arr_data['users']['special_note'])       ?$arr_data['users']['special_note']:"-";
          $first_name             = isset($arr_data['users']['first_name'])         ?$arr_data['users']['first_name']:"-";
          $last_name              = isset($arr_data['users']['last_name'])          ?$arr_data['users']['last_name']:"-";
          $name                   = $first_name.' '.$last_name;
          $email                  = isset($arr_data['users']['email'])              ?$arr_data['users']['email']:"-";
          $date_of_birth          = isset($arr_data['users']['birth_date'])         ?$arr_data['users']['birth_date']:"-";
          $birth_date             = explode('-', $date_of_birth);
          $gender                 = isset($arr_data['users']['gender'])             ?$arr_data['users']['gender']:"-";
          $mobile_no              = isset($arr_data['users']['mobile_no'])          ?$arr_data['users']['mobile_no']:"-";
          $telephone_no           = isset($arr_data['users']['telephone_no'])       ?$arr_data['users']['telephone_no']:"-";
          $address                = isset($arr_data['users']['address'])            ?$arr_data['users']['address']:"-";
          $city                   = isset($arr_data['users']['city'])               ?$arr_data['users']['city']:"-";
          $country                = isset($arr_data['users']['country'])            ?$arr_data['users']['country']:"-";
          $national_id            = isset($arr_data['users']['national_id'])        ?$arr_data['users']['national_id']:"-";

          $board                  = isset($arr_data[$role]['educational_board'])    ?$arr_data[$role]['educational_board']:"-";
          $parent_national_id     = isset($arr_data[$role]['parent_national_id'])   ?$arr_data[$role]['parent_national_id']:"-";
          $alt_mobile_no          = isset($arr_data[$role]['alternate_mobile_no'])  ?$arr_data[$role]['alternate_mobile_no']:"-";
          $occupation             = isset($arr_data[$role]['occupation'])           ?$arr_data[$role]['occupation']:"-";
          $qualification_degree   = isset($arr_data[$role]['qualification_degree']) ?$arr_data[$role]['qualification_degree']:"-";
          $relation               = isset($arr_data[$role]['relation'])             ?$arr_data[$role]['relation']:"-";
          $marital_status         = isset($arr_data[$role]['marital_status'])       ?$arr_data[$role]['marital_status']:"-";
          $yr_of_exp              = isset($arr_data[$role]['year_of_experience'])   ?$arr_data[$role]['year_of_experience']:"-";
          $no                     = isset($arr_data[$role][$role.'_no'])            ?$arr_data[$role][$role.'_no']:"-";
          $joining_data           = isset($arr_data[$role]['joining_date'])         ?$arr_data[$role]['joining_date']:"-";
          $joining_date           = explode(' ',$joining_data);
          $joining                = explode('-',$joining_date[0]);
          $subject                = isset($arr_data[$role]['subject_id'])           ?$arr_data[$role]['subject_id']:"-";
          $role_type              = isset($arr_data[$role]['user_role'])            ?$arr_data[$role]['user_role']:"-";
          $license                = isset($arr_data[$role]['license_no'])           ?$arr_data[$role]['license_no']:"-";
          $admission_date         = isset($arr_data[$role]['admission_date'])       ?$arr_data[$role]['admission_date']:"-";
          $admission              = explode('-', $admission_date); 
          $admission_no           = isset($arr_data[$role]['admission_no'])         ?$arr_data[$role]['admission_no']:"-";
          $level_name             = isset($arr_data['level']['level_name'])         ?$arr_data['level']['level_name']:"-";
          $school_name            = isset($arr_data[$role]['school_name'])          ?$arr_data[$role]['school_name']:"-";

        ?>
          <div class="clearfix"></div>
          <div class="box-content view-details-seciton-main details-section-main-block">
            <div class="row">
              <div class="col-md-12" >
                  {{-- <img src="{{url('/')}}/uploads/profile_images/{{$profile_image}}" height="150px" width="50%%" style="margin-left: 50px;margin-top: 20px"> --}}

                  <div class="">
                    @if(isset($arr_data['users']['profile_image']) && $arr_data['users']['profile_image'] != '')
                      <img src="{{$user_profile_public_img_path.$arr_data['users']['profile_image'] }}" height="150px" width="150px" style="margin: 20px 0;">
                    @else
                      <img src="{{url('/').'/uploads/profile_image/default-profile.png' }}" height="150px" width="150px" style="margin: 20px 0;">
                    @endif
                  </div>
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
                                 <label class="control-label"><b> {{translation('email')}}  </b>: </label>
                                 <div class="controls">
                                    {{$email or ''}}
                                 </div>
                                 <div class="clearfix"></div>
                              </div>

                              <div class="form-group">
                                 <label class="control-label"><b> {{translation('birth_date')}}  </b>: </label>
                                 <div class="controls">
                                    {{$birth_date[2].'-'.$birth_date[1].'-'.$birth_date[0]}}
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
                              <div class="form-group">
                                 <label class="control-label"> <b>{{translation('country')}}</b> :</label>
                                 <div class="controls">
                                    {{$country or ''}}
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
                            </div>
                        </div>
                    </div>
                  

                  </div>
                </div>
            </div>
          </div>

        @if(isset($arr_data['school']) && count($arr_data['school'])>0)
          <div class="col-md-12">
            <div class="row">
            <div class="clearfix"></div>
              <div class="col-md-12">
                <div class="user-details-section-main">
                  <div class="details-infor-section-block">
                      {{translation('school_details')}} 
                  </div>
                   <?php $value =''?>
                   
                
                   @if(isset($arr_data['template']) && count($arr_data['template'])>0)
                      @foreach($arr_data['template'] as $template)
                      
                        @foreach($arr_data['school'] as $data)
                          @if($template['id'] == $data['school_template_id'])
                            <?php 
                              $value = isset($data['value'])?$data['value']: '-' ?>
                          @endif
                        @endforeach
                          <div class="row">
                            <div class="col-md-12" >
                            <div class="form-group">
                             
                                <div class="col-md-3" >
                                  @if(strcmp(strslug($template['title']),'school_logo')==0)
                                    @if($value)
                                      <img src="{{$user_profile_public_img_path.$value}}" height="150px" width="150px" style="margin: 20px 0;">
                                    @else
                                      <img src="{{$user_profile_public_img_path}}/default.png"  height="150px" width="150px" style="margin-left: 20px 0;">
                                    @endif  
                                  @endif
                                </div>
                              
                                <div class="col-md-9">
                                  @if(strcmp(strslug($template['title']),'school_logo') != 0)
                                  
                                    <label class="control-label"><b> {{$template['title'] }} </b>: </label>
                                    <div class="controls">
                                        {{$value}}  
                                    </div>
                                  @endif
                                </div>
                            </div>
                            <div class="clearfix"></div>
                          </div>
                          </div>
                      @endforeach 
                    @endif
                </div>
            </div>
          </div>
        </div>
      @endif
            

      <div class="form-group">
     <div class="col-sm-9 col-lg-12 controls">
        <a href="{{ $module_url_path }}" class="btn btn-primary" style="float: right;" > <i class="fa fa-arrow-left"></i> {{translation('back')}} </a>
     </div>
  </div><div class="clearfix"></div>

    
<!-- END Main Content -->
@stop

