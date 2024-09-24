@extends('schooladmin.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home">
      </i>
      <a href="{{ url($school_admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}}
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
        <h1><i class="fa fa-users"></i>{{$page_title}}</h1>
    </div>
</div>
<!-- END Page Title -->



<!-- BEGIN Main Content -->
{{-- <div class="row"> --}}

   <div class="box {{ $theme_color }}">
      <div class="box-title">
         <h3>
            <i class="fa fa-list"></i>
           Personal Details  
         </h3>
         
      </div>
      
        <?php
     
        $no   = $role.'no';
          if($arr_data['users']['profile_image'] == "")
          {
            $profile_image = "default-profile.png"; 
          }
          else
          {
            $profile_image = $arr_data['users']['profile_image'];
          }
          
          $special_note    = isset($arr_data['users']['special_note']) ?$arr_data['users']['special_note']:"-";
          $parent_national_id    = isset($arr_data[$role]['parent_national_id']) ?$arr_data[$role]['parent_national_id']:"-";
          $first_name    = isset($arr_data['users']['first_name']) ?$arr_data['users']['first_name']:"-";
          $last_name     = isset($arr_data['users']['last_name']) ?$arr_data['users']['last_name']:"-";
          $name = $first_name.' '.$last_name;
          $email         = isset($arr_data['users']['email']) ?$arr_data['users']['email']:"-";
          $birth_date    = isset($arr_data['users']['birth_date']) ?$arr_data['users']['birth_date']:"-";
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
          <div class="box-content">
            <div class="row">
              <div class="col-md-3" >
                  <img src="{{url('/')}}/uploads/profile_image/{{$profile_image}}" height="150px" width="50%%" style="margin-left: 50px;margin-top: 20px">
              </div>
                <div class="col-md-9">
                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('name')}} </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$name or ''}}
                     </div>
                  </div><div class="clearfix"></div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation($role.'_number')}} </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$no or ''}}
                     </div>
                  </div><div class="clearfix"></div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('email')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$email or ''}}
                     </div>
                  </div><div class="clearfix"></div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('birth_date')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$birth_date or ''}}
                     </div>
                  </div><div class="clearfix"></div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('gender')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$gender or ''}}
                     </div>
                  </div><div class="clearfix"></div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('mobile_no')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$mobile_no or ''}}
                     </div>
                  </div><div class="clearfix"></div>
                  
                  @if($role == 'parent')
                   <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"> <b>{{translation('alternate_mobile_no')}}</b> :  </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$alt_mobile_no or ''}}
                     </div>
                  </div><div class="clearfix"></div>
                  @endif

                   @if($role == 'student')
                   <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"> <b>{{translation('special_note')}}</b> : </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$special_note or ''}}
                     </div>
                  </div><div class="clearfix"></div>
                  @endif

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"> <b>{{translation('telephone_number')}}</b> :</label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$telephone_no or ''}}
                     </div>
                  </div><div class="clearfix"></div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"> <b>{{translation('address')}}</b> :</label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$address or ''}}
                     </div>
                  </div><div class="clearfix"></div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"> <b>{{translation('city')}}</b> :</label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$city or ''}}
                     </div>
                  </div><div class="clearfix"></div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"> <b>{{translation('national_id')}}</b> :</label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$national_id or ''}}
                     </div>
                  </div><div class="clearfix"></div>
                
                  @if($role == 'student')
                    <div class="form-group">
                       <label class="col-sm-4 col-lg-4 control-label"> <b>{{translation('parent_national_id')}}</b> :</label>
                       <div class="col-sm-9 col-lg-4 controls">
                          {{$parent_national_id or ''}}
                       </div>
                    </div><div class="clearfix"></div>
                  @endif
                
                
                    @if($role == 'parent')
                      <div class="form-group">
                       <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('occupation')}} </b> : </label>
                       <div class="col-sm-9 col-lg-4 controls">
                          {{$occupation or ''}}
                       </div>
                      </div><div class="clearfix"></div>
                    @endif
{{-- 
                    @if($role == 'professor')
                      <div class="form-group">
                       <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('subject_teach')}} </b>: </label>
                       <div class="col-sm-9 col-lg-4 controls">
                          {{$subject or ''}}
                       </div>
                      </div><div class="clearfix"></div>
                    @endif
 --}}
                    @if($role == 'parent' || $role == 'professor')
                    <div class="form-group">
                       <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('qualification_degree')}}  </b>: </label>
                       <div class="col-sm-9 col-lg-4 controls">
                          {{$qualification_degree or ''}}
                       </div>
                    </div><div class="clearfix"></div>
                    @endif

                    @if($role == 'parent')
                    <div class="form-group">
                       <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('relation')}}  </b>: </label>
                       <div class="col-sm-9 col-lg-4 controls">
                          {{$relation or ''}}
                       </div>
                    </div><div class="clearfix"></div>
                    @endif

                    @if($role == 'parent')
                    <div class="form-group">
                       <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('marital_status')}}  </b>: </label>
                       <div class="col-sm-9 col-lg-4 controls">
                          {{$marital_status or ''}}
                       </div>
                    </div><div class="clearfix"></div>
                    @endif

                    @if($role == 'professor')
                    <div class="form-group">
                       <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('year_of_experience')}}  </b>: </label>
                       <div class="col-sm-9 col-lg-4 controls">
                          {{$yr_of_exp or ''}}
                       </div>
                    </div><div class="clearfix"></div>
                    @endif

                    @if($role == 'professor')
                    <div class="form-group">
                       <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('joining_date')}}  </b>: </label>
                       <div class="col-sm-9 col-lg-4 controls">
                          {{$joining_date[0] or ''}}
                       </div>
                    </div><div class="clearfix"></div>
                    @endif
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                    <a href="{{ $module_url_path }}" class="btn btn-primary" style="float: right;" > Back </a>
                </div>
          </div>
            </div>
            
          </div>
      <div class="clearfix"></div>
    
{{-- </div> --}}
<!-- END Main Content -->
@stop

