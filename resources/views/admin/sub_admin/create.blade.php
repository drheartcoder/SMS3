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
                <i class="fa fa-home"></i>
                <a href="{{ url($admin_panel_slug.'/dashboard') }}">Dashboard</a>
            </li>
            <span class="divider">
                <i class="fa fa-angle-right"></i>
            </span>
            <li>
                <i class="fa fa-users"></i>
                <a href="{{ $module_url_path }}">{{ $module_title or ''}}</a>
            </li>   
            <span class="divider">
                <i class="fa fa-angle-right"></i>
            </span>
            <li class="active"><i class="fa fa-plus-square-o"></i> {{ $page_title or ''}}</li>
        </ul>
    </div>
    <!-- END Breadcrumb -->



    <!-- BEGIN Main Content -->
    <div class="row">
      <div class="col-md-12">
          <div class="box {{ $theme_color }}">
            <div class="box-title">
              <h3>
                <i class="fa fa-plus-square-o"></i>
                {{ isset($page_title)?$page_title:"" }}
              </h3>
              <div class="box-tool">
                <a data-action="collapse" href="#"></a>
                <a data-action="close" href="#"></a>
              </div>
            </div>
            <div class="box-content">

          @include('admin.layout._operation_status')  
           {!! Form::open([ 'url' => $module_url_path.'/store',
                                 'method'=>'POST',
                                 'enctype' =>'multipart/form-data',   
                                 'class'=>'form-horizontal', 
                                 'id'=>'validation-form' 
                                ]) !!} 

           {{ csrf_field() }}

            <div class="form-group" style="margin-top: 25px;">
                  <label class="col-sm-3 col-lg-2 control-label">First Name<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <input type="text" class="form-control" name="first_name" placeholder="Enter First Name" data-rule-required="true" value="{{ old('first_name') }}" data-rule-pattern="^[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$"/>
                      <span class="help-block">{{ $errors->first('first_name') }}</span>
                  </div>
            </div>

            <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Last Name<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <input type="text" class="form-control" name="last_name" placeholder="Enter Last Name" data-rule-required="true" value="{{ old('last_name') }}" data-rule-pattern="^[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" />
                      <span class="help-block">{{ $errors->first('last_name') }}</span>
                  </div>
            </div>

            <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Email Address<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <input type="text" class="form-control" name="email" placeholder="Email Address" data-rule-required="true" data-rule-email="true" value="{{ old('email') }}" />
                      <span class="help-block">{{ $errors->first('email') }}</span>
                  </div>
            </div>

            <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Mobile No.<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      
                      <input type="text" class="form-control" name="mobile_no" placeholder="Enter mobile No." data-rule-required="true" data-rule-digits="true" maxlength="15" minlength="6" />

                      <span class="help-block">{{ $errors->first('mobile_no') }}</span>
                  </div>
            </div>

            <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Password<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <input type="password" class="form-control" name="password" placeholder="Enter Password" data-rule-required="true" value="{{ old('password') }}" minlength="6" />
                      <span class="help-block">{{ $errors->first('password') }}</span>
                  </div>
            </div>

            {{-- <div class="form-group" style="">
                <label class="col-sm-3 col-lg-2 control-label">Services<i style="color: red;">*</i></label>
                <div class="col-sm-9 col-lg-4 controls" >
                    <select data-placeholder="Select Service" class="form-control chosen" multiple="multiple" style="display: none;" id="services" name="services[]" data-rule-required="true">
                    <div class="chosen-container chosen-container-multi chosen-container-active chosen-with-drop" style="width: 100%;" title="">
                      @if(isset($arr_services) && count($arr_services)>0)
                        @foreach($arr_services as $key => $value)
                          <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                        @endforeach
                      @endif
                    </div>  
                    </select>

                    <span class="help-block">{{ $errors->first('role') }}</span>
                </div>
            </div>  --}}

          {{--  <div class="form-group" style="">
                <label class="col-sm-3 col-lg-2 control-label">Country<i style="color: red;">*</i></label>
                <div class="col-sm-9 col-lg-4 controls" >
                    <select class="form-control" id="country" name="country" data-rule-required="true" onchange="javascript: return loadStates(this);">
                      <option value="">--Select Country--</option>
                      @if(isset($arr_countries) && count($arr_countries)>0)
                        @foreach($arr_countries as $key => $value)
                          <option value="{{ $value['id'] }}">{{ $value['country_name'] }}</option>
                        @endforeach
                      @endif
                    </select>
                    <span class="help-block">{{ $errors->first('role') }}</span>
                </div>
            </div>

            <div class="form-group" style="">
                <label class="col-sm-3 col-lg-2 control-label">State<i style="color: red;">*</i></label>
                <div class="col-sm-9 col-lg-4 controls" >
                    <select class="form-control"  name="state" id="state" data-rule-required="true" onchange="javascript: return loadCities(this);">
                      <option value="">--Select State--</option>
                    </select>
                    <span class="help-block">{{ $errors->first('state') }}</span>
                </div>
            </div>

            <div class="form-group" style="">
                <label class="col-sm-3 col-lg-2 control-label">City<i style="color: red;">*</i></label>
                <div class="col-sm-9 col-lg-4 controls" >
                    <select class="form-control"  name="city" id="city" data-rule-required="true">
                      <option value="">--Select City--</option>
                    </select>
                    <span class="help-block">{{ $errors->first('city') }}</span>
                </div>
            </div> --}}

            <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Address<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <input type="text" class="form-control" name="address" id="autocomplete"  placeholder="Enter Address" data-rule-required="true" value="{{ old('address') }}" />
                      <span class="help-block">{{ $errors->first('address') }}</span>
                  </div>
            </div>


{{--             <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Zip Code </label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <input type="text" class="form-control" name="zipcode" id="postal_code" data-rule-required="" placeholder="Zip Code"  minlength="4" maxlength="12"  />
                      <span class="help-block">{{ $errors->first('zipcode') }}</span>
                  </div>
            </div> --}}


             <div class="form-group">
                <label class="col-sm-3 col-lg-2 control-label"> Profile Image </label>
                <div class="col-sm-9 col-lg-10 controls">
                   <div class="fileupload fileupload-new" data-provides="fileupload">
                      <div class="fileupload-new img-thumbnail" style="width: 200px; height: 150px;">
                          
                      </div>
                      <div class="fileupload-preview fileupload-exists img-thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                      <div>
                         <span class="btn btn-default btn-file"><span class="fileupload-new" >Select Image</span> 
                         <span class="fileupload-exists">Change</span>
                         
                         {!! Form::file('profile_image',['id'=>'image_proof','class'=>'file-input']) !!}

                         </span> 
                         <a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
                         <span>
                         </span> 
                      </div>
                   </div>
                   <i class="red"> {!! image_validate_note(250,250) !!} </i>
                    <span class='help-block'>{{ $errors->first('profile_image') }}</span>  
                </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 col-lg-2 control-label">Permissions <i class="red">*</i></label>
              <div class="col-sm-9 col-lg-8 controls" >
                    <div class="table-responsive" style="border:0">
                        <table class="table border-tebls" id="table_module">
                            <thead>
                              <tr>
                                    <th style="width:5%;">Module Name</th> 
                                    <th style="text-align:center;width:5%;">List / View</th>
                                    <th style="text-align:center;width:5%;">Create</th>
                                    <th style="text-align:center;width:5%;">Update / Multiple Action</th>
                                    <th style="text-align:center;width:5%;">Delete(Single delete)</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr class="info">
                                      <td><b>All</b></td>
                                      <td class="text-center">
                                           <input id="filled-in-box" class="filled-in" type="checkbox" data-module-action="List" onclick="selectAll(this)">
                                            <label for="filled-in-box"></label>
                                       </td>
                                      <td class="text-center"> 
                                       <input id="filled-in-box1" class="filled-in" type="checkbox" data-module-action="Create" onclick="selectAll(this)">
                                       <label for="filled-in-box1"></label>
                                       </td>
                                      <td class="text-center"> 
                                      <input id="filled-in-box2" class="filled-in" type="checkbox" data-module-action="Update" onclick="selectAll(this)">
                                       <label for="filled-in-box2"></label>
                                      </td>
                                      <td class="text-center"> 
                                       <input id="filled-in-box3" class="filled-in" type="checkbox" data-module-action="Delete" onclick="selectAll(this)">
                                       <label for="filled-in-box3"></label>
                                        </td>
                              </tr>     
                  
                            @if(isset($arr_modules) && sizeof($arr_modules)>0)
                             @foreach($arr_modules as $key => $row )
                              <tr class="info">
                                  <td><b>{{$row['title']}}</b></td>                                                                   
                                        
                                        <?php 
                                              $slug = $row['slug']; 
                                              $checkbox_checked ='';
                                              $required_checked ='';

                                              if($row['slug'] == 'account_settings' || $row['slug'] == 'change_password')
                                              {
                                                $checkbox_checked ='checked=checked';
                                                $required_checked ='data-rule-required=true';
                                              }
                                        ?>

                                        <td class="text-center"> 
                                         <input id="filled-in-box4{{$key}}" class="filled-in" type="checkbox" data-module-ref="{{$slug}}" data-module-action-ref="List" name="arr_permisssion[subadmin][{{$slug}}.list]"   value="true">
                                       <label for="filled-in-box4{{$key}}"></label>
                                        </td>
                                                                                                      
                                        
                                        <td class="text-center">
                                        <input id="filled-in-box5{{$key}}" class="filled-in" type="checkbox" data-module-ref="{{$slug}}" data-module-action-ref="Create" name="arr_permisssion[subadmin][{{$slug}}.create]"  value="true">
                                       <label for="filled-in-box5{{$key}}"></label>
                                        </td>
                                                                                                      
                                        
                                        <td class="text-center"> 
                                        <input id="filled-in-box6{{$key}}" class="filled-in" {{$checkbox_checked}} {{ $required_checked }} type="checkbox" data-module-ref="{{$slug}}" data-module-action-ref="Update" name="arr_permisssion[subadmin][{{$slug}}.update]" value="true">
                                        <label for="filled-in-box6{{$key}}"></label>
                                        </td>                                               
                                                       
                                        
                                        <td class="text-center"> 
                                        <input id="filled-in-box7{{$key}}" class="filled-in" type="checkbox" data-module-ref="{{$slug}}" data-module-action-ref="Delete" name="arr_permisssion[subadmin][{{$slug}}.delete]" value="true">
                                        <label for="filled-in-box7{{$key}}"></label>
                                        </td>
                              </tr>
                              @endforeach
                            @endif
                          </tbody>
                        </table>
              
                    </div>
                </div>
            </div>

            <div class="form-group">
              <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
               
                {!! Form::submit('Save',['class'=>'btn btn btn-primary','value'=>'true'])!!}
                &nbsp;
                <a class="btn" href="{{ $module_url_path }}">Back</a>
              </div>
            </div>
    
          {!! Form::close() !!}
      </div>
    </div>
  </div>
  
  <!-- END Main Content -->


<script src="https://maps.googleapis.com/maps/api/js?v=3&key={{config('constants.GOOGLE_API_KEY')}}&libraries=places&callback=initAutocomplete"
        async defer>
</script>

<script>  
  var glob_autocomplete;
  var glob_component_form = 
  {
    street_number: 'short_name',
    route: 'long_name',
    locality: 'long_name',
    administrative_area_level_1: 'long_name',
    postal_code: 'short_name'
  };

  var glob_options = {};
  glob_options.types = ['address'];

  function initAutocomplete(country_code) 
  {
    glob_options.componentRestrictions = {country: country_code}; 

    glob_autocomplete = false;
    glob_autocomplete = initGoogleAutoComponent($('#autocomplete')[0],glob_options,glob_autocomplete);
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

  function destroyPlaceChangeListener(autocomplete_ref)
  {
    google.maps.event.clearInstanceListeners(autocomplete_ref);
  }

  function fillInAddress() 
  {
    // Get the place details from the autocomplete object.
    var place = glob_autocomplete.getPlace();
    console.log(place)  ;
    for (var component in glob_component_form) 
    {
        $("#"+component).val("");
        $("#"+component).attr('disabled',false);
    }
    
    if(place.address_components.length > 0 )
    {
      $.each(place.address_components,function(index,elem)
      {
          var addressType = elem.types[0];
          if(glob_component_form[addressType])
          {
            var val = elem[glob_component_form[addressType]];
            $("#"+addressType).val(val) ;  
          }
      });  
    }
  }

</script>     
  <script type="text/javascript">
  
  var glob_fields_modified = false;

  function selectAll(ref)
  {
    var action = $(ref).attr('data-module-action');

    var is_checked = $(ref).is(":checked");

    var arr_input = $('input[data-module-action-ref="'+action+'"]');  

    if(is_checked)
    {
      $.each(arr_input,function(index,elem)
      {
        $(elem).prop('checked', true);
      });  
    }
    else
    {
      
      $.each(arr_input,function(index,elem)
      {
        $(elem).prop('checked', false);
      });   
    }
    
  }

  
</script> 

@stop                    
