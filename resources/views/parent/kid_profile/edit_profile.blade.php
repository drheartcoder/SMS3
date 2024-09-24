@extends('parent.layout.master')    
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
                    <a href="{{ url($parent_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
                </li>
                <span class="divider">
                    <i class="fa fa-angle-right"></i>
                </span> 
                <li class="active">{{translation('kid_profile')}}</li>
               
            </ul>
        </div>
        
<!--         BEGIN Page Title -->
        <div class="page-title new-agetitle">
            <div>
                <h1><i class="fa {{$module_icon}}"></i>{{$module_title}}</h1>
            </div>
        </div>
<!--         END Page Title -->

        

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
         'enctype'=>'multipart/form-data',
         'onsubmit'=>"return addLoader()" 
         ]) !!}
          
            <input type="hidden" name="enc_id" value="{{isset($arr_data['id']) ? base64_encode($arr_data['id']) : 0}}">
            <input type="hidden" name="oldimage" value="{{$arr_data['profile_image'] or ''}}">
            <br>
                
            <div class="col-sm-8 col-sm-offset-4 col-lg-8 col-lg-offset-3">
              <div class="profile-section-block">
                    <div class="profile-img-block">
                      @if(isset($arr_data['profile_image']) && $arr_data['profile_image'] != '')
                        <input type="hidden" name="old_image" value="{{$arr_data['profile_image']}}">
                        <div class="pro-img"><img src="{{url('/')}}/uploads/profile_image/{{$arr_data['profile_image']}}" class="img-responsive img-preview2" alt=""></div>
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
                <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('first_name')}}<i class="red">*</i></label>
                <div class="col-sm-9 col-md-8 col-lg-4 controls">
                    <input type="text" name="first_name" placeholder="{{translation('enter_first_name')}}"  class="form-control" data-rule-required='true' pattern="[a-zA-Z àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" data-rule-maxlength='255' value="{{isset($arr_data['first_name'])?$arr_data['first_name']:''}}">
                    <span class='help-block'>{{ $errors->first('first_name') }}</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('last_name')}}<i class="red">*</i></label>
                <div class="col-sm-9 col-md-8 col-lg-4 controls">
                   <input type="text" name="last_name" class="form-control" data-rule-required='true' pattern="[a-zA-Z àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" data-rule-maxlength='255' placeholder="{{translation('enter_last_name')}}" value="{{isset($arr_data['last_name'])?$arr_data['last_name']:''}}">
                    <span class='help-block'>{{ $errors->first('last_name') }}</span>
                </div>
            </div>
                                    
            <div class="form-group">
                <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('parent')}} {{translation('national_id')}}<i class="red">*</i></label>
                <div class="col-sm-9 col-md-8 col-lg-4 controls">
                    <input class="form-control" name="parent_national_id" type="text" placeholder="{{translation('enter')}} {{translation('parent')}} {{translation('national_id')}}" data-rule-required='true' value="{{isset($arr_data['student_details']['parent_national_id'])?$arr_data['student_details']['parent_national_id']:''}}"   pattern="[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" title="{{translation('letters_and_numbers_only')}}"/>
                    <span class='help-block'>{{ $errors->first('parent_national_id') }}</span>
                </div>
            </div>
                                   
            <div class="form-group">
                <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('student_id')}}<i class="red">*</i></label>
                <div class="col-sm-9 col-md-8 col-lg-4 controls">
                    <input class="form-control" name="student_no" type="text" placeholder="{{translation('enter_student_id')}}" data-rule-required='true' value="{{isset($arr_data['student_details']['student_no'])?$arr_data['student_details']['student_no']:''}}" readonly="true" />
                    <span class='help-block'>{{ $errors->first('student_no') }}</span>
                </div>
            </div>
                                    
            <div class="form-group">
                <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('national_id')}}<i class="red">*</i></label>
                <div class="col-sm-9 col-md-8 col-lg-4 controls">
                    <input class="form-control" name="national_id" type="text" placeholder="{{translation('enter_national_id')}}" data-rule-required='true' value="{{isset($arr_data['national_id'])?$arr_data['national_id']:''}}"  pattern="[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" title="{{translation('letters_and_numbers_only')}}" />
                    <span class='help-block'>{{ $errors->first('national_id') }}</span>
                </div>
            </div>
                                     
           <div class="form-group">
               <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('gender')}}<i class="red">*</i></label>
               <div class="col-sm-9 col-md-8 col-lg-4 controls">
               
                      <div class="radio-btns">
                           <div class="radio-btn">
                                 <input type="radio" id="f-option" name="gender" value="Male"  <?php echo(@$arr_data['gender']=='MALE')?'checked':''; ?> />
                                <label for="f-option">{{translation('male')}}</label>
                                <div class="check"></div>
                              </div>
                                 
                             <div class="radio-btn">
                              <input type="radio" id="s-option" name="gender" value="Female"  <?php echo(@$arr_data['gender']=='FEMALE')?'checked':''; ?> />
                                <label for="s-option">{{translation('female')}}</label>
                                <div class="check"><div class="inside"></div></div>
                              </div>
                      </div>
               </div>
             </div>

                                       
            <div class="form-group">
                  <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('telephone_number')}}</label>
                  <div class="col-sm-9 col-md-8 col-lg-4 controls">
                      <input class="form-control" name="telephone_no" type="text" placeholder="{{translation('enter')}} {{translation('telephone_number')}}" data-rule-required='true' data-rule-pattern="[- +()0-9]+" data-rule-minlength="6" data-rule-maxlength="14" placeholder="{{translation('enter_telephone_number')}}" data-msg-minlength="{{translation('telephone_number_should_be_at_least_6_digits')}}" data-msg-maxlength="{{translation('telephone_number_should_be_at_most_14_digits')}}" value="{{isset($arr_data['telephone_no'])?$arr_data['telephone_no']:''}}" data-rule-number='true'/>
                      <span class='help-block'>{{ $errors->first('telephone_no') }}</span>
                  </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('mobile_no')}}<i class="red">*</i></label>
                <div class="col-sm-9 col-md-8 col-lg-4 controls">
                    <input class="form-control" name="mobile_no" type="text" placeholder="{{translation('enter')}} {{translation('mobile_no')}}" data-rule-required='true' data-rule-minlength='10' data-rule-maxlength='14' data-rule-number= "true" data-msg-minlength="{{translation('mobile_number_should_be_at_least_10_digits')}}." data-msg-maxlength="{{translation('mobile_number_should_be_at_most_14_digits')}}." value="{{isset($arr_data['mobile_no'])?$arr_data['mobile_no']:''}}" />
                    <span class='help-block'>{{ $errors->first('mobile_no') }}</span>
                </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('birth_date')}}<i class="red">*</i></label>
              <div class="col-sm-9 col-md-8 col-lg-4 controls">
                  <input class="form-control datepikr" name="birth_date" id="datepicker" placeholder="{{translation('enter_birth_date')}}" type="text" data-rule-required='true' value="{{isset($arr_data['birth_date'])?$arr_data['birth_date']:''}}" />
                  <span class='help-block'>{{ $errors->first('birth_date') }}</span>
              </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('email')}}<i class="red">*</i></label>
                <div class="col-sm-9 col-md-8 col-lg-4 controls">
                    <input class="form-control" name="email" type="email" placeholder="{{translation('enter_email')}}" data-rule-required='true' value="{{isset($arr_data['email'])?$arr_data['email']:''}}"  data-rule-email="true"  readonly="readonly" style="cursor: pointer;" />
                    <span class='help-block'>{{ $errors->first('email') }}</span>
                </div>
            </div>
                                       
            <div class="form-group">
              <label class="col-sm-3 col-md-4 col-lg-3 control-label" >{{translation('address')}}  
                 <i class="red">*</i> 
              </label>
              <div class="col-sm-9 col-lg-4 controls">        
                 <input type="hidden" name="latitude"  id="latitude" class="field" value="{{@$arr_data['latitude']}}">
                 <input type="hidden" name="longitude"  id="longitude" class="field" value="{{@$arr_data['longitude']}}">
                 <input type="text" name="address" id="location" data-rule-required='true'  class="form-control" value="{{isset($arr_data['address'])?$arr_data['address']:''}}" />
             
                 <span class="help-block">{{ $errors->first('address') }}</span>
              </div> 
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('country')}}</label>
                <div class="col-sm-9 col-md-8 col-lg-4 controls">
                  <input class="form-control" name="country"  id="country"  placeholder="{{translation('enter_country')}}" value="{{isset($arr_data['country'])?$arr_data['country']:''}}"/>
                        <span class='help-block'>{{ $errors->first('city') }}</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('city')}}</label>
                <div class="col-sm-9 col-md-8 col-lg-4 controls">
                  <input class="form-control" name="city"  id="city"  placeholder="{{translation('enter_city')}}"  value="{{isset($arr_data['city'])?$arr_data['city']:''}}"/>
                        <span class='help-block'>{{ $errors->first('city') }}</span>
                </div>
            </div>
            
            <div class="form-group">
                <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('special_note')}}</label>
                <div class="col-sm-9 col-md-8 col-lg-4 controls">
                    {{-- <input class="form-control" name="special_note" type="text" placeholder="{{translation('enter_special_note')}}" value="{{isset($arr_data['special_note'])?$arr_data['special_note']:''}}"/> --}}
                    <textarea class="form-control" name="special_note" placeholder="{{translation('enter_special_note')}}">{{isset($arr_data['special_note'])?$arr_data['special_note']:''}}</textarea>
                </div>
            </div>

           

           <div class="form-group">
            <div class="col-sm-3 col-md-4 col-lg-3" >
            </div>
            <div class="col-sm-9 col-lg-4 controls">
               <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('update')}}</button>
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
      var today = new Date();
        $(function() {
            $( "#datepicker" ).datepicker({
                todayHighlight: true,
                autoclose: true,
                format:'yyyy-mm-dd',
                endDate: "today",
                maxDate: today
            });
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
      $('#city').val(place.address_components[2].short_name);
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

    function showNote()
        {
          var language = "{{isset($arr_data['student_details']['language'])?$arr_data['student_details']['language']:''}}";
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

</script> 

<!-- END Main Content --> 
@endsection

