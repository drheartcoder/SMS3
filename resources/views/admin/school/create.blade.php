@extends('admin.layout.master')    
@section('main_content')

<style type="text/css">
 .profile-img{width: 120px; height: 120px;border-radius: 50% !important;overflow: hidden;padding: 0;}
 .profile-img img{height: 100% !important;width: 100% ;}
</style>
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
      <a href="{{ url($admin_panel_slug.'/dashboard') }}"> {{translation('dashboard')}}
      </a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa {{$module_icon}}">
      </i>
    </span> 
    <li >  <a href="{{$module_url_path}}"> {{ isset($module_title)?$module_title:"" }}</a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa {{$create_icon}}">
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
              <i class="fa {{$create_icon}}">
              </i> {{ isset($page_title)?$page_title:"" }} 
            </h3>
            <div class="box-tool">
            </div>
          </div>

           <div class="box-content">
              @include('admin.layout._operation_status')

              <?php $count=0; ?>

              <div class="tabbable">

                  <form class="form-horizontal" name="validation-form1" id="validation-form1" method="POST" action="{{$school_url_path}}/store" enctype="multipart/form-data" onsubmit="return addLoader()">
                    {{ csrf_field() }}
                    <input type="hidden" name="enc_id" value="{{$enc_id}}">
                    <input type="hidden" name="name" id="school_name">
                
                    <div  class="tab-content">
                       

                              @if(isset($arr_template) && count($arr_template)>0)
                        
                                @foreach($arr_template as $key => $template)
                                @if(isset($arr_template[$key]))
                                     <?php $label = isset($arr_template[$key]['title'])?$arr_template[$key]['title']:''; ?>
                                  @endif

                                  @if(isset($template['is_active']) && $template['is_active']==1)

                                        <div class="form-group">
                                            
                                            <label class="col-sm-5 col-lg-2 control-label" for="state">{{$label}} 
                                              
                                              @if($template['is_required']==1)
                                                <i class="red">*</i> 
                                              @endif     
                                            </label>

                                            <div class="col-sm-5 col-lg-4 controls">
                                              @if($template['get_question_category']['slug'] == 'multiple')
                                                  <?php 
                                                      $options = $template['options'];  
                                                      $arr_options = explode(",",$options);
                                                  ?>

                                                  @foreach($arr_options as $option)
                                                      <label><input type="checkbox" name="{{strslug($label)}}[]" id="{{strslug($template['title'])}}_{{$count++}}" value="dummy"  @if(old(strslug($template['id'])) == $option)
                                                              checked 
                                                      @endif

                                                      @if($template['is_required']==1)
                                                        data-rule-required="true"
                                                      @endif 
                                                        />
                                                      {{$option}} &nbsp; &nbsp;</label>
                                                      <span class='help-block'>{{ $errors->first(strslug($label)) }}</span>
                                                  @endforeach

                                              @elseif($template['get_question_category']['slug'] == 'single') 
                                                  <?php 
                                                    $options = $template['options'];  
                                                    $arr_options = explode(",",$options);
                                                  ?>
                                                  @foreach($arr_options as $key => $option) 
                                                      <label><input type="radio" name="{{strslug($label)}}" value="{{$option}}"
                                                      @if(old($template['id']) == $option)
                                                        checked
                                                      @endif
                                                      @if($template['is_required']==1)
                                                        data-rule-required="true"
                                                      @endif 
                                                      > {{$option}} &nbsp;&nbsp;</label>
                                                      <span class='help-block'>{{ $errors->first(strslug($label)) }}</span>
                                                  @endforeach  

                                              @elseif($template['get_question_category']['slug'] == 'short')
                                                 
                                                  <input type="text" class="form-control" 
                                                      @if($template['id'] == 4)
                                                        name="school_name" pattern="[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ.&]+$" 
                                                        value="{{old('school_name')}}"
                                                      @elseif($template['id'] == 13)
                                                        name="email" data-rule-email="true"
                                                        value="{{old('email')}}"
                                                      @else
                                                        name="{{strslug($label)}}"
                                                        value="{{old(strslug($label))}}"
                                                      @endif
                                                    @if($template['is_required']==1)
                                                      data-rule-required="true"
                                                    @endif  

                                                    <?php
                                                      $arr_validations = [];
                                                      if(isset($template['validations']) && $template['validations']!='')
                                                      {
                                                        $arr_validations = explode(',', $template['validations']);
                                                      }
                                                    
                                                  if(count($arr_validations)>0)
                                                  {
                                                    if(!in_array('mobile_no', $arr_validations) && !in_array('telephone_no', $arr_validations) && !in_array('email', $arr_validations))
                                                    {
                                                      $pattern = '^[';
                                                      if(in_array('letters',$arr_validations))
                                                      {
                                                        $pattern .= "a-zA-ZàâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ" ;
                                                      }
                                                      if(in_array('digits',$arr_validations))
                                                      {
                                                        $pattern .= '0-9';
                                                      }
                                                      if(in_array('white_space',$arr_validations))
                                                      {
                                                        $pattern .= ' ';
                                                      }
                                                      if(in_array('hyphen',$arr_validations))
                                                      {
                                                        $pattern .= '\-';
                                                      }
                                                      if(in_array('special_symbols',$arr_validations))
                                                      {
                                                        $pattern .= '$&+,:;=?@#|\'<>^*()%!~`\"';
                                                      }
                                                      if(in_array('dot',$arr_validations))
                                                      {
                                                        $pattern .= '.';
                                                      }
                                                      
                                                      $pattern .= ']+$';
                                                      ?>
                                                      pattern="{{$pattern}}"
                                                      <?php   
                                                    }
                                                    else
                                                    {
                                                    ?>
                                                    @if(in_array('mobile_no',$arr_validations))
                                                     data-rule-digits="true"
                                                     data-rule-minlength="10"
                                                     data-rule-maxlength="14"
                                                      data-msg-minlength="{{translation('please_enter_at_least_10_digits')}}." data-msg-maxlength="{{translation('please_enter_not_more_than_14_digits')}}."
                                                    @endif

                                                    @if(in_array('telephone_no',$arr_validations))
                                                      data-rule-digits="true"
                                                      data-rule-minlength="6"
                                                      data-rule-maxlength="14"
                                                      data-msg-minlength="{{translation('please_enter_at_least_6  _digits')}}." data-msg-maxlength="{{translation('please_enter_not_more_than_14_digits')}}."
                                                    @endif
                                                    <?php
                                                    }
                                                  }
                                                    ?>
                                                    
                                                    placeholder="{{$label}}"
                                                    />
                                                    <span class='help-block'>{{ $errors->first(strslug($label)) }}</span>
                                              @elseif($template['get_question_category']['slug'] == 'address')
                                                    <input type="text" name="address" id="location" class="form-control" value="{{old('address')}}"  
                                                    @if($template['is_required']==1) 
                                                      data-rule-required="true" 
                                                    @endif  
                                                    /> 
                                                    <span class='help-block'>{{ $errors->first(strslug($label)) }}</span>

                                              @elseif($template['get_question_category']['slug'] == 'latitude')
                                                 <input type="text" name="{{strslug($label)}}" id="latitude" class="form-control" value="{{old(strslug($label))}}" 
                                                  @if($template['is_required']==1)
                                                    data-rule-required="true"
                                                  @endif 
                                                  <?php
                                                      $arr_validations = [];
                                                      if(isset($template['validations']) && $template['validations']!='')
                                                      {
                                                        $arr_validations = explode(',', $template['validations']);
                                                      }
                                                    
                                                  if(count($arr_validations)>0)
                                                  {
                                                      $pattern = '^[';
                                                      if(in_array('letters',$arr_validations))
                                                      {
                                                        $pattern .= 'a-zA-ZàâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ';
                                                      }
                                                      if(in_array('digits',$arr_validations))
                                                      {
                                                        $pattern .= '0-9';
                                                      }
                                                      if(in_array('white_space',$arr_validations))
                                                      {
                                                        $pattern .= ' ';
                                                      }
                                                      if(in_array('hyphen',$arr_validations))
                                                      {
                                                        $pattern .= '\-';
                                                      }
                                                      if(in_array('special_symbols',$arr_validations))
                                                      {
                                                        $pattern .= '$&+,:;=?@#|\'<>^*()%!~`\"';
                                                      }
                                                      if(in_array('dot',$arr_validations))
                                                      {
                                                        $pattern .= '.';
                                                      }
                                                      
                                                      $pattern .= ']+$';
                                                  }
                                                      ?>
                                                      pattern="{{$pattern}}"
                                                /> 
                                                <span class='help-block'>{{ $errors->first(strslug($label)) }}</span>
                                              
                                              @elseif($template['get_question_category']['slug'] == 'longitude')
                                                 <input type="text" name="{{strslug($label)}}" id="longitude" class="form-control" value="{{old(strslug($label))}}"
                                                  @if($template['is_required']==1)
                                                    data-rule-required="true"
                                                  @endif 
                                                  <?php
                                                      $arr_validations = [];
                                                      if(isset($template['validations']) && $template['validations']!='')
                                                      {
                                                        $arr_validations = explode(',', $template['validations']);
                                                      }
                                                    
                                                  if(count($arr_validations)>0)
                                                  {
                                                      $pattern = '^[';
                                                      if(in_array('letters',$arr_validations))
                                                      {
                                                        $pattern .= 'a-zA-ZàâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ';
                                                      }
                                                      if(in_array('digits',$arr_validations))
                                                      {
                                                        $pattern .= '0-9';
                                                      }
                                                      if(in_array('white_space',$arr_validations))
                                                      {
                                                        $pattern .= ' ';
                                                      }
                                                      if(in_array('hyphen',$arr_validations))
                                                      {
                                                        $pattern .= '\-';
                                                      }
                                                      if(in_array('special_symbols',$arr_validations))
                                                      {
                                                        $pattern .= '$&+,:;=?@#|\'<>^*()%!~`\"';
                                                      }
                                                      if(in_array('dot',$arr_validations))
                                                      {
                                                        $pattern .= '.';
                                                      }
                                                      
                                                      $pattern .= ']+$';
                                                    }
                                                      ?>
                                                      pattern="{{$pattern}}" 
                                                /> 
                                                <span class='help-block'>{{ $errors->first(strslug($label)) }}</span>

                                              @elseif($template['get_question_category']['slug'] == 'long') 
                                                  <textarea class="form-control" name="{{strslug($label)}}"
                                                            @if($template['is_required']==1)
                                                              data-rule-required="true"
                                                            @endif  

                                                            <?php
                                                              $arr_validations = [];
                                                              if(isset($template['validations']) && $template['validations']!='')
                                                              {
                                                                $arr_validations = explode(',', $template['validations']);
                                                              }
                                                            $pattern = '^[';

                                                            if(in_array('letters',$arr_validations))
                                                            {
                                                              $pattern .= 'a-zA-ZàâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ';
                                                            }
                                                            if(in_array('digits',$arr_validations))
                                                            {
                                                              $pattern .= '0-9';
                                                            }
                                                            if(in_array('white_space',$arr_validations))
                                                            {
                                                              $pattern .= ' ';
                                                            }
                                                            if(in_array('special_symbols',$arr_validations))
                                                            {
                                                              $pattern .= '$&+,:;=?@#|\'<>^*()%!~`\"';
                                                            }
                                                            if(in_array('hyphen',$arr_validations))
                                                            {
                                                              $pattern .= '-';
                                                            }
                                                            if(in_array('dot',$arr_validations))
                                                            {
                                                              $pattern .= '.';
                                                            }
                                                            
                                                            $pattern .= ']+$';
                                                            ?>
                                                            pattern="{{$pattern}}"
                                                  ></textarea> 
                                                  <span class='help-block'>{{ $errors->first(strslug($label)) }}</span>

                                              @elseif($template['get_question_category']['slug'] == 'browse_image')  
                                                {{-- <img src="" height="150px" width="50%%" style="margin-left: 50px;margin-top: 20px">  --}}
                                             
                                             
                                                    <div class="fileupload fileupload-new" data-provides="fileupload">
                                                       <div class="fileupload-new img-thumbnail profile-img img" >
                                                           <img src="{{$image_path}}/default.png">
                                                       </div>
                                                       <div class="fileupload-preview fileupload-exists img-thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                                                       <div>
                                                          <span class="btn btn-default btn-file" style="height:32px;">
                                                          <span class="fileupload-new">{{translation('select_image')}}</span>
                                                          <span class="fileupload-exists">{{translation('change')}}</span>
                                                          <input type="file"  data-validation-allowing="jpg, png, gif" class="file-input news-image validate-image" name="image" id="image"  /><br>
                                                          </span>
                                                          <a href="#" id="remove" class="btn btn-default fileupload-exists" data-dismiss="fileupload">{{translation('remove')}}</a>
                                                       </div>
                                                       <i class="red"> {!! image_validate_note(250,250,2000,2000) !!} </i>
                                                       <span for="image" id="err-image" class="help-block"></span>
                                                    </div>
                                                 <div class="clearfix"></div>
                                                 <div class="col-sm-6 col-lg-5 control-label help-block-red" style="color:#b94a48;" id="err_logo"></div>
                                                 <br/>
                                                 <div class="col-sm-6 col-lg-5 control-label help-block-green" style="color:#468847;" id="success_logo"></div>
                                          
                                                 @elseif($template['get_question_category']['slug'] == 'dropdown')   
                                                    <select class="form-control" name="{{strslug($label)}}"
                                                      @if($template['is_required']==1)
                                                        data-rule-required="true"
                                                      @endif  
                                                    > 
                                                      <option value="{{$count}}">{{translation('default')}}</option>
                                                      {{$template['options']}}
                                                      <?php 
                                                        $options = $template['options'];  
                                                        $arr_options = explode(",",$options);
                                                      ?>
                                                      @foreach($arr_options as $option)
                                                        <option value="{{$option}}"

                                                        >{{$option}}</option>   
                                                      @endforeach
                                                    </select>  
                                                    <span class='help-block'>{{ $errors->first(strslug($label)) }}</span>
                                                 @endif  
                                            </div>
                                        </div>

                                      @endif

                                @endforeach

                              @endif
                            
                    </div><br/>
                    <div class="form-group">
                       <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                          <a href="{{ url($admin_panel_slug.'/school_admin') }}" class="btn">{{translation('back')}}</a> 
                          <button type="submit" id="submit_button" class="btn btn-primary">{{translation('save')}}</button> 
                       </div>
                    </div>   
                  </form>
              </div>
           </div>

  <!-- END Main Content --> 
   
  <script>
 var count=2;
 function getOptions(locale)
 {
    var q_category = $("#q_category").val();

    if(q_category=='3' || q_category=='4')
    {
      <?php foreach($arr_lang as $lang)
         {
      ?>
        $(".options_{{$lang['locale']}}").css("display","block"); 
        $("")
      <?php
        }
      ?>  
    }
    else
    {
      $(".options_"+locale).css("display","none"); 
    }
 }
 function addOption(locale,enter,options)
 {  

  <?php foreach($arr_lang as $key=>$lang)
    {
  ?>
      var str = "<div class='clearfix'></div><div class='form-group option_"+count+"_{{$lang['locale']}}'><label class='col-sm-3 col-lg-2 control-label'></label><div class='col-sm-4 col-lg-4 controls'><input type='text' name='options_"+count+"_{{$lang['locale']}}' class='form-control' placeholder='"+enter+" "+options+"' data-rule-required='true'/>";

      <?php if($key==0) {?>
            str = str+"<i class='fa fa-minus option_"+count+"_{{$lang['locale']}}' onclick=\"remove('"+count+"','"+locale+"')\"></i>";
      <?php } ?>

      str = str+"<span class='help-block'>{{$errors->first('options_"+count+"_$lang[\"locale\"]')}}</span></div></div><div class='clearfix'></div>";

      $(".options_{{$lang['locale']}}").append(str);  
  <?php
  }
  ?>
    $(".option_count").val(count);    
    count++;
  }
  function remove(count,locale)
  {
    <?php foreach($arr_lang as $lang)
    {
    ?>
      $(".option_"+count+"_{{$lang['locale']}}").remove();
    <?php
    }
    ?>
  }
 </script> 
   <script type="text/javascript">
   $(document).on("change",".validate-image", function()
    {            
        var file=this.files;
        validateImage(this.files, 250,250);
    });
  </script>
  <script>
    
    $('#validation-form1').on('submit',function(){
      console.log($('#school').val());
      $('#name').val($('#school').val());
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