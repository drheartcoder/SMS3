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
          <a href="{{$module_url_path}}">{{$page_title}}</a>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        <i class=""></i>
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
<!-- BEGIN Main Content -->

<div class="row">
<div class="col-md-12">
   <div class="box box-navy_blue">
      <div class="box-title">
         <h3>
            <i class="fa fa-edit">
            </i>{{ isset($module_title)?$module_title:"" }} 
         </h3>
         <div class="box-tool">
         </div>
      </div>
      <div class="box-content">
         @include('schooladmin.layout._operation_status')
         <div class="tobbable">
            <form method="post" onsubmit="return addLoader()"  action="{{$module_url_path}}/update/{{$enc_id}}"  class="form-horizontal" id="validation-form1" enctype ='multipart/form-data'>
                                {{ csrf_field() }}

               
            
               <div  class="tab-content">
                              <br>
                              <div class="row">
                                 <div class="col-sm-12 col-md-12 col-lg-6">
                                    
                                    <div class="form-group">
                                       <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('profile_image')}}
                                       <i class="red">*</i>
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
                                                      <input type="file"  data-validation-allowing="jpg, png, gif" class="file-input news-image validate-image" name="image" id="image"  /><br>
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
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('email')}}  
                                             <i class="red">*</i> 
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                             <input type="text" name="email" placeholder="{{translation('enter_email')}}" id="email" class="form-control" readonly="true" value="{{$arr_data['get_user_details']['email']}}">
                                             <span class="help-block">{{ $errors->first('email') }}</span>
                                             <span id="err_email" style="display: none;color: red"></span>
                                          </div> 
                                       </div>  
                        
                                    <div class="form-group">
                                       <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('first_name')}}
                                       <i class="red">*</i>
                                       </label>
                                       <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                          <input type="text" name="first_name" class="form-control" data-rule-required='true' data-rule-pattern="^[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" data-rule-maxlength='255' placeholder="{{translation('enter_first_name')}}" value="{{$arr_data['get_user_details']['first_name']}}">
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
                                          <input type="text" name="last_name" class="form-control" data-rule-required='true' data-rule-pattern="^[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" data-rule-maxlength='255' placeholder="{{translation('enter_last_name')}}" value="{{$arr_data['get_user_details']['last_name']}}">
                                          <span class='help-block'>{{ $errors->first('last_name') }}
                                          </span>
                                       </div>
                                    </div>

                                    <div class="form-group">
                                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('level')}}<i class="red">*</i></label>
                                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">
                                                  <select name="level" id="level" class="form-control level" data-rule-required='true' readonly>
                                                    <option value="">{{translation('select_level')}}  </option>
                                                      @if(isset($arr_levels))
                                                          @foreach($arr_levels as $key => $value)
                                                            <option value="{{$value['level_id']}}" @if($arr_data['get_level_class']['level_id']==$value['level_id']) selected @endif>{{$value['level_details']['level_name']}}</option>
                                                          @endforeach
                                                      @endif
                                                  </select>
                                                  <span class='help-block'>{{ $errors->first('level') }}
                                              </div>
                                       </div>
                                    
                                       <div class="form-group">
                                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('class')}}<i class="red">*</i></label>
                                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">
                                                  <select name="class" id="class" class="form-control level-class"  data-rule-required='true' readonly>
                                                    <option value="">{{translation('select_class')}}</option>  
                                                    @if(isset($arr_class))
                                                          @foreach($arr_class as $key => $value)
                                                            <option value="{{$value['id']}}" @if($arr_data['get_level_class']['id']==$value['id']) selected @endif>{{$value['class_details']['class_name']}}</option>
                                                          @endforeach
                                                      @endif
                                                  </select>
                                                  <span class='help-block'>{{ $errors->first('class')}}
                                              </div>
                                       </div>
                                      
                                      <div class="form-group">
                                      <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('special_note')}}  
                                      </label>
                                      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                         <textarea type="text" name="special_note" id="special_note" class="form-control" placeholder="{{translation('enter')}} {{translation('special_note')}}">{{$arr_data['get_user_details']['special_note']}}</textarea>
                                     
                                         <span class="help-block">{{ $errors->first('special_note') }}</span>
                                      </div> 
                                   </div>
                                      
                                      <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('national_id')}}  
                                              <i class="red">*</i> 
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                             <input type="text" name="national_id" id="national_id" data-rule-required="true" class="form-control" value="{{$arr_data['get_user_details']['national_id']}}" pattern="^[A-Za-z0-9]*$" placeholder="{{translation('enter_national_id')}}"/>
                                             <span id="err_nationalid" style="display: none; color: red"></span>
                                             <span class="help-block">{{ $errors->first('national_id') }}</span>
                                          </div> 
                                       </div> 
                                    
                                 </div>

                                 <div class="col-sm-12 col-md-12 col-lg-6">
                                   
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('birth_date')}}  
                                             <i class="red">*</i> 
                                          </label>
                                          
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                             <input type="text" name="birth_date" style="cursor: pointer;" id="datepicker" class="form-control datepikr" data-rule-required='true' placeholder="{{translation('enter_birth_date')}}" value="{{isset($arr_data['get_user_details']['birth_date']) && ($arr_data['get_user_details']['birth_date']!='0000-00-00')?$arr_data['get_user_details']['birth_date']:''}}" data-rule-date="true" readonly>
                                             <span class="help-block">{{ $errors->first('birth_date') }}</span>
                                          </div> 
                                       </div>
                                   
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('gender')}}   
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
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
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('mobile_no')}}
                                          <i class="red">*
                                          </i>
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                             <input type="text" name="mobile_no" class="form-control" data-rule-required='true' data-rule-number='true' data-rule-minlength='10' data-rule-maxlength='14' value="{{$arr_data['get_user_details']['mobile_no']}}" data-msg-minlength="{{translation('mobile_number_should_be_at_least_10_digits')}}" data-msg-maxlength="{{translation('mobile_number_should_be_at_most_14_digits')}}" placeholder="{{translation('enter_mobile_number')}}" value="{{$arr_data['get_user_details']['mobile_no']}}">
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
                                             <input type="text" name="telephone_no" class="form-control" data-rule-required='true' data-rule-number='true' value="{{$arr_data['get_user_details']['telephone_no']}}" data-rule-minlength='6' data-rule-maxlength='14' placeholder="{{translation('enter_telephone_number')}}" data-msg-minlength="{{translation('telephone_number_should_be_at_least_6_digits')}}" data-msg-maxlength="{{translation('telephone_number_should_be_at_most_14_digits')}}" value="{{$arr_data['get_user_details']['telephone_no']}}">
                                             <span class='help-block'>{{ $errors->first('telephone_no') }}
                                             </span>
                                          </div>
                                       </div>

                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('address')}}  
                                             <i class="red">*</i> 
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                             <input type="hidden" name="latitude"  id="latitude" class="field" value="{{$arr_data['get_user_details']['latitude']}}">
                                             <input type="hidden" name="longitude"  id="longitude" class="field" value="{{$arr_data['get_user_details']['longitude']}}">
                                             <input type="text" name="address" id="location" placeholder="{{translation('enter_location')}}" data-rule-required='true'  class="form-control" value="{{$arr_data['get_user_details']['address']}}" />
                                         
                                             <span class="help-block">{{ $errors->first('address') }}</span>
                                          </div> 
                                       </div>
                                    
                                    
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('city')}}  
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                             <input type="text" name="city" id="locality" class="form-control" placeholder="{{translation('enter_city')}}" value="{{$arr_data['get_user_details']['city']}}" />
                                         
                                             <span class="help-block">{{ $errors->first('city') }}</span>
                                          </div> 
                                       </div>
                                   
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('country')}}  
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                             <input type="text" name="country" id="country" class="form-control" placeholder="{{translation('enter_country')}}" value="{{$arr_data['get_user_details']['country']}}" />
                                         
                                             <span class="help-block">{{ $errors->first('country') }}</span>
                                          </div> 
                                       </div>

                                       <div class="form-group">
                                          <label class="col-sm-3 col-lg-4 control-label">{{translation('bus_transport')}}</label>
                                            <div class="col-sm-9 col-lg-8 controls">
                                                <div class="radio-btns">
                                                    <div class="radio-btn">
                                                        <input type="radio" id="yes-option" name="bus_transport" value="yes" onclick="showAddress()" @if(isset($arr_data['bus_transport']) && $arr_data['bus_transport']==1 )checked @endif/>
                                                        <label for="yes-option">{{translation('yes')}}</label>
                                                        <div class="check"></div>
                                                    </div>
                                                    <div class="radio-btn">
                                                        <input type="radio" id="no-option" name="bus_transport" value="no" onclick="hideAddress()" @if(isset($arr_data['bus_transport']) && $arr_data['bus_transport']==0 )checked @endif/>
                                                        <label for="no-option">{{translation('no')}}</label>
                                                        <div class="check">
                                                            <div class="inside"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>  

                                          
                                       </div>
                                       
                                       <div class="form-group" id="pickup_location_div" @if(isset($arr_data['bus_transport']) && $arr_data['bus_transport']==0 )hidden @endif>
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('pickup_address')}}  
                                            
                                          </label>

                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                             <input type="hidden" name="pickup_latitude"  id="pickup_latitude" class="field" value="@if(isset($pickup['latitude'])){{$pickup['latitude']}}@endif">
                                             <input type="hidden" name="pickup_longitude"  id="pickup_longitude" class="field" value="@if(isset($pickup['longitude'])){{$pickup['longitude']}}@endif">
                                             <input type="text" name="pickup_address" id="pickup_location"  class="form-control" value="{{$arr_data['pickup_address']}}" placeholder="{{translation('enter_location')}}"/>
                                         
                                             <span class="help-block">{{ $errors->first('pickup_address') }}</span>
                                          </div> 
                                       </div>
                                    
                                       <div class="form-group" id="drop_location_div" @if(isset($arr_data['bus_transport']) && $arr_data['bus_transport']==0 )hidden @endif>
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('drop_address')}}  
                                             
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                             <input type="hidden" name="drop_latitude"  id="drop_latitude" class="field" value="@if(isset($drop['latitude'])){{$drop['latitude']}}@endif">
                                             <input type="hidden" name="drop_longitude"  id="drop_longitude" class="field" value="@if(isset($drop['longitude'])){{$drop['longitude']}}@endif">
                                             <input type="text" name="drop_address" id="drop_location" class="form-control" value="@if(isset($arr_data['drop_address'])){{$arr_data['drop_address']}}@endif" placeholder="{{translation('enter_location')}}"/>
                                             <span class="help-block">{{ $errors->first('drop_address') }}</span>
                                          </div> 
                                       </div>

                                 </div>
                        </div> 
         </div>

      
           <div class="form-group">
            <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
               <a href="{{ url($school_admin_panel_slug.'/professor') }}" class="btn btn-primary">{{translation('back')}}</a> 
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

   $(document).on("change",".validate-image", function()
    {            
        var file=this.files;
        validateImage(this.files, 250,250);
    });
</script>

 <script type="text/javascript">
   $(".js-example-basic-multiple").select2();
   /* $('#example-getting-started').multiselect();  */
      
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

        $('#location').on('change',function(){
          var address = $('#location').val();
          $(".map-section-block").html('<iframe src="https://www.google.com/maps/embed/v1/place?q='+address+'&amp;key={{config('constants.GOOGLE_API_KEY')}}" width="100%" height="642" frameborder="0" style="border:0" allowfullscreen></iframe>');
        });
        $(".map-show").on("click", function () {
            $(".map-section-block").slideToggle("slow");
            var address = $('#location').val();
            if(address == '')
            {
                $(".map-section-block").html('<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3749.4162339110662!2d73.78048131497653!3d19.991037986569637!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bddeae4e0245423%3A0xeb6a128eb0f552ae!2sWebwing+Technologies!5e0!3m2!1sen!2sin!4v1523085614284" allowfullscreen></iframe>');
            }
            else
            {
              $(".map-section-block").html('<iframe src="https://www.google.com/maps/embed/v1/place?q='+address+'&amp;key={{config('constants.GOOGLE_API_KEY')}}" width="100%" height="642" frameborder="0" style="border:0" allowfullscreen></iframe>');
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
                    city : 'long_name'
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
  glob_options.types = ['address'];
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
  glob_options.types = ['address'];
 
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

<!-- END Main Content --> 
@endsection