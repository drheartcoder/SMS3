@extends('schooladmin.layout.master')                
@section('main_content')
<style type="text/css">
   .profile-img{width: 120px;
   height: 120px;
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
      <i class="{{$edit_icon}}"></i>
      <li class="active">{{$page_title}}</li>
   </ul>
</div>
<!-- END Breadcrumb -->
<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
   <div>
      <h1>{{str_plural($module_title)}}</h1>
   </div>
</div>
<!-- END Page Title -->
<!-- BEGIN Main Content -->
<div class="row">
   <div class="col-md-12">
      <div class="box box-navy_blue">
         <div class="box-title">
            <h3>
               <i class="fa {{$edit_icon}}">
               </i>{{ isset($page_title)?$page_title:"" }} 
            </h3>
            <div class="box-tool">
            </div>
         </div>
         <div class="box-content">
            @include('schooladmin.layout._operation_status')
            <div class="tobbable">
               <form method="post" onsubmit="return addLoader()"  action="{{$module_url_path}}/update/{{base64_encode($id)}}"  class="form-horizontal" id="validation-form1" enctype ='multipart/form-data'>
                  {{ csrf_field() }}
                  <div  class="tab-content">
                     <br>
                     <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-6">
                           <div class="row">
                              <div class="form-group">
                                 <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('profile_image')}}
                                 </label>
                                 <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                    <div class="fileupload fileupload-new" data-provides="fileupload">
                                       <div class="fileupload-new img-thumbnail profile-img img">
                                          @if(isset($arr_data['get_user_details']['profile_image']) && ($arr_data['get_user_details']['profile_image'] == "" || !file_exists($base_url.$arr_data['get_user_details']['profile_image'] )))
                                           <img src="{{$image_path}}/default.png">
                                         @else
                                           <input type="hidden" name="oldimage" value="{{$arr_data['get_user_details']['profile_image']}}">
                                           <img src="{{$image_path.'/'.$arr_data['get_user_details']['profile_image']}}">
                                         @endif
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
                              </div>
                              <div class="form-group">
                                 <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('first_name')}}
                                 <i class="red">*</i>
                                 </label>
                                 <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">
                                    <input type="text" name="first_name" class="form-control" data-rule-required='true' pattern="[a-zA-Z àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" data-rule-maxlength='255' value="{{isset($arr_data['get_user_details']['first_name'])?$arr_data['get_user_details']['first_name']:''}}">
                                    <span class='help-block'>{{ $errors->first('first_name')}}
                                    </span>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('last_name')}}
                                 <i class="red">*
                                 </i>
                                 </label>
                                 <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">
                                    <input type="text" name="last_name" class="form-control" data-rule-required='true' pattern="[a-zA-Z àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" data-rule-maxlength='255' value="{{isset($arr_data['get_user_details']['last_name'])?$arr_data['get_user_details']['last_name']:''}}">
                                    <span class='help-block'>{{ $errors->first('last_name_') }}
                                    </span>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('role')}}<i class="red">*</i></label>
                                 <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">
                                    <select name="role" class="form-control" data-rule-required='true'>
                                       <option value="">{{translation('select_role')}}</option>
                                       @if(isset($role))
                                       @foreach($role as $key => $value)
                                       <option value="{{$value['role_details']['slug']}}" @if(isset($arr_data['user_role']) && $arr_data['user_role'] == $value['role_details']['slug']) selected @endif>{{$value['role_details']['name']}}</option>
                                       @endforeach
                                       @endif
                                    </select>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('email')}}  
                                 <i class="red">*</i> 
                                 </label>
                                 <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">        
                                    <input type="text" name="email"  id="email" class="form-control"  value="{{isset($arr_data['get_user_details']['email'])?$arr_data['get_user_details']['email']:''}}" readonly="true">
                                    <span class="help-block">{{ $errors->first('email') }}</span>
                                    <span id="err_email" style="display: none;color: red"></span>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('address')}}  
                                 <i class="red">*</i> 
                                 </label>
                                 <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">        
                                    <input type="hidden" name="latitude"  id="latitude" class="field" value="{{$arr_data['get_user_details']['latitude']}}">
                                    <input type="hidden" name="longitude"  id="longitude" class="field" value="{{$arr_data['get_user_details']['longitude']}}">
                                    <input type="text" name="address" id="location" data-rule-required='true'  class="form-control" value="{{isset($arr_data['get_user_details']['address'])?$arr_data['get_user_details']['address']:''}}" />
                                     <span class="note" style="font-size:10px;font-weight:600"><b>{{translation('note')}}:</b> {{translation("if_you_dont_find_your_location_try_our_google_map")}}</span>
                                    <span for="location" class="help-block">{{ $errors->first('address') }}</span>
                                 </div>
                              </div>
                              {{-- <div class="form-group">
                                 <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('city')}}  
                                 </label>
                                 <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">        
                                    <input type="text" name="city" id="locality" class="form-control" value="{{isset($arr_data['get_user_details']['city'])?$arr_data['get_user_details']['city']:''}}" />
                                    <span class="help-block">{{ $errors->first('city') }}</span>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('country')}}  
                                 </label>
                                 <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">        
                                    <input type="text" name="country" id="country" class="form-control" value="{{isset($arr_data['get_user_details']['country']) ? $arr_data['get_user_details']['country']:''}}" />
                                    <span class="help-block">{{ $errors->first('country') }}</span>
                                 </div>
                              </div> --}}

                              <div class="form-group">
                                 <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('country')}}  
                                 </label>
                                 <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">                
                                    {{-- <input type="text" name="country" id="country" class="form-control form-cascade-control" value="{{old('country')}}" placeholder="{{translation('enter_country')}}"/> --}}
                                    <div class="frmSearch relative-block">
                                      <input type="text" name="country" id="country" class="form-control" data-rule-required='true' placeholder="{{translation('enter_country')}}" value="{{isset($arr_data['get_user_details']['country']) ? $arr_data['get_user_details']['country']:''}}" autocomplete="off"/>
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
                                      <input type="text" name="city" id="locality" class="form-control" data-rule-required='true' placeholder="{{translation('enter_city')}}" value="{{isset($arr_data['get_user_details']['city'])?$arr_data['get_user_details']['city']:''}}" autocomplete="off"/>
                                      <div class="suggestion-box autoselect-drop" id="suggesstion-box" style="height: 200px;display: none"></div>
                                    </div>
                                    <span class="help-block">{{ $errors->first('city') }}</span>
                                 </div>
                              </div>
                             
                           </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-6">
                           <div class="row">
                              <div class="form-group">
                                 <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('national_id')}}  
                                 <i class="red">*</i> 
                                 </label>
                                 <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">        
                                    <input type="text" name="national_id" id="national_id" data-rule-required="true" class="form-control" value="{{isset($arr_data['get_user_details']['national_id'])?$arr_data['get_user_details']['national_id']:''}}" pattern="[a-zA-Z0-9àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$"/>
                                    <span id="err_nationalid" style="display: none; color: red"></span>
                                    <span class="help-block">{{ $errors->first('national_id') }}</span>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('birth_date')}}  
                                 <i class="red">*</i> 
                                 </label>
                                 <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">        
                                    <input type="text" name="birth_date"  id="datepicker" class="form-control datepikr" data-rule-required='true' value="{{isset($arr_data['get_user_details']['birth_date']) && $arr_data['get_user_details']['birth_date'] != '0000-00-00'?$arr_data['get_user_details']['birth_date']:''}}" data-rule-date="true" readonly style="cursor: pointer;">
                                    <span class="help-block">{{ $errors->first('birth_date') }}</span>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('gender')}}   
                                 </label>
                                 <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">
                                    <div class="radio-btns">
                                       <div class="radio-btn">
                                          <input type="radio" id="f-option" name="gender" value="MALE" checked @if(isset($arr_data['get_user_details']['gender']) && $arr_data['get_user_details']['gender'] == 'MALE') checked @endif>
                                          <label for="f-option">{{translation('male')}}</label>
                                          <div class="check"></div>
                                       </div>
                                       <div class="radio-btn">
                                          <input type="radio" id="s-option" name="gender" value="FEMALE" @if(isset($arr_data['get_user_details']['gender']) && $arr_data['get_user_details']['gender'] == 'FEMALE') checked @endif>
                                          <label for="s-option">{{translation('female')}}</label>
                                          <div class="check">
                                             <div class="inside"></div>
                                          </div>
                                       </div>
                                    </div>
                                    <span class="help-block">{{ $errors->first('gender') }}</span>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('status')}}  
                                 </label>
                                 <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">
                                    <select class="form-control" name="status">
                                       <option value="" selected>{{translation('select_status')}}</option>
                                       <option value="MARRIED" @if(isset($arr_data['marital_status']) && $arr_data['marital_status'] == 'MARRIED') selected @endif>{{translation('married')}}</option>
                                       <option value="SINGLE" @if(isset($arr_data['marital_status']) &&  $arr_data['marital_status'] == 'SINGLE') selected @endif>{{translation('single')}}</option>
                                       <option value="ENGAGED" @if(isset($arr_data['marital_status']) && $arr_data['marital_status'] == 'ENGAGED') selected @endif>{{translation('engaged')}}</option>
                                       <option value="DIVORCED" @if(isset($arr_data['marital_status']) && $arr_data['marital_status'] == 'DIVORCED') selected @endif>{{translation('divorced')}}</option>
                                    </select>
                                    <span class="help-block">{{ $errors->first('status') }}</span>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('year_of_experience')}}  
                                 <i class="red">*</i> 
                                 </label>
                                 <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls"> 
                                    <input type="text" name="year_of_experience"  data-rule-number="true" id="year_of_experience" class="form-control" data-rule-required='true' value="{{isset($arr_data['year_of_experience'])?$arr_data['year_of_experience']:''}}" data-rule-maxlength="5"  max="50" >
                                    <span class="help-block">{{ $errors->first('year_of_experience') }}</span>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('mobile_no')}}
                                 <i class="red">*
                                 </i>
                                 </label>
                                 <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">
                                    <input type="text" name="mobile_no" class="form-control" data-rule-required='true' data-rule-number='true' data-rule-minlength='10' data-rule-maxlength='14' value="{{isset($arr_data['get_user_details']['mobile_no'])?$arr_data['get_user_details']['mobile_no']:''}}"  placeholder="{{translation('enter_mobile_number')}}" data-msg-minlength="{{translation('mobile_number_should_be_at_least_10_digits')}}" data-msg-maxlength="{{translation('mobile_number_should_be_at_most_14_digits')}}">
                                    <span class='help-block'>{{ $errors->first('mobile_no') }}
                                    </span>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('telephone_number')}}
                                 <i class="red">*
                                 </i>
                                 </label>
                                 <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">
                                    <input type="text" name="telephone_no" class="form-control" data-rule-required='true' data-rule-number='true' data-rule-minlength='6' data-rule-maxlength='14' placeholder="{{translation('enter_telephone_number')}}" data-msg-minlength="{{translation('telephone_number_should_be_at_least_6_digits')}}" data-msg-maxlength="{{translation('telephone_number_should_be_at_most_14_digits')}}"  value="{{isset($arr_data['get_user_details']['telephone_no'])?$arr_data['get_user_details']['telephone_no']:''}}">
                                    <span class='help-block'>{{ $errors->first('telephone_no') }}
                                    </span>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('qualification_degree')}}
                                 <i class="red">*</i>
                                 </label>
                                 <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">        
                                    <input type="text" name="qualification_degree" pattern="[a-zA-Z0-9\, àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" id="qualification_degree" class="form-control" data-rule-required='true' value="{{isset($arr_data['qualification_degree'])?$arr_data['qualification_degree']:''}}">
                                    <span class="help-block">{{ $errors->first('qualification_degree') }}</span>
                                 </div>
                              </div>
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
                  <div class="row">
                     <div class="form-group">
                        <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                           <a href="{{ url($school_admin_panel_slug.'/employee') }}" class="btn btn-primary">{{translation('back')}}</a> 
                           <input type="submit" id="submit_button" name="update" value="{{translation('update')}}" class="btn btn-primary">
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
  var latitude='{{$arr_data['get_user_details']['latitude']}}';
   var longitude='{{$arr_data['get_user_details']['longitude']}}';
   var address='{{$arr_data['get_user_details']['address']}}';
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
      newdate = (newdate.getFullYear()-15)+'-12-31';
       $("#datepicker").datepicker({
           todayHighlight: true,
           autoclose: true,
          format:'yyyy-mm-dd',
          endDate: newdate
       });
       $("#datepicker2").datepicker({
           todayHighlight: true,
           autoclose: true,
            format:'yyyy-mm-dd'
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
   });
</script>
<!--    Image Upload -->
<script type="text/javascript">
   $(document).ready(function () {
      
   
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
/*SetMarker()*/
   }
   
   function hideBox(val) {
      $("#locality").val(val);
      $("#suggesstion-box").hide();
   }

   function selectCity(val) {
      $("#country").val(val);
      $("#suggesstion-box-country").hide();
   }
   
   var BASE_URL = "{{url('/')}}";
   
   window.onload = function () {
         setTimeout(function(){ 
             initAutocomplete();
            
         }, 2000);
   
   
     };
     $('#location').keydown(function(event){
          if(event.keyCode == 13) {
            event.preventDefault();
            return false;
          }
   });
   
</script> 
<script src="{{url('/')}}/js/google_map.js"></script>
<script src="{{ url('/') }}/js/school_admin/transport_route/SlidingMarker.js"></script>
       <!-- function for geocomplete end  -->  
<script src="{{url('/')}}/js/city_country.js"></script>
<!-- END Main Content --> 
@endsection