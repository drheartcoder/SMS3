@extends('schooladmin.layout.master')    
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

          <input type="hidden" name="enc_id" id="id" value="{{isset($arr_data['user']['id']) ? base64_encode($arr_data['user']['id']) : 0}}">

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
           <label class="col-sm-3 col-md-4 col-lg-3 control-label" >{{translation('first_name')}}
           <i class="red">*</i>
           </label>
           <div class="col-sm-9 col-lg-4 controls">

              <input type="text" name="first_name" class="form-control" data-rule-required='true' data-rule-pattern="^[a-zA-Z àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" data-rule-maxlength='255' value="{{isset($arr_data['user']['first_name'])?$arr_data['user']['first_name']:''}}">

              <span class='help-block'>{{ $errors->first('first_name') }}
              </span>
           </div>
        </div>

        <div class="form-group">
           <label class="col-sm-3 col-md-4 col-lg-3 control-label" >{{translation('last_name')}}
           <i class="red">*
           </i>
           </label>
           <div class="col-sm-9 col-lg-4 controls">

              <input type="text" name="last_name" class="form-control" data-rule-required='true' data-rule-pattern="^[a-zA-Z àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" data-rule-maxlength='255' value="{{isset($arr_data['user']['last_name'])?$arr_data['user']['last_name']:''}}">

              <span class='help-block'>{{ $errors->first('last_name') }}
              </span>
           </div>
        </div>

           <div class="form-group">
              <label class="col-sm-3 col-md-4 col-lg-3 control-label" >{{translation('mobile_no')}}
              <i class="red">*
              </i>
              </label>
              <div class="col-sm-9 col-lg-4 controls">

                 <input type="text" name="mobile_no" class="form-control" data-rule-required='true' data-rule-minlength='10' data-rule-maxlength='14' data-msg-minlength="{{translation('mobile_number_should_be_at_least_10_digits')}}" data-msg-maxlength="{{translation('mobile_number_should_be_at_most_14_digits')}}" value="{{isset($arr_data['user']['mobile_no'])?$arr_data['user']['mobile_no']:''}}" data-rule-number='true'>

                 <span class='help-block'>{{ $errors->first('mobile_no') }}
                 </span>
              </div>
           </div>
        
           <div class="form-group">
              <label class="col-sm-3 col-md-4 col-lg-3 control-label" >{{translation('address')}}  
                 <i class="red">*</i> 
              </label>
              <div class="col-sm-9 col-lg-4 controls">        

                 <input type="hidden" name="latitude"  id="latitude" class="field" value="{{isset($arr_data['user']['latitude'])?$arr_data['user']['latitude']:''}}">
                 <input type="hidden" name="longitude"  id="longitude" class="field" value="{{isset($arr_data['user']['longitude'])?$arr_data['user']['longitude']:''}}">
                 <input type="text" name="address" id="location" data-rule-required='true'  class="form-control" value="{{isset($arr_data['user']['address'])?$arr_data['user']['address']:''}}" />

             
                 <span class="help-block">{{ $errors->first('address') }}</span>
              </div> 
           </div>

           <div class="form-group">
              <label class="col-sm-3 col-md-4 col-lg-3 control-label" >{{translation('email')}}  
                 <i class="red">*</i> 
              </label>
              <div class="col-sm-9 col-lg-4 controls">        

                 <input type="text" name="email" class="form-control" data-rule-required='true'  id="email" value="{{isset($arr_data['user']['email'])?$arr_data['user']['email']:''}}" data-rule-email="true" readonly>

                 <span for="email" class="help-block">{{ $errors->first('email') }}</span>
              </div> 
              <div class="col-sm-9 col-lg-4 controls">
                <a href="javascript:void(0)" class="btn btn-info" id="change_btn" onClick="changeEmail();">{{translation('change_email')}}</a>
              </div>
           </div>

            <div class="form-group">
                <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('set_default_language')}}</label>
                <div class="col-sm-9 col-lg-4 controls">
                    <select id="language" name="language" class="form-control" onChange="showNote();">
                      <option value="">{{translation('select_language')}}</option>
                      <option value="en" @if(isset($arr_data['school_admin']['language']) && $arr_data['school_admin']['language']=='en') selected @endif>ENGLISH</option>
                      <option value="fr" @if(isset($arr_data['school_admin']['language']) && $arr_data['school_admin']['language']=='fr') selected @endif>FRENCH</option>
                    </select>
                </div>
            </div>
                       
           <div class="form-group">
            <div class="col-sm-3 col-md-4 col-lg-3" >
            </div>
            <div class="col-sm-9 col-lg-4 controls">
               <a href="{{ url($school_admin_panel_slug.'/dashboard') }}" class="btn btn-primary">{{translation('back')}}</a> 

               <input type="submit" name="update" value="{{translation('update')}}" class="btn btn-primary">

            </div>
          </div>
         {!! Form::close() !!}
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
          beforeSend: function( xhr ) {
             $("#change_btn").html("<span><i class='fa fa-spinner fa-spin'></i> Processing...</span> ");
             $("#change_btn").attr('disabled', true);
            },
          success:function(data){
            if(data.status=='success')
            {
              $('.ajax_messages').show();
              $('#success').show();
              $('#success').text("{{translation('change_email_link_sent_successfully_to_your_email_id')}}");
              setTimeout(function(){
                  $('.ajax_messages').hide();
              }, 3000);  
              $("#change_btn").attr('disabled', false);
                 $("#change_btn").html('<span>Change Email</span>');
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

  function showNote()
  {
    var language = "{{isset($arr_data['school_admin']['language'])?$arr_data['school_admin']['language']:''}}";
    var change_language = $('#language').val();
    if(language!= change_language)
    {
      swal({
            title: "{{translation('warning')}}",
            text: "{{translation('do_you_really_want_to_set_this_language_as_default')}}?\n {{translation('once_you_set_this_language_your_acoount_will_be_logged_out')}}",
            icon: "warning",
            confirmButtonText: '{{translation('ok')}}',
            closeOnConfirm: true,
            dangerMode: true,
          });
    }
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

