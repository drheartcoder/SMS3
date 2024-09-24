@extends('schooladmin.layout.master')                
@section('main_content')

<style type="text/css">
   .profile-img{width: 130px;
   height: 130px;
   border-radius: 50% !important;
   overflow: hidden;
   padding: 0;}
   .profile-img img{height: 100% !important;width: 100% ;}
</style>

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
        <i class="{{$module_icon}}"></i>
        <li class="active">{{$page_title}}</li>
    </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="{{$module_icon}}"></i>{{$module_title}}</h1>

    </div>
</div>
<!-- END Page Title -->

<!-- BEGIN Main Content -->
         
<div class="row">
<div class="col-md-12">
   <div class="box box-navy_blue">
      <div class="box-title">
         <h3>
            <i class="fa fa-plus-circle">
            </i>{{ isset($page_title)?$page_title:"" }} 
         </h3>
         <div class="box-tool">
         </div>
      </div>
      <div class="box-content">
         @include('schooladmin.layout._operation_status')
         <div class="tobbable">
            <form method="post" onsubmit="return addLoader()"  action="{{$module_url_path}}/store_admission"  class="form-horizontal" id="validation-form1" enctype ='multipart/form-data'>
              {{ csrf_field() }}

                              <br>
                              <div class="row">

                                
                                <div class="form-group-nms">
                                    <div class="col-sm-3 col-lg-2"></div>
                                    <div class="col-sm-12 col-lg-8">{{translation('add').' '.translation('new_admission')}}</div>
                                    <div class="clearfix"></div>
                                </div>


                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label" for="category_name">{{translation('admission_number')}}<i class="red">*</i></label>
                                    <div class="col-sm-9 col-lg-4 controls">
                                        <input class="form-control" name="admission_number" id="admission_number" pattern="[A-Z0-9\- àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" data-rule-required='true' type="text" placeholder="{{translation('admission_number')}}">
                                        <span class="help-block">{{ $errors->first('admission_number') }}</span>
                                    </div>
                                    <div class="col-sm-9 col-lg-4 controls">
                                        <input type="button" class="btn btn-primary" onclick="generateNumber()" value="{{translation('generate_number')}}"></input>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('education_board')}}</label>
                                    <div class="col-sm-9 col-lg-4 controls">
                                        <select name="education_board"  class="form-control" >
                                        @if(count($arr_boards)>0)
                                        <option value="">{{translation('select')}} {{translation('education_board')}}</option>    
                                            @foreach($arr_boards as $boards)
                                                <option value="{{$boards['id']}}">{{$boards['board']}}</option>
                                            @endforeach
                                        @endif
                                        </select>
                                        <span class="help-block">{{ $errors->first('education_board') }}</span> 
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('admission_date')}}</label>
                                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                        <input class="form-control datepikr" name="admission_date" id="datepicker" placeholder="{{translation('admission_date')}}" type="text" style="cursor: pointer;" readonly />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('student_id_code')}}<i class="red">*</i></label>
                                    <div class="col-sm-9 col-lg-4 controls">
                                        <input class="form-control" data-rule-required="true" pattern="[a-zA-Z0-9àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" placeholder="N123" name="national_id" id="national_id" data-rule-maxlength="15" type="text">
                                        <span class="help-block" id="err_student_nationalid"></span>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('parent').' '.translation('national_id')}}<i class="red">*</i></label>
                                    <div class="col-sm-9 col-lg-4 controls">
                                        <input class="form-control" data-rule-required="true" pattern="[a-zA-Z0-9àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" placeholder="N123" name="parent_national_id" id="parent_national_id" type="text">
                                        <span class="help-block" id="err_parent_nationalid"></span>
                                    </div>
                                </div>
                                <hr/>

                                <input type="hidden" name="promote" id="promote" value="no">
                                <input type="hidden" name="user" id="user" value="no">

                                <div class="form-group-nms">
                                    <div class="col-sm-3 col-lg-2"></div>
                                    <div class="col-sm-12 col-lg-8">{{translation('add')}} {{translation('student_details')}}</div>
                                    <div class="clearfix"></div>
                                </div>

                                
                                <div class="col-sm-8 col-sm-offset-4 col-lg-10 col-lg-offset-2">
                                    <div class="fileupload fileupload-new" data-provides="fileupload">
                                          <div class="fileupload-new img-thumbnail profile-img img">
                                             <img src="{{url('/')}}/uploads/profile_image/default.png" height="100px" width="100px">
                                          </div>
                                          <div class="fileupload-preview fileupload-exists img-thumbnail profile-img" ></div>
                                          <div>
                                             <span class="btn btn-default btn-file" style="height:32px;">
                                             <span class="fileupload-new">Select Image</span>
                                             <span class="fileupload-exists">Change</span>
                                             <input type="file"  data-validation-allowing="jpg, png, gif" class="file-input news-image validate-image" name="profile_image" id="image"  /><br>
                                             </span>
                                             <a href="#" id="remove" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
                                          </div>
                                          <i class="red"> {!! image_validate_note(250,250,2000,2000) !!} </i>
                                          <span for="image" id="err-image" class="help-block">{{ $errors->first(' image') }}</span>
                                       </div>
                                       <div class="clearfix"></div>
                                       <div class="col-sm-6 col-lg-5 control-label help-block-red" style="color:#b94a48;" id="err_logo"></div>
                                       <br/>
                                       <div class="col-sm-6 col-lg-5 control-label help-block-green" style="color:#468847;" id="success_logo"></div>
                                </div>
                                

                                <div class="col-sm-6 col-md-6 col-lg-6">
                                    <div class="row">
                                    <div class="form-group">
                                       <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('first_name')}}
                                       <i class="red">*</i>
                                       </label>
                                       <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                          <input type="text" name="first_name" id="first_name" class="form-control" data-rule-required='true' data-rule-pattern="^[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" data-rule-maxlength='255' value="{{old('first_name')}}" placeholder="{{translation('enter_first_name')}}">
                                          <span class='help-block'>{{ $errors->first('first_name') }}
                                          </span>
                                       </div>
                                    </div>

                                    <div class="form-group">
                                       <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('last_name')}}
                                       <i class="red">*
                                       </i>
                                       </label>
                                       <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                          <input type="text" name="last_name" id="last_name" class="form-control" data-rule-required='true' data-rule-pattern="^[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" data-rule-maxlength='255' value="{{old('last_name')}}" placeholder="{{translation('enter_last_name')}}">
                                          <span class='help-block'>{{ $errors->first('last_name') }}
                                          </span>
                                       </div>
                                    </div>

                                       <div class="form-group">
                                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('previous_level')}}<i class="red">*</i></label>
                                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">
                                                  <select name="previous_level" class="form-control" data-rule-required='true'>
                                                    <option value="">{{translation('select_level')}}  </option>
                                                    <option value="0">{{translation('none_of_these')}}  </option>
                                                      @if(isset($arr_levels))
                                                          @foreach($arr_levels as $key => $value)
                                                            <option value="{{$value['level_id']}}">{{$value['level_details']['level_name']}}</option>
                                                          @endforeach
                                                      @endif
                                                  </select>
                                                  <span class='help-block'>{{ $errors->first('level') }}
                                              </div>
                                       </div> 
                                    
                                       <div class="form-group">
                                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('level')}}<i class="red">*</i></label>
                                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">
                                                  <select name="level" id="level" class="form-control level" data-rule-required='true'>
                                                    <option value="">{{translation('select_level')}}  </option>
                                                      @if(isset($arr_levels))
                                                          @foreach($arr_levels as $key => $value)
                                                            <option value="{{$value['level_id']}}">{{$value['level_details']['level_name']}}</option>
                                                          @endforeach
                                                      @endif
                                                  </select>
                                                  <span class='help-block'>{{ $errors->first('level') }}
                                              </div>
                                       </div>
                                    
                                       <div class="form-group">
                                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('class')}}<i class="red">*</i></label>
                                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">
                                                  <select name="class" id="class" class="form-control level-class"  data-rule-required='true'>
                                                    <option value="">{{translation('select_class')}}</option>  
                                                  </select>
                                                  <span class='help-block'>{{ $errors->first('class')}}
                                              </div>
                                       </div>
                                    
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('email')}}  
                                             <i class="red">*</i> 
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                             <input type="text" name="email"  id="email" class="form-control" data-rule-required='true' data-rule-email="true" value="{{old('email')}}" placeholder="{{translation('enter_email')}}">
                                             <span for='email' class="help-block">{{ $errors->first('email') }}</span>
                                             <span id="err_email" style="display: none;color: red"></span>
                                          </div> 
                                       </div>
                                        <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('mobile_no')}}
                                          <i class="red">*
                                          </i>
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                             <input type="text" name="mobile_no" id="mobile_no" class="form-control" data-rule-required='true' data-rule-number='true' data-rule-minlength='10' data-rule-maxlength='14' value="{{old('mobile_no')}}" placeholder="{{translation('enter_mobile_no')}}" data-msg-minlength="Please enter at least 10 digits." data-msg-maxlength="Please enter not more than 14 digits.">
                                             <span class='help-block'>{{ $errors->first('mobile_no') }}
                                             </span>
                                          </div>
                                       </div>
                                   
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('telephone_number')}}
                                          <i class="red">*
                                          </i>
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                             <input type="text" name="telephone_no" id="telephone_no" class="form-control" data-rule-required='true' data-rule-number='true' data-rule-minlength='6' data-rule-maxlength='14' data-msg-minlength="Please enter at least 6 digits." data-msg-maxlength="Please enter not more than 14 digits." value="{{old('telephone_no')}}" placeholder="{{translation('enter_telephone_number')}}">
                                             <span class='help-block'>{{ $errors->first('telephone_no') }}
                                             </span>
                                          </div>
                                       </div>

                                       <div class="form-group">
                                        <label class="ol-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('relation')}}<i class="red">*</i></label>
                                        
                                        <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                          <select name="relation" id="relation" class="form-control">
                                                  <option value="Mother">{{translation('mother')}}</option>
                                                  <option value="Father">{{translation('father')}}</option>
                                                  <option value="Brother">{{translation('brother')}}</option>
                                                  <option value="Sister">{{translation('sister')}}</option>
                                                  <option value="Guardian">{{translation('guardian')}}</option>
                                          </select>
                                          <span class="help-block">{{ $errors->first('relation') }}</span>   
                                        </div>
                                      </div>

                                           </div>
                                 </div>
                                 <div class="col-sm-12 col-md-12 col-lg-6">
                                     <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('address')}}  
                                             <i class="red">*</i> 
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                             <input type="hidden" name="latitude"  id="latitude" class="field" value="{{old('latitude')}}">
                                             <input type="hidden" name="longitude"  id="longitude" class="field" value="{{old('longitude')}}">
                                             <input type="text" name="address" id="location" data-rule-required='true'  class="form-control" value="{{old('address')}}" placeholder="{{translation('enter_location')}}"/>
                                            <span class="note" style="font-size:10px;font-weight:600"><b>{{translation('note')}}:</b> {{translation("if_you_dont_find_your_location_try_our_google_map")}}</span>
                                             <span for="location" class="help-block">{{ $errors->first('address') }}</span>
                                          </div> 
                                       </div>
                                    {{-- <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('city')}}  
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                             <input type="text" name="city" id="locality" class="form-control" id="locality" value="{{old('city')}}" placeholder="{{translation('enter_city')}}"/>
                                         
                                             <span class="help-block">{{ $errors->first('city') }}</span>
                                          </div> 
                                       </div>
                                    
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('country')}}  
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                             <input type="text" name="country" id="country" class="form-control" value="{{old('country')}}" placeholder="{{translation('enter_country')}}"/>
                                         
                                             <span class="help-block">{{ $errors->first('country') }}</span>
                                          </div> 
                                       </div> --}}

                                       <div class="form-group">
                                         <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('country')}}  
                                         </label>
                                         <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">                
                                            {{-- <input type="text" name="country" id="country" class="form-control form-cascade-control" value="{{old('country')}}" placeholder="{{translation('enter_country')}}"/> --}}
                                            <div class="frmSearch relative-block">
                                              <input type="text" name="country" id="country" class="form-control" data-rule-required='true' placeholder="{{translation('enter_country')}}" value="{{old('country')}}" autocomplete="off" />
                                              <div class="suggestion-box autoselect-drop" id="suggesstion-box-country" style="height: 200px;display: none"></div>
                                            </div>
                                            <span class="help-block">{{ $errors->first('country') }}</span>
                                         </div>
                                      </div>

                                      <div class="form-group">
                                         <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('city')}}  
                                         </label>
                                         <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">                              
                                            <div class="frmSearch relative-block">
                                              <input type="text" name="city" id="locality" class="form-control" data-rule-required='true' placeholder="{{translation('enter_city')}}" value="{{old('city')}}"  autocomplete="off"/>
                                              <div class="suggestion-box autoselect-drop" id="suggesstion-box" style="height: 200px;display: none"></div>
                                            </div>
                                            <span class="help-block">{{ $errors->first('city') }}</span>
                                         </div>
                                      </div>
                                  
                                       <div class="form-group">
                                          <label class="col-sm-3 col-lg-4 control-label">{{translation('gender')}}</label>
                                            <div class="col-sm-9 col-lg-8 controls">
                                                <div class="radio-btns">
                                                    <div class="radio-btn">
                                                        <input type="radio" id="male" name="gender" value="MALE" checked/>
                                                        <label for="male">{{translation('male')}}</label>
                                                        <div class="check"></div>
                                                    </div>
                                                    <div class="radio-btn">
                                                        <input type="radio" id="female" name="gender" value="FEMALE"/>
                                                        <label for="female">{{translation('female')}}</label>
                                                        <div class="check">
                                                            <div class="inside"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="help-block">{{ $errors->first('gender') }}</span>  
                                       </div>
                                   
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('birth_date')}}  
                                             <i class="red">*</i> 
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                             <input type="text" name="birth_date"  id="datepicker2" class="form-control datepikr" data-rule-required='true' value="{{old('birth_date')}}" placeholder="{{translation('birth_date')}}" readonly="">
                                             <span class="help-block">{{ $errors->first('birth_date') }}</span>
                                          </div> 
                                       </div>
                                    
                                       <div class="form-group">
                                          <label class="col-sm-3 col-lg-4 control-label">{{translation('bus_transport')}}</label>
                                            <div class="col-sm-9 col-lg-8 controls">
                                                <div class="radio-btns">
                                                    <div class="radio-btn">
                                                        <input type="radio" id="yes-option" name="bus_transport" value="yes" onclick="showAddress()" checked/>
                                                        <label for="yes-option">{{translation('yes')}}</label>
                                                        <div class="check"></div>
                                                    </div>
                                                    <div class="radio-btn">
                                                        <input type="radio" id="no-option" name="bus_transport" value="no" onclick="hideAddress()"/>
                                                        <label for="no-option">{{translation('no')}}</label>
                                                        <div class="check">
                                                            <div class="inside"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>   
                                       </div>
                                    
                                       <div class="form-group" id="pickup_location_div">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('pickup_address')}}  
                                            
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                             <input type="hidden" name="pickup_latitude"  id="pickup_latitude" class="field" value="">
                                             <input type="hidden" name="pickup_longitude"  id="pickup_longitude" class="field" value="">
                                             <input type="text" name="pickup_address" id="pickup_location"  class="form-control" value="{{old('address')}}" placeholder="{{translation('enter_location')}}"/>
                                             
                                             <span class="help-block">{{ $errors->first('pickup_address') }}</span>
                                          </div> 
                                       </div>
                                    
                                       <div class="form-group" id="drop_location_div">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('drop_address')}}  
                                             
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                             <input type="hidden" name="drop_latitude"  id="drop_latitude" class="field" value="">
                                             <input type="hidden" name="drop_longitude"  id="drop_longitude" class="field" value="">
                                             <input type="text" name="drop_address" id="drop_location" class="form-control" value="{{old('address')}}" placeholder="{{translation('enter_location')}}"/>
                                             <span class="help-block">{{ $errors->first('drop_address') }}</span>
                                          </div> 
                                       </div>

                                        <div class="form-group">
                                      <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('special_note')}}  
                                      </label>
                                      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                         <textarea type="text" name="special_note" id="special_note" class="form-control" value="{{old('special_note')}}" placeholder="{{translation('enter')}} {{translation('special_note')}}"></textarea>
                                     
                                         <span class="help-block">{{ $errors->first('special_note') }}</span>
                                      </div> 
                                   </div>
                                    
                                 </div>

                          </div>
                            <div class="form-group">
                              <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                                <span class="note" style="font-size:10px;font-weight:600"><b>{{translation('note')}}:</b> {{translation("if_you_dont_find_your_location_try_our_google_map")}}</span>
                                <input class="btn btn btn-primary map-show" value="{{translation('click_here')}}" type="button">

                             </div>
                          </div>
                          <div class="form-group">
                            <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                               <div id="dvMap" style=" height: 400px; display: none;"></div>
                            </div>
                         </div>  
                        <hr/>
                          <input type="hidden" name="parent_id" id="parent_id" value="">
                          <input type="hidden" name="count" id="count" value="0">
                        
                        <div class="row">
                        <div class="form-group-nms">
                            <div class="col-sm-3 col-lg-2"></div>
                            <div class="col-sm-12 col-lg-8">{{translation('add')}} {{translation('parent_details')}}</div>
                            <div class="clearfix"></div>
                        </div>
                        </div>
                        <div class="row">
                        <div class="col-sm-6 col-md-6 col-lg-6">
                            <div class="row">
                          <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('email')}}  
                                 <i class="red">*</i> 
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                 <input type="text" name="parent_email" id="parent_email" class="form-control" data-rule-required='true'  data-rule-email="true" value="{{old('parent_email')}}" placeholder="{{translation('enter_email')}}">
                                 <span for="parent_email" class="help-block">{{ $errors->first('parent_email') }}</span>
                                 <span id="err_parent_email" style="display: none;color: red"></span>
                              </div> 
                           </div>
                          
                          <div class="form-group">
                             <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('first_name')}}
                             <i class="red">*</i>
                             </label>
                             <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                <input type="text" name="parent_first_name"  id="parent_first_name" class="form-control" data-rule-required='true' pattern= "^[a-zA-Z ]+$" data-rule-maxlength='255' value="{{old('parent_first_name')}}" placeholder="{{translation('enter_first_name')}}">
                                <span class='help-block'>{{ $errors->first('parent_first_name') }}
                                </span>
                             </div>
                          </div>

                          <div class="form-group">
                             <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('last_name')}}
                             <i class="red">*
                             </i>
                             </label>
                             <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                <input type="text" name="parent_last_name" id="parent_last_name" class="form-control" data-rule-required='true' data-rule-maxlength='255' pattern= "^[a-zA-Z ]+$" value="{{old('parent_last_name')}}" placeholder="{{translation('enter_last_name')}}">
                                <span class='help-block'>{{ $errors->first('parent_last_name') }}
                                </span>
                             </div>
                          </div>
                         
                           
                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('mobile_no')}}
                              <i class="red">*
                              </i>
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                 <input type="text" name="parent_mobile_no" id="parent_mobile_no" class="form-control" data-rule-required='true' data-rule-number='true' data-rule-minlength='10' data-rule-maxlength='14' data-msg-minlength="Please enter at least 10 digits." data-msg-maxlength="Please enter not more than 14 digits." value="{{old('parent_mobile_no')}}" placeholder="{{translation('enter_mobile_no')}}">
                                 <span class='help-block'>{{ $errors->first('parent_mobile_no') }}
                                 </span>
                              </div>
                           </div>
                        
                            </div>
                        </div> 
                                    
                        <div class="clearfix"></div>
                        
                        
                          <div class="form-group-nms">
                            <div class="col-sm-3 col-lg-2"></div>
                            <div class="col-sm-12 col-lg-8">{{translation('brotherhood')}}</div>
                            <div class="clearfix"></div>
                          </div>
                        

                        <div class="clearfix"></div>
                        
                        <div class="text-block">                                    
                            <div class="main-col-block">
                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('student_number')}}</label>
                                    <div class="col-sm-9 col-lg-4 controls input-group-block">
                                        <input class="form-control" name="kid_national_id_0" type="text" placeholder="{{translation('student_number')}}" pattern="^[A-Za-z0-9]*$" onblur="checkBrotherhood(this)">
                                        <button class="btn btn-success add-remove-btn" type="button" onclick="education_fields('{{translation('student_number')}}');"> <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> </button>
                                        <span id="kid_national_id_0_error" class="help-block"></span>
                                    </div>
                                </div>                                      
                                
                                <div class="clearfix"></div>
                            </div>
                            <div id="education_fields">

                            </div>
                          
                        </div> 
                        
                       
                        <div class="form-group">
                          <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                             <a href="{{ url($school_admin_panel_slug.'/dashboard') }}" class="btn btn-primary">{{translation('back')}}</a> 
                             <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('save')}}
                          </div>
                        </div>
                        </div> 
                    </form>
      </div>
      </div>
   </div>
</div>
</div>  
<script type="text/javascript">

   var city_ajax_url = "{{url('/school_admin')}}/get_cities?keyword=";
   var country_ajax_url = "{{url('/school_admin')}}/get_countries?keyword=";
    var token = "<?php echo csrf_token();?>";

   var latitude='';
   var longitude='';
   var address='';
   var bounds = '';
   var map;
   var marker;
   $(document).on("change",".validate-image", function()
    {            
      var file=this.files;
      validateImage(this.files, 250,250);
    });
   
</script>

<script>
    $(function () {
      var newdate = new Date();
      newdate = (newdate.getFullYear()-2)+'-12-31';
        $("#datepicker").datepicker({
            todayHighlight: true,
            autoclose: true,
            format:'yyyy-mm-dd',
            endDate: "{{\Session::get('end_date')}}",
            startDate: "{{\Session::get('start_date')}}"
        });
        $("#datepicker2").datepicker({
            todayHighlight: true,
            autoclose: true,
            format:'yyyy-mm-dd',
            endDate: newdate,
        });
    });
    
    $(".map-show").on("click", function () {
          $("#dvMap").slideToggle("slow");
          address = $('#location').val();
          if(!$("#dvMap").is(":hidden")){
            if(latitude==''&& longitude=='')
               initMap();    
            else
               placeMarker();
         } 

      });
</script>

<!--    Image Upload -->
<script type="text/javascript">
 
    function hideAddress()
    {
        $('#pickup_location_div').hide();
        $('#drop_location_div').hide();
    }
    function showAddress()
    {
      $('#pickup_location_div').show();
        $('#drop_location_div').show();  
    }
    
    $(document).ready(function () {

        $(function () {
                        $("#datepicker").datepicker({                    
                            todayHighlight: true,
                            autoclose: true,               
                        });         
                    });                 
        function readURL(input) {             
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('.img-preview2').attr('src', e.target.result);

                };

                reader.readAsDataURL(input.files[0]);
            }
        }
        

    });
</script>
<script type="text/javascript">
   $(document).ready(function(){
    $('#email').on('blur',function(){

      var parent_email = $("#parent_email").val();
      var student_email = $("#email").val();
      if(parent_email==student_email){
        $(this).next('span').text("{{translation('this_email_is_already_exist_for_this_school')}}");
        return;
      }

      var email   =   $('#email').val();
      var promote = $('#promote').val();
      if(email != '' && promote=='no')
      {
       $.ajax({
              url  :"{{ $module_url_path }}/checkEmail",
              type :'POST',
              data :{'user_type':'student','email':email ,'_token':'<?php echo csrf_token();?>'},
              success:function(data){
                if(data.status=='success')
                  {
                    $('#err_email').text();
                  }
                  if(data.status=='error')
                  {
                    $('#err_email').show();
                    $('#err_email').text('This email is already exist');
                  }
              }
            });
      }
    });

    $('#email').on('keyup',function(){
      $('#err_email').css('display','none');
    });

    $('#parent_email').on('blur',function(){
      var email   =   $('#parent_email').val();
      var parent_id = $('#parent_id').val()
      var parent_email = $("#parent_email").val();
      var student_email = $("#email").val();
      if(parent_email==student_email){
        $(this).next('span').text("{{translation('this_email_is_already_exist_for_this_school')}}");
        return;
      } 
      if(email != '' && parent_id=='')
      {
       $.ajax({
              url  :"{{ $module_url_path }}/checkEmail",
              type :'POST',
              data :{'user_type':'parent','email':email ,'_token':'<?php echo csrf_token();?>'},
              success:function(data){
                  if(data.status=='error')
                  {
                    $('#err_parent_email').show();
                    $('#err_parent_email').text('This email is already exist');

                    $('#parent_first_name').val('');
                      $('#parent_last_name').val('');
                      $('#parent_first_name').removeAttr("readonly","true");
                      $('#parent_last_name').removeAttr("readonly","true");
                      $("#parent_mobile_no").val('');
                      $("#parent_mobile_no").removeAttr("readonly","true");
                      
                  }
                  else{
                    $('#err_parent_email').text();
                    if(data.status=='exist'){
                      var data =data.data;
                      $('#parent_first_name').val(data['first_name']);
                      $('#parent_last_name').val(data['last_name']);
                      $('#parent_first_name').attr("readonly","true");
                      $('#parent_last_name').attr("readonly","true");
                      $("#parent_mobile_no").val(data.mobile_no);
                      $("#parent_mobile_no").attr("readonly","true");
                      $("#parent_national_id").val(data.national_id);
                    }
                    else{
                      $('#parent_first_name').val('');
                      $('#parent_last_name').val('');
                      $('#parent_first_name').removeAttr("readonly","true");
                      $('#parent_last_name').removeAttr("readonly","true");
                      $("#parent_mobile_no").val('');
                      $("#parent_mobile_no").removeAttr("readonly","true");
                      
                    }
                  }
              }
            });
      }
    });

    $('#parent_email').on('keyup',function(){
      $('#err_parent_email').css('display','none');
    });
    

 });
 </script>

 <script type="text/javascript">
   $(document).ready(function(){

    $('#parent_national_id').on('blur',function(){

      var national_id   =   $('#parent_national_id').val();
      var masar_code    =   $('#national_id').val();
      if(national_id==masar_code){
        $('#national_id').text("{{translation('student_code_id_should_be_unique')}}");
        return false;
      }
      else{
        $('#national_id').text("");
      }
      if(national_id != '')
      {
            $.ajax({
              url  :"{{ $module_url_path }}/get_parent_details",
              type :'POST',
              data :{'national_id':national_id ,'_token':'<?php echo csrf_token();?>','user_type':'parent'},
              success:function(data){
                  if(data=='')
                  {
                 
                    $('#parent_first_name').val('');
                    $('#parent_last_name').val('');
                    $('#parent_first_name').removeAttr("readonly");
                    $('#parent_last_name').removeAttr("readonly");

                    $("#parent_id").val('');  
                    $("#parent_email").val('');
                    $("#parent_email").removeAttr("readonly");
                    $("#parent_mobile_no").val('');
                    $("#parent_mobile_no").removeAttr("readonly");
                  }
                  else
                  {

                    $('#parent_first_name').val(data['first_name']);
                    $('#parent_last_name').val(data['last_name']);
                    $('#parent_first_name').attr("readonly","true");
                    $('#parent_last_name').attr("readonly","true");
                    $("#parent_id").val(data.parent_id);
                    $("#parent_email").val(data.email);
                    $("#parent_email").attr("readonly","true");
                    $("#parent_mobile_no").val(data.mobile_no);
                    $("#parent_mobile_no").attr("readonly","true");   
                  }
      }
  });
}
});

$('#parent_national_id').on('keyup',function(){
  $('#err_parent_national_id').css('display','none');
});

$('#national_id').on('blur',function(){

  var national_id   =   $('#national_id').val();
  var parent_national_id   =   $('#parent_national_id').val();
  if(national_id==parent_national_id){
    $("#err_student_nationalid").text("{{translation('national_id_should_be_unique')}}");
    return false;
  }
  else{
   $("#err_student_nationalid").text(); 
  }
  if(national_id != '')
  {
    $.ajax({
      url  :"{{ $module_url_path }}/get_parent_details",
      type :'POST',
      data :{'national_id':national_id ,'_token':'<?php echo csrf_token();?>','user_type':'student'},
      success:function(data){
          if(data=='')
          {
               
            $('#first_name').val('');
            $('#last_name').val('');
            $('#first_name').removeAttr("readonly");
            $('#last_name').removeAttr("readonly");

            $('#parent_first_name').val('');
            $('#parent_last_name').val('');
            $('#parent_first_name').removeAttr("readonly");
            $('#parent_last_name').removeAttr("readonly");


            $("#parent_id").val('');
            $("#parent_national_id").val('');
            $("#parent_national_id").removeAttr("readonly");
            $("#parent_email").val('');
            $("#parent_email").removeAttr("readonly");
            $("#parent_mobile_no").val('');
            $("#parent_mobile_no").removeAttr("readonly");
            $("#no-option").removeAttr("checked");
            $("#yes-option").attr("checked",'true');
            $('#pickup_location_div').show();
            $('#drop_location_div').show();
            $('#promote').val('no');
            $("#email").val('');
            $("#location").val('');
            $("#locality").val('');
            $("#country").val('');
            $("#email").removeAttr("readonly");
            $("#mobile_no").val('');
            $("#datepicker2").val('');
            $("#telephone_no").val('');
            $("#pickup_location").val(data.pickup_address);
            $("#drop_location").val(data.drop_address);
            $("#female").removeAttr("checked");
            $("#male").attr("checked",'true');
          }
          else
          {        
                        
            $('#first_name').val(data['first_name']);
            $('#last_name').val(data['last_name']);
            $('#special_note').val(data['special_note']);
            $('#first_name').attr("readonly","true");
            $('#last_name').attr("readonly","true");

            $('#parent_first_name').val(data['parent_first_name']);
            $('#parent_last_name').val(data['parent_last_name']);
            $('#parent_first_name').attr("readonly","true");
            $('#parent_last_name').attr("readonly","true");

            $("#parent_id").val(data.parent_id);
            $("#parent_national_id").val(data.parent_national_id);
            $("#parent_national_id").attr("readonly","true");
            $("#user").val(data.user_id);
            $("#parent_email").val(data.parent_email);
            $("#parent_email").attr("readonly","true");
            $("#parent_mobile_no").val(data.parent_mobile_no);
            $("#parent_mobile_no").attr("readonly","true");
            $('select#level option[value="' + data.level+ '"]').attr("selected","true");
            $(".level-class").append(data.options);
            $('select#class option[value="' + data.class+ '"]').attr("selected","true");
            $("#email").val(data.student_email);
            $("#email").attr("readonly","true");
            $("#location").val(data.address);
            $("#locality").val(data.city);
            $("#country").val(data.country);
            $('select#relation option[value="' + data.relation+ '"]').attr("selected","true");
            if(data.image != '')
            {
              $(".img-responsive ").attr('src',data.profile_image);
            }

            $("#datepicker2").val(data.birth_date);
            if(data.gender=='male')
            {
              $("#male").attr("checked",'true');
            } 
            else
            {    
              $("#female").attr("checked",'true');
            }
            $("#mobile_no").val(data.student_mobile_no);
            $("#telephone_no").val(data.telephone_no);
            $('#promote').val(data.student_id);
            if(data.bus_transport==1)
            {
              $("#yes-option").attr("checked",'true');
              $("#pickup_latitude").val(data.pickup_latitude);
              $("#pickup_longitude").val(data.pickup_longitude);
              $("#drop_latitude").val(data.drop_latitude);
              $("#drop_longitude").val(data.drop_longitude);
              $("#pickup_location").val(data.pickup_address);
              $("#drop_location").val(data.drop_address);

            }
            else
            {
              $("#no-option").attr("checked",'true');
              $('#pickup_location_div').hide();
              $('#drop_location_div').hide();
               
            }
          }    
        }
      });
    }
  });
});
 </script>

<!-- script to init map for auto complete geo location for address -->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{config('constants.GOOGLE_API_KEY')}}&libraries=places"></script>


<script type="text/javascript">
  $(document).ready(function(){

     initMap();
  });   
  var glob_autocomplete;

  var glob_component_form = 
                {
                    street_number: 'short_name',
                    route: 'long_name',
                    locality: 'long_name',
                    administrative_area_level_1: 'long_name',
                    postal_code: 'short_name',
                    country : 'long_name',
                    postal_code : 'short_name',
                    city : 'long_name'
                };

  var glob_options   = {};
  glob_options.types = ['establishment'];

  function initAutocomplete() {
      glob_autocomplete = false;
      glob_autocomplete = initGoogleAutoComponent($('#location')[0],glob_options,glob_autocomplete);
  }


  function initGoogleAutoComponent(elem,options,autocomplete_ref)
  {
    autocomplete_ref = new google.maps.places.Autocomplete(elem,options);
    autocomplete_ref = createPlaceChangeListener(autocomplete_ref,fillInAddress);

    return autocomplete_ref;
  }

  function createPlaceChangeListener(autocomplete_ref,fillInAddress)
  {
    autocomplete_ref.addListener('place_changed', fillInAddress);
    return autocomplete_ref;
  }

  function fillInAddress() 
  {
      var place = glob_autocomplete.getPlace();
      
      latitude = place.geometry.location.lat();
      longitude = place.geometry.location.lng();
      $('#latitude').val(place.geometry.location.lat());
      $('#longitude').val(place.geometry.location.lng());

      address = $('#location').val();
      placeMarker();
      
      for (var component in glob_component_form) 
      {
          $("#"+component).val("");
          $("#"+component).attr('disabled',false);
      }
      if(place.address_components.length > 0 )
      {
          
        $.each(place.address_components,function(index,elem){

            var addressType = elem.types[0];
        
          if(addressType!=undefined){
            if(glob_component_form[addressType]!=undefined){
                var val = elem[glob_component_form[addressType]];
                
                $("#"+addressType).val(val) ;  
            }
          }
        });  
      }
    }

  var pick_location_geometry;
  var glob_component_form = 
                {
                    street_number: 'short_name',
                    route: 'long_name',
                    locality: 'long_name',
                    administrative_area_level_1: 'long_name',
                    postal_code: 'short_name',
                    country : 'long_name',
                    postal_code : 'short_name',
                };

  var glob_options   = {};
  glob_options.types = ['establishment'];
    initAutocompletePickup();

  function initAutocompletePickup() {
    
      pick_location_geometry = false;
      pick_location_geometry = initGoogleAutoComponentPickup($('#pickup_location')[0],glob_options,pick_location_geometry);
  }


  function initGoogleAutoComponentPickup(elem,options,autocomplete_ref)
  {
    autocomplete_ref = new google.maps.places.Autocomplete(elem,options);
    autocomplete_ref = createPlaceChangeListenerPickup(autocomplete_ref,fillInAddressPickup);

    return autocomplete_ref;
  }

  function createPlaceChangeListenerPickup(autocomplete_ref,fillInAddressPickup)
  {
    autocomplete_ref.addListener('place_changed', fillInAddressPickup);
    return autocomplete_ref;

  }

  function fillInAddressPickup() 
  {
      var place = pick_location_geometry.getPlace();
      $('#pickup_latitude').val(place.geometry.location.lat());
      $('#pickup_longitude').val(place.geometry.location.lng());
  }

  var drop_location_geometry;
  var glob_component_form = 
                {
                    street_number: 'short_name',
                    route: 'long_name',
                    locality: 'long_name',
                    administrative_area_level_1: 'long_name',
                    postal_code: 'short_name',
                    country : 'long_name',
                    postal_code : 'short_name',
                };

  var glob_options   = {};
  glob_options.types = ['establishment'];
 
  initAutocompleteDrop();

  function initAutocompleteDrop() {
    
      drop_location_geometry = false;
      drop_location_geometry = initGoogleAutoComponentDrop($('#drop_location')[0],glob_options,drop_location_geometry);
  }


  function initGoogleAutoComponentDrop(elem,options,autocomplete_ref)
  {
    autocomplete_ref = new google.maps.places.Autocomplete(elem,options);
    autocomplete_ref = createPlaceChangeListenerDrop(autocomplete_ref,fillInAddressDrop);

    return autocomplete_ref;
  }

  function createPlaceChangeListenerDrop(autocomplete_ref,fillInAddressDrop)
  {
    autocomplete_ref.addListener('place_changed', fillInAddressDrop);
    return autocomplete_ref;
  }

  function fillInAddressDrop() 
  {
      var place = drop_location_geometry.getPlace();
      $('#drop_latitude').val(place.geometry.location.lat());
      $('#drop_longitude').val(place.geometry.location.lng());
    
  }

  var BASE_URL = "{{url('/')}}";

  window.onload = function () {
        setTimeout(function(){ 
            initAutocomplete();
           
        }, 2000);


    };

</script>

       <!-- function for geocomplete end  -->  
<script>
        var room = 1;
        var count = 1;
            function education_fields(label) {

                room++;
                var objTo = document.getElementById('education_fields')
                var divtest = document.createElement("div");
                divtest.setAttribute("class", "form-group removeclass"+room);
                var rdiv = 'removeclass'+room;
                divtest.innerHTML = '<div class="form-group"><label class="col-sm-3 col-lg-2 control-label">'+label+'<i class="red">*</i></label><div class="col-sm-9 col-lg-4 controls input-group-block"><input class="form-control" pattern="^[A-Za-z0-9]*$" name="kid_national_id_'+count+'" type="text" placeholder="'+label+'" onblur="checkBrotherhood(this)"><button class="btn btn-danger remove-btn-block" type="button" onclick="remove_education_fields('+ room +');"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button><span id="kid_national_id_'+count+'_error" class="help-block"></span></div></div><div class="clear"></div>';

                objTo.appendChild(divtest);
                count++;
                $("#count").val(count);
            }
             function remove_education_fields(rid) {
                 $('.removeclass'+rid).remove();
             }

            $(".level").on('change',function(){
                var level = $('.level').val();
                $(".level-class").empty();
                   $.ajax({
                      url  :"{{ $module_url_path }}/get_classes",
                      type :'POST',
                      data :{'_token':'<?php echo csrf_token();?>','level':level},
                      success:function(data){
                        $(".level-class").append(data);
                      }
                });

            });  

           function generateNumber(){

              $.ajax({
                      url  :"{{$module_url_path }}/generate_admission_number",
                      type :'GET',
                      
                      success:function(data){
                        $("input[name='admission_number']").val(data);
                      }
                });
           }


           $("#admission_number").on('blur',function(){
              var number = $("#admission_number").val();
                
                   $.ajax({
                      url  :"{{ $module_url_path }}/check_admission_no_exist/"+number,
                      type :'GET',
                      
                      success:function(data){
                        if(data=="error"){
                          
                        }

                      }
                });

           })

           function checkBrotherhood(e)
           {
               var number = $(e).val();
               var parent_national_id = $('#parent_national_id').val();
               $.ajax({
                      url  :"{{ $module_url_path }}/check_brotherhood",
                      type :'post',
                      data :{'_token':'<?php echo csrf_token();?>','number':number,'parent_national_id':parent_national_id},
                      success:function(data){
                        if(data=='error')
                        { 
                          var name = $(e).attr('name');

                          $("#"+name+"_error").text("{{translation('invalid_input')}}");
                        }
                        else{
                          $("#"+name+"_error").text(""); 
                        }
                      }
                });
           }

           function hideBox(val) {
              $("#locality").val(val);
              $("#suggesstion-box").hide();
           }

           function selectCity(val) {
              $("#country").val(val);
              $("#suggesstion-box-country").hide();
           }
        </script>
       <!-- function for geocomplete end  --> 
       <script src="{{url('/')}}/js/google_map.js"></script>
<script src="{{ url('/') }}/js/school_admin/transport_route/SlidingMarker.js"></script>
       <!-- function for geocomplete end  -->  
<script src="{{url('/')}}/js/city_country.js"></script>
<!-- END Main Content --> 
@endsection