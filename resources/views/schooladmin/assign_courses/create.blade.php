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
          <a href="{{$module_url_path}}">{{$page_title}}</a>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        <i class="{{$create_icon}}"></i>
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
                            <h3><i class="{{$create_icon}}"></i>{{$module_title}}</h3>
                            <div class="box-tool">
                            </div>
                        </div>  
                        <div class="box-content">
                           @include('schooladmin.layout._operation_status')
                            <form method="post" onsubmit="return addLoader()"  action="{{$module_url_path}}/store"  class="form-horizontal" id="validation-form1">
                                {{ csrf_field() }}
                               <div class="col-md-12 ajax_messages">
                                  <div class="alert alert-danger" id="error" style="display:none;"></div>
                                  <div class="alert alert-success" id="success" style="display:none;"></div>
                               </div>
                                       <div class="form-group">
                                            <label class="col-lg-2 control-label">{{translation('assign_level')}}<i class="red">*</i></label>
                                            <div class="col-lg-4 controls">
                                                  <div class="frmSearch">
                                                    <select  class="form-control" name="assign_level"  id="assign_level" data-rule-required="true" onChange="getCourses();"> 
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
                                                  </div>
                                                <span class="help-block"></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                          <label class=" col-lg-2 control-label">{{translation('assign_class')}}<i class="red">*</i></label>
                                            <div class="col-lg-4 controls">
                                              <div class="frmSearch">
                                                   <select  class="js-example-basic-multiple form-control" name="assign_class[]" id="assign_class"  multiple="multiple" data-rule-required="true" disabled="">
                                                  <option value="">{{ translation('select') }} {{translation('class')}}</option>   
                                                  </select>
                                                  <div id="suggesstion-box"></div>
                                                </div>
                                               <span class="help-block"></span>
                                            </div>
                                        </div>  

                                        <div class="form-group">
                                          <label class="col-lg-2 control-label">{{translation('subject')}}
                                            <i class="red">*</i>
                                          </label>
                                          <div class="col-lg-4">      
                                             <select class="js-example-basic-multiple form-control" multiple="multiple" name="subject[]" data-rule-required='true' id="subjects">
                                               {{-- @foreach($course as $key => $value)
                                                  <option value="{{$value['get_course']['id']}}" @if(old('subject') == $value['get_course']['id']) selected @endif>{{$value['get_course']['course_name'] or ''}}</option>
                                               @endforeach --}}
                                             </select>
                                             <span class="help-block">{{ $errors->first('subject') }}</span>
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
     $(".js-example-basic-multiple").select2();
</script> 
<script type="text/javascript">
$(document).ready(function(){
 
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
});

function getCourses()
 {
    var level = $('#assign_level').val();
    if(level != '')
      {
       $.ajax({
              url  :"{{ $module_url_path }}/get_courses",
              type :'POST',
              data :{'level':level ,'_token':'<?php echo csrf_token();?>'},
              success:function(data){
                if(data.status=='success')
                {
                  $('#subjects').empty();
                  $('#subjects').append(data.data);
                }
                else
                {
                    $('.ajax_messages').show();
                    $('#error').css('display','block');
                    $('#error').text(data.data);
                    setTimeout(function(){
                        $('.ajax_messages').hide();
                    }, 3000);
                }
              }
            });
      }
 }
</script>
@endsection
