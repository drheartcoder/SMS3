@extends('schooladmin.layout.master')                
@section('main_content')
<style>
    .chosen-container-single .chosen-single div b{right: 0px !important; }
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
      <a href="{{$module_url_path}}">{{str_plural($page_title)}}</a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right"></i>
    </span>
    <i class="{{$edit_icon}}"></i>
    <li class="active">{{$module_title}}</li>
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


<!-- BEGIN Tiles -->
<div class="row">
  <div class="col-md-12">
    <div class="box  box-navy_blue">
      <div class="box-title">
        <h3><i class="{{$edit_icon}}"></i>{{$module_title}}</h3>
        <div class="box-tool">
        </div>
      </div>  
      <div class="box-content">
       @include('schooladmin.layout._operation_status')
       
       <form method="post" onsubmit="return addLoader()" action="{{$module_url_path}}/update/{{base64_encode($obj_club->id)}}"  class="form-horizontal" ddid="validation-form1">
        {{ csrf_field() }}
        <input type="hidden" name="editable" value="{{$editable}}">
        <div class="form-group">
          <label class="col-sm-3 col-lg-2 control-label">{{translation('club_name')}}<i class="red">*</i></label>
          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
            <input type="text" class="form-control" name="club_name" id="club_name" data-rule-required='true' data-rule-pattern="^[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" placeholder="{{translation('enter').' '. strtolower(translation('club_name'))}}" value="{{isset($obj_club->club_name) ? $obj_club->club_name : ''}}"/>
            <span class='help-block'>{{ $errors->first('club_name')}}</span>
          </div>

          <div class="clearfix"></div>
        </div> 

        <div class="form-group">
          <label class="col-sm-3 col-lg-2 control-label">{{translation('club_id')}}<i class="red">*</i></label>
          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
            <input type="text" class="form-control" name="club_id" id="club_id" data-rule-required='true' pattern="^[a-zA-Z0-9 ]+$" placeholder="{{translation('enter').' '. strtolower(translation('club_id'))}}" value="{{isset($obj_club->club_no) ? $obj_club->club_no : ''}}"/>
            <span class='help-block'>{{ $errors->first('club_id')}}</span>
          </div>

          <div class="clearfix"></div>
        </div> 
   
          <div class="form-group">
            <label class="col-sm-3 col-lg-2 control-label">{{translation('supervisor')}}<i class="red">*</i></label>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">  
            <select name="supervisor" id="supervisor" class="form-control chosen" data-rule-required='true'>
                <option value="">{{translation('select')}}</option>
                @if(count($arr_professors>0))
                 @foreach($arr_professors as $value)
                  <option value="{{$value->user_id}}" @if(isset($obj_club->supervisor_id) && $obj_club->supervisor_id==$value->user_id) selected="" @endif>{{ucwords($value->user_name)}}</option>
                 @endforeach
                @endif 
              </select>
              <span class='help-block'>{{ $errors->first('supervisor')}}</span>
          
          </div>
          <div class="clearfix"></div>
          </div>

        <div class="form-group">
          <label class="col-sm-3 col-lg-2 control-label">{{translation('club_type')}}<i class="red">*</i></label>
          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">  
            <div class="radio-btns">  
              <div class="radio-btn">
                <input type="radio" id="paid" name="club_type" value="paid" @if(isset($obj_club->is_free) && $obj_club->is_free=="PAID") checked="" @endif @if($editable=='no') disabled @endif>
                <label for="paid">{{translation('paid')}}</label>
                <div class="check"></div>
              </div>
              <div class="radio-btn">
                <input type="radio" id="free" name="club_type" value="free" @if(isset($obj_club->is_free) && $obj_club->is_free=="FREE") checked="" @endif @if($editable=='no') disabled @endif>
                <label for="free">{{translation('free')}}</label>
                <div class="check"></div>
              </div>
            </div>   
          </div>
          <div class="clearfix"></div>
        </div>  

        <div class="form-group club_fee">
          <label class="col-sm-3 col-lg-2 control-label">{{translation('club_fees')}} ({{config('app.project.currency')}})<i class="red">*</i></label>
          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
            <input type="text" class="form-control" name="club_fees" id="club_fees" data-rule-required='true' data-rule-number="true" placeholder="{{translation('enter').' '. strtolower(translation('club_fees'))}}"  data-rule-min="0" value="{{isset($obj_club->club_fee) ? $obj_club->club_fee : ''}}" @if($editable=='no') readonly @endif/>
            <span class='help-block'>{{ $errors->first('club_fees')}}</span>
          </div>

          <div class="clearfix"></div>
        </div>  

        <div class="form-group">
          <label class="col-sm-3 col-lg-2 control-label">{{translation('description')}}<i class="red">*</i></label>
          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
            <textarea class="form-control" name="description" id="description" data-rule-required='true' placeholder="{{translation('enter').' '. strtolower(translation('description'))}}">{{isset($obj_club->description) ? $obj_club->description : ''}}</textarea>
            <span class='help-block'>{{ $errors->first('description')}}</span>
          </div>

          <div class="clearfix"></div>
        </div>  

        <div class="form-group">
          <label class="col-sm-3 col-lg-2 control-label">{{translation('place')}}<i class="red">*</i></label>
          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
            <input type="text" class="form-control" name="club_place" id="club_place" data-rule-required='true' placeholder="{{translation('enter').' '. strtolower(translation('place'))}}" value="{{isset($obj_club->place) ? $obj_club->place : ''}}"/>
            <span class='help-block'>{{ $errors->first('club_place')}}</span>
          </div>

          <div class="clearfix"></div>
        </div>
        <div class="col-md-12">
                            <div class="form-group back-btn-form-block">
                                <div class="controls">
                                    <div class="map-section-block">
                                      
                                    </div>
                                </div>
                            </div> 
                        </div>       
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
</div>
<script>
  $(document).ready()
  {
    $('#club_place').on('change',function(){
      var address = $('#club_place').val();
      $(".map-section-block").html('<iframe src="https://www.google.com/maps/embed/v1/place?q='+address+'&amp;key={{config('constants.GOOGLE_API_KEY')}}" width="100%" height="642" frameborder="0" style="border:0" allowfullscreen></iframe>');
    });
    $(".map-section-block").show();
  }
</script>
<script>
  $(document).ready(function() {

    if($('input[id="paid"]').is(':checked')){
       
        $('.club_fee').show();
    }
    else{
      
          $('.club_fee').hide();
    }


    $('input[name="club_type"]').click(function(){
      var inputValue = $(this).attr("value");
      if(inputValue=='free'){

          $('#club_fees').removeAttr('data-rule-required');
          $('#club_fees').removeAttr('data-rule-number');
          $("#club_fees").attr('disabled','true');
          $("#club_fees").next('span').html(" ");
          $('.club_fee').removeClass('has-error');
          $('.club_fee').hide();
      }
      else{
        $('#club_fees').attr('data-rule-required','true');
        $('#club_fees').attr('data-rule-number','true');
        $("#club_fees").removeAttr('disabled');
        $('.club_fee').show();
      }

    });

    $(".level").on('change',function(){
      var level = $('.level').val();
      if(level!='')
      {
        $(".level-class").empty().not('select-class');
        $.ajax({
          url  :"{{ $module_url_path }}/getClasses",
          type :'get',
          data :{'_token':'<?php echo csrf_token();?>','level':level},
          success:function(data){
            $(".level-class").append(data);
          }
        });  
      }
    });
    $('input[name="supervisor_type"]').click(function(){
        var user = $(this).val();
        
        if(user == 'employee')
        {
          $("#supervisor").empty();  
          $.ajax({
            url  :"{{ $module_url_path }}/get_employees",
            type :'get',
            data :{'_token':'<?php echo csrf_token();?>'},
            success:function(data){
              $("#supervisor").append(data);
            }
          });
        }
        else
        {
          $("#supervisor").empty();
          $.ajax({
            url  :"{{ $module_url_path }}/get_professors",
            type :'get',
            data :{'_token':'<?php echo csrf_token();?>'},
            success:function(data){
              $("#supervisor").append(data);
              $("#supervisor").trigger("chosen:updated");
            }
          });
        }
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
       glob_autocomplete = initGoogleAutoComponent($('#club_place')[0],glob_options,glob_autocomplete);
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
       var address = $('#club_place').val();
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
@endsection
