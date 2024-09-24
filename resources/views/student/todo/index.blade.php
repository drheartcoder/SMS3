@extends('student.layout.master')                
@section('main_content')
<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
   <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{url('/')}}/{{$student_panel_slug}}/dashboard">{{translation('dashboard')}}</a>
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
            <i class="fa fa-eye"></i>
            <li class="active">{{$page_title}}</li>
    </ul>

   
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="{{$module_icon}}"></i>{{$module_title}}</h1>

    </div>
</div>
<!-- END Page Title -->
   <div class="box  box-navy_blue">

            <div class="box-title">
                <h3><i class="{{$module_icon}}"></i>{{translation('to_do_list')}}</h3>
                <div class="box-tool">
                    <div class="dropup-down-uls">
                      <a href="javascript:void(0)" class="export-lists"><i class="fa fa-upload"></i> {{ translation('export')}} </a>
                      <div class="export-content-links">
                          <div class="li-list-a">
                              <a href="javascript:void(0)" onclick="exportForm('pdf');">{{translation('pdf')}}</a>
                          </div>
                          <div class="li-list-a">
                              <a href="javascript:void(0)" onclick="exportForm('{{config("app.project.export_file_formate")}}');">{{translation('excel')}}</a>
                          </div>
                           
                      </div>
                  </div>
                    <a data-toggle="modal" data-target="#my-pus-popup"  class="addToDo popu"  >{{translation('add')}} {{translation('todo')}}</a> 
                </div>
            </div>
            <div class="box-content edit-btns">
              @include('student.layout._operation_status')  

              {!! Form::open([ 'url' => $module_url_path.'/multi_action',
             'method'=>'POST',
             'enctype' =>'multipart/form-data',   
             'class'=>'form-horizontal', 
             'id'=>'frm_manage' 
             ]) !!}
             {{ csrf_field() }}
                <div class="col-sm-12 col-md-6 col-lg-offset-3">
                    <div class="todo-list-sction">


                      <input type="hidden" name="multi_action" value="" />
                      <input type="hidden" name="search" id="search" value="" />
                      <input type="hidden" name="file_format" id="file_format" value="" />

                      @if(!empty($arr_data) && sizeof($arr_data['data']) > 0)
                       <div class="todo-list-sction">
                        <ul class="todolistul">
                            @foreach($arr_data['data'] as $key => $value)
                            <li class="@if($value['status']==1) dash-line-block @endif removeToDo{{$value['id']}}">
                                <div class="checkbox-doto">
                                    <div class="check-box">
                                      <input class="markAsDone filled-in" value="{{$value['id']}}" name="checked_record[]" id="mult_changecheck{{$value['id']}}" type="checkbox" @if($value['status']==1) checked="checked" @endif>
                                        <label for="mult_changecheck{{$value['id']}}"></label>
                                    </div>
                                </div>
                                <div class="title-to-do-li" >
                                    {{ $value['todo_description'] or '' }}
                                </div>
                                <div class="minus-icn-close">
                                    <a href="javascript:void(0)"  data-id="{{$value['id']}}" onclick="return confirm_action(this,event,'{{translation('do_you_really_want_to_delete_this_record')}}')"  class="todo-lst-li"><i class="fa fa-minus"></i></a>
                                </div>
                            </li>
                            @endforeach
                             
                            
                        </ul>
                    </div>
                        @endif
                       
                    </div>
                <div class="paging todo-pagi" style="text-align: right"> {{ $pagination_links }}</div>
                </div>
                  {!! Form::close() !!}
           <div class="clearfix"></div>
            </div>
        </div>

<!-- Modal -->
<div class="student-popu-wrapper">
<div id="my-pus-popup" class="modal fade" role="dialog" data-backdrop="static">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
          <span class="modal-title">{{translation('add')}} {{ translation('todo')}}</span>
       </div>
      <div class="modal-body">
          <div id="msgSuccess"></div>
          <form class="form-horizontal"  name="addTodo" id="addTodo" method="POST" action="{{$module_url_path}}/store" enctype="multipart/form-data">
             {{ csrf_field() }}
            <div class="form-group">
                <label class="popup-control-label">{{translation('todo')}} <i class="red">*</i></label>
                <div class="controls">
                    <input class="form-control" data-rule-required="true" data-rule-maxlength="1000" name="todo_description" id="todo_description" placeholder="{{translation('enter')}} {{translation('todo')}}" type="text" maxlength="1000">
                    <span class="help-block" id="error-todo_description"></span>
                </div>
            </div>
             <div class="my-popup-button">
                <input type="hidden" name="isSubmit" value="1">
                <a href="javascript:void(0)" class="btn btn btn-primary"  data-dismiss="modal">{{translation('cancel')}}</a>
                  <a  href="javascript:void(0);"  id="submit_button" class="btn btn btn-primary"  name="submit_button">{{translation('save')}} </a>
                
            </div>
          </form>
        </div>
    </div>

  </div>
</div>
</div>
<script src="{{url('/')}}/js/jquery.form.min.js" type="text/javascript"></script>
<script type="text/javascript">
              var SITE_URL  = "{{ url('/') }}/{{$student_panel_slug}}";
              var csrf_token = "{{ csrf_token() }}";
              function confirm_action(ref,evt,msg)
              {
                     var msg = msg || false;

                     evt.preventDefault();  
                     swal({
                      title: "Are you sure ?",
                      text: msg,
                      type: "warning",
                      showCancelButton: true,
                      confirmButtonColor: "#DD6B55",
                      confirmButtonText: "Yes",
                      cancelButtonText: "No",
                      closeOnConfirm: true,
                      closeOnCancel: true
                    },
                    function(isConfirm)
                    {
                      if(isConfirm==true)
                      {
                    
                    var todoId = $(ref).attr('data-id');

                    $.ajax({
                      url: SITE_URL+'/todo/delete_todo',
                      type:'POST',
                      data:{
                        '_token' : csrf_token,
                        'todoId' : todoId
                      },
                      success: function( res ) {
                        if(res=='done'){
                          $('.removeToDo'+todoId).remove();
                        }else if(res =='InvalidTodo'){
                          swal("{{translation('invalid_todo')}}");  
                        }else{
                          swal("{{translation('something_went_wrong_please_try_again_later')}}");  
                        }

                      },
                      error: function( res ){
                        swal("{{translation('something_went_wrong_please_try_again_later')}}");
                      }
                    });

                  }
                });
             } 

             $('body').on('click','.markAsDone',function(event){
                  var todoId = $(this).val();
                  if(todoId>0){

                       $.ajax({
                            url: SITE_URL+'/todo/mark_as_read_todo',
                            type:'POST',
                            data:{
                              '_token' : csrf_token,
                              'todoId' : todoId
                            },
                            success: function( resData ) {
                              var result = resData.split('####');
                              var res = result[0];
                              var status = result[1];
                              console.log(result);
                              if(res=='done'){
                                
                                if(status==1){
                                  $('.removeToDo'+todoId).addClass('dash-line-block')
                                }else if(status==0){
                                  $('.removeToDo'+todoId).removeClass('dash-line-block')
                                }



                              }else if(res =='InvalidTodo'){  
                                swal("{{translation('invalid_todo')}}");  
                              }else if(res =='oopsSomething'){ 
                                swal("{{translation('oopssomething_went_wrong_while_updating_the_todo')}}");  
                              }else{
                                swal("{{translation('something_went_wrong_please_try_again_later')}}");  
                              }
                                

                            },
                            error: function( res ){
                              swal("{{translation('something_went_wrong_please_try_again_later')}}");
                            }
                          });
                  }

            }); 

 
$(document).ready(function ()
{ 
  var runToDoAdd = null;
  $(document).on('click', "#submit_button", function(){
      var todo_description      = $("#todo_description").val();
      var flag = 0;
     

      if($.trim(todo_description) == '')
      {
      
        $("#error-todo_description").html("{{translation('please_enter_todo')}}");
        flag = 1;
      }else if(todo_description.length > 1000){
        $("#error-todo_description").html("{{translation('todo_should_not_be_more_than_1000_characters')}}");
        flag = 1;
      }else{
        $("#error-todo_description").html('');
      }

      if(flag == 1)
      {  
        return false;
      }
      else
      {
        $('.error-red').html('');
        runToDoAdd = $("#addTodo").ajaxSubmit({
            headers   :{'X-CSRF-Token': $('input[name="_token"]').val()},
            dataType  : 'json',
            beforeSend:function(data, statusText, xhr, wrapper) 
            {

               if(runToDoAdd != null){
                 return false;
               }
            
              $('.text-danger').html('');
              $("#submit_button").attr('disabled', true);
              $("#submit_button").html("<b><i class='fa fa-spinner fa-spin'></i></b> Sending...");
           },
           success :function(data){ 
              runToDoAdd = null;
              $("#submit_button").html("Send");
              $("#submit_button").attr('disabled', false);
              if(data.status == 'success'){

                $('form[name="addTodo"]')[0].reset();
                $("#msgSuccess").html('<div class="alert alert-success no-border">  <button type="button" class="close" style="margin-top: 0px !important;padding: 0px !important;" data-dismiss="alert" aria-hidden="true">&times;</button><span class="text-semibold">Success!</span> '+data.customError+'<a href="#" class="alert-link"></a></div>');
                setTimeout(function(){
                  window.location.href = '{{$module_url_path}}';
              }, 2000);
            }else if(data.errors != ''){
                var errorsHtml = '';
                    $.each(data.errors, function( key, value ) {
                      errorsHtml = $('#error-'+key).html(value[0]);
                    });
                }else  if(data.customError!=''){
                $("#msgSuccess").html('<div class="alert alert-danger no-border">  <button type="button" class="close" style="margin-top: 0px !important;padding: 0px !important;" data-dismiss="alert" aria-hidden="true">&times;</button><span class="text-semibold">Error!</span> '+data.customError+'<a href="#" class="alert-link"></a></div>'); 
              }
              
            },
            error  :function(data, statusText, xhr, wrapper)
            { 
              runToDoAdd = null;
              console.log(data);
              $("#msgSuccess").html('<div class="alert alert-danger no-border">  <button type="button" class="close" style="margin-top: 0px !important;padding: 0px !important;" data-dismiss="alert" aria-hidden="true">&times;</button><span class="text-semibold">Error!</span> Oops,Something went wrong,Please try again later. <a href="#" class="alert-link"></a></div>');
              
            }
          });
      }

  });
});
$(".addToDo").on('click', function(){
    $("#todo_description").val('');
    $('.error-red').html('');
});
$(document).ready(function() {
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
});
/* ADD Adds */     
</script>
<script type="text/javascript">
  function exportForm(file_format)
  {
    document.getElementById('file_format').value = file_format;
    var serialize_form   = $("#frm_manage").serialize();
    window.location.href = '{{ $module_url_path }}/export?'+serialize_form+'&export=true';
  }
  $(document).on("change","[type='search']",function(){
      var search_hidden = $(this).val();
      document.getElementById('search').value = search_hidden;
   });
</script>

@endsection