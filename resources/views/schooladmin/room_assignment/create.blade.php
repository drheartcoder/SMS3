@extends('schooladmin.layout.master')                
@section('main_content')
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
          <a href="{{$module_url_path}}">{{$module_title}}</a>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        <i class="{{$create_icon}}"></i>
        <li class="active">{{$page_title}}</li>
    </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="{{$module_icon}}"></i>{{str_plural($module_title)}}</h1>

    </div>
</div>
<!-- END Page Title -->


     <!-- BEGIN Tiles -->
            <div class="row">
                <div class="col-md-12">

                    <div class="box  box-navy_blue">
                        <div class="box-title">
                            <h3><i class="{{$create_icon}}"></i>{{$page_title}}</h3>
                            <div class="box-tool">
                            </div>
                        </div>  
                        <div class="box-content">
                           @include('schooladmin.layout._operation_status')
                            <form method="post" onsubmit="return addLoader()"  action="{{$module_url_path}}/store"  class="form-horizontal" id="validation-form1">
                                {{ csrf_field() }}

                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('tag')}}<i class="red">*</i></label>
                                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                                          <div class="frmSearch">
                                            <select  class="form-control" name="tag" id="tag" data-rule-required="true">
                                            <option value="">{{ translation('select') }} {{translation('tag')}}</option>    
                                            @if(!empty($arr_room_data) && count($arr_room_data) > 0)
                                                @foreach($arr_room_data as $val)
                                                <option value="{{ $val['id'] }}" data-tag-name="{{$val['tag_name']}}" >
                                                    {{ ucwords($val['tag_name']) }}
                                                </option>    
                                                @endforeach

                                            @endif

                                            </select>
                                            <div id="suggesstion-box"></div>
                                          </div>
                                        <span class="help-block"></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('floor_no')}}<i class="red">*</i></label>
                                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                                          <div class="frmSearch">
                                            <select  class="form-control" name="floor_no" id="floor_no" data-rule-required="true" disabled="">
                                            <option value="">{{ translation('select') }} {{translation('floor_no')}}</option>   
                                            </select>
                                            <div id="suggesstion-box" ></div>
                                          </div>
                                        <span class="help-block"></span>
                                    </div>
                                </div>

                                

                                 <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('room_number')}}<i class="red">*</i></label>
                                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                                          <div class="frmSearch">
                                              <select  class="form-control" name="room_number" id="room_number" data-rule-required="true" disabled="">
                                              <option value="">{{ translation('select') }} {{translation('room_number')}}</option>   
                                            </select>
                                            <div id="suggesstion-box"></div>
                                          </div>
                                        <span class="help-block"></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('room_name')}}<i class="red">*</i></label>
                                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                                          <div class="frmSearch">
                                            <input type="text" name="room_name" id="room_name" class="form-control"   data-rule-required='true' maxlength="100" placeholder="{{translation('enter')}} {{translation('room_name')}}" />
                                            <div id="suggesstion-box"></div>
                                          </div>
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('level')}}<i class="red">*</i></label>
                                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                                      <select  class="form-control" name="assign_level" id="assign_level" data-rule-required="true">
                                      <option value="">{{ translation('select') }} {{translation('level')}}</option>    
                                      @if(!empty($arr_level) && count($arr_level) > 0)
                                          @foreach($arr_level as $val)

                                          <option value="{{ $val['level_id'] }}"   >
                                              {{ ucwords($val['level_details']['level_name']) }}
                                          </option>    
                                          @endforeach
                                      @endif
                                       </select>
                                      <div id="suggesstion-box"></div>
                                      <span class="help-block"></span>
                                    </div>
                                </div>
                                                
                                <div class="form-group">
                                  <label class="col-sm-3 col-lg-2 control-label">{{translation('class')}}<i class="red">*</i></label>
                                  <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                                     <select  class="form-control" name="assign_class" id="assign_class" data-rule-required="true" disabled="">
                                    <option value="">{{ translation('select') }} {{translation('class')}}</option>   
                                    </select>
                                    <div id="suggesstion-box"></div>
                                    <span class="help-block"></span>  
                                  </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                                        <a href="{{$module_url_path}}" class="btn btn-primary">{{translation('back')}}</a>
                                        <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('save')}}</button>
                                    </div>
                                </div>  
                             
                            </form>
                        </div>
                    </div>
                </div>
              </div>
              <script type="text/javascript">
$(document).ready(function(){

$(document).on("change", "#tag", function(){

    var _id = $(this).val();
    var _tag_name =  $('option:selected', this).attr('data-tag-name');  
    var token   = $("input[name=_token]").val();
    
    if(_id == '' &&  _tag_name ==''){
      $('#tag').html('<option value="">{{ translation('select') }} {{translation('floor_no')}}</option>');
    }
    else
    {
        $.ajax({
            headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
            url : '{{url('/').'/'.$school_admin_panel_slug }}/room/ajax/get_floors',
            type : "POST",
            dataType: 'JSON',
            data : {room_management:_id, tag_name : _tag_name},
            beforeSend:function(data, statusText, xhr, wrapper){
                $('#floor_no').html('<option value="">{{ translation('select') }} {{translation('floor_no')}}</option>');
                
            },
            success:function(data, statusText, xhr, wrapper){
              $('#floor_no').html('');
              allData = ['<option value="">{{ translation('select') }} {{translation('floor_no')}}</option>'];
              if(data.status == 'done')
              {
                    $("#floor_no").attr("disabled",false);
                    var responseArray = data.categories;
                    if(responseArray.length){
                      var obj = $.parseJSON(responseArray);
                      $.each(obj, function() {
                         allData.push('<option value="'+this['id']+'" data-total-rooms="'+this['totalRooms']+'">'+this['name']+'<\/option>'); 
                         console.log(allData);
                      });  
                    } 
                    $('#floor_no').html(allData.join(''));
                
              }
              else
              {
                $('#floor_no').html(allData.join(''));
                if(data.errors == '')
                {
                  $("#err_optSubCategory").html(data.message);
                }
                 
              }

            }
      });
        
    }
    
});

$(document).on("change", "#assign_level", function(){
    var _id = $(this).val();
    var token   = $("input[name=_token]").val();
    
    if(_id == ''){
      $('#assign_class').html('<option value="">{{ translation('select') }} Class</option>');
    }
    else
    {
        $.ajax({
            headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
            url : '{{url('/').'/'.$school_admin_panel_slug }}/room/ajax/get_class',
            type : "POST",
            dataType: 'JSON',
            data : {level_id:_id},
            beforeSend:function(data, statusText, xhr, wrapper){
                $('#assign_class').html('<option value="">{{ translation('select') }} Class</option>');
                
            },
            success:function(data, statusText, xhr, wrapper){
              $('#assign_class').html('');
              allData = ['<option value="">{{ translation('select') }} Class </option>'];
              if(data.status == 'done')
              {
                    $("#assign_class").attr("disabled",false);
                    var responseArray = data.categories;
                    if(responseArray.length){
                      var obj = $.parseJSON(responseArray);
                      $.each(obj, function() {
                         allData.push('<option value="'+this['id']+'">'+this['name']+'<\/option>'); 
                         console.log(allData);
                      });  
                    } 
                    $('#assign_class').html(allData.join(''));
                
              }
              else
              {
                $('#assign_class').html(allData.join(''));
              }

            }
      });
        
    }
    
});


$(document).on("change", "#floor_no", function(){
  
  var totalRooms =  $('option:selected', this).attr('data-total-rooms');  
  $("#room_number").attr("disabled",false);
  allData = ['<option value="">{{ translation('select') }} {{translation('room_number')}}</option>'];
  if(totalRooms > 0)
  { 
       for($i=1;$i<=totalRooms;$i++){
             allData.push('<option value="'+$i+'">'+$i+'<\/option>'); 
             console.log(allData);
        } 
  }
 $('#room_number').html(allData.join(''));

});

 

});

</script>
@endsection
