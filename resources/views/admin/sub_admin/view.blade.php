@extends('admin.layout.master')                
@section('main_content')

<style type="text/css">
  .ui-autocomplete
  {
    max-width: 26% !important;
  }
  .mass_min {
    background: #fcfcfc none repeat scroll 0 0;
    border: 1px dashed #d0d0d0;
    float: left;
    margin-bottom: 20px;
    margin-right: 21px;
    margin-top: 10px;
    padding: 5px;
  }
  .mass_addphoto {
    display: inline-block;
    margin: 0 10px;
    padding-top: 27px;
    text-align: center;
    vertical-align: top;
  }
  .mass_addphoto {
    text-align: center;
  }
  .upload_pic_btn {
    cursor: pointer;
    font-size: 14px;
    height: 100% !important;
    left: 0;
    margin: 0;
    opacity: 0;
    padding: 0;
    position: absolute;
    right: 0;
    top: 0;
  }
</style>

<!-- BEGIN Page Title -->
<div class="page-title">
  <div>
  </div>
</div>
<!-- END Page Title -->
<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home">
      </i>
      <a href="{{ url($admin_panel_slug.'/dashboard') }}">Dashboard
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
<!-- BEGIN Main Content -->
<div class="row">
  <div class="col-md-12">
    <div class="box ">
      <div class="box-title">
        <h3>
          <i class="fa fa-eye">
          </i> {{ $page_title or '' }} 
        </h3>
        <div class="box-tool">
        </div>
      </div>
      <div class="box-content">
        <?php
            
            $first_name    = isset($arr_user['first_name']) ?$arr_user['first_name']:"";
            $last_name     = isset($arr_user['last_name']) ?$arr_user['last_name']:"";
            $name = $first_name.' '.$last_name;

            $contact_number= isset($arr_user['mobile_no']) ?$arr_user['mobile_no']:"";
            $email         = isset($arr_user['email']) ?$arr_user['email']:"";
            $profile_image = isset($arr_user['profile_image']) ?$arr_user['profile_image']:"";

            $country       = isset($arr_user['country_details']['country_name']) ?$arr_user['country_details']['country_name']:"";
            $state         = isset($arr_user['state_details']['state_name']) ?$arr_user['state_details']['state_name']:"";
            $city          = isset($arr_user['city_details']['city_name']) ?$arr_user['city_details']['city_name']:"";
            $address       = isset($arr_user['address']) ?$arr_user['address']:"";

            $business_logo         = isset($arr_user['business_logo']) ?$arr_user['business_logo']:"";
            $business_name         = isset($arr_user['business_name']) ?$arr_user['business_name']:"";
            $business_website      = isset($arr_user['business_website']) ?$arr_user['business_website']:"";
            $industry_affilliation = isset($arr_user['industry_affilliation']) ?$arr_user['industry_affilliation']:"";
            $facebook_link         = isset($arr_user['facebook_link']) ?$arr_user['facebook_link']:"";
            $g_plus_link           = isset($arr_user['g_plus_link']) ?$arr_user['g_plus_link']:"";
            $tweeter_link          = isset($arr_user['tweeter_link']) ?$arr_user['tweeter_link']:"";
            $linked_in_link        = isset($arr_user['linked_in_link']) ?$arr_user['linked_in_link']:"";
            $awards                = isset($arr_user['awards']) ?$arr_user['awards']:"";
            $business_description  = isset($arr_user['description']) ?$arr_user['description']:"";
        ?>
        
        <div class="box">
          <div class="box-content studt-padding">
            <div class="row">
                <div class="col-md-8">
                <h3>{{$module_title or ''}} Personal Information</h3>
                <br>
                    <table class="table table-bordered">
                      <tbody>

                            <tr>
                              <th style="width: 30%">Profile Image
                              </th>
                              <td>
                                <img alt="pic" src="{{ get_resized_image($profile_image,config('app.project.img_path.user_profile_images') , 150,200) }}" />
                              </td>
                            </tr>

                            <tr>
                              <th style="width: 30%">Name
                              </th>
                              <td>
                                {{$name or ''}}
                              </td>
                            </tr> 

                            <tr>
                              <th style="width: 30%">Email 
                              </th>
                              <td>
                                {{$email or ''}}
                              </td>
                            </tr>
                            
                            <tr>
                              <th style="width: 30%">Mobile Number
                              </th>
                              <td>
                                {{$contact_number or ''}}
                              </td>
                            </tr>

                            {{-- <tr>
                              <th style="width: 30%">Services
                              </th>
                              <td>
                                @if(isset($arr_services) && count($arr_services)>0)
                                  @foreach($arr_services as $key => $value)
                                    @if(isset($arr_subadmin_services) && !empty($arr_subadmin_services))
                                      @foreach($arr_subadmin_services as $service)
                                        @if($value['id']==$service['service_id'])
                                          {{$value['name']}},
                                        @endif
                                      @endforeach
                                    @endif
                                  @endforeach
                                @endif
                              </td>
                            </tr>
                            <tr>
                              <th style="width: 30%">Address
                              </th>
                              <td>
                                {{ $address or '' }}
                              </td>
                            </tr> --}}

                    </tbody>
                  </table>  
                  <center><a class="btn" href="{{ url($module_url_path) }}">Back</a></center>
                </div> 

              </div>

          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- END Main Content --> 
  @endsection
