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
          <a href="{{$module_path}}">{{$module_title}}</a>
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
                                    
                                    <div class="form-group">
                                       <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('profile_image')}}
                                       </label>
                                       <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                          <div class="fileupload fileupload-new" data-provides="fileupload">
                                                   <div class="fileupload-new img-thumbnail profile-img img">
                                                        @if(isset($arr_data['get_user_details']['profile_image']) && $arr_data['get_user_details']['profile_image'] != '')
                                                          <input type="hidden" name="old_image" value="{{$arr_data['get_user_details']['profile_image']}}">
                                                          <img src="{{url('/')}}/uploads/profile_image/{{$arr_data['get_user_details']['profile_image']}}" height="100px" width="100px">
                                                        @else
                                                          <img src="{{url('/')}}/uploads/profile_image/default.png" height="100px" width="100px">
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
                                            </div>
                                        </div>
                                    
         
                                    <div class="form-group">
                                       <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('first_name')}}
                                       <i class="red">*</i>
                                       </label>
                                       <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">
                                          <input type="text" name="first_name" class="form-control" data-rule-required='true' data-rule-pattern="^[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" data-rule-maxlength='255' value="{{isset($arr_data['get_user_details']['first_name'])?$arr_data['get_user_details']['first_name']:''}}" placeholder="{{translation('enter_first_name')}}">
                                          <span class='help-block'>{{ $errors->first('first_name') }}
                                          </span>
                                       </div>
                                    </div>

                                    <div class="form-group">
                                       <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('last_name')}}
                                       <i class="red">*
                                       </i>
                                       </label>
                                       <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">
                                          <input type="text" name="last_name" class="form-control" data-rule-required='true' data-rule-pattern="^[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" data-rule-maxlength='255' value="{{isset($arr_data['get_user_details']['last_name'])?$arr_data['get_user_details']['last_name']:''}}" placeholder="{{translation('enter_last_name')}}">
                                          <span class='help-block'>{{ $errors->first('last_name') }}
                                          </span>
                                       </div>
                                    </div>
 
                                    
                                       <div class="form-group">
                                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('role')}}<i class="red">*</i></label>
                                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">
                                                  <select name="role" class="form-control" data-rule-required='true'>
                                                      <option value="">{{translation('select_position')}}</option>
                                                      @if(isset($role))
                                                          @foreach($role as $key => $value)
                                                            <option value="{{$value['slug']}}" @if(isset($arr_data['user_role']) && $arr_data['user_role'] == $value['slug']) selected @endif>{{$value['name']}}</option>
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
                                             <input type="text" name="email"  id="email" class="form-control" readonly="true" value="{{isset($arr_data['get_user_details']['email'])?$arr_data['get_user_details']['email']:''}}" placeholder="{{translation('enter_email')}}">
                                             <span class="help-block">{{ $errors->first('email') }}</span>
                                             <span id="err_email" style="display: none;color: red"></span>
                                          </div> 
                                       </div>
                                    
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('address')}}  
                                             <i class="red">*</i> 
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">        
                                             <input type="hidden" name="latitude"  id="latitude" class="field" value="@if(isset($arr_data['get_user_details']['latitude'])){{$arr_data['get_user_details']['latitude']}} @endif">
                                             <input type="hidden" name="longitude"  id="longitude" class="field" value="@if(isset($arr_data['get_user_details']['longitude'])){{$arr_data['get_user_details']['longitude']}} @endif">
                                             <input type="text" name="address" id="location" data-rule-required='true'  class="form-control" value="{{isset($arr_data['get_user_details']['address'])?$arr_data['get_user_details']['address']:''}}" />
                                         
                                             <span class="help-block">{{ $errors->first('address') }}</span>
                                          </div> 
                                       </div>
                                  
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('city')}}  
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">        
                                             <input type="text" name="city" id="locality" class="form-control" value="{{isset($arr_data['get_user_details']['city'])?$arr_data['get_user_details']['city']:''}}" placeholder="{{translation('enter_city')}}" />
                                         
                                             <span class="help-block">{{ $errors->first('city') }}</span>
                                          </div> 
                                       </div>
                                    
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('country')}}  
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">        
                                             <input type="text" name="country" id="country" class="form-control" value="{{isset($arr_data['get_user_details']['country'])?$arr_data['get_user_details']['country']:''}}" placeholder="{{translation('enter_country')}}" />
                                         
                                             <span class="help-block">{{ $errors->first('country') }}</span>
                                          </div> 
                                       </div>
                                   
                                 </div>

                                 <div class="col-sm-12 col-md-12 col-lg-6">

                                  
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('national_id')}}  
                                              <i class="red">*</i> 
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">        
                                             <input type="text" name="national_id" id="national_id" data-rule-required="true" class="form-control" value="{{isset($arr_data['get_user_details']['national_id'])?$arr_data['get_user_details']['national_id']:''}}" placeholder="{{translation('enter_national_id')}}" pattern="^[A-Za-z0-9]*$" />
                                             <span id="err_nationalid" style="display: none; color: red"></span>
                                             <span class="help-block">{{ $errors->first('national_id') }}</span>
                                          </div> 
                                       </div>
                                   
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('birth_date')}}  
                                             <i class="red">*</i> 
                                          </label>
                                          
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">        
                                             <input type="text" name="birth_date"  id="datepicker" class="form-control datepikr" data-rule-required='true' value="{{isset($arr_data['get_user_details']['birth_date'])?$arr_data['get_user_details']['birth_date']:''}}" placeholder="{{translation('enter_birth_date')}}" data-rule-date="true" readonly style="cursor: pointer;">
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
                                                    <div class="check"><div class="inside"></div></div>
                                                </div>
                                            </div> 
                                             <span class="help-block">{{ $errors->first('gender') }}</span>
                                          </div> 
                                       </div>
                                    
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('marital_status')}}  
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">        
                                             
                                             <select class="form-control" name="status">
                                                <option value="" selected> {{translation('select_status')}}</option>
                                                <option value="MARRIED" @if(isset($arr_data['marital_status']) && $arr_data['marital_status'] == 'MARRIED') selected @endif>{{translation('married')}}</option>
                                                <option value="SINGLE" @if(isset($arr_data['marital_status']) && $arr_data['marital_status'] == 'SINGLE') selected @endif>{{translation('single')}}</option>
                                                <option value="ENGAGED" @if(isset($arr_data['marital_status']) && $arr_data['marital_status'] == 'ENGAGED') selected @endif>{{translation('engaged')}}</option>
                                                <option value="DIVORCED" @if(isset($arr_data['marital_status']) && $arr_data['marital_status'] == 'DIVORCED') selected @endif> {{translation('divorced')}}</option>
                                               
                                             </select>
                                             <span class="help-block">{{ $errors->first('status') }}</span>
                                          </div> 
                                       </div>
                                    
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('year_of_experience')}}  
                                             <i class="red">*</i> 
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">        
                                             <input type="text" name="year_of_experience"  id="year_of_experience" class="form-control" data-rule-required='true' value="{{isset($arr_data['year_of_experience'])?$arr_data['year_of_experience']:''}}" placeholder="{{translation('enter_year_of_experience')}}"  data-msg-maxlength="{{translation('year_of_experience_can_not_be_greater_than_2_digits')}}" data-rule-maxlength="2">
                                             <span class="help-block">{{ $errors->first('year_of_experience') }}</span>
                                          </div> 
                                       </div>
                                  
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('mobile_no')}}
                                          <i class="red">*
                                          </i>
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">
                                             <input type="text" name="mobile_no" class="form-control" data-rule-required='true' data-rule-number='true' data-rule-minlength='10' data-rule-maxlength='14' value="{{isset($arr_data['get_user_details']['mobile_no'])?$arr_data['get_user_details']['mobile_no']:''}}" data-msg-minlength="{{translation('mobile_number_should_be_at_least_10_digits')}}" data-msg-maxlength="{{translation('mobile_number_should_be_at_most_14_digits')}}" placeholder="{{translation('enter_mobile_no')}}">
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
                                             <input type="text" name="telephone_no" class="form-control" data-rule-required='true' data-rule-number='true' data-rule-minlength='6' data-rule-maxlength='14' data-msg-minlength="{{translation('telephone_number_should_be_at_least_6_digits')}}" data-msg-maxlength="{{translation('telephone_number_should_be_at_most_14_digits')}}"  value="{{isset($arr_data['get_user_details']['telephone_no'])?$arr_data['get_user_details']['telephone_no']:''}}" placeholder="{{translation('enter_telephone_number')}}">
                                             <span class='help-block'>{{ $errors->first('telephone_no') }}
                                             </span>
                                          </div>
                                       </div>
                                  
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('qualification_degree')}}
                                            <i class="red">*</i>
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">        
                                             <input type="text" name="qualification_degree" pattern="^[a-zA-Z0-9 ,]*$" id="qualification_degree" class="form-control" data-rule-required='true' value="{{isset($arr_data['qualification_degree'])?$arr_data['qualification_degree']:''}}" placeholder="{{translation('enter_qualificationdegree')}}">
                                             <span class="help-block">{{ $errors->first('qualification_degree') }}</span>
                                          </div> 
                                       </div>
                                   
                                 </div>
                        </div>
           <div class="form-group">
            <div class="col-sm-9 col-lg-10">
               <a href="{{ url($school_admin_panel_slug.'/canteen/manage_canteen_staff') }}" class="btn btn-primary">{{translation('back')}}</a> 
               <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('update')}}</button>
            </div>
          </div>
         </form>
      </div>
      </div>
   </div>
</div>
</div>  
<script type="text/javascript">

   $(document).on("change",".validate-image", function()
    {            
        var file=this.files;
        validateImage(this.files, 250,250);
    });

   
   
</script>

<script>
    $(function () {
        $("#datepicker").datepicker({
            todayHighlight: true,
            autoclose: true,
            format:'yyyy-mm-dd',
            endDate: new Date()
        });
        $("#datepicker2").datepicker({
            todayHighlight: true,
            autoclose: true,
             format:'yyyy-mm-dd'
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
       

         $(document).on("change","#image", function()
        {            
            var file=this.files;
            var reader = new FileReader();
                        reader.readAsDataURL(file[0]);

                        reader.onload = function (e) 
                        {
                                var image = new Image();
                                image.src = e.target.result;
                                $('.img-preview2').attr('src',image.src);
                        }
            validateImage(this.files, 250,250);
            $('.img-preview2').attr('src','{{url('/')}}/uploads/profile_image/default.png');
        });

    });
</script>


<!-- script to init map for auto complete geo location for address -->
       <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{config('constants.GOOGLE_API_KEY')}}&libraries=places"></script>

 <script type="text/javascript">
  
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
  glob_options.types = ['address'];

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
      $('#latitude').val(place.geometry.location.lat());
      $('#longitude').val(place.geometry.location.lng());
      /*$('#city').val(place.address_components[4].short_name);*/
      var address = $('#location').val();
      $(".map-section-block").html('<iframe src="https://www.google.com/maps/embed/v1/place?q='+address+'&amp;key={{config('constants.GOOGLE_API_KEY')}}" width="100%" height="642" frameborder="0" style="border:0" allowfullscreen></iframe>');
      
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

  var BASE_URL = "{{url('/')}}";

  window.onload = function () {
        setTimeout(function(){ 
            initAutocomplete();
           
        }, 2000);


    };

</script> 
       <!-- function for geocomplete end  -->  

<!-- END Main Content --> 
@endsection