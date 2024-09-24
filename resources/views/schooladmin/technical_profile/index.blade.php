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
      <i class="fa {{$module_icon}}">
      </i>
      </span> 
      <li class="active">  {{ isset($page_title)?$page_title:"" }}
      </li>
   </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
    <div class="page-title new-agetitle">
        <div>
            <h1><i class="fa fa-pencil"></i>{{translation('profile')}}</h1>
        </div>
    </div>
    <!-- END Page Title -->

<!-- BEGIN Main Content -->
<div class="row">
<div class="col-md-12">
   <div class="box {{ $theme_color }}">
      <div class="box-title">
         <h3>
            <i class="fa {{$module_icon}}">
            </i>{{ isset($page_title)?$page_title:"" }} 
         </h3>
         <div class="box-tool">
         </div>
      </div>
      <div class="box-content">
         @include('schooladmin.layout._operation_status')
         <div class="tobbable">
         {!! Form::open([ 'url' => $module_url_path.'/update',
         'method'=>'POST',
         'id'=>'validation-form1',
         'name'=>'validation-form1',
         'class'=>'form-horizontal', 
         'enctype'=>'multipart/form-data',
         'onsubmit'=>"return addLoader()"
         ]) !!}
       

                                    <input type="hidden" name="enc_id" id="id" value="{{$enc_id}}">
                                    <br>
                                          <div class="form-group">
                                            <label class="col-sm-3 col-md-4 col-lg-3 control-label" >{{translation('profile_image')}} <i class="red">*</i> </label>
                                               <div class="col-sm-5 col-lg-4 controls">  
                                               <div class="fileupload fileupload-new" data-provides="fileupload">

                                                     <div class="fileupload-new img-thumbnail profile-img img">

                                                         @if(isset($arr_data['user']['profile_image']) && ($arr_data['user']['profile_image'] == "" || !file_exists($base_url.$arr_data['user']['profile_image'] )))
                                                           <img src="{{$image_path}}/default.png">
                                                         @else
                                                           <input type="hidden" name="oldimage" value="{{$arr_data['user']['profile_image']}}">
                                                           <img src="{{$image_path.'/'.$arr_data['user']['profile_image']}}">
                                                         @endif
                                                     </div>
                                                  
                                                      <div class="fileupload-preview fileupload-exists img-thumbnail profile-img" ></div>
                                                      <div >
                                                         <span class="btn btn-default btn-file" style="height:32px;">

                                                         <span class="fileupload-new">{{translation('select_image')}}</span>
                                                         <span class="fileupload-exists">{{translation('change')}}</span>
                                                         <input type="file"  data-validation-allowing="jpg, png, gif" class="file-input news-image validate-image" name="image" id="image"  /><br>
                                                         </span>
                                                         <a href="#" id="remove" class="btn btn-default fileupload-exists" data-dismiss="fileupload">{{translation('remove')}}</a>
                                                      </div>
                                                      <i class="red "> {!! image_validate_note(250,250,2000,2000) !!} </i>
                                                      <span for="image" id="err-image" class="help-block"></span>
                                               </div>
                                               </div>
                                            <div class="clearfix"></div>
                                            <div class="col-sm-6 col-lg-5 control-label help-block-red" style="color:#b94a48;" id="err_logo"></div>
                                            <br/>
                                            <div class="col-sm-6 col-lg-5 control-label help-block-green" style="color:#468847;" id="success_logo"></div>
                                         </div>
         
                                    <div class="form-group">
                                       <label class="col-sm-3 col-lg-2 control-label">{{translation('first_name')}}
                                       <i class="red">*</i>
                                       </label>
                                       <div class="col-sm-9 col-lg-4 controls">
                                          <input type="text" name="first_name" id="first_name" class="form-control" data-rule-required='true' data-rule-pattern="^[a-zA-Z àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" data-rule-maxlength='255' value="{{isset($arr_data['user_details']['first_name'])?$arr_data['user_details']['first_name']:''}}">
                                          <span class='help-block'>{{ $errors->first('first_name') }}
                                          </span>
                                       </div>
                                    </div>

                                    <div class="form-group">
                                       <label class="col-sm-3 col-lg-2 control-label">{{translation('last_name')}}
                                       <i class="red">*
                                       </i>
                                       </label>
                                       <div class="col-sm-9 col-lg-4 controls">
                                          <input type="text" name="last_name" class="form-control" data-rule-required='true' data-rule-pattern="^[a-zA-Z àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" data-rule-maxlength='255' value="{{isset($arr_data['user_details']['last_name'])?$arr_data['user_details']['last_name']:''}}">
                                          <span class='help-block'>{{ $errors->first('last_name') }}
                                          </span>
                                       </div>
                                    </div>
                                       <div class="form-group">
                                          <label class="col-sm-3 col-lg-2 control-label">{{translation('email')}}  
                                             <i class="red">*</i> 
                                          </label>
                                          <div class="col-sm-9 col-lg-4 controls">        
                                             <input type="text" name="email"  id="email" class="form-control" data-rule-required='true'  value="{{$arr_data['user']['email']}}" readonly style="cursor: pointer;">
                                             <span for="email" class="help-block">{{ $errors->first('email') }}</span>
                                          </div> 
                                          <div class="col-sm-9 col-lg-4 controls">
                                             <a href="javascript:void(0)" class="btn btn-info" onClick="changeEmail();">{{translation('change_email')}}</a>
                                          </div>
                                       </div>
                                       <div class="form-group">
                                          <label class="col-sm-3 col-lg-2 control-label">{{translation('mobile_no')}}
                                          <i class="red">*
                                          </i>
                                          </label>
                                          <div class="col-sm-9 col-lg-4 controls">
                                             <input type="text" name="mobile_no" class="form-control" data-rule-required='true' data-rule-minlength='12' data-rule-maxlength='16' data-msg-minlength="Please enter at least 12 digits." data-msg-maxlength="Please enter no more than 16 digits." value="{{$arr_data['user']['mobile_no']}}">
                                             <span class='help-block'>{{ $errors->first('mobile_no') }}
                                             </span>
                                          </div>
                                       </div>
                                       <div class="form-group">
                                          <label class="col-sm-3 col-lg-2 control-label">{{translation('telephone_number')}}
                                          <i class="red">*
                                          </i>
                                          </label>
                                          <div class="col-sm-9 col-lg-4 controls">
                                             <input type="text" name="telephone_no" class="form-control" data-rule-required='true' data-rule-number='true' data-rule-minlength='7' data-rule-maxlength='16' value="{{$arr_data['user']['telephone_no']}}">
                                             <span class='help-block'>{{ $errors->first('telephone_no') }}
                                             </span>
                                          </div>
                                       </div>
                                       <div class="form-group">
                                          <label class="col-sm-3 col-lg-2 control-label">{{translation('address')}}  
                                             <i class="red">*</i> 
                                          </label>
                                          <div class="col-sm-9 col-lg-4 controls">        
                                             <input type="hidden" name="latitude"  id="latitude" class="field" value="{{$arr_data['user']['latitude']}}">
                                             <input type="hidden" name="longitude"  id="longitude" class="field" value="{{$arr_data['user']['longitude']}}">
                                             <input type="text" name="address" id="location" data-rule-required='true'  class="form-control" value="{{$arr_data['user']['address']}}" />
                                         
                                             <span class="help-block">{{ $errors->first('address') }}</span>
                                          </div> 
                                       </div>
                                       <div class="form-group">
                                          <label class="col-sm-3 col-lg-2 control-label">{{translation('country')}}</label>
                                          <div class="col-sm-9 col-lg-4 controls">
 
                                            <div class="frmSearch relative-block">
                                              <input type="text" name="country" id="country" class="form-control" data-rule-required='true' placeholder="{{translation('enter_country')}}" value="{{isset($arr_data['user']['country'])?$arr_data['user']['country']:''}}" autocomplete="off"/>
                                              <div class="suggestion-box autoselect-drop" id="suggesstion-box-country" style="height: 200px;display: none"></div>
                                            </div>  
                                            <span class='help-block'>{{ $errors->first('country') }}</span>
                                          </div>
                                       </div>
                                       <div class="form-group">
                                          <label class="col-sm-3 col-lg-2 control-label">{{translation('city')}}  
                                          </label>
                                          <div class="col-sm-9 col-lg-4 controls">        
                                            <div class="frmSearch relative-block">
                                              <input type="text" name="city" id="locality" class="form-control" data-rule-required='true' placeholder="{{translation('enter_city')}}" value="{{isset($arr_data['user']['city'])?$arr_data['user']['city']:''}}" autocomplete="off"/>
                                              <div class="suggestion-box autoselect-drop" id="suggesstion-box" style="height: 200px;display: none"></div>
                                            </div>
                                         
                                             <span class="help-block">{{ $errors->first('city') }}</span>
                                          </div> 
                                       </div>
                                       <div class="form-group">
                                          <label class="col-sm-3 col-lg-2 control-label">{{translation('national_id')}}  
                                              <i class="red">*</i> 
                                          </label>
                                          <div class="col-sm-9 col-lg-4 controls">        
                                             <input type="text" name="national_id" id="national_id" data-rule-required='true'  class="form-control" value="{{$arr_data['user']['national_id']}}" />
                                         
                                             <span class="help-block">{{ $errors->first('national_id') }}</span>
                                          </div> 
                                       </div>
                                        <div class="form-group">
                                          <label  class="col-sm-3 col-lg-2 control-label">{{translation('gender')}}   
                                          </label>
                                          <div  class="col-sm-9 col-lg-4">        
                                             <div class="radio-btns">  
                                                      <div class="radio-btn">
                                                          <input type="radio" id="f-option" name="gender" value="MALE" checked @if(isset($arr_data['user']['gender']) && $arr_data['user']['gender'] == 'MALE') checked @endif>
                                                          <label for="f-option">{{translation('male')}}</label>
                                                          <div class="check"></div>
                                                      </div>
                                                      <div class="radio-btn">
                                                          <input type="radio" id="s-option" name="gender" value="FEMALE" @if(isset($arr_data['user']['gender']) && $arr_data['user']['gender'] == 'FEMALE') checked @endif>
                                                          <label for="s-option">{{translation('female')}}</label>
                                                          <div class="check"><div class="inside"></div></div>
                                                      </div>
                                                  </div> 
                                             <span class="help-block">{{ $errors->first('gender') }}</span>
                                          </div> 
                                       </div>

                                       
                                       <div class="form-group">
                                          <label class="col-sm-3 col-lg-2 control-label">{{translation('birth_date')}}  
                                             <i class="red">*</i> 
                                          </label>
                                          <div class="col-sm-9 col-lg-4 controls">        
                                             <input type="text" name="birth_date"  id="birth_date" class="form-control datepikr" data-rule-required='true' value="{{$arr_data['user']['birth_date']}}">
                                             <span class="help-block">{{ $errors->first('birth_date') }}</span>
                                          </div> 
                                       </div>
                                       <div class="form-group">
                                          <label class="col-sm-3 col-lg-2 control-label">{{translation('year_of_experience')}}  
                                          </label>
                                          <div class="col-sm-9 col-lg-4 controls">        
                                             <input type="text" name="year_of_experience"  id="year_of_experience" class="form-control" data-rule-required='true' value="{{$arr_data['technical_details']['year_of_experience']}}">
                                             <span class="help-block">{{ $errors->first('year_of_experience') }}</span>
                                          </div> 
                                       </div>
                                       <div class="form-group">
                                          <label class="col-sm-3 col-lg-2 control-label">{{translation('qualification_degree')}}
                                          </label>
                                          <div class="col-sm-9 col-lg-4 controls">        
                                             <input type="text" name="qualification_degree"  id="qualification_degree" class="form-control" data-rule-required='true' value="{{$arr_data['technical_details']['qualification_degree']}}">
                                             <span class="help-block">{{ $errors->first('qualification_degree') }}</span>
                                          </div> 
                                       </div>
                      
           <div class="form-group">
            <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
               <a href="{{ url($school_admin_panel_slug.'/dashboard') }}" class="btn btn-primary">{{translation('back')}}</a> 
               <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('update')}}</button>
            </div>
          </div>
         {!! Form::close() !!}
      </div>
      </div>
   </div>
</div>
</div>
<script>

  var city_ajax_url = "{{url('/school_admin')}}/get_cities?keyword=";
  var country_ajax_url = "{{url('/school_admin')}}/get_countries?keyword=";

    $(document).ready(function () {
      var date  = new Date();
      var today = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate();
       $("#birth_date").datepicker({
            todayHighlight: true,
            autoclose: true,
            format:'yyyy-mm-dd',
            endDate: today
        });
    });
</script>
<script type="text/javascript">

   $(document).on("change",".validate-image", function()
    {            
        var file=this.files;
        validateImage(this.files, 250,250);
    });
   
   $("#email").keyup(function()
   {
      $("#email").next('span').html(""); 
   });

   $("#email").on('blur',function(){
      pattern =/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/;
      var email = $("#email").val();

      if (pattern.test(email))
      {          
       
          $("#email").next().html("");
      }
      else
      {
          $("#email").next().html("Please enter valid email");
      }
   });
  
  function changeEmail()
  {
    var first_name = $('#first_name').val();
    var email      = $('#email').val();
    var id         = $('#id').val();
    if(first_name != '')
    {
        $.ajax({
          url  :"{{url('/')}}/{{config('app.project.role_slug.school_admin_role_slug')}}/change_email",
          type :'POST',
          data :{'first_name':first_name, 'email':email , 'id':id  ,'_token':'<?php echo csrf_token();?>'},
          success:function(data){
            if(data.status=='success')
            {
              $('.ajax_messages').show();
              $('#success').show();
              $('#success').text("{{translation('change_email_link_sent_successfully_to_your_email_id')}}");
              setTimeout(function(){
                  $('.ajax_messages').hide();
              }, 3000);  
            }
            else if(data.status=='error')
            {
              $('.ajax_messages').show();
              $('#error').show();
              $('#error').text("{{translation('error_while_sending_change_email_link')}}");
              setTimeout(function(){
                  $('.ajax_messages').hide();
              }, 3000);   
            }
            
          }
        });
    }
  }
   
</script>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key={{config('constants.GOOGLE_API_KEY')}}&libraries=places"></script>

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
<script src="{{url('/')}}/js/city_country.js"></script>
<!-- END Main Content --> 
@endsection

