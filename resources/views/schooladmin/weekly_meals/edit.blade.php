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
        <h1>{{$module_title}}</h1>

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


               <ul  class="nav nav-tabs">
                          @include('admin.layout._multi_lang_tab')
               </ul>

               <div  class="tab-content">

                     @if(isset($arr_lang) && sizeof($arr_lang)>0)
                           @foreach($arr_lang as $lang_key => $lang)

                              <div class="tab-pane fade {{$lang_key==0 ?'in active':'' }}" 
                                     id="{{ $lang['locale'] }}">
                              <br>
                              <div class="row">
                                 <div class="col-sm-12 col-md-12 col-lg-6">
                                    @if($lang_key==0)
                                    <div class="form-group">
                                       <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('profile_image',$lang['locale'])}}
                                       </label>
                                       <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                          <div class="fileupload fileupload-new" data-provides="fileupload">
                                                   <div class="fileupload-new img-thumbnail profile-img img">
                                                        @if(isset($arr_data['users']['profile_image']) && $arr_data['users']['profile_image'] != '')
                                                          <input type="hidden" name="old_image" value="{{$arr_data['users']['profile_image']}}">
                                                          <img src="{{url('/')}}/uploads/profile_image/{{$arr_data['users']['profile_image']}}" height="100px" width="100px">
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
                                    @endif
         
                                    <div class="form-group">
                                       <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('first_name',$lang['locale'])}}
                                       <i class="red">*</i>
                                       </label>
                                       <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">
                                          <input type="text" name="first_name_{{$lang['locale']}}" class="form-control" data-rule-required='true' data-rule-lettersonly='true' data-rule-maxlength='255' value="{{isset($arr_data['users']['translations'][$lang['locale']]['first_name'])?$arr_data['users']['translations'][$lang['locale']]['first_name']:''}}" placeholder="{{translation('enter',$lang['locale'])}} {{translation('first_name',$lang['locale'])}}">
                                          <span class='help-block'>{{ $errors->first('first_name_'.$lang['locale']) }}
                                          </span>
                                       </div>
                                    </div>

                                    <div class="form-group">
                                       <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('last_name',$lang['locale'])}}
                                       <i class="red">*
                                       </i>
                                       </label>
                                       <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">
                                          <input type="text" name="last_name_{{$lang['locale']}}" class="form-control" data-rule-required='true' data-rule-lettersonly='true' data-rule-maxlength='255' value="{{isset($arr_data['users']['translations'][$lang['locale']]['last_name'])?$arr_data['users']['translations'][$lang['locale']]['last_name']:''}}" placeholder="{{translation('enter',$lang['locale'])}} {{translation('last_name',$lang['locale'])}}">
                                          <span class='help-block'>{{ $errors->first('last_name_'.$lang['locale']) }}
                                          </span>
                                       </div>
                                    </div>
 
                                    @if($lang_key==0)
                                       <div class="form-group">
                                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">Select Position<i class="red">*</i></label>
                                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">
                                                  <select name="role" class="form-control" data-rule-required='true'>
                                                      <option value="">{{translation('select_position')}}</option>
                                                      @if(isset($role))
                                                          @foreach($role as $key => $value)
                                                            <option value="{{$value['slug']}}" @if(isset($arr_data['employee']['user_role']) && $arr_data['employee']['user_role'] == $value['slug']) selected @endif>{{$value['name']}}</option>
                                                          @endforeach
                                                      @endif
                                                  </select>
                                              </div>
                                       </div>
                                    @endif

                                    @if($lang_key==0)
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('email',$lang['locale'])}}  
                                             <i class="red">*</i> 
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">        
                                             <input type="text" name="email"  id="email" class="form-control" readonly="true" value="{{isset($arr_data['users']['email'])?$arr_data['users']['email']:''}}" placeholder="{{translation('enter',$lang['locale'])}} {{translation('email',$lang['locale'])}}">
                                             <span class="help-block">{{ $errors->first('email') }}</span>
                                             <span id="err_email" style="display: none;color: red"></span>
                                          </div> 
                                       </div>
                                    @endif

                                    @if($lang_key==0)
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('address',$lang['locale'])}}  
                                             <i class="red">*</i> 
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">        
                                             <input type="hidden" name="latitude"  id="latitude" class="field" value="{{$arr_data['users']['latitude']}}">
                                             <input type="hidden" name="longitude"  id="longitude" class="field" value="{{$arr_data['users']['longitude']}}">
                                             <input type="text" name="address" id="location" data-rule-required='true'  class="form-control" value="{{isset($arr_data['users']['address'])?$arr_data['users']['address']:''}}" />
                                         
                                             <span class="help-block">{{ $errors->first('address') }}</span>
                                          </div> 
                                       </div>
                                    @endif
                      
                                    @if($lang_key==0)
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('city',$lang['locale'])}}  
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">        
                                             <input type="text" name="city" id="locality" class="form-control" value="{{isset($arr_data['users']['city'])?$arr_data['users']['city']:''}}" placeholder="{{translation('enter',$lang['locale'])}} {{translation('city',$lang['locale'])}}" />
                                         
                                             <span class="help-block">{{ $errors->first('city') }}</span>
                                          </div> 
                                       </div>
                                    @endif

                                    @if($lang_key==0)
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('country',$lang['locale'])}}  
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">        
                                             <input type="text" name="country" id="country" class="form-control" value="{{isset($arr_data['users']['country'])?$arr_data['users']['country']:''}}" placeholder="{{translation('enter',$lang['locale'])}} {{translation('country',$lang['locale'])}}" />
                                         
                                             <span class="help-block">{{ $errors->first('country') }}</span>
                                          </div> 
                                       </div>
                                    @endif
                                 </div>

                                 <div class="col-sm-12 col-md-12 col-lg-6">

                                    @if($lang_key==0)
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('national_id',$lang['locale'])}}  
                                              <i class="red">*</i> 
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">        
                                             <input type="text" name="national_id" id="national_id" data-rule-required="true"  class="form-control" value="{{isset($arr_data['users']['national_id'])?$arr_data['users']['national_id']:''}}" placeholder="{{translation('enter',$lang['locale'])}}  {{translation('national_id',$lang['locale'])}}" pattern="^[A-Za-z0-9]*$" />
                                             <span id="err_nationalid" style="display: none; color: red"></span>
                                             <span class="help-block">{{ $errors->first('national_id') }}</span>
                                          </div> 
                                       </div>
                                    @endif

                                    @if($lang_key==0)
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('birth_date',$lang['locale'])}}  
                                             <i class="red">*</i> 
                                          </label>
                                          
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">        
                                             <input type="text" name="birth_date"  id="datepicker" class="form-control datepikr" data-rule-required='true' value="{{isset($arr_data['users']['birth_date'])?$arr_data['users']['birth_date']:''}}" placeholder="{{translation('select',$lang['locale'])}} {{translation('birth_date',$lang['locale'])}}" data-rule-date="true" readonly style="cursor: pointer;">
                                             <span class="help-block">{{ $errors->first('birth_date') }}</span>
                                          </div> 
                                       </div>
                                    @endif

                                    @if($lang_key==0)
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('gender',$lang['locale'])}}   
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">   
                                               <div class="radio-btns">  
                                                <div class="radio-btn">
                                                    <input type="radio" id="f-option" name="gender" value="MALE" checked @if(isset($arr_data['users']['gender']) && $arr_data['users']['gender'] == 'MALE') checked @endif>
                                                    <label for="f-option">{{translation('male')}}</label>
                                                    <div class="check"></div>
                                                </div>
                                                <div class="radio-btn">
                                                    <input type="radio" id="s-option" name="gender" value="FEMALE" @if(isset($arr_data['users']['gender']) && $arr_data['users']['gender'] == 'FEMALE') checked @endif>
                                                    <label for="s-option">{{translation('female')}}</label>
                                                    <div class="check"><div class="inside"></div></div>
                                                </div>
                                            </div> 
                                             <span class="help-block">{{ $errors->first('gender') }}</span>
                                          </div> 
                                       </div>
                                    @endif

                                    @if($lang_key==0)
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('status',$lang['locale'])}}  
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">        
                                             
                                             <select class="form-control" name="status">
                                                <option value="" selected> {{translation('select_status')}}</option>
                                                <option value="MARRIED" @if(isset($arr_data['employee']['marital_status']) && $arr_data['employee']['marital_status'] == 'MARRIED') selected @endif> Married</option>
                                                <option value="SINGLE" @if(isset($arr_data['employee']['marital_status']) && $arr_data['employee']['marital_status'] == 'SINGLE') selected @endif> Single</option>
                                                <option value="ENGAGED" @if(isset($arr_data['employee']['marital_status']) && $arr_data['employee']['marital_status'] == 'ENGAGED') selected @endif> Engaged</option>
                                                <option value="DIVORCED" @if(isset($arr_data['employee']['marital_status']) && $arr_data['employee']['marital_status'] == 'DIVORCED') selected @endif> Divorced</option>
                                               
                                             </select>
                                             <span class="help-block">{{ $errors->first('status') }}</span>
                                          </div> 
                                       </div>
                                    @endif

                                    @if($lang_key==0)
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('year_of_experience',$lang['locale'])}}  
                                             <i class="red">*</i> 
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">        
                                             <input type="text" name="year_of_experience"  id="year_of_experience" class="form-control" data-rule-required='true' value="{{isset($arr_data['employee']['year_of_experience'])?$arr_data['employee']['year_of_experience']:''}}" placeholder="{{translation('enter',$lang['locale'])}} {{translation('year_of_experience',$lang['locale'])}}">
                                             <span class="help-block">{{ $errors->first('year_of_experience') }}</span>
                                          </div> 
                                       </div>
                                    @endif

                                    @if($lang_key==0)
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('mobile_no',$lang['locale'])}}
                                          <i class="red">*
                                          </i>
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">
                                             <input type="text" name="mobile_no" class="form-control" data-rule-required='true' data-rule-number='true' data-rule-minlength='10' data-rule-maxlength='14' value="{{isset($arr_data['users']['mobile_no'])?$arr_data['users']['mobile_no']:''}}" data-msg-minlength="Please enter at least 10 digits." data-msg-maxlength="Please enter not more than 14 digits." placeholder="{{translation('enter')}} {{translation('mobile_no')}}">
                                             <span class='help-block'>{{ $errors->first('mobile_no') }}
                                             </span>
                                          </div>
                                       </div>
                                    @endif

                                    @if($lang_key==0)
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('telephone_number',$lang['locale'])}}
                                          <i class="red">*
                                          </i>
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">
                                             <input type="text" name="telephone_no" class="form-control" data-rule-required='true' data-rule-number='true' data-rule-minlength='6' data-rule-maxlength='14' data-msg-minlength="Please enter at least 6 digits." data-msg-maxlength="Please enter not more than 14 digits."  value="{{isset($arr_data['users']['telephone_no'])?$arr_data['users']['telephone_no']:''}}" placeholder="{{translation('enter',$lang['locale'])}} {{translation('telephone_no',$lang['locale'])}}">
                                             <span class='help-block'>{{ $errors->first('telephone_no') }}
                                             </span>
                                          </div>
                                       </div>
                                    @endif

                                    @if($lang_key==0)
                                       <div class="form-group">
                                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('qualification_degree',$lang['locale'])}}
                                            <i class="red">*</i>
                                          </label>
                                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">        
                                             <input type="text" name="qualification_degree"  id="qualification_degree" class="form-control" data-rule-required='true' value="{{isset($arr_data['employee']['qualification_degree'])?$arr_data['employee']['qualification_degree']:''}}" placeholder="{{translation('enter',$lang['locale'])}} {{translation('qualification_degree',$lang['locale'])}}">
                                             <span class="help-block">{{ $errors->first('qualification_degree') }}</span>
                                          </div> 
                                       </div>
                                    @endif


                                 </div>
                        </div>  
                      </div>
                     @endforeach
               @endif
         </div>

      
           <div class="form-group">
            <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
               <a href="{{ url($school_admin_panel_slug.'/canteen/manage_canteen_staff') }}" class="btn btn-primary">{{translation('back')}}</a> 
               <button type="submit" name="update" class="btn btn-primary" id="submit_button">{{translation('update')}}</button>
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
            format:'yyyy-mm-dd'
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