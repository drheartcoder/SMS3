@extends('admin.layout.master')    
@section('main_content')
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
         <a href="{{ url($admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}}
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
         @include('admin.layout._operation_status')
         <div class="tobbable">
         {!! Form::open([ 'url' => $module_url_path.'/update',
         'method'=>'POST',
         'id'=>'validation-form1',
         'name'=>'validation-form1',
         'class'=>'form-horizontal', 
         'enctype'=>'multipart/form-data'
         ]) !!}
       
         

        <input type="hidden" name="enc_id" value="{{isset($arr_data['user']['id']) ? base64_encode($arr_data['user']['id']) : 0}}">
        <br>
              <div class="form-group">
                 <label class="col-sm-3 col-lg-2 control-label">{{translation('profile_image')}} <i class="red">*</i> </label>
                 <div class="col-sm-9 col-lg-10 controls">
                    <div class="fileupload fileupload-new" data-provides="fileupload">
                       <div class="fileupload-new img-thumbnail" style="width: 110px; height: 110px;border-radius: 50% !important;padding: 0;">
                          @if(isset($arr_data['user']['profile_image']) && !empty($arr_data['user']['profile_image']))
                          <img style="height: 100%" src="{{$profile_image_public_img_path.$arr_data['user']['profile_image'] }}">
                          @else
                          <img style="height: 100%" src="{{url('/').'/uploads/default.png' }}">
                          @endif
                       </div>
                       <div class="fileupload-preview fileupload-exists img-thumbnail" style="width: 110px; height: 110px;border-radius: 50% !important;padding: 0; line-height: 20px;"></div>
                       <div>
                          <span class="btn btn-default btn-file" style="height:32px;">
                          <span class="fileupload-new">{{translation('select_image')}}</span>
                          <span class="fileupload-exists">{{translation('change')}}</span>
                          <input type="file"  data-validation-allowing="jpg, png, gif" class="file-input news-image validate-image" name="image" id="image"  /><br>
                          <input type="hidden" class="file-input " name="oldimage" id="oldimage"  
                             value="{{ $arr_data['user']['profile_image'] }}"/>
                          </span>
                          <a href="#" id="remove" class="btn btn-default fileupload-exists" data-dismiss="fileupload">{{translation('remove')}}</a>
                       </div>
                       <i class="red"> {!! image_validate_note(250,250,2000,2000) !!} </i>
                       <span for="image" id="err-image" class="help-block">{{ $errors->first(' image') }}</span>
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
                    <input type="text" name="first_name" class="form-control" data-rule-required='true' data-rule-pattern="^[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" data-rule-maxlength='255' value="{{isset($arr_data['user']['first_name'])?$arr_data['user']['first_name']:''}}">
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
                    <input type="text" name="last_name" class="form-control" data-rule-required='true' data-rule-pattern="^[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" data-rule-maxlength='255' value="{{isset($arr_data['user']['last_name'])?$arr_data['user']['last_name']:''}}">
                    <span class='help-block'>{{ $errors->first('last_name') }}
                    </span>
                 </div>
              </div>

             <div class="form-group">
                <label class="col-sm-3 col-lg-2 control-label">{{translation('mobile_no')}}
                <i class="red">*
                </i>
                </label>
                <div class="col-sm-9 col-lg-4 controls">
                   <input type="text" name="mobile_no" class="form-control" data-rule-required='true' data-rule-minlength='10' data-rule-maxlength='14' data-msg-minlength="{{translation('mobile_number_should_be_at_least_10_digits')}}" data-msg-maxlength="{{translation('mobile_number_should_be_at_most_14_digits')}}"value="{{isset($arr_data['user']['mobile_no'])?$arr_data['user']['mobile_no']:''}}" data-rule-number='true'>
                   <span class='help-block'>{{ $errors->first('mobile_no') }}
                   </span>
                </div>
             </div>
                                    
             <div class="form-group">
                <label class="col-sm-3 col-lg-2 control-label">{{translation('address')}}  
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
                <label class="col-sm-3 col-lg-2 control-label">{{translation('email')}}  
                   <i class="red">*</i> 
                </label>
                <div class="col-sm-9 col-lg-4 controls">        
                   <input type="text" name="email"  id="email" class="form-control" data-rule-required='true' value="{{isset($arr_data['user']['email'])?$arr_data['user']['email']:''}}"   data-rule-email="true">
                   <span for="email" class="help-block">{{ $errors->first('email') }}</span>
                </div> 
             </div>
                     
           <div class="form-group">
            <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
               <a href="{{ url($admin_panel_slug.'/dashboard') }}" class="btn btn-primary">{{translation('back')}}</a> 
               <input type="submit" name="update" value="{{translation('update')}}" class="btn btn-primary">
            </div>
          </div>
         {!! Form::close() !!}
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

 <script async="" type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{config('constants.GOOGLE_API_KEY')}}&libraries=places"></script>

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

  var BASE_URL = "{{url('/')}}";

  window.onload = function () {
        setTimeout(function(){ 
            initAutocomplete();
           
        }, 2000);


    };

  function showNote()
  {
    var language = "{{isset($arr_data['school_admin']['language'])?$arr_data['school_admin']['language']:''}}";
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

</script> 
<!-- END Main Content --> 
@endsection

