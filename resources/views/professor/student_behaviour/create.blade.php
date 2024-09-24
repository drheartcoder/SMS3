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
                <li><a href="{{$module_url_path}}">{{$module_title}}</a></li> 
                <span class="divider">
                    <i class="fa fa-angle-right"></i>
                </span> 
                <li class="active">{{$page_title}}</li>
            </ul>
        </div>
        <!-- END Breadcrumb -->
<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
  <div>
    <h1><i class="fa fa-file"></i>{{$module_title}}</h1>

  </div>
</div>
<!-- END Page Title -->
        
        <div class="box  box-navy_blue">
            <div class="box-title">
                <h3 id="title"><i class="{{$create_icon}}"></i>{{$page_title}}</h3>
                <div class="box-tool">
                </div>
            </div>
            <div class="box-content">
                 @include('professor.layout._operation_status')
                  <form method="POST" action="{{$module_url_path}}/store" onsubmit="return addLoader()" id="validation-form1" accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data" >
                       {{ csrf_field() }}  

                      <div class="col-md-12 ajax_messages">
                        <div class="alert alert-danger" id="error" style="display:none;"></div>
                        <div class="alert alert-success" id="success" style="display:none;"></div>
                      </div>
                          <div class="row">
                            
                             <div class="col-md-6">
                                  <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('level')}} <label style="color: red">*</label></label>
                                        <div class="col-sm-9 col-md-8 col-lg-9 controls">
                                           <select name="level" id="level" class="form-control" onChange="getClasses();" data-rule-required="true">
                                              <option value="">{{translation('select_level')}}</option>
                                              @if(isset($arr_levels) && count($arr_levels)>0)
                                               
                                                @foreach($arr_levels as $key => $level)
                                                  <option value="{{isset($level['level_details']['level_id'])?$level['level_details']['level_id']:''}}">{{isset($level['level_details']['level_name'])?$level['level_details']['level_name']:''}}</option>
                                                @endforeach
                                              @endif
                                          </select>
                                        </div>
                                    </div>
                              </div> 

                                <div class="col-md-6">
                                   <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('class')}} <label style="color: red">*</label></label>
                                        <div class="col-sm-9 col-md-8 col-lg-9 controls">
                                           <select name="class" id="class" class="form-control" onChange="getCourses();" data-rule-required="true">
                                              <option value="">{{translation('select_class')}}</option>
                                              
                                          </select>
                                        </div>
                                    </div>
                              </div>
                              
                              <div class="col-md-6">
                                  <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('course')}} <label style="color: red">*</label></label>
                                        <div class="col-sm-9 col-md-8 col-lg-9 controls">
                                           <select name="course" id="course" class="form-control" data-rule-required="true">
                                              <option value="">{{translation('select_course')}}</option>
                                          </select>
                                        </div>
                                    </div>
                              </div>

                              <div class="col-md-6">
                                  <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('behaviour_period')}}</label>
                                        <div class="col-sm-9 col-md-8 col-lg-9 controls">
                                           
                                          <input type="text" class="form-control" name="period" id="period" value="{{isset($period)?$period:''}}" readonly style="cursor: pointer;">
                                        </div>
                                    </div>
                              </div>
                          

                              <div class="col-md-6">
                                  <div class="form-group">
                                      <label class="col-sm-3 col-md-4 col-lg-3 control-label"></label>
                                      <div class="col-sm-3 col-md-4 col-lg-3 controls"> 
                                        <div id="button">
                                          <input type="button" name="show" id="show" value="{{translation('show')}}" class="form-control btn btn-primary" onClick="getStudents();"> 
                                        </div>
                                      </div>
                                  </div>
                              </div>
                          </div>

                          <div class="clearfix">
                          </div>                            
                          <div class="border-box" id="box" hidden="true">
                              <div class="filter-section">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">                            
                                            <input class="form-control" name="search_key" id="search_key" type="text" placeholder="{{translation('search')}}...">
                                        </div>
                                    </div>
                                </div>
                              </div>
                              <div class="table-responsive attendance-create-table-section" id="table_div" style="border:0;display: none;">
                                  <table class="table table-advance" id="table_module" cellpadding="35px">
                                      <thead>
                                          <tr>
                                              <th>
                                                {{translation('sr_no')}}
                                              </th>
                                              <th>
                                                  {{translation('name')}}
                                              </th>
                                              <th>
                                                  {{translation('national_id')}}
                                              </th>
                                              <th>
                                                  {{translation('notation')}}<label style="color: red">*</label>
                                              </th>
                                              <th>
                                                  {{translation('description')}}
                                              </th>
                                          </tr>
                                      </thead>
                                      <tbody id="tbody">
                                      </tbody>
                                  </table>
                                  <div class="form-group">
                                      <div class="col-xs-12 col-sm-2 col-md-4 col-lg-2"></div>
                                      <div class="col-xs-12 col-sm-10 col-md-8 col-lg-10">
                                          <a href="{{$module_url_path}}" class="btn btn-primary">{{translation('back')}}</a>
                                          <button type="submit" class="btn btn-primary" id="button">{{translation('save')}}</button>
                                      </div>
                                  </div>
                              </div>

                              <div class="table-responsive attendance-create-table-section" id="div" style="border:0;display: none;">
                              </div>
                          </div>

                          
                  </form>
            </div>       
          </div>

          <div class="box  box-navy_blue">
           
<script>

function getClasses()
  {
      var level   =   $('#level').val();
      if(level != '')
      {
      $('#class').empty();
       $.ajax({
              url  :"{{ $module_url_path }}/getClasses",
              type :'POST',
              data :{'level':level ,'_token':'<?php echo csrf_token();?>'},
              success:function(data){
                $('#class').append(data);
              }
            });
      }
  }

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

  function getStudents()
  {
    var level      =   $('#level').val();
    var cls_name   =   $('#class').val();
    var course     =   $('#course').val();

    if(level != '' && cls_name != '' && course !='')
    {

      $('#button').html("<a class='form-control btn btn-primary'><i class='fa fa-spinner fa-spin'></i> {{translation('processing')}}...</a>");
      $('#button').attr('disabled', true);

      $.ajax({
                url  :"{{ $module_url_path }}/get_students",
                type :'POST',
                data :{'level':level ,'class':cls_name , 'course':course,'_token':'<?php echo csrf_token();?>'},
                success:function(data){
                
                  if(data.flag == true)
                  {
                    $('#div').css('display','none');
                    $('#table_div').show();
                    $('#box').show();
                    $('#tbody').empty();
                    $('#tbody').append(data.data);
                    if(data.status == 'update')
                    {
                      $('#button').html('{{translation('update')}}');
                      $('#validation-form1').attr('action','{{$module_url_path}}/update/'+data.enc_id);
                      $('#title').html('<i class="fa fa-edit"></i>{{translation('edit')}} {{translation('student_behaviour')}}');
                      $('.active').html('{{translation('edit')}} {{translation('student_behaviour')}}');
                    }
                    else
                    {
                      $('#button').html('{{translation('save')}}');
                      $('#validation-form1').attr('action','{{$module_url_path}}/store');
                      $('#title').html('<i class="{{$create_icon}}"></i>{{$page_title}}');
                      $('.active').html('{{$page_title}}'); 
                    }

                  }
                  else if(data.flag == false)
                  {
                    
                    $('#box').show();
                    $('#table_div').css('display','none');
                    $('.filter-section').hide();
                    
                    $('#div').show();
                    $('#div').css('display','block');
                    $('#div').empty();
                    $('#div').append(data.data);
                  }

                  $('#button').html('<input type="button" name="show" id="show" value="{{translation('show')}}" class="form-control btn btn-primary" onClick="getStudents();"> ');
                  $('#button').attr('disabled','none');
                }
              });
    }
    else
    {
      $('.ajax_messages').show();
          $('#error').css('display','block');
          $('#error').text('{{translation('select_level_class_course_first')}}');
          setTimeout(function(){
              $('.ajax_messages').hide();
          }, 4000);
    }
  }

  function getComment(obj,student_id)
  {

    var notation = $(obj).val();
    if(notation<=10 && notation>=0 && notation!='')
    {
      if(notation>=8 && notation<=10)
      {
        $('#comment_'+student_id).val('{{translation('excellent_behaviour')}}');
      }
      else if(notation>=6 && notation<8)
      {
        $('#comment_'+student_id).val('{{translation('good_behaviour')}}');
      }
      else if(notation>=4 && notation<6)
      {
        $('#comment_'+student_id).val('{{translation('average_behaviour')}}');
      }
      else if(notation>=0 && notation<4)
      {
        $('#comment_'+student_id).val('{{translation('poor_behaviour')}}'); 
      }
    }
  }

   $("#search_key").keyup(function(){
    var flag=0;
        $("tbody tr").each(function(){
          
            var td = $(this).find("td");
            $(td).each(function(){
              var data = $(this).text().trim();
              data = data.toLowerCase();

              var search_key = $("#search_key").val();
              search_key = search_key.toLowerCase();
              search_key = new RegExp(search_key) ; 
                console.log(search_key.test(data));
                if(search_key.test(data)){
                  flag=1;
                  $(this).parent().show();
                  return false;
                }
                else{
                  $(this).parent().hide();
                }
                console.log(data);
                

            });
         })
         if(flag==0)
          {
            $("#hide_row").show();
          }
          else
          {
            $("#hide_row").hide();
          }  
      })
</script>  
@endsection