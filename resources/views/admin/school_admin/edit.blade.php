    @extends('admin.layout.master')                
    @section('main_content')
    <!-- BEGIN Page Title -->

<style type="text/css">
 .profile-img{width: 120px; height: 120px;border-radius: 50% !important;overflow: hidden;padding: 0;}
 .profile-img img{height: 100% !important;width: 100% ;}
</style>

    <!-- BEGIN Breadcrumb -->
    <div id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url($admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
            </li>
            <span class="divider">
                <i class="fa fa-angle-right"></i>
                <i class="fa fa-question-circle"></i>
                <a href="{{ $module_url_path }}">{{ $module_title or ''}}</a>
            </span> 
            <span class="divider">
                <i class="fa fa-angle-right"></i>
                  <i class="fa fa-edit"></i>
            </span>
            <li class="active">{{ $page_title or ''}}</li>
        </ul>
      </div>
    <!-- END Breadcrumb -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="fa fa-user"></i> {{ isset($module_title)?$module_title:"" }}</h1>
    </div>
</div>
<!-- END Page Title -->

    <!-- BEGIN Main Content -->
    <div class="row">
      <div class="col-md-12">

          <div class="box">
            <div class="box-title">
              <h3>
                <i class="fa fa-edit"></i>
                {{ isset($page_title)?str_singular($page_title):"" }}
            </h3>
            <div class="box-tool">
                <a data-action="collapse" href="#"></a>
                <a data-action="close" href="#"></a>
            </div>
        </div>
        <div class="box-content">

          @include('admin.layout._operation_status')  
          
                <form class="form-horizontal"  onsubmit="return addLoader()"  id="validation-form1" method="POST" action="{{ $module_url_path }}/update/{{$enc_id}}" enctype="multipart/form-data">
                  {{ csrf_field() }}
              
                              <div class="form-group">
                                 <label class="col-sm-3 col-lg-2 control-label">{{translation('profile_image')}} <i class="red">*</i> </label>
                                 <div class="col-sm-9 col-lg-10 controls">
                                    <div class="fileupload fileupload-new" data-provides="fileupload">
                                       <div class="fileupload-new img-thumbnail profile-img img">
                                          @if(isset($arr_data['user']['profile_image']) && !empty($arr_data['user']['profile_image']))
                                          <img src="{{$user_profile_public_img_path.$arr_data['user']['profile_image'] }}">
                                          @else
                                          <img src="{{url('/').'/uploads/profile_image/default.png' }}">
                                          @endif
                                       </div>
                                       <div class="fileupload-preview fileupload-exists img-thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                                       <div>
                                          <span class="btn btn-default btn-file" style="height:32px;">
                                          <span class="fileupload-new">{{translation('select_image')}}</span>
                                          <span class="fileupload-exists">{{translation('change')}}</span>
                                          <input type="file"  data-validation-allowing="jpg, png, gif" class="file-input news-image validate-image" name="image" id="image"  /><br>
                                          <input type="hidden" class="file-input " name="oldimage" id="oldimage"  
                                             value="{{ isset($arr_data['user']['profile_image'])?$arr_data['user']['profile_image']:'' }}"/>
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
                              <div class="clearfix"></div>
                              
                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('first_name')}} 
                                    <i class="red">*</i> 
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">
                                    <input type="text" name="first_name" data-rule-required='true'  data-rule-pattern="^[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" class="form-control" value="{{isset($arr_data['user']['first_name'])?ucwords($arr_data['user']['first_name']):''}}" placeholder="{{translation('enter_first_name')}}"   />
                                    <span class='help-block'>{{ $errors->first('first_name') }}</span>
                                </div>
                              </div>
                              <div class="clearfix"></div>

                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('last_name')}} 
                                  <i class="red">*</i> 
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">        
                                    <input type="text" name="last_name" class="form-control" value="{{isset($arr_data['user']['last_name'])?ucwords($arr_data['user']['last_name']):''}}"  data-rule-required="true" data-rule-pattern="^[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" placeholder="{{translation('enter_last_name')}}"/>  
                                    <span class='help-block'>{{ $errors->first('last_name') }}</span>
                                </div> 
                              </div>
                              <div class="clearfix"></div>

                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('email')}} 
                                  <i class="red">*</i> 
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">        
                                    <input type="text" name="email" class="form-control"  value="{{isset($arr_data['user']['email'])?$arr_data['user']['email']:''}}" data-rule-required="true" data-rule-email="true" id="email" readonly="true" placeholder="{{translation('enter_email')}}" />  
                                    <span class='help-block'>{{ $errors->first('email') }}</span>
                                    <span id="err_email" style="color: red"></span>
                                </div> 
                              </div>
                              <div class="clearfix"></div>

                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('mobile_no')}} 
                                  <i class="red">*</i> 
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">        
                                    <input type="text" name="mobile_no" class="form-control"  value="{{isset($arr_data['user']['mobile_no'])?$arr_data['user']['mobile_no']:0}}" data-rule-required="true" placeholder="{{translation('enter_mobile_no')}}" data-rule-digits='true' data-rule-minlength='10' data-rule-maxlength='14' data-msg-minlength="{{translation('please_enter_at_least_10_digits')}}." data-msg-maxlength="{{translation('please_enter_not_more_than_14_digits')}}."/>  
                                    <span class='help-block'>{{ $errors->first('mobile_no') }}</span>
                                </div>
                              </div>
                              <div class="clearfix"></div>

                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('telephone_number')}} 
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">        
                                    <input type="text" name="telephone_no" class="form-control"  value="{{isset($arr_data['user']['telephone_no'])?$arr_data['user']['telephone_no']:0}}" placeholder="{{translation('enter_telephone_number')}}" data-rule-minlength='6' data-rule-maxlength='14' data-msg-minlength="{{translation('please_enter_at_least_6_digits')}}." data-msg-maxlength="{{translation('please_enter_not_more_than_14_digits')}}." />  
                                    <span class='help-block'>{{ $errors->first('telephone_no') }}</span>
                                </div> 
                              </div>
                              <div class="clearfix"></div>

                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('national_id')}}  <i class="red">*</i> 
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">        
                                    <input type="text" name="national_id" id="national_id" class="form-control"  value="{{isset($arr_data['user']['national_id'])?$arr_data['user']['national_id']:''}}"  pattern="[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" data-rule-alphanumeric="true" placeholder="{{translation('enter_national_id')}}" data-rule-required="true" />  
                                    <span class='help-block' for="national_id">{{ $errors->first('national_id') }}</span>
                                </div> 
                              </div>
                              <div class="clearfix"></div> 

                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('birth_date')}} 
                                  <i class="red">*</i> 
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">       
                                    <?php
                                      $birth_date = '';
                                      if(isset($arr_data['user']['birth_date']))
                                      {
                                        $date       = explode('-', $arr_data['user']['birth_date']);
                                        $birth_date = $date[1].'/'.$date[2].'/'.$date[0];
                                      }
                                    ?>
                                    <input type="text" name="birth_date" id="birth_date" class="form-control datepikr"  value="{{$birth_date}}" placeholder="{{translation('enter_birth_date')}}" data-rule-required="true"/>  
                                    <span class='help-block'>{{ $errors->first('birth_date') }}</span>
                                </div> 
                              </div>
                              <div class="clearfix"></div>

                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('gender')}} 
                                  <i class="red">*</i> 
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">   
                                   <div class="radio-btns">
                                        <div class="radio-btn">
                                             <input type="radio" id="a-male" name="gender" value="MALE"   @if(isset($arr_data['user']['gender']) && $arr_data['user']['gender'] == 'MALE') checked @endif> 
                                             
                                            <label for="a-male"> {{translation('male')}}</label>
                                            <div class="check"></div>
                                        </div>
                                        <div class="radio-btn">
                                           <input type="radio" id="b-female" name="gender" value="FEMALE" @if(isset($arr_data['user']['gender']) && $arr_data['user']['gender'] == 'FEMALE') checked @endif > 
                                            <label for="b-female">{{translation('female')}} </label>
                                            <div class="check">
                                                <div class="inside"></div>
                                            </div>
                                        </div>
                                    </div>     
                                   
                                </div> 
                              </div>
                              <div class="clearfix"></div>  

                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('address')}}
                                  <i class="red">*</i> 
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">        
                                  <input type="hidden" name="latitude"  id="latitude" class="field" value="{{isset($arr_data['user']['latitude'])?$arr_data['user']['latitude']:''}}">
                                <input type="hidden" name="longitude"  id="longitude" class="field"  value="{{isset($arr_data['user']['longitude'])?$arr_data['user']['longitude']:''}}" >
                                <input type="text" name="address" id="location" value="{{isset($arr_data['user']['address'])?$arr_data['user']['address']:''}}" class="form-control" />
                                
                                <span class="help-block">{{ $errors->first('address') }}</span>
                                </div> 
                              </div>
                              <div class="clearfix"></div>  

                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('city')}}  
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">   
                                    <input type="text" name="city"  id="locality" class="form-control"  value="{{isset($arr_data['user']['city'])?$arr_data['user']['city']:''}}" placeholder="{{translation('enter_city')}}" />
                                    <span class="help-block">{{ $errors->first('city') }}</span>
                                </div> 
                              </div>
                              <div class="clearfix"></div>  

                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('country')}}  
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">   
                                    <input type="text" name="country"  id="country" class="form-control"  value="{{isset($arr_data['user']['country'])?$arr_data['user']['country']:''}}" placeholder="{{translation('enter_country')}}"/>
                                    <span class="help-block">{{ $errors->first('country') }}</span>
                                </div> 
                              </div>
                              <div class="clearfix"></div>                               
                          <br/>
                    <div class="form-group">
                       <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                           <a href="{{$module_url_path}}" class="btn btn-primary">{{translation('back')}}</a>
                        <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('update')}}</button>  
                       </div>
                    </div>
                </form>

         
</div>
</div>
</div>

<!-- END Main Content -->
<script>
  var today = new Date();
   $(function () {
              $("#birth_date").datepicker({
                  todayHighlight: true,
                  autoclose: true,
                  endDate: "today",
                  maxDate: today
              });
          });
</script>
<script type="text/javascript">
    function saveTinyMceContent()
    {
      tinyMCE.triggerSave();
    }
    $(document).ready(function()
    {
            tinymce.init({
              selector: 'textarea',
              height:350,
              plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table contextmenu paste code'
              ],
              valid_elements : '*[*]',
              toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image ',
              content_css: [
                '//fast.fonts.net/cssapi/e6dc9b99-64fe-4292-ad98-6974f93cd2a2.css',
                '//www.tinymce.com/css/codepen.min.css'
              ]
            });
    });
  </script>

  <script type="text/javascript">
   $(document).on("change",".validate-image", function()
    {            
        var file=this.files;
        validateImage(this.files, 250,250);
    });
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

  var BASE_URL = "{{url('/')}}";

  window.onload = function () {
        setTimeout(function(){ 
            initAutocomplete();
           
        }, 2000);


    };

</script>

@stop                    
