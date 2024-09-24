@extends('parent.layout.master')    
@section('main_content')
<!-- <style type="text/css">
 .profile-img{width: 150px;
height: 150px;
border-radius: 50% !important;
overflow: hidden;
padding: 0;}
.profile-img img{height: 100% !important;width: 100% ;}
</style> -->
        
      @if(\Session::get('locale') == 'fr')
      <style type="text/css">
        .fileupload.fileupload-new .btn.btn-default.btn-file:before{content: 
          'Sélectionnez une photo'}
      </style>
      @else
      <style type="text/css">
        .fileupload.fileupload-new .btn.btn-default.btn-file:before{content: "Select Photo";}
      </style>
      @endif
     
        <div id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <a href="{{ url($parent_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
                </li>
                <span class="divider">
                    <i class="fa fa-angle-right"></i>
                </span> 
                <li>
                  <a href="{{ url($parent_panel_slug.'/profile') }}">{{translation('my_profile')}}</a>
                </li>
                <span class="divider">
                    <i class="fa fa-angle-right"></i>
                </span> 
                <li class="active">{{translation('edit_profile')}}</li>
            </ul>
        </div>

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
      <div class="box-content edit-btns">
         @include('schooladmin.layout._operation_status')
         <div class="tobbable">
         {!! Form::open([ 'url' => $module_url_path.'/update',
         'method'=>'POST',
         'id'=>'validation-form1',
         'name'=>'validation-form1',
         'class'=>'form-horizontal', 
         'enctype'=>'multipart/form-data'
         ]) !!}
        
          <div class="col-md-12 ajax_messages">
            <div class="alert alert-danger" id="error" style="display:none;"></div>
            <div class="alert alert-success" id="success" style="display:none;"></div>
         </div>
         <div  >
                
                   
                        <div >
                            <input type="hidden" name="enc_id" id="id" value="{{isset($arr_data['id']) ? base64_encode($arr_data['id']) : 0}}">
                            <input type="hidden" name="oldimage" value="{{$arr_data['profile_image']}}">
                                    <br>

                            <div class="col-sm-8 col-sm-offset-4 col-lg-8 col-lg-offset-3">
                              <div class="profile-section-block">
                                    <div class="profile-img-block">
                                      @if(isset($arr_data['profile_image']) && $arr_data['profile_image'] != '' &&   
            file_exists($profile_image_base_img_path.'/'.$arr_data['profile_image']) )
                                        <input type="hidden" name="old_image" value="{{$arr_data['profile_image']}}">
                                        <div class="pro-img"><img src="{{ $profile_image_public_img_path }}/{{$arr_data['profile_image']}}" class="img-responsive img-preview2" alt=""></div>
                                      @else
                                        <div class="pro-img"><img src="{{url('/')}}/uploads/profile_image/default.png" class="img-responsive img-preview2" alt=""></div>
                                      @endif

                                        <div class="update-pic-btns">
                                            <button class="up-btn"> <span><i class="fa fa-camera"></i></span></button>
                                            <input style="height: 100%; width: 100%; z-index: 99;" id="logo-id2" name="image" class="attachment_upload" type="file" data-validation-allowing="jpg, png, gif">
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                  </div>
                          </div>
                                  
        
                                    <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('first_name')}} <i class="red">*</i></label>
                                        <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                            <input type="text" name="first_name" placeholder="{{translation('enter')}} {{translation('first_name')}}" class="form-control" data-rule-required='true' pattern="[a-zA-Z àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" data-rule-maxlength='255' value="{{isset($arr_data['first_name'])?$arr_data['first_name']:''}}">
                                            <span class='help-block'>{{ $errors->first('first_name') }}</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('last_name')}} <i class="red">*</i></label>
                                        <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                           <input type="text" name="last_name" class="form-control" data-rule-required='true' pattern="[a-zA-Z àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" data-rule-maxlength='255' placeholder="{{translation('enter')}} {{translation('last_name')}}"  value="{{isset($arr_data['last_name'])?$arr_data['last_name']:''}}">
                                            <span class='help-block'>{{ $errors->first('last_name') }}</span>
                                        </div>
                                    </div>


                                    
                                    
                                     <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('email')}} 
                                          <i class="red">*</i></label>
                                        <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                              <input class="form-control" data-rule-required='true'  name="email" type="text"
                                               data-rule-email="true" 
                                              value="{{isset($arr_data['email'])?$arr_data['email']:''}}" 
                                              placeholder="{{translation('enter')}} {{translation('email')}}" 
                                              readonly style="cursor: pointer;"  />
                                              <span class='help-block'>{{ $errors->first('email') }}</span>
                                        </div>
                                        <div class="col-sm-9 col-lg-4 controls">
                                          <a href="javascript:void(0)" class="btn btn-info" onClick="changeEmail();">{{translation('change_email')}}</a>
                                        </div>
                                    </div>
                                     <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('national_id')}} 
                                          <i class="red">*</i></label>
                                        <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                              <input class="form-control"  data-rule-required='true'  name="national_id" type="text" value="{{isset($arr_data['national_id'])?$arr_data['national_id']:''}}"   pattern="[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$"  placeholder="{{translation('enter')}} {{translation('national_id')}}" />
                                              <span class='help-block'>{{ $errors->first('national_id') }}</span>
                                        </div>
                                    </div>

                                     <div class="form-group">
                                            <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('birth_date')}} <i class="red">*</i></label>
                                            <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                                <input class="form-control datepikr" name="birth_date"
                                                 id="datepicker" placeholder="{{translation('enter')}} {{translation('birth_date')}}"  type="text" data-rule-required='true' value="{{isset($arr_data['birth_date'])?$arr_data['birth_date']:''}}" />
                                                <span class='help-block'>{{ $errors->first('birth_date') }}</span>
                                            </div>
                                        </div>
                                      
                                     <div class="form-group">
                                     <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('gender')}} <i class="red">*</i></label>
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
                                          <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('occupation')}} </label>
                                          <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                            <input class="form-control" name="occupation" type="text" value="{{isset($arr_data['get_parent_details']['occupation'])?$arr_data['get_parent_details']['occupation']:''}}" placeholder="{{translation('enter')}} {{translation('occupation')}}"   />
                                             <span class='help-block'>{{ $errors->first('occupation') }}</span>
                                          </div>
                                    </div>  

                                    <div class="form-group">
                                          <label class="col-sm-3 col-md-4 col-lg-3 control-label" >{{translation('address')}}  
                                             <i class="red">*</i> 
                                          </label>
                                          <div class="col-sm-9 col-lg-4 controls">        
                                             <input type="hidden" name="latitude"  id="latitude" class="field" value="{{$arr_data['latitude']}}">
                                             <input type="hidden" name="longitude"  id="longitude" class="field" value="{{$arr_data['longitude']}}">
                                             <input type="text" name="address" id="location" data-rule-required='true'  class="form-control" value="{{$arr_data['address']}}" />
                                             <span class="help-block">{{ $errors->first('address') }}</span>
                                          </div> 
                                       </div>

                                    {{-- <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('city')}}</label>
                                        <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                          <input class="form-control" name="city" id="city" placeholder="City"  value="{{isset($arr_data['city'])?$arr_data['city']:''}}"/>
                                          <span class='help-block'>{{ $errors->first('city') }}</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('country')}}</label>
                                        <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                          <input class="form-control" name="country" placeholder="Country"  id="country" value="{{isset($arr_data['country'])?$arr_data['country']:''}}" />
                                          <span class='help-block'>{{ $errors->first('country') }}</span>
                                        </div>
                                    </div>
 --}}
                                     <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('country')}}</label>
                                        <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                            <div class="frmSearch relative-block">
                                              <input type="text" name="country" id="country" class="form-control" data-rule-required='true' placeholder="{{translation('enter_country')}}" value="{{isset($arr_data['country'])?$arr_data['country']:''}}" autocomplete="off"/>
                                              <div class="suggestion-box autoselect-drop" id="suggesstion-box-country" style="height: 200px;display: none"></div>
                                            </div>
                                        <span class='help-block'>{{ $errors->first('country') }}</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('city')}}</label>
                                        <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                            <div class="frmSearch relative-block">
                                              <input type="text" name="city" id="locality" class="form-control" data-rule-required='true' placeholder="{{translation('enter_city')}}" value="{{isset($arr_data['city'])?$arr_data['city']:''}}" autocomplete="off"/>
                                              <div class="suggestion-box autoselect-drop" id="suggesstion-box" style="height: 200px;display: none"></div>
                                            </div>
                                          <span class='help-block'>{{ $errors->first('city') }}</span>
                                        </div>
                                    </div>


                                       <div class="form-group">
                                            <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('mobile_no')}} <i class="red">*</i></label>
                                            <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                                <input class="form-control" name="mobile_no" type="text"  data-rule-required='true' 
                                                 data-rule-minlength='10' data-rule-maxlength='14'
                                                 data-msg-minlength="{{translation('mobile_number_should_be_at_least_10_digits')}}" data-msg-maxlength="{{translation('mobile_number_should_be_at_most_14_digits')}}" 
                                                 placeholder="{{translation('enter_mobile_number')}}" 

                                                data-rule-number = "true"  value="{{isset($arr_data['mobile_no'])?$arr_data['mobile_no']:''}}" />
                                                <span class='help-block'>{{ $errors->first('mobile_no') }}</span>
                                            </div>
                                        </div>


                                       <div class="form-group">
                                          <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('alternate_mobile_no')}}<i class="red">*</i></label>
                                          <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                             <input class="form-control" name="alternate_mobile_no" type="text" value="{{isset($arr_data['get_parent_details']['alternate_mobile_no'])?$arr_data['get_parent_details']['alternate_mobile_no']:''}}"  data-rule-required='true'   
                                                 data-rule-minlength='10' data-rule-maxlength='14'
                                                 data-msg-minlength="{{translation('mobile_number_should_be_at_least_10_digits')}}" data-msg-maxlength="{{translation('mobile_number_should_be_at_most_14_digits')}}" 
                                                 placeholder="{{translation('enter_alternate_mobile_number')}}"
                                                 data-rule-number = "true" 

                                             />
                                              <span class='help-block'>{{ $errors->first('alternate_mobile_no') }}</span>
                                          </div>
                                          </div>
                                   <div class="form-group">
                                      <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('qualification_degree')}}</label>
                                      <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                         <input class="form-control"  name="qualification_degree" type="text" value="{{isset($arr_data['get_parent_details']['qualification_degree'])?$arr_data['get_parent_details']['qualification_degree']:''}}"     placeholder="{{translation('enter')}} {{translation('qualification_degree')}}" />
                                         <span class='help-block'>{{ $errors->first('qualification_degree') }}</span>
                                      </div>
                                  </div>
                                

                                  <div class="form-group">
                                      <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('relation')}}</label>
                                      <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                        <select name="relation" id="relation" class="form-control">
                                                <option value="MOTHER" @if($arr_data['get_parent_details']['relation']=="MOTHER") selected="selected" @endif>{{translation('mother')}}
                                                </option>
                                                <option value="FATHER" @if($arr_data['get_parent_details']['relation']=="FATHER") selected="selected" @endif>{{translation('father')}}
                                                </option>
                                                <option value="BROTHER"
                                                @if($arr_data['get_parent_details']['relation']=="BROTHER") selected="selected" @endif>{{translation('brother')}}
                                                </option>
                                                <option value="SISTER"@if($arr_data['get_parent_details']['relation']=="SISTER") selected="selected" @endif>{{translation('sister')}}</option>
                                                <option value="GUARDIAN"@if($arr_data['get_parent_details']['relation']=="GUARDIAN") selected="selected" @endif>{{translation('guardian')}}</option>
                                        </select>
                                             <span class='help-block'>{{ $errors->first('relation') }}</span>
                                      </div>
                                   </div>


                                    <div class="form-group">
                                      <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('status')}}</label>
                                      <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                         <select id="marital_status" name="marital_status"  class="form-control">
                                                 
                                                <option value="MARRIED"@if($arr_data['get_parent_details']['marital_status']=="MARRIED") selected="selected" @endif>{{translation('married')}}</option>
                                                <option value="SINGLE"@if($arr_data['get_parent_details']['marital_status']=="SINGEL") selected="selected" @endif>{{translation('single')}}</option>
                                        </select>
                                         <span class='help-block'>{{ $errors->first('marital_status') }}</span>
                                       </div>
                                   </div>

                                   <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('language')}}</label>
                                        <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                            <select id="language" name="language" class="form-control"  onChange="showNote();">
                                              <option value="">{{translation('select_language')}}</option>
                                              <option value="en" @if(isset($parent['language']) && $parent['language']=='en') selected @endif>ENGLISH</option>
                                              <option value="fr" @if(isset($parent['language']) && $parent['language']=='fr') selected @endif>FRENCH</option>
                                            </select>
                                        </div>
                                    </div>
                                 
                                   
                                 
                        </div>
                  
            
         </div>
           <div class="form-group">
            <div class="col-sm-3 col-md-4 col-lg-3" >
            </div>
            <div class="col-sm-9 col-lg-4 controls">
               <a href="{{ url($parent_panel_slug.'/profile') }}" class="btn btn-primary">{{translation('back')}}</a> 
               <input type="submit" name="update" value="{{translation('update')}}" class="btn btn-primary">
            </div>

             <div class="col-sm-12 col-md-12 col-lg-12">
             <div class="map-block">
             @if(isset($arr_data['address']) && $arr_data['address'] !='')
                   <iframe src="https://www.google.com/maps/embed/v1/place?q={{$arr_data['address']}}&amp;key={{config('constants.GOOGLE_API_KEY')}}" width="100%" height="400" frameborder="0" style="border:0" allowfullscreen></iframe>
             @else
                   <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7010.673388385852!2d77.22197766923573!3d28.529597894093683!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390ce1f0adb989f9%3A0xd7b634f1086a782c!2sDistrict+Court+Saket!5e0!3m2!1sen!2sin!4v1485424825713" width="100%" height="400" frameborder="0" style="border:0"></iframe>
             @endif
             </div>
            </div>

          </div>
         {!! Form::close() !!}
      </div>
      </div>
   </div>
</div>
</div>



    <script>
      var city_ajax_url = "{{url('/parent')}}/get_cities?keyword=";
      var country_ajax_url = "{{url('/parent')}}/get_countries?keyword=";

      var newdate = new Date();
      newdate = (newdate.getFullYear()-20)+'-12-31';
        $("#datepicker").datepicker({
            todayHighlight: true,
            autoclose: true,
             format:'yyyy-mm-dd',
            endDate: newdate
        });
    </script> 

    <script type="text/javascript">
        $('#location').on('change',function(){
        var address = $('#location').val();
        $(".map-block").html('<iframe src="https://www.google.com/maps/embed/v1/place?q='+address+'&amp;key={{config('constants.GOOGLE_API_KEY')}}" width="100%" height="400" frameborder="0" style="border:0" allowfullscreen></iframe>');
      });
    </script>


<script type="text/javascript">

   $(document).on("change",".attachment_upload", function()
    {         
        var file=this.files;
        validateImage(this.files, 250,250);
    });
</script>
<script type="text/javascript">
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
                url  :"{{url($parent_panel_slug)}}/change_email",
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

<!--    Image Upload -->
<script type="text/javascript">
    $(document).ready(function () {
        var brand = document.getElementById('logo-id2');
        brand.className = 'attachment_upload';
        /*brand.onchange = function () {
            document.getElementById('fakeUploadLogo').value = this.value.substring(12);
            
        };*/

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('.img-preview2').attr('src', e.target.result);

                };

                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#logo-id2").change(function () {
            readURL(this);
        });

    });
</script>


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
      $('#locality').val(place.address_components[2].short_name);
      $('#country').val(place.address_components[4].long_name);
      
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

  function hideBox(val) {
      $("#locality").val(val);
      $("#suggesstion-box").hide();
  }

   function selectCity(val) {
      $("#country").val(val);
      $("#suggesstion-box-country").hide();
   }

    function showNote()
    {
      var language = "{{isset($parent['language'])?$parent['language']:''}}";
      var change_language = $('#language').val();
      if(language!= change_language)
      {
        swal({
              title: "{{translation('warning')}}",
              text: "{{translation('on_change_of_language_your_account_will_be_logged_out')}}",
              icon: "warning",
              confirmButtonText: '{{translation('ok')}}',
              closeOnConfirm: true,
              dangerMode: true,
            });
      }
    }

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

