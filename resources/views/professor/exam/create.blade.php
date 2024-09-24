@extends('professor.layout.master')    
@section('main_content')  
  
        <!-- BEGIN Breadcrumb -->
        <div id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <a href="{{ url($professor_panel_slug.'/dashboard') }}"> {{translation('dashboard')}}</a>
                </li>
                <span class="divider">
                    <i class="fa fa-angle-right"></i>
                </span> 
                <li><a href="{{$module_url_path}}">{{translation('exam')}}</a></li> 
                <span class="divider">
                    <i class="fa fa-angle-right"></i>
                </span> 
                <li class="active"> {{translation('add')}} {{translation('exam')}}</li>
            </ul>
        </div>
        
        <!-- END Breadcrumb -->

      <!-- BEGIN Page Title -->
      <div class="page-title new-agetitle">
          <div>
              <h1><i class="{{$module_icon}}"></i>{{$page_title}}</h1>

          </div>
      </div>
      <!-- END Page Title -->  
        <div class="box  box-navy_blue">
            <div class="box-title">
                <h3><i class="{{$create_icon}}"></i>{{translation('add')}} {{translation('exam')}}</h3>
                <div class="box-tool">
                </div>
            </div>
            <div class="box-content">
             @include('professor.layout._operation_status')
              <form method="POST" action="{{$module_url_path}}/store" onsubmit="return addLoader()" id="validation-form1" accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data" >
                   {{ csrf_field() }}  
                <div class="form-group">
                      <label class="col-sm-3 col-lg-2 control-label">{{translation('level')}}<i class="red">*</i></label>
                      <div class="col-sm-9 col-md-8 col-lg-4 controls">
                        <select name="level" id="level" class="form-control level" data-rule-required='true'>
                        <option value="">{{translation('select_level')}}</option>
                          @if(isset($arr_levels) && count($arr_levels)>0)
                              @foreach($arr_levels as $value)
                                  <option value="{{$value['level_id']}}" >{{$value['level_details']['level_name']}}</option>
                              @endforeach
                          @endif    
                        </select>
                      </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 col-lg-2 control-label">{{translation('class')}}<i class="red">*</i></label>
                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                      <select name="class" id="class" class="form-control level-class" data-rule-required='true'  onChange="getCourses();">
                      <option value="">{{translation('select_class')}}</option>
                                                      
                      </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 col-lg-2 control-label">{{translation('exam_period')}}<i class="red">*</i></label>
                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                      <select name="exam_period" id="" class="form-control" data-rule-required='true'>
                      <option value="">{{translation('select_exam_period')}}</option>
                          @if(isset($arr_exam_period) && count($arr_exam_period)>0)
                              @foreach($arr_exam_period as $value)
                                  <option value="{{$value['exam_id']}}" >{{$value['get_exam_period']['exam_name']}}</option>
                              @endforeach
                          @endif    
                      </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 col-lg-2 control-label">{{translation('exam_type')}}<i class="red">*</i></label>
                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                      <select name="exam_type" id="exam_type" class="form-control" data-rule-required='true'>
                      <option value="">{{translation('select_exam_type')}}</option>
                          @if(isset($arr_exam_type) && count($arr_exam_type)>0)
                              @foreach($arr_exam_type as $value)

                                  <option value="{{$value['exam_type_id']}}" >{{$value['get_exam_type']['exam_type']}}</option>
                              @endforeach
                          @endif    
                      </select>
                      <span class='help-block'>{{ $errors->first('exam_type') }}</span> 
                    </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 col-lg-2 control-label">{{translation('course')}}<i class="red">*</i></label>
                  <div class="col-sm-9 col-md-8 col-lg-4 controls">
                     <select name="course" id="course" class="form-control course" data-rule-required='true'>
                      <option value="">{{translation('select_course')}}</option>
                            @if(isset($arr_course) && count($arr_course)>0)
                                @foreach($arr_course as $value)
                                    <option value="{{$value['course_id']}}" >{{$value['get_course']['course_name']}}</option>
                                
                                @endforeach
                            @endif    
                        </select>
                        <span class='help-block'>{{ $errors->first('level') }}</span>    
                  </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 col-lg-2 control-label">{{translation('assessment_scale')}}<i class="red">*</i></label>
                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                       <select name="assessment_scale" class="form-control assessment-scale-class"  data-rule-required='true'>
                       <option value="">{{translation('select_assessment_scale')}}</option>          
                        </select>
                        <span class='help-block'>{{ $errors->first('assessment_scale')}}</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 col-lg-2 control-label">{{translation('exam_name')}}<i class="red">*</i></label>
                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                         <input class="form-control" name="exam_name" pattern="[a-zA-Z0-9 \-àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$"  placeholder="{{translation('enter')}} {{translation('exam_name')}}" type="text" data-rule-required='true'>
                         <span class='help-block'>{{ $errors->first('exam_name') }}</span>    
                    </div>
                </div>
                 <div class="form-group">
                    <label class="col-sm-3 col-lg-2 control-label">{{translation('exam_date')}}<i class="red">*</i></label>
                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                       <input class="form-control datepikr" id="datepicker"  name="exam_date" placeholder="{{translation('enter')}} {{translation('exam_date')}}" type="text" data-rule-required='true'/>
                       <span class='help-block'>{{ $errors->first('exam_date') }}</span>   
                    </div>
                </div>   
                <div class="form-group start-towpro">
                    <label class="col-sm-3 col-lg-2 control-label">{{translation('exam_start_time')}}</label>
                    <div class="col-sm-4 col-md-4 col-lg-2 controls">
                        <input class="form-control timepickerss timepicker-default" name="exam_start_time" type="text" placeholder="Start Time" />
                    </div>
                    <div class="col-sm-4 col-md-4 col-lg-2 controls">
                        <input class="form-control timepickerss timepicker-default " name="exam_end_time" type="text" placeholder="End Time" />
                    </div>
                </div>
                <div class="form-group">
                 <label class="col-sm-3 col-lg-2 control-label">{{translation('exam_place')}}</label>
                 <div class="col-sm-9 col-md-8 col-lg-4 controls">
                        <div class="radio-btns">
                           <div class="radio-btn">
                                <input type="radio" id="f-option" name="exam_place" value="red" checked>
                                <label for="f-option">{{translation('premises')}}</label>
                                <div class="check"></div>
                            </div>
                            <div class="radio-btn">
                                <input type="radio" id="s-option" name="exam_place" value="green">
                                <label for="s-option">{{translation('other')}}</label>
                                <div class="check"><div class="inside"></div></div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="red box-new">
                            <div class="form-group">
                                <label class="col-sm-4 col-lg-4 control-label">{{translation('building_name')}}<i class="red">*</i></label>
                                <div class="col-sm-8 col-lg-8 controls">
                                    <select name="building" id="building" class="form-control building" data-rule-required='true'>
                                    <option value="">{{translation('select_building_name')}}</option>
                                    @if(isset($arr_building) && count($arr_building)>0)
                                        @foreach($arr_building as $value)
                                            <option value="{{$value['tag_name']}}" >{{$value['tag_name']}}</option>
                                        @endforeach
                                    @endif
                                    </select>
                                     <span class='help-block'>{{ $errors->first('building_name')}}</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 col-lg-4 control-label">{{translation('floor_no')}}<i class="red">*</i></label>
                                <div class="col-sm-8 col-lg-8 controls">
                                    <select name="floor_no" id="floor_no" class="form-control building-class" >
                                    <option value="">{{translation('select_floor')}}</option>

                                    </select>
                                    <span class='help-block'>{{ $errors->first('floor_no')}}</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 col-lg-4 control-label">{{translation('room')}}<i class="red">*</i></label>
                                <div class="col-sm-8 col-lg-8 controls">
                                    <select name="room" id="room" class="form-control room">
                                    <option value="">{{translation('select_room')}}</option>
                                    </select>
                                    <span class='help-block'>{{ $errors->first('room')}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="green box-new">
                            <div class="form-group">
                                <label class="col-sm-4 col-lg-4 control-label">{{translation('place')}} <i class="red">*</i></label>
                                <div class="col-sm-8 col-lg-8 controls">
                                    <input class="form-control place" id="place" name="place" placeholder="{{translation('enter')}} {{translation('place')}}" type="text" value="" data-rule-required='true'>
                                </div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-4 col-lg-4 control-label">{{translation('building_name')}}</label>
                              <div class="col-sm-8 col-lg-8 controls">
                                  <input class="form-control other_building" name="other_building" placeholder="{{translation('enter')}} {{translation('building_name')}}" type="text" value="">
                              </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 col-lg-4 control-label">{{translation('floor_no')}}</label>    
                                <div class="col-sm-8 col-lg-8 controls">
                                    <input class="form-control other_floor" pattern="^[0-9]+$" name="other_floor" placeholder="{{translation('enter')}} {{translation('floor_no')}}" type="text" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 col-lg-4 control-label">{{translation('room')}}</label>    
                                <div class="col-sm-8 col-lg-8 controls">
                                    <input class="form-control other_room" pattern="[a-zA-Z0-9 \-àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" name="other_room" placeholder="{{translation('enter')}} {{translation('room')}}" type="text" value="">
                                </div>
                            </div>
                        </div>
                     </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 col-lg-2 control-label">{{translation('exam_description')}}<i class="red">*</i></label>
                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                        <textarea class="form-control" name="exam_description" placeholder="{{translation('enter')}} {{translation('exam_description')}}" type="text" data-rule-required='true'></textarea>
                        <span class='help-block'>{{ $errors->first('exam_description') }}</span>  
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 col-lg-2 control-label">{{translation('supervisor')}}<i class="red">*</i></label>
                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                        <select name="supervisor" class="form-control" data-rule-required='true'>
                         <option value="">{{translation('select')}} {{translation('supervisor')}}</option>
                            @if(isset($arr_professor) && count($arr_professor)>0)
                                @foreach($arr_professor as $value)
                                    <option value="{{$value['user_id']}}" >{{$value['get_user_details']['first_name']}} {{$value['get_user_details']['last_name']}} ({{$value['get_user_details']['national_id']}})</option>
                                @endforeach
                            @endif    
                        </select>
                        <span class='help-block'>{{ $errors->first('supervisor') }}</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 col-lg-2 control-label">{{translation('gradebook')}}<i class="red">*</i></label>
                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                        <select name="gradebook" class="form-control" data-rule-required='true'>
                         <option value="test" selected>{{translation('test')}}</option>
                         <option value="other">{{translation('other')}}</option>
                            
                        </select>
                        <span class='help-block'>{{ $errors->first('gradebook') }}</span>
                    </div>
                </div>
                 
                <div class="col-sm-8 col-sm-offset-4 col-lg-9 col-lg-offset-3">
                    <a href="{{$module_url_path}}" class="btn btn-primary">{{translation('back')}}</a>
                    <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('save')}}</button>
                </div>
                <div class="clearfix"></div>
              </form>
              </span></div></div>
           
<script>
$('.green').hide();
$(document).ready(function()
{
    if($('#f-option:checked').val())
    {
        var targetBox = $(".red");
        $("input.place").attr('data-rule-required','false');
        $('.building').attr('data-rule-required','true');
        $('.building-class').attr('data-rule-required','true');
        $(".room").attr('data-rule-required','true');
        $(targetBox).show();
    }
    else
    {
        var targetBox = $(".green");
        $("input.place").attr('data-rule-required','true');
        $('.building').attr('data-rule-required','false');
        $('.building-class').attr('data-rule-required','false');
        $('.room').attr('data-rule-required','false');
        $(targetBox).show();
    }


    var level = $('.level').val();

    if(level!='')
    {
        $(".level-class").empty();
        $.ajax({
          url  :"{{ $module_url_path }}/get_classes",
          type :'get',
          data :{'_token':'<?php echo csrf_token();?>','level':level},
          success:function(data){
            
                 $(".level-class").append(data);
              
          }
        });
    }

    var course = $('.course').val();
    if(course!='')
    {
        $(".assessment-scale-class").empty();
        $.ajax({
          url  :"{{ $module_url_path }}/get_assessment_scale",
          type :'get',
          data :{'_token':'<?php echo csrf_token();?>','course':course},
          success:function(data){
                console.log(data);
                 $(".assessment-scale-class").append(data);
              
          }
        });
    }

    var building = $('.building').val();
    if(building!='')
    {
        $(".building-class").empty();
        $.ajax({
          url  :"{{ $module_url_path }}/get_floor",
          type :'get',
          data :{'_token':'<?php echo csrf_token();?>','building':building},
          success:function(data){
                 $(".building-class").append(data);
                 var floor = $('.building-class').val();
                if(floor!='')
                {
                    $(".room").empty();
                    $.ajax({
                      url  :"{{ $module_url_path }}/get_rooms",
                      type :'get',
                      data :{'_token':'<?php echo csrf_token();?>','id':floor},
                      success:function(data){
                    
                             $(".room").append(data);
                          
                      }
                    });
                }
          }
        });
    }
});

$(".level").on('change',function(){
    var level = $('.level').val();
 
    $(".level-class").empty();
       $.ajax({
          url  :"{{ $module_url_path }}/get_classes",
          type :'get',
          data :{'_token':'<?php echo csrf_token();?>','level':level},
          success:function(data){
            
                 $(".level-class").append(data);
              
          }
    });

});

$(".course").on('change',function(){
    var course = $('.course').val();
    if(course!='')
    {
        $(".assessment-scale-class").empty();
        $.ajax({
          url  :"{{ $module_url_path }}/get_assessment_scale",
          type :'get',
          data :{'_token':'<?php echo csrf_token();?>','course':course},
          success:function(data){
            
                 $(".assessment-scale-class").append(data);
              
          }
        });
    }
});

$(".building").on('change',function(){
    var building = $('.building').val();
    $(".room").empty();
        if(building!='')
        {
            $(".building-class").empty();
            $.ajax({
              url  :"{{ $module_url_path }}/get_floor",
              type :'get',
              data :{'_token':'<?php echo csrf_token();?>','building':building},
              success:function(data){
                        
                    $(".building-class").append(data);
                    var floor = $('.building-class').val();
                    if(floor!='')
                    {
                        $(".room").empty();
                        $.ajax({
                          url  :"{{ $module_url_path }}/get_rooms",
                          type :'get',
                          data :{'_token':'<?php echo csrf_token();?>','id':floor},
                          success:function(data){
                        
                                 $(".room").append(data);
                              
                          }
                        });
                    }
                    
              }
            });
        }
});

$(".building-class").on('change',function(){
    var floor = $('.building-class').val();
        if(floor!='')
        {
            $(".room").empty();
            $.ajax({
              url  :"{{ $module_url_path }}/get_rooms",
              type :'get',
              data :{'_token':'<?php echo csrf_token();?>','id':floor},
              success:function(data){
            
                     $(".room").append(data);
                  
              }
            });
        }
});        

$('input[type="radio"]').click(function(){
    var inputValue = $(this).attr("value");
    if(inputValue=='red')
    {
        $('.building').attr('data-rule-required','true');
        $('.building-class').attr('data-rule-required','true');
        $('.room').attr('data-rule-required','true');
        $("input.place").attr('data-rule-required','false');
    }
    else
    {
        $("input.place").attr('data-rule-required','true');
        $('.building').attr('data-rule-required','false');
        $('.building-class').attr('data-rule-required','false');
        $('.room').attr('data-rule-required','false');
    }
    var targetBox = $("." + inputValue);
    $(".box-new").not(targetBox).hide();
    $(targetBox).show();
});


    </script>    
    <script type="text/javascript">

        $(function() {

          var today = new Date();
          today = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
          console.log(today);
           $( "#datepicker" ).datepicker({
              todayHighlight: true,
              autoclose: true,
              format:'yyyy-mm-dd',
              endDate: "{{\Session::get('end_date')}}",
              startDate: today
           });
            
         
        });
         $( function() {    
                $('.timepicker-default').timepicker(); 
            });
         $.fn.timepicker.defaults = {
          defaultTime: 'current',
          disableFocus: false,
          disableMousewheel: false,
          isOpen: false,
          minuteStep: 15,
          modalBackdrop: false,
          orientation: { x: 'auto', y: 'auto'},
          secondStep: 15,
          showSeconds: false,
          showInputs: true,
          showMeridian: false,
          template: 'dropdown',
          appendWidgetTo: 'body',
          showWidgetOnAddonClick: true
        };
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
       glob_autocomplete = initGoogleAutoComponent($('#place')[0],glob_options,glob_autocomplete);
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
       var address = $('#place').val();
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

<script>
  function getCourses() {
    var level      =   $('#level').val();
    var cls_name   =   $('#class').val();

    $('#course').empty();
    $.ajax({
              url  :"{{ $module_url_path }}/get_courses",
              type :'POST',
              data :{'level':level ,'class':cls_name ,'_token':'<?php echo csrf_token();?>'},
              success:function(data){
                $('#course').empty();
                $('#course').append(data);
              }
            });
  }
</script>
@endsection