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
            <li class="active"><i class="fa fa-edit"></i> {{ $page_title or ''}}</li>
        </ul>
    </div>
    <!-- END Breadcrumb -->



    <!-- BEGIN Main Content -->
    <div class="row">
      <div class="col-md-12">
          <div class="box {{ $theme_color }}">
            <div class="box-title">
              <h3>
                <i class="fa fa-edit"></i>
                {{ isset($page_title)?$page_title:"" }}
              </h3>
              <div class="box-tool">
                <a data-action="collapse" href="#"></a>
                <a data-action="close" href="#"></a>
              </div>
            </div>
            <div class="box-content">

          @include('admin.layout._operation_status')  
           {!! Form::open([ 'url' => $module_url_path.'/update',
                                 'method'=>'POST',
                                 'enctype' =>'multipart/form-data',   
                                 'class'=>'form-horizontal', 
                                 'id'=>'validation-form' 
                                ]) !!} 

           {{ csrf_field() }}

           @if(isset($arr_data) && count($arr_data) > 0)   

           {!! Form::hidden('user_id',isset($arr_data['id']) ? $arr_data['id']: "")!!}

            <div class="form-group" style="margin-top: 25px;">
                  <label class="col-sm-3 col-lg-2 control-label">Firstname<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      
                      {!! Form::text('first_name',isset($arr_data['first_name']) ? $arr_data['first_name']: "",['class'=>'form-control','data-rule-required'=>'true','data-rule-pattern'=>"^[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$",'placeholder'=>'Enter Firstname' , 'maxlength'=>"255" ]) !!}  
                      
                      <span class="help-block">{{ $errors->first('first_name') }}</span>
                  </div>
            </div>

            <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Lastname<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      
                      {!! Form::text('last_name',isset($arr_data['last_name']) ? $arr_data['last_name']: "",['class'=>'form-control','data-rule-required'=>'true', 'data-rule-pattern'=>"^[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$", 'placeholder'=>'Enter Lastname' , 'maxlength'=>"255"]) !!}  

                      <span class="help-block">{{ $errors->first('last_name') }}</span>
                  </div>
            </div>

            <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Email Address<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      
                      {!! Form::text('email',isset($arr_data['email']) ? $arr_data['email']: "",['class'=>'form-control', 'placeholder'=>'Enter Email Address' ,'maxlength'=>"255" ]) !!}  

                      <span class="help-block">{{ $errors->first('email') }}</span>
                  </div>
            </div>  

            <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Mobile No<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      
                      {!! Form::text('text',isset($arr_data['mobile_no']) ? $arr_data['mobile_no']: "",['class'=>'form-control', 'placeholder'=>'Enter Mobile No','name'=>'mobile_no','data-rule-required'=>'true', 'data-rule-digits' => 'true', 'maxlength'=>"15" ,'minlength'=>"6" ]) !!}  

                      <span class="help-block">{{ $errors->first('mobile_no') }}</span>
                  </div>
            </div>

            {{-- <div class="form-group" style="">
                <label class="col-sm-3 col-lg-2 control-label">Services<i style="color: red;">*</i></label>
                <div class="col-sm-9 col-lg-4 controls" >
                    <select data-placeholder="Select Service" class="form-control chosen" multiple="multiple" style="display: none;" id="services" name="services[]" data-rule-required="true">
                    <div class="chosen-container chosen-container-multi chosen-container-active chosen-with-drop" style="width: 100%;" title="">
                      @if(isset($arr_services) && count($arr_services)>0)
                        @foreach($arr_services as $key => $value)
                          <option 
                            @if(isset($arr_subadmin_services) && !empty($arr_subadmin_services))
                                @if(in_array($value['id'],$arr_subadmin_services))
                                  selected=""
                                @endif   
                            @endif
                            value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                        @endforeach
                      @endif
                    </div>  
                    </select>

                    <span class="help-block">{{ $errors->first('role') }}</span>
                </div>
            </div>  --}}

            {{-- <div class="form-group" style="">
                <label class="col-sm-3 col-lg-2 control-label">Country<i style="color: red;">*</i></label>
                <div class="col-sm-9 col-lg-4 controls" >
                    <select class="form-control" id="country" name="country" data-rule-required="true" onchange="javascript: return loadStates(this);">
                      <option value="">--Select Country--</option>
                      @if(isset($arr_countries) && count($arr_countries)>0)
                        @foreach($arr_countries as $key => $value)
                          <option 
                              @if($value['id'] == $arr_data['country_id'])
                                selected="" 
                              @endif
                              value="{{ $value['id'] }}">{{ $value['country_name'] }}</option>
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
                      @if(isset($arr_states) && count($arr_states)>0)
                        @foreach($arr_states as $key => $value)
                          <option 
                              @if($value['id'] == $arr_data['state_id'])
                                selected="" 
                              @endif
                              value="{{ $value['id'] }}">{{ $value['state_name'] }}</option>
                        @endforeach
                      @endif
                    </select>
                    <span class="help-block">{{ $errors->first('state') }}</span>
                </div>
            </div>

            <div class="form-group" style="">
                <label class="col-sm-3 col-lg-2 control-label">City<i style="color: red;">*</i></label>
                <div class="col-sm-9 col-lg-4 controls" >
                    <select class="form-control"  name="city" id="city" data-rule-required="true">
                      <option value="">--Select City--</option>
                      @if(isset($arr_cities) && count($arr_cities)>0)
                        @foreach($arr_cities as $key => $value)
                          <option 
                              @if($value['id'] == $arr_data['city_id'])
                                selected="" 
                              @endif
                              value="{{ $value['id'] }}">{{ $value['city_name'] }}</option>
                        @endforeach
                      @endif
                    </select>
                    <span class="help-block">{{ $errors->first('city') }}</span>
                </div>
            </div> --}}

            <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Address<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >

                      {!! Form::text('address',isset($arr_data['address']) ? $arr_data['address']: "",['class'=>'form-control','data-rule-required'=>'true', 'placeholder'=>'Enter Street Address', 'id'=>"autocomplete"]) !!}

                      <span class="help-block">{{ $errors->first('address') }}</span>
                  </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 col-lg-2 control-label">Profile Image<i style="color: red;">*</i> </label>
              <div class="col-sm-9 col-lg-10 controls">
                 <div class="fileupload fileupload-new" data-provides="fileupload">
                   <div class="fileupload-new img-thumbnail" style="width: 200px; height: 150px;">
                     @if(isset($arr_data['profile_image']) && !empty($arr_data['profile_image']))
                      <img src={{ $user_profile_public_img_path.$arr_data['profile_image']}} alt="" /> 
                    @else
                         <img src={{ url("uploads/default-profile.png")}} alt="" />
                      @endif 
                  </div>
                    <div class="fileupload-preview fileupload-exists img-thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;">
                         @if(isset($arr_data['profile_image']) && !empty($arr_data['profile_image']))
                      <img src={{ $user_profile_public_img_path.$arr_data['profile_image']}} alt="" /> 
                    @else
                         <img src={{ url("uploads/default-profile.png")}} alt="" />
                      @endif   
                    </div>
                    <div>
                       <span class="btn btn-default btn-file"><span class="fileupload-new" >Select Image</span> 
                       <span class="fileupload-exists">Change</span>
                       
                       {!! Form::file('profile_image',['id'=>'profile_image','class'=>'file-input','data-rule-required'=>'']) !!}

                       </span> 
                       <a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
                       <span>
                       </span> 
                    </div>
                 </div>
                  <i class="red"> {!! image_validate_note(250,250) !!} </i>
                  <span class='help-block'><b>{{ $errors->first('profile_image') }}</b></span>  
              </div>
            </div>
            <input type="hidden" name="oldimage" value="{{isset($arr_data['profile_image']) ? $arr_data['profile_image'] :''}}">

            <div class="form-group">
              <label class="col-sm-3 col-lg-2 control-label">Permissions <i class="red">*</i></label>
              <div class="col-sm-9 col-lg-8 controls" >
                    <div class="table-responsive" style="border:0">
                        <table class="table " id="table_module">
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
                                      <td> 
                                        <div class="check-box">
                                            <input type="checkbox" class="filled-in" id="selectall_list" data-module-action="List" onclick="selectAll(this)" />
                                            <label for="selectall_list"></label>
                                        </div>
                                      </td>
                                      <td>
                                        <div class="check-box">
                                            <input type="checkbox" class="filled-in" id="selectall_create" data-module-action="Create" onclick="selectAll(this)" />
                                            <label for="selectall_create"></label>
                                        </div> 
                                      <td> 
                                        <div class="check-box">
                                          <input type="checkbox" class="filled-in" id="selectall_update" data-module-action="Update" onclick="selectAll(this)" />
                                          <label for="selectall_update"></label>
                                        </div>
                                      <td> 
                                        <div class="check-box">
                                          <input type="checkbox" class="filled-in" id="selectall_delete" data-module-action="Delete" onclick="selectAll(this)" />
                                          <label for="selectall_delete"></label>
                                        </div>
                                      </td>
                                </tr>     
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

                                        <td>
                                          <div class="check-box">
                                            <input class="filled-in case" type="checkbox" id="mult_change_{{$row['id']}}_list" data-module-ref="{{$slug}}" data-module-action-ref="List" name="arr_permisssion[subadmin][{{$slug}}.list]"    

                                            @if(isset($arr_data['permissions']))
                                              @if(array_key_exists($slug.'.list', $arr_data['permissions']))
                                                @if($arr_data['permissions'][$slug.'.list']=="true")
                                                  checked = "checked"
                                                @endif
                                              @endif
                                            @endif
                                            value="true" > 
                                            <label for="mult_change_{{$row['id']}}_list"></label>
                                          </div>

                                        </td>
                                                                                                      
                                        
                                        <td> 
                                          <div class="check-box">
                                            <input class="filled-in case" type="checkbox" id="mult_change_{{$row['id']}}_create" data-module-ref="{{$slug}}" data-module-action-ref="Create" name="arr_permisssion[subadmin][{{$slug}}.create]" 

                                            @if(isset($arr_data['permissions']))
                                              @if(array_key_exists($slug.'.create', $arr_data['permissions']))
                                                @if($arr_data['permissions'][$slug.'.create']=="true")
                                                checked = "checked"
                                                @endif
                                              @endif
                                            @endif
                                            value="true" > 
                                            <label for="mult_change_{{$row['id']}}_create"></label>
                                          </div>
                                        </td>
                                                                                                      
                                        
                                        <td> 
                                          <div class="check-box">
                                            <input class="filled-in case" {{$checkbox_checked}} {{ $required_checked }} id="mult_change_{{$row['id']}}_update" type="checkbox" data-module-ref="{{$slug}}" data-module-action-ref="Update" name="arr_permisssion[subadmin][{{$slug}}.update]" 
                                            @if(isset($arr_data['permissions']))
                                              @if(array_key_exists($slug.'.update', $arr_data['permissions']))
                                                @if($arr_data['permissions'][$slug.'.update']=="true")
                                                checked = "checked"
                                                @endif
                                              @endif
                                            @endif  
                                            value="true" ">
                                            <label for="mult_change_{{$row['id']}}_update"></label>
                                          </div> 
                                        </td>                                               
                                                       
                                        
                                        <td> 
                                          <div class="check-box">
                                            <input class="filled-in case"  type="checkbox" id="mult_change_{{$row['id']}}_delete" data-module-ref="{{$slug}}" data-module-action-ref="Delete" name="arr_permisssion[subadmin][{{$slug}}.delete]" 

                                            @if(isset($arr_data['permissions']))
                                              @if(array_key_exists($slug.'.delete', $arr_data['permissions']))
                                                @if($arr_data['permissions'][$slug.'.delete']=="true")
                                                  checked = "checked"
                                                @endif
                                              @endif
                                            @endif   
                                            value="true" > 
                                            <label for="mult_change_{{$row['id']}}_delete"></label>
                                          </div> 
                                        </td>
                              </tr>
                              @endforeach 
                        
                          </tbody>
                        </table>
              
                    </div>
                </div>
            </div>
            <div class="form-group">
              <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
               
                {!! Form::submit('Update',['class'=>'btn btn btn-primary','value'=>'true'])!!}
                &nbsp;
                <a class="btn" href="{{ $module_url_path }}">Back</a>
              </div>
            </div>

            @else 
              <div class="form-group">
                <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                  <h3><strong>No Record found..</strong></h3>     
                </div>
              </div>
            @endif
    
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
