@extends('admin.layout.master')    

@section('main_content')

   <!-- BEGIN Breadcrumb -->
    <div id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url($admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
            </li>
            <span class="divider">
                <i class="fa fa-angle-right"></i>
                <i class="fa fa-wrench"></i>
            </span> 
            <li class="active">  {{ isset($page_title)?$page_title:"" }}</li>
        </ul>
    </div>
    <!-- END Breadcrumb -->
   
    <!-- BEGIN Page Title -->
    <div class="page-title new-agetitle">
        <div>
            <h1>{{translation('site_setting')}}</h1>
        </div>
    </div>
    <!-- END Page Title -->

    

    <!-- BEGIN Main Content -->
    
    
    <div class="row">
        <div class="col-md-12">
            <div class="box  {{ $theme_color }}">
                <div class="box-title">
                    <h3><i class="fa fa-wrench"></i> {{ isset($page_title)?$page_title:"" }}</h3>
                    <div class="box-tool">
                    </div>
                </div>
                <div class="box-content">
                    @include('admin.layout._operation_status')
                    
                    {!! Form::open([ 'url' => $module_url_path.'/update/'.base64_encode($arr_data['id']),
                                 'method'=>'POST',   
                                 'class'=>'form-horizontal', 
                                 'id'=>'validation-form1' ,
                                 'enctype'=>'multipart/form-data'
                                ]) !!}

                    
                    <div class="form-group-nms">
                        <div class="col-sm-3 col-lg-2"></div>
                        <div class="col-sm-12 col-lg-8"> {{translation('website_details')}}</div>
                        <div class="clearfix"></div>
                    </div>
                      
                        <div class="form-group">
                            <label class="col-sm-3 col-lg-2 control-label">{{translation('website_name')}}<i class="red">*</i></label>
                            <div class="col-sm-9 col-lg-4 controls">
                                {!! Form::text('site_name',isset($arr_data['site_name'])?$arr_data['site_name']:'',['class'=>'form-control','data-rule-required'=>'true','data-rule-maxlength'=>'255']) !!}
                                <span class='help-block'>{{ $errors->first('site_name') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 col-lg-2 control-label" for="category_name">{{translation('address')}}<i class="red">*</i></label>
                            <div class="col-sm-9 col-lg-4 controls">
                                <input type="text" name="site_address" id="location" value="{{ old('site_address') }}" class="form-control" data-rule-required='true'/> 
                                <span class='help-block'>{{ $errors->first('site_address') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 col-lg-2 control-label">{{translation('email')}}<i class="red">*</i></label>
                            <div class="col-sm-9 col-lg-4 controls">
                                {!! Form::text('site_email_address',isset($arr_data['site_email_address'])?$arr_data['site_email_address']:'',['class'=>'form-control', 'data-rule-required'=>'true', 'data-rule-email'=>'true', 'data-rule-maxlength'=>'255']) !!}
                                <span class='help-block'>{{ $errors->first('site_email_address') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 col-lg-2 control-label">{{translation('contact_number')}}<i class="red">*</i></label>
                            <div class="col-sm-9 col-lg-4 controls">
                                {!! Form::text('site_contact_number',isset($arr_data['site_contact_number'])?$arr_data['site_contact_number']:'',['class'=>'form-control','data-rule-required'=>'true','data-rule-minlength'=>'7','data-rule-maxlength'=>'16','data-rule-digits'=>'true']) !!}
                                <span class='help-block'>{{ $errors->first('site_contact_number') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 col-lg-2 control-label" for="category_name">{{translation('meta_description')}}<i class="red"></i></label>
                            <div class="col-sm-9 col-lg-4 controls">
                                {!! Form::text('meta_desc',isset($arr_data['meta_desc'])?$arr_data['meta_desc']:'',['class'=>'form-control','data-rule-maxlength'=>'255']) !!}
                                <span class='help-block'>{{ $errors->first('meta_desc') }}</span>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-sm-3 col-lg-2 control-label">{{translation('meta_keyword')}}</label>
                            <div class="col-sm-9 col-lg-4 controls">
                            {!! Form::text('meta_keyword',isset($arr_data['meta_keyword'])?$arr_data['meta_keyword']:'',['class'=>'form-control','data-rule-maxlength'=>'255']) !!}
                                <span class='help-block'>{{ $errors->first('meta_keyword') }}</span>
                            </div>
                        </div>

                        

                        <hr/>

                    
                        <div class="form-group-nms">
                            <div class="col-sm-3 col-lg-2"></div>
                            <div class="col-sm-12 col-lg-8">{{translation('social_links_details')}}</div>
                            <div class="clearfix"></div>
                        </div>
                       
                        <div class="form-group">
                            <label class="col-sm-3 col-lg-2 control-label">{{translation('linkedin_url')}}<i class="red">*</i></label>
                            <div class="col-sm-9 col-lg-4 controls">
                                 {!! Form::text('google_plus_url',isset($arr_data['linkedIn_url'])?$arr_data['linkedIn_url']:'',['class'=>'form-control','data-rule-required'=>'true', 'data-rule-url'=>'true', 'data-rule-maxlength'=>'500','placeholder'=>translation('linkedin_url')]) !!}
                                <span class='help-block'>{{ $errors->first('google_plus_url') }}</span>
                            </div>
                        </div>
  

                        <div class="form-group">
                            <label class="col-sm-3 col-lg-2 control-label">{{translation('facebook_url')}}<i class="red">*</i></label>
                            <div class="col-sm-9 col-lg-4 controls">
                                 {!! Form::text('fb_url',isset($arr_data['fb_url'])?$arr_data['fb_url']:'',['class'=>'form-control','data-rule-required'=>'true', 'data-rule-url'=>'true', 'data-rule-maxlength'=>'500','placeholder'=>translation('facebook_url')]) !!}
                                <span class='help-block'>{{ $errors->first('fb_url') }}</span>
                            </div>
                        </div>
                       
                        <div class="form-group">
                            <label class="col-sm-3 col-lg-2 control-label">{{translation('twitter_url')}}<i class="red">*</i></label>
                            <div class="col-sm-9 col-lg-4 controls">
                                {!! Form::text('twitter_url',isset($arr_data['twitter_url'])?$arr_data['twitter_url']:'',['class'=>'form-control','data-rule-required'=>'true', 'data-rule-url'=>'true', 'data-rule-maxlength'=>'500','placeholder'=>translation('twitter_url')]) !!}
                                <span class='help-block'>{{ $errors->first('twitter_url') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 col-lg-2 control-label">{{translation('youtube_url')}}<i class="red">*</i></label>
                            <div class="col-sm-9 col-lg-4 controls">
                                 {!! Form::text('linked_in_url',isset($arr_data['linked_in_url'])?$arr_data['linked_in_url']:'',['class'=>'form-control','data-rule-required'=>'true', 'data-rule-url'=>'true', 'data-rule-maxlength'=>'500', 'placeholder'=>translation('youtube_url')])!!}
                                <span class='help-block'>{{ $errors->first('linked_in_url') }}</span>
                            </div>
                        </div>

                        <hr/>

                        <div class="form-group-nms">
                            <div class="col-sm-3 col-lg-2"></div>
                            <div class="col-sm-12 col-lg-8">{{translation('emergency_contact_details')}}</div>
                            <div class="clearfix"></div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 col-lg-2 control-label">{{translation('first_emergency_contact')}}<i class="red">*</i></label>
                            <div class="col-sm-9 col-lg-4 controls">
                                {!! Form::text('emergency_contact_one',isset($arr_data['emergency_contact_one'])?$arr_data['emergency_contact_one']:'',['class'=>'form-control','data-rule-required'=>'true','data-rule-minlength'=>'7','data-rule-maxlength'=>'16','data-rule-digits'=>'true','placeholder'=>translation('enter_first_emergency_contact')]) !!}
                                <span class='help-block'>{{ $errors->first('emergency_contact_one') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 col-lg-2 control-label">{{translation('second_emergency_contact')}}<i class="red">*</i></label>
                            <div class="col-sm-9 col-lg-4 controls">
                                {!! Form::text('emergency_contact_two',isset($arr_data['emergency_contact_two'])?$arr_data['emergency_contact_two']:'',['class'=>'form-control','data-rule-required'=>'true','data-rule-minlength'=>'7','data-rule-maxlength'=>'16','data-rule-digits'=>'true','placeholder'=>translation('enter_second_emergency_contact')]) !!}
                                <span class='help-block'>{{ $errors->first('emergency_contact_two') }}</span>
                            </div>
                        </div>

                        <hr/>
                
                        <!-- <div class="form-group-nms">
                            <div class="col-sm-3 col-lg-2"></div>
                            <div class="col-sm-12 col-lg-8">{{translation('banner_details')}}</div>
                            <div class="clearfix"></div>
                        </div> -->
                        
                        <div class="form-group">
                            <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                                {!! Form::submit(translation('update'),['class'=>'btn btn btn-primary','value'=>'true'])!!}
                            </div>
                       </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    
    <!-- END Main Content --> 
    <script type="text/javascript">
     $(document).on("change",".validate-image", function()
    {            
        var file=this.files;
        validateImage(this.files, 640 , 1920);
    });   
    </script>

    <script async type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{config('constants.GOOGLE_API_KEY')}}&libraries=places"></script>

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


@endsection