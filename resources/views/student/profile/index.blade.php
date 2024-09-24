@extends('student.layout.master')    
@section('main_content')
<style type="text/css">
 .profile-img{width: 150px;
height: 150px;
border-radius: 50% !important;
overflow: hidden;
padding: 0;}
.profile-img img{height: 100% !important;width: 100% ;}
</style>

     <!-- BEGIN Breadcrumb -->
        <div id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <a href="student-dashboard.html">{{translation('dashboard')}}</a>
                </li>
                <span class="divider">
                    <i class="fa fa-angle-right"></i>
                </span> 
                <li class="active">{{translation('my_profile')}}</li>
            </ul>
        </div>
        
        <!-- BEGIN Page Title -->
        <div class="page-title new-agetitle student-dashtitl-pg">
            <div>
                <h1> {{translation('profile')}}</h1>
            </div>
        </div>
        <!-- END Page Title -->
        
         
        <!-- END Breadcrumb -->
        <div class="box  box-navy_blue">
            <div class="box-title">
                <h3>{{translation('my_profile')}}</h3>
                <div class="box-tool">
                </div>
            </div>
            <div class="box-content edit-btns">
           
               <a href="{{url($student_panel_slug)}}/edit_profile" title="Edit" class="edit-btn-profile">
                   <i class="fa fa-pencil"></i>
               </a>
               <form method="POST" action="" class="form-horizontal">
               <div class="col-sm-8 col-sm-offset-4 col-lg-8 col-lg-offset-3">
                    <div class="profile-section-block edit-imgprent">
                      @if(isset($arr_data['profile_image']) && !empty($arr_data['profile_image']))
                        <img src="{{$image_path.'/'.$arr_data['profile_image']}}">
                      @else
                        <img src="{{$image_path}}/default.png">
                      @endif
                  </div>
                </div>
                <div class="clearfix"></div>
                
                <div class="form-group">
                    <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('first_name')}}</label>
                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                        <input class="form-control" name="site_name" type="text" value="{{isset($arr_data['first_name'])?$arr_data['first_name']:''}}" disabled />
                    </div>
                </div>
                 <div class="form-group">
                    <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('last_name')}}</label>
                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                        <input class="form-control" name="site_name" type="text" value="{{isset($arr_data['last_name'])?$arr_data['last_name']:''}}" disabled />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('parent')}} {{translation('national_id')}}</label>
                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                       <input class="form-control" name="site_name" type="text" value="{{isset($arr_data['student_details']['parent_national_id'])?$arr_data['student_details']['parent_national_id']:''}}" disabled />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('level')}}</label>
                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                        <input class="form-control" name="site_name" type="text" placeholder="{{translation('enter')}} {{translation('level')}}"  value="{{isset($arr_data['student_details']['get_level_class']['get_level']['level_name'])?$arr_data['student_details']['get_level_class']['get_level']['level_name']:''}}" disabled />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('class')}}</label>
                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                        <input class="form-control" name="site_name" type="text" placeholder="{{translation('enter')}} {{translation('class')}}"  value="{{isset($arr_data['student_details']['get_level_class']['get_class']['class_name'])?$arr_data['student_details']['get_level_class']['get_class']['class_name']:''}}" disabled />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('student_id')}}</label>
                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                       <input class="form-control" name="site_name" type="text" value="{{isset($arr_data['student_details']['student_no'])?$arr_data['student_details']['student_no']:''}}" disabled />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('national_id')}}</label>
                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                        <input class="form-control" name="site_name" type="text" value="{{isset($arr_data['national_id'])?$arr_data['national_id']:''}}" disabled />
                    </div>
                </div>
                 <div class="form-group">
                 <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('gender')}}</label>
                 <div class="col-sm-9 col-md-8 col-lg-4 controls">
                         <div class="radio-btns">
                            <div class="radio-btn">
                               <input type="radio" id="f-option" name="gender" value="Male"  <?php echo($arr_data['gender']=='MALE')?'checked':''; ?> />
                              <label for="f-option">{{translation('male')}}</label>
                              <div class="check"></div>
                            </div>
                               
                           <div class="radio-btn">
                            <input type="radio" id="s-option" name="gender" value="Female"  <?php echo($arr_data['gender']=='FEMALE')?'checked':''; ?> />
                              <label for="s-option">{{translation('female')}}</label>
                              <div class="check"><div class="inside"></div></div>
                            </div>
                            </div>
                     </div>
                   </div>
                   <div class="form-group">
                      <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('telephone_number')}}</label>
                      <div class="col-sm-9 col-md-8 col-lg-4 controls">
                         <input class="form-control" name="site_name" type="text" value="{{isset($arr_data['telephone_no'])?$arr_data['telephone_no']:''}}" disabled />
                      </div>
                      </div>
                    <div class="form-group">
                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('mobile_no')}}</label>
                        <div class="col-sm-9 col-md-8 col-lg-4 controls">
                           <input class="form-control" name="site_name" type="text" value="{{isset($arr_data['mobile_no'])?$arr_data['mobile_no']:''}}" disabled />
                        </div>
                        </div>
                     <div class="form-group">
                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('birth_date')}}</label>
                        <div class="col-sm-9 col-md-8 col-lg-4 controls">
                            <input class="form-control" name="site_name" type="text" value="{{isset($arr_data['birth_date'])?$arr_data['birth_date']:''}}" disabled />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('email')}}</label>
                        <div class="col-sm-9 col-md-8 col-lg-4 controls">
                             <input class="form-control" name="site_name" type="text" value="{{isset($arr_data['email'])?$arr_data['email']:''}}" disabled />
                        </div>
                     </div>
                 
    
                 <div class="form-group">
                    <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('address')}}</label>
                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                      <input class="form-control" name="site_name" type="text" value="{{isset($arr_data['address'])?$arr_data['address']:''}}" disabled />
                    </div>
                </div> 
                <div class="form-group">
                      <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('country')}}</label>
                      <div class="col-sm-9 col-md-8 col-lg-4 controls">
                        <input class="form-control" name="site_name" type="text" value="{{isset($arr_data['country'])?$arr_data['country']:''}}" disabled />
                      </div>
                  </div>

                  <div class="form-group">
                      <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('city')}}</label>
                      <div class="col-sm-9 col-md-8 col-lg-4 controls">
                        <input class="form-control" name="site_name" type="text" value="{{isset($arr_data['city'])?$arr_data['city']:''}}" disabled />
                      </div>
                  </div>
                <div class="form-group">
                    <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('special_note')}}</label>
                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                        <input class="form-control" name="site_name" type="text" placeholder="Enter Special Notes" value="{{isset($arr_data['special_note'])?$arr_data['special_note']:''}}" disabled />
                    </div>
                </div>
                <div class="clearfix"></div>
                </form>
                

          
            <div class="col-sm-12 col-md-12 col-lg-12">
             <div class="map-block">
               <iframe src="https://www.google.com/maps/embed/v1/place?q={{$arr_data['address']}}&amp;key={{config('constants.GOOGLE_API_KEY')}}" width="100%" height="400" frameborder="0" style="border:0" allowfullscreen></iframe>
             </div>
          </div>
          
          
           <div class="clearfix"></div>
           
            </div>
        </div>
<!-- END Main Content --> 
@endsection

