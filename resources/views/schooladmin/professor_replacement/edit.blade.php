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
    <i class="{{$edit_icon}}"></i>
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
        <h3><i class="{{$edit_icon}}"></i>{{$page_title}}</h3>
        <div class="box-tool">
        </div>
      </div>  
      <div class="box-content">
       @include('schooladmin.layout._operation_status')

      <form method="POST" action="{{$module_url_path}}/update/{{$enc_id}}" accept-charset="UTF-8" class="form-horizontal"  enctype="multipart/form-data"  id="validation-form1" onsubmit="return addLoader()">
        {{ csrf_field() }}

        <div class="col-md-12 ajax_messages">
          <div class="alert alert-danger" id="error" style="display:none;"></div>
          <div class="alert alert-success" id="success" style="display:none;"></div>
        </div>

        <div class="col-sm-12 col-md-12 col-lg-12">
          <div class="row">
            <div class="form-group">
              <label class="col-sm-3 col-lg-2 control-label">{{translation('absent_professor')}} <i class="red">*</i></label>
              
              <div class="col-sm-9 col-lg-4 controls">

                  {{-- <select name="professor_id" id="professor_id" class="form-control chosen" onChange="getProfessorNo();" data-rule-required="true">
                    <option value="">{{translation('select_professor')}}</option>
                    @if(isset($professors) && count($professors)>0)
                      @foreach($professors as $key => $value)
                        <option value="{{isset($value['id'])?$value['id']:''}}">{{isset($value['first_name'])?ucwords($value['first_name']):''}} {{isset($value['last_name'])?ucwords($value['last_name']):''}}</option>
                      @endforeach
                    @endif
                </select> --}}
                <?php
                  $first_name = isset($arr_data['professor_details']['first_name'])?$arr_data['professor_details']['first_name']:'';
                  $last_name  = isset($arr_data['professor_details']['last_name'])?$arr_data['professor_details']['last_name']:'';
                  $user_name  = $first_name.' '.$last_name;
                ?>
                <input type="text" name="professor_name" name="professor_name" class="form-control" readonly style="cursor: pointer;" value="{{ucwords($user_name)}}">
                <input type="hidden" name="professor_id" id="professor_id" value="{{isset($arr_data['professor_id'])?$arr_data['professor_id']:''}}">
              </div>
              <div class="clearfix"></div>
            </div>
          </div>
        </div>
        

        <div class="col-sm-12 col-md-12 col-lg-12">
          <div class="row">
            <div class="form-group">
                  <label class="col-sm-3 col-lg-2 control-label">{{translation('absent_professor_no')}}<i class="red">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls">
                      <input class="form-control" name="professor_no" id="professor_no" type="text" placeholder="{{translation('absent_professor_no')}}" readonly style="cursor: pointer;" data-rule-required="true" value="{{isset($arr_data['professor_no'])?$arr_data['professor_no']:''}}">
                      <span class="help-block"></span>
                  </div>
                  <div class="clearfix"></div>
            </div>
          </div>
        </div>
        <div class="col-sm-12 col-md-12 col-lg-12">
          <div class="row">
            <div class="form-group">
              <label class="col-sm-3 col-lg-2 control-label">{{translation('date_of_replacement')}} <i class="red">*</i></label>
              <div class="col-sm-9 col-lg-4 controls">
               <div class="row">
                  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                      <input class="form-control" name="from_date" id="from_date" placeholder="{{translation('from')}}" type="text" readonly style="cursor: pointer;" value="{{isset($arr_data['from_date'])?$arr_data['from_date']:''}}" />
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                      <input class="form-control" name="to_date" id="to_date" placeholder="{{translation('to')}}" type="text" readonly style="cursor: pointer;"  value="{{isset($arr_data['to_date'])?$arr_data['to_date']:''}}" />
                  </div>
                </div>
              </div>
              <div class="clearfix"></div>
           </div>
         </div>
        </div>  


        <!-- tables inside this DIV could have draggable content -->
        <div class="clearfix"></div>
          <div class="row">
            <div class="col-lg-12">
              <div class="teacher-list-section">
                {{translation('timetable')}}
              </div>

              <div class="timetable-data-new timetable-section-main professor-timetable-section" style="display: block!important;"> 
                   
                   <div class="table-responsive" style="border:1!important;" id="timetable">
                   </div>
              </div>
            </div>
          </div>

          <!-- Modal -->
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">{{translation('professor_replacement')}}</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group">
                                    <label class="col-sm-4 col-md-2 col-lg-3 control-label">{{translation('professor')}}</label>
                                    <div class="col-sm-9 col-md-8 col-lg-6 controls">
                                      <select name="professor" id="professor" class="form-control chosen" onChange="getUserNo();" data-rule-required="true">
                                         <option value="">{{translation('select_professor_to_replace')}}</option>
                                      </select>
                                     
                                        <span id="err_start_date" style="color: red"></span>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 col-md-2 col-lg-3 control-label ">{{translation('professor_no')}}</label>
                                    <div class="col-sm-9 col-md-8 col-lg-6 controls">
                                        <input class="form-control" name="user_id" id="user_id" type="text" placeholder="{{translation('user_no')}}" readonly data-rule-required="true"/>
                                        <span class='help-block'>{{ $errors->first('user_id') }}
                                          
                                       </span>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <input type="hidden" name="replacement_date" id="replacement_date">
                                <input type="hidden" name="level_class_id" id="level_class_id">
                                <input type="hidden" name="start_time" id="start_time">
                                <input type="hidden" name="end_time" id="end_time">
                                <input type="hidden" name="period_no" id="period_no">
                                <input type="hidden" name="td_id" id="td_id">
                                <input type="hidden" name="day" id="day">
                                <input type="hidden" name="id" id="id">
                                <input type="hidden" name="course_id" id="course_id">
                            </div>
                        </div>
                        <div class="modal-footer">                        
                            <button type="button" class="btn btn-primary" id="replace">{{translation('replace')}}</button>
                        </div>
                    </div>
                </div>
            </div>

      </form>
 
   
<div class="clearfix"></div>

</div>
</div>
</div>
</div>    
<script type="text/javascript">
  $(document).ready(function(){
    
  
    var from_date    = $('#from_date').val();
    var to_date      = $('#to_date').val();
    var professor_id = {{isset($arr_data['professor_id'])?$arr_data['professor_id']:''}};
    var professor_no = '{{isset($arr_data['professor_no'])?$arr_data['professor_no']:''}}';
    
    $.ajax({
                url  :"{{ $module_url_path }}/get_details",
                type :'post',
                data :{'start_date':from_date ,'end_date':to_date ,'id':professor_id,'no':professor_no,'status':'edit','_token':'<?php echo csrf_token();?>'},
                success:function(data){
                  $('#timetable').empty();
                  $('#timetable').append(data);
                }
          });
      
 });
</script>

<script>
 function get_details(obj,course_id,start_time,end_time,level_class_id,day,date,period_no,id)
 {
    $('#td_id').val($(obj).attr('id'));
    $('#course_id').val(course_id);
    $('#period_no').val(period_no);
    $('#replacement_date').val(date);
    $('#level_class_id').val(level_class_id);
    $('#start_time').val(start_time);
    $('#end_time').val(end_time);
    $('#day').val(day);

    if(id != '')
    { 
      $('#id').val(id);
    }
    $.ajax({
                  url:"{{$module_url_path.'/get_free_professors'}}",
                  type:'POST',
                  data:{'start_time':start_time,'end_time':end_time,'level_class_id':level_class_id,'day':day,'date':date,'course_id':course_id,'_token':'<?php echo csrf_token();?>'},           
                    success:function(data)
                    {
                      $('#professor').empty();
                      $('#professor').append(data);
                      $("#professor").trigger("chosen:updated");
                    }

          });
  }

  $('#replace').on('click',function()
  {
        var period         = $('#period_no').val();
        var date           = $('#replacement_date').val();
        var level_class_id = $('#level_class_id').val();
        var start_time     = $('#start_time').val();
        var end_time       = $('#end_time').val();
        var professor_id   = $('#professor_id').val();
        var professor_no   = $('#professor_no').val();
        var professor      = $('#professor').val();
        var user_id        = $('#user_id').val();
        var from_date      = $('#from_date').val();
        var to_date        = $('#to_date').val();
        var day            = $('#day').val();
        var id             = $('#id').val();
        var assignment_id  = $('#assignment_id').val();
        var course_id      = $('#course_id').val();

        var data = {'day':day,'from_date':from_date,'to_date':to_date,'period':period,'date':date,'level_class_id':level_class_id,'start_time':start_time,'end_time':end_time,'professor_id':professor_id,'professor_no':professor_no,'professor':professor,'user_id':user_id,'assignment_id':assignment_id,'course_id':course_id,'_token': '<?php echo csrf_token();?>'};

        if(id!='')
        {
            $.ajax({
                    url:"{{$module_url_path.'/update/'}}"+id,
                    type:'POST',
                    data:data,           
                      success:function(data)
                      {
                        if(data.type=='success')
                        {
                          $('#'+$('#td_id').val()).empty();

                          $('#'+$('#td_id').val()).html('<div class="seperate_subjects" style="color:#1275ed;" >'+data.data.user_name+'<br>'+data.data.level_name+' '+data.data.class_name+'<br/>'+data.data.course_name+'<br/>'+data.data.start_time+' - '+data.data.end_time+'</div>');
                          $('#myModal').modal('hide');
                          $('.ajax_messages').show();
                          $('#success').css('display','block');
                          $('#success').text(data.msg);
                          setTimeout(function(){
                              $('.ajax_messages').hide();
                          }, 4000);
                          $('#'+$('#td_id').val()).attr('data-target','#');
                        }
                        else if(data.type=='error')
                        {
                          $('#myModal').modal('hide');

                          $('.ajax_messages').show();
                          $('#error').css('display','block');
                          $('#error').text(data.msg);
                          setTimeout(function(){
                              $('.ajax_messages').hide();
                          }, 4000);  
                        }
                      }

            });
        }
      
        else
        {
          $.ajax({
                    url:"{{$module_url_path.'/store'}}",
                    type:'POST',
                    data:data,           
                      success:function(data)
                      {
                        if(data.type=='success')
                        {
                          $('#'+$('#td_id').val()).empty();

                          $('#'+$('#td_id').val()).html('<div class="seperate_subjects" style="color:#1275ed;" >'+data.data.user_name+'<br> '+data.data.level_name+' '+data.data.class_name+'<br/>'+data.data.course_name+'<br/>'+data.data.start_time+' - '+data.data.end_time+'</div>');
                          $('#myModal').modal('hide');
                          $('.ajax_messages').show();
                          $('#success').css('display','block');
                          $('#success').text(data.msg);
                          setTimeout(function(){
                              $('.ajax_messages').hide();
                          }, 4000);
                          $('#'+$('#td_id').val()).attr('data-target','#');
                        }
                        else if(data.type=='error')
                        {
                          $('#myModal').modal('hide');

                          $('.ajax_messages').show();
                          $('#error').css('display','block');
                          $('#error').text(data.msg);
                          setTimeout(function(){
                              $('.ajax_messages').hide();
                          }, 4000);  
                        }
                      }

            });
        }

  });

 function getUserNo()
 {
    var professor_id  = $('#professor').val();

    $.ajax({
                  url:"{{$module_url_path.'/get_user_no'}}",
                  type:'POST',
                  data:{'professor_id':professor_id,'_token':'<?php echo csrf_token();?>'},           
                    success:function(data)
                    {
                      $('#user_id').val(data);
                    }

          });
 }
</script>

 @endsection
