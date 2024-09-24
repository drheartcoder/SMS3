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
            <i class="fa fa-eye"></i>
           {{translation('driver_details')}}
         </h3>
         <div class="box-tool">
         </div>
      </div>

        <?php

          
          $first_name                 = isset($arr_data['user_details']['first_name']) ?$arr_data['user_details']['first_name']:"-";
          $last_name                  = isset($arr_data['user_details']['last_name']) ?$arr_data['user_details']['last_name']:"-";
          $name                       = ucfirst($first_name).' '.ucfirst($last_name);
          $email                      = isset($arr_data['user_details']['email']) ?$arr_data['user_details']['email']:"-";
          $birth_date                 = isset($arr_data['user_details']['birth_date']) ?$arr_data['user_details']['birth_date']:"-";
          if($birth_date == '-')
          {
            $date                       = '-';
          }
          else
          {
            $date                       = getDateFormat($birth_date);  
          }
          $gender                     = isset($arr_data['user_details']['gender']) ?$arr_data['user_details']['gender']:"-";
          $mobile_no                  = isset($arr_data['user_details']['mobile_no']) ?$arr_data['user_details']['mobile_no']:"-";
          $telephone_no               = isset($arr_data['user_details']['telephone_no']) ?$arr_data['user_details']['telephone_no']:"-";
          $address                    = isset($arr_data['user_details']['address']) ?$arr_data['user_details']['address']:"-";
          $city                       = isset($arr_data['user_details']['city']) ?$arr_data['user_details']['city']:"-";
          $country                    = isset($arr_data['user_details']['country']) ?$arr_data['user_details']['country']:"-";
          $national_id                = isset($arr_data['user_details']['national_id']) ?$arr_data['user_details']['national_id']:"-";
          $qualification_degree       = isset($arr_data['qualification_degree']) ?$arr_data['qualification_degree']:"-";
          $marital_status             = isset($arr_data['marital_status']) ?$arr_data['marital_status']:"-";
          $yr_of_exp                  = isset($arr_data['year_of_experience']) ?$arr_data['year_of_experience']:"-";
          $no                         = isset($arr_data['employee_no']) ?$arr_data['employee_no']:"-";
          $license_no                 = isset($arr_data['license_no']) ?$arr_data['license_no']:"-";
          
        ?>
          <div class="clearfix"></div>
          <div class="box-content view-details-seciton-main details-section-main-block">
            <div class="row">
                <div class="col-md-12" >
                  @if(isset($arr_data['user_details']['profile_image']) && $arr_data['user_details']['profile_image'] != "")
                    <img src="{{url('/')}}/uploads/profile_image/{{$arr_data['user_details']['profile_image']}}" height="150px" width="150px" style="margin: 20px 0;">
                  @else
                    <img src="{{url('/')}}/uploads/profile_image/default-profile.png" height="150px" width="" style="margin: 20px 0;">
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
                                     <label class="control-label"><b> {{translation('employee_number')}} </b>: </label>
                                     <div class="controls">
                                        {{$no}}
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
                                        {{$national_id }}
                                     </div>
                                     <div class="clearfix"></div>
                                  </div>

                                    <div class="form-group">
                                       <label class="control-label"><b> {{translation('qualification_degree')}}  </b>: </label>
                                       <div class="controls">
                                          {{$qualification_degree}}
                                       </div>
                                       <div class="clearfix"></div>
                                    </div>

                                    <div class="form-group">
                                       <label class="control-label"><b> {{translation('marital_status')}}  </b>: </label>
                                       <div class="controls">
                                          {{$marital_status}}
                                       </div>
                                       <div class="clearfix"></div>
                                    </div>

                                    <div class="form-group">
                                       <label class="control-label"><b> {{translation('year_of_experience')}}  </b>: </label>
                                       <div class="controls">
                                          {{$yr_of_exp}}
                                       </div>
                                       <div class="clearfix"></div>
                                    </div>

                                    <div class="form-group">
                                       <label class="control-label"><b> {{translation('license_number')}}  </b>: </label>
                                       <div class="controls">
                                          {{$license_no}}
                                       </div>
                                       <div class="clearfix"></div>
                                    </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group back-btn-form-block">
                                <div class="controls">
                                    <div class="map-section-block">
                                         <iframe src="https://www.google.com/maps/embed/v1/place?q={{$address}}&amp;key={{config('constants.GOOGLE_API_KEY')}}" width="80%" height="600" frameborder="0" style="border:0" allowfullscreen></iframe>
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
            

      
    
{{-- </div> --}}
<script>
  $(document).ready()
  {
    $(".map-section-block").show();
  }
</script>
<!-- END Main Content -->
@stop

