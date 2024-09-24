@extends('parent.layout.master')    
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
                    <a href="{{url('/')}}/{{$parent_panel_slug}}/dashboard">Dashboard</a>
                </li>
                <span class="divider">
                    <i class="fa fa-angle-right"></i>
                </span> 
                <li class="active"> My Profile</li>
            </ul>
        </div>
        
         
        <!-- END Breadcrumb -->
        <div class="box  box-navy_blue">
            <div class="box-title">
                <h3>My Profile</h3>
                <div class="box-tool">
                </div>
            </div>
            <div class="box-content edit-btns">
               <a href="{{url('/').'/'.$parent_panel_slug}}/edit_profile" title="Edit" class="edit-btn-profile">
                   <i class="fa fa-pencil"></i>
               </a>
               <form method="POST" action="" class="form-horizontal">
               <div class="col-sm-8 col-sm-offset-4 col-lg-8 col-lg-offset-3">
                    <div class="profile-section-block edit-imgprent">
                      
                      @if(isset($arr_data['profile_image']) && !empty($arr_data['profile_image'] && 
                      file_exists($profile_image_base_img_path.'/'.$arr_data['profile_image']) ))
                        <img src="{{$profile_image_public_img_path.'/'.$arr_data['profile_image']}}">
                      @else
                        <img src="{{$profile_image_public_img_path}}/default.png">
                      @endif
                  </div>
                </div>
                <div class="clearfix"></div>
                
                <div class="form-group">
                    <label class="col-sm-3 col-md-4 col-lg-3 control-label">First Name</label>
                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                        <input class="form-control" name="site_name" type="text" value="{{isset($arr_data['first_name'])?$arr_data['first_name']:''}}" disabled />
                    </div>
                </div>
                 <div class="form-group">
                    <label class="col-sm-3 col-md-4 col-lg-3 control-label">Last Name</label>
                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                        <input class="form-control" name="site_name" type="text" value="{{isset($arr_data['last_name'])?$arr_data['last_name']:''}}" disabled />
                    </div>
                </div>
                   <div class="form-group">
                    <label class="col-sm-3 col-md-4 col-lg-3 control-label">Email Address</label>
                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                      <input class="form-control" name="site_name" type="text" value="{{isset($arr_data['email'])?$arr_data['email']:''}}" disabled />
                    </div>
                  </div>
                <div class="form-group">
                    <label class="col-sm-3 col-md-4 col-lg-3 control-label">National ID</label>
                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                        <input class="form-control" name="site_name" type="text" value="{{isset($arr_data['national_id'])?$arr_data['national_id']:''}}" disabled />
                    </div>
                </div>
                 

                  

                  <div class="form-group">
                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">Date of Birth</label>
                        <div class="col-sm-9 col-md-8 col-lg-4 controls">
                            <input class="form-control" name="site_name" type="text" value="{{isset($arr_data['birth_date'])?$arr_data['birth_date']:''}}" disabled />
                        </div>
                  </div>

                  
                  <div class="form-group">
                   <label class="col-sm-3 col-md-4 col-lg-3 control-label">Gender</label>
                   <div class="col-sm-9 col-md-8 col-lg-4 controls">
                           <div class="radio-btns">
                              <div class="radio-btn">
                                 <input type="radio" id="f-option" name="gender" value="Male"  <?php echo($arr_data['gender']=='MALE')?'checked':''; ?> />
                                <label for="f-option">Male</label>
                                <div class="check"></div>
                              </div>
                                 
                             <div class="radio-btn">
                              <input type="radio" id="s-option" name="gender" value="Female"  <?php echo($arr_data['gender']=='FEMALE')?'checked':''; ?> />
                                <label for="s-option">Female</label>
                                <div class="check"><div class="inside"></div></div>
                              </div>
                              </div>
                       </div>
                     </div>

                  <div class="form-group">
                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">Occupation</label>
                        <div class="col-sm-9 col-md-8 col-lg-4 controls">
                            <input class="form-control" name="site_name" type="text" value="{{isset($arr_data['get_parent_details']['occupation'])?$arr_data['get_parent_details']['occupation']:''}}" disabled />
                        </div>
                  </div>  


                 <div class="form-group">
                    <label class="col-sm-3 col-md-4 col-lg-3 control-label">Address</label>
                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                      <input class="form-control" name="site_name" type="text" value="{{isset($arr_data['address'])?$arr_data['address']:''}}" disabled />
                    </div>
                </div> 
                

                  <div class="form-group">
                      <label class="col-sm-3 col-md-4 col-lg-3 control-label">City</label>
                      <div class="col-sm-9 col-md-8 col-lg-4 controls">
                        <input class="form-control" name="site_name" type="text" value="{{isset($arr_data['city'])?$arr_data['city']:''}}" disabled />
                      </div>
                  </div>

                  <div class="form-group">
                      <label class="col-sm-3 col-md-4 col-lg-3 control-label">Country</label>
                      <div class="col-sm-9 col-md-8 col-lg-4 controls">
                        <input class="form-control" name="site_name" type="text" value="{{isset($arr_data['country'])?$arr_data['country']:''}}" disabled />
                      </div>
                  </div>

                    <div class="form-group">
                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">Mobile No.</label>
                        <div class="col-sm-9 col-md-8 col-lg-4 controls">
                           <input class="form-control" name="site_name" type="text" value="{{isset($arr_data['mobile_no'])?$arr_data['mobile_no']:''}}" disabled />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">Qualification Degree</label>
                        <div class="col-sm-9 col-md-8 col-lg-4 controls">
                           <input class="form-control" name="site_name" type="text" value="{{isset($arr_data['get_parent_details']['qualification_degree'])?$arr_data['get_parent_details']['qualification_degree']:''}}" disabled />
                        </div>
                    </div>
                  

                   <div class="form-group">
                      <label class="col-sm-3 col-md-4 col-lg-3 control-label">Alternate Mobile No.</label>
                      <div class="col-sm-9 col-md-8 col-lg-4 controls">
                         <input class="form-control" name="site_name" type="text" value="{{isset($arr_data['get_parent_details']['alternate_mobile_no'])?$arr_data['get_parent_details']['alternate_mobile_no']:''}}" disabled />
                      </div>
                      </div>
                  
                    <div class="form-group">
                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">Relation</label>
                        <div class="col-sm-9 col-md-8 col-lg-4 controls">
                             <input class="form-control" name="site_name" type="text" value="{{isset($arr_data['get_parent_details']['relation'])?$arr_data['get_parent_details']['relation']:''}}" disabled />
                        </div>
                     </div>


                      <div class="form-group">
                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">Status</label>
                        <div class="col-sm-9 col-md-8 col-lg-4 controls">
                             <input class="form-control" name="site_name" type="text" value="{{isset($arr_data['get_parent_details']['marital_status'])?$arr_data['get_parent_details']['marital_status']:''}}" disabled />
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