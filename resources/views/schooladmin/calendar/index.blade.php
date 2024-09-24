@extends('schooladmin.layout.master')                
@section('main_content')

<style>

    
 #wrap {
  
    margin: 0 auto;
    }
    
  #external-events {
    float: left;
  
    padding: 0 10px;
    border: 1px solid #ccc;
    background: #eee;
    text-align: left;
    }
    
  #external-events h4 {
    font-size: 16px;
    margin-top: 0;
    padding-top: 1em;
    }
    
  .external-event {  try to mimick the look of a real event 
    margin: 10px 0;
    padding: 2px 4px;
    background: #3366CC;
    color: #fff;
    font-size: .85em;
    cursor: pointer;
    }
    
  #external-events p {
    margin: 1.5em 0;
    font-size: 11px;
    color: #666;
    }
    
  #external-events p input {
    margin: 0;
    vertical-align: middle;
    }

  #calendar {
    float: right;
    width:100%;
    }

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
            <i class="fa fa-eye"></i>
            <li class="active">{{$page_title}}</li>
    </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="fa fa-calendar"></i>{{$page_title}}</h1>

    </div>
</div>
<!-- END Page Title -->
   
   <link rel="stylesheet" href="{{url('/')}}/assets/fullcalendar/fullcalendar/fullcalendar.css" />

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-title">
                
                <div class="box-tool">

                </div>
                 
            </div>
     
            <!-- Modal -->
<div class="modal fade edit-event-main" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display:none">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Update title</h4>
            </div>

            <div class="modal-body">

                            <div class="form-group">
                                <label class="control-label date-label">{{translation('start_date')}} :</label>
                                
                                <span id="model_date"></span>    
                                    
                          
                                <div class="clearfix"></div>
                            </div>
                            <div class="form-group">
                              <label class="control-label">{{translation('end_date')}}</label>
                              <div class="controls">
                                  <input class="form-control datepikr" id="end_date" name="end_date" readonly placeholder="{{translation('enter')}} {{translation('end_date')}}" type="text" style="cursor: pointer;" />
                                  <span id="err_end_date" style="color: red;font-size: 10px"></span>
                                  <span class='help-block'>{{ $errors->first('end_date') }}</span>    
                              </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{translation('type')}}</label>
                                <div class="controls">
                                    <select class="form-control" name='type' id="type">
                                      <option value="" selected> {{translation('select_type')}} </option>
                                      <option value="EVENT"> {{translation('event')}} </option>
                                      <option value="HOLIDAY"> {{translation('holiday')}} </option>
                                </select>
                                <span class='help-block'>{{ $errors->first('class')}}</span>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            
                            <div class="form-group">
                              <label class="control-label">{{translation('user_type')}}</label>
                              <div class="row">
                              <div class="col-sm-4 col-lg-4 col-md-4">
                                <div class="controls">
                                      <div class="check-box">
                                        <input type="checkbox" class="filled-in case chk" name="user_type[]" id="employee" value="employee" />
                                        <label for="employee">{{translation('employee')}}</label>
                                      </div>                                          
                                      
                                </div>
                              </div>
                              <div class="col-sm-4 col-lg-4 col-md-4">
                              <div class="controls">
                                      <div class="check-box">
                                        <input type="checkbox" class="filled-in case chk" name="user_type[]" id="professor" value="professor" />
                                        <label for="professor">{{translation('professor')}}</label>
                                      </div>                                          
                                      
                                </div>
                              </div>
                              </div>
                              <div class="row">
                              <div class="col-sm-4 col-lg-4 col-md-4">
                              <div class="controls">
                                      <div class="check-box">
                                        <input type="checkbox" class="filled-in case chk" name="user_type[]" id="student" value="student" />
                                        <label for="student">{{translation('student')}}</label>
                                      </div>                                          
                                      
                                </div>
                              </div>
                           
                              <div class="col-sm-4 col-lg-4 col-md-4">
                              <div class="controls">
                                      <div class="check-box">
                                        <input type="checkbox" class="filled-in case chk" name="user_type[]" id="parent" value="parent" />
                                        <label for="parent">{{translation('parent')}}</label>
                                      </div>                                          
                                      
                                </div>
                              </div>
                              </div>
                              <span class='help-block err-users'></span>
                            </div>
                            
                           
                            <div class="clearfix"></div>        
                             <div class="form-group">
                                        
                                        <div class="controls">  
                                                <div class="radio-btns">  
                                                    <div class="radio-btn">
                                                        <input type="radio" id="f-option" name="individual" value="red" checked="true">
                                                        <label for="f-option">{{translation('individual')}}</label>
                                                        <div class="check"></div>
                                                    </div>
                                                    <div class="radio-btn">
                                                        <input type="radio" id="s-option" name="individual" value="green">
                                                        <label for="s-option">{{translation('all')}}</label>
                                                        <div class="check"><div class="inside"></div></div>
                                                    </div>
                                                </div>                                                
                                                
                                                   
                                        </div>
                                        <div class="clearfix"></div>
                              </div>             
                              <div class="red box-new">
                                  <div class="form-group">
                                        <label class="control-label">{{translation('level')}}</label>
                                        <div class="controls">
                                            <select name="level" id="level" class="form-control level">
                                            <option value='' selected id='select-level'>{{translation('select_level')}}</option>
                                                @if(isset($arr_levels) && count($arr_levels)>0)
                                                    @foreach($arr_levels as $value)
                                                        <option value="{{$value['level_id']}}" >{{$value['level_details']['level_name']}}</option>
                                                    @endforeach
                                                @endif    
                                            </select>
                                            <span class='help-block'>{{ $errors->first('level') }}</span>    
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                            <div class="form-group">
                                      <label class="control-label">{{translation('class')}}</label>
                                      <div class="controls">
                                          <select name="class" id="level_class" class="form-control level-class" data-rule-required='true'>
                                          
                                          </select>
                                          
                                          <span class='help-block'>{{ $errors->first('class')}}</span>

                                      </div>
                                      <div class="clearfix"></div>
                                    </div>
                                                </div>
                            <div class="green box-new">
                                                    
                                                </div>  
                            <div class="form-group">
                                    <label class="control-label">{{translation('title')}}</label>
                                      <div class="controls">
                                      <input type="text" class="form-control" name="title" id="title"/>
                                      <span class='help-block'>{{ $errors->first('class')}}</span>
                                      </div>
                                      
                                   <div class="clearfix"></div>
                                  </div>

                              <div class="form-group">
                                <label class="control-label">{{translation('description')}}</label>
                                  <div class="controls">
                                  <textarea class="form-control" name="description" id="description"></textarea>
                                  <span class='help-block'>{{ $errors->first('description')}}</span>
                                  </div>
                                  
                               <div class="clearfix"></div>
                               
                              </div>
                              <input type="hidden" id="start">
                              <input type="hidden" id="end">
                              <input type="hidden" id="allDay">
                              <input type="hidden" id="event_id">

            </div>

            <div class="modal-footer">
              <div class="row">
                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                            <button type="submit" class="btn btn-primary" id='add' style="display:inline-block">{{translation('add')}}</button>
                            <button type="submit" class="btn btn-primary" id='update' style="display:none">{{translation('update')}}</button>
                            @if(array_key_exists('calendar.delete', $arr_current_user_access))
                            <button type="submit" class="btn btn-primary" id='btn_delete' style="display:none">{{translation('delete')}}</button>
                            @endif
                    </div>
                </div>
              </div>      
            </div>
        </div>
    </div>
</div>  
                  <div class="row">
                   <div class="dropup-down-uls" style="float:right">
                      <a href="javascript:void(0)" class="export-lists"><i class="fa fa-upload"></i> {{ translation('export')}} </a>
                      <div class="export-content-links">
                          <div class="li-list-a">
                              <a href="javascript:void(0)" onclick="exportForm('pdf');">PDF</a>
                          </div>
                          <div class="li-list-a">
                              <a href="javascript:void(0)" onclick="exportForm('csv');">CSV</a>
                          </div>
                           
                      </div>
                  </div>
                  </div>
                  <input type="hidden" name="search" id="search" value="" />
            <input type="hidden" name="file_format" id="file_format" value="" />
                   <div class="row">

                       <div class="col-sm-12 col-md-4 col-lg-2">
                        
                           <div class="event-list">
                              <h5>{{translation('event')}}</h5>
                              <div class="event-wrapper">
                               <ul class="content-d">
                                   @foreach($arr_events as $value)
                                      <li><a href="javascript:void(0)" onclick="goToMonth('{{$value['event_date']}}')">{{$value['event_title']}}</a></li>
                                   @endforeach
                               </ul>
                               </div>
                           </div>
                           
                           <div class="event-list ">
                              <h5>{{translation('holiday')}}</h5>
                              <div class="event-wrapper">
                               <ul class="content-d">
                                   @foreach($arr_holidays as $value)
                                      <li><a href="javascript:void(0)" onclick="goToMonth('{{$value['event_date']}}')">{{$value['event_title']}}</a></li>
                                   @endforeach
                               </ul>
                               </div>
                           </div>
                       </div>
                       <div class="col-sm-12 col-md-8 col-lg-10">
                            <div class="cal-wrapper">
                                <div class="box-content">
                                  @include('schooladmin.layout._operation_status') 
                                    <div id='calendar'></div>
                                    <div style='clear:both'></div>                               
                                </div>
                            </div>
                       </div>
                   </div>
        </div>
    </div>
</div>
  
  <script src="{{url('/')}}/assets/fullcalendar/lib/jquery-ui.custom.min.js"></script>
  <script src="{{url('/')}}/assets/fullcalendar/fullcalendar/fullcalendar.min.js"></script>       

<script>

$( "#end_date" ).datepicker({
        todayHighlight: true,
        autoclose: true,
        format:'yyyy-mm-dd',
        endDate: "{{\Session::get('end_date')}}",
        startDate: "{{\Session::get('start_date')}}"
    });

$(".js-example-basic-multiple").select2();
  $(document).ready(function() {
   
    if($('#f-option:checked').val())
    {
        var targetBox = $(".red");
        $("#level_class").attr('data-rule-required','true');
        $('#level').attr('data-rule-required','true');
        $(targetBox).show();
    }
    else
    {
        var targetBox = $(".green");
        $("#level_class").attr('data-rule-required','false');
        $('#level').attr('data-rule-required','false');
        $(targetBox).show();
    }

    
  
    /* initialize the external events
    -----------------------------------------------------------------*/
  
    $('#external-events div.external-event').each(function() {
    
      // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
      // it doesn't need to have a start or end
      var eventObject = {
        title: $.trim($(this).text()) // use the element's text as the event title
      };
      
      // store the Event Object in the DOM element so we can get to it later
      $(this).data('eventObject', eventObject);
      
      // make the event draggable using jQuery UI
      $(this).draggable({
        zIndex: 999,
        revert: true,      // will cause the event to go back to its
        revertDuration: 0  //  original position after the drag
      });
      
    });
    
  });

</script>
<script>

  $(document).ready(function() {
    var calendar;
     $(".level-class").append('<option value="" >{{translation('select_class')}}</option>');
  var events = '';
    $.ajax({
              url  :"{{ $module_url_path }}/get_events",
              type :'get',
              success:function(data){
                if(data)
                {
                  events = JSON.parse(data);
                  
                  var date = new Date();
                  var d = date.getDate();
                  var m = date.getMonth();
                  var y = date.getFullYear();
                  
                  calendar = $('#calendar').fullCalendar({
                   
                    header: {
                      left: 'prev,today,next',
                      center: 'title',
                      right: 'month,agendaWeek,agendaDay'
                    },
                    editable:false,
                    eventLimit:true,
                    eventClick: function(event, element) {
                        @if(array_key_exists('calendar.update', $arr_current_user_access))
                          if(event.type!="EXAM")
                          {
                            $("#add").css("display","none");
                             $("#update").css("display","inline-block");
                             $("#btn_delete").css("display","inline-block");
                            
                            $(".chk").each(function() {
                              $(this).removeAttr("checked");
                              $(this).prop( "checked", false );
                            });
                            $(".modal-title").html("Update Event");
                            var date = new Date(event.start);
                            var month = date.getMonth()+1;
                            var newdate = date.getFullYear()+'-'+month+'-'+date.getDate();

                            
                            $('#type').val('');
                            $('#title').val('');
                            $('#level').val('');
                            $('.level-class').val('');
                            $('#description').val('');

                            $('#title').next().html('');
                            $('#type').next().html('');
                            $('#level').next().html('');
                            $('#level_class').next().html('');
                            $('#description').next().html('');
                            $("#type option[value="+event.type+"]").attr("selected","selected");
                            
                           var users = event.user_type;
                           for(var i=0 ; i<users.length ;i++)
                           {
                              $("#"+users[i]).attr("checked","checked");
                              $("#"+users[i]).prop( "checked", true );
                           }
                           $(".red").hide();
                           $(".green").hide();

                           var targetBox='';
                           var hideBox='';

                           if(event.is_individual=='1')
                           {  
                              $( "#s-option" ).prop( "checked", false );
                              $( "#f-option" ).prop( "checked", true );
                              $("#level").val(event.level);
                              $('#level option[value='+event.level+']').attr("selected","selected");
                              var level = event.level;
                              var class_id = event.class;
                              if(level!='')
                              {
                                $(".level-class").empty().not('select-class');
                                 $.ajax({
                                    url  :"{{ $module_url_path }}/get_classes",
                                    type :'get',
                                    data :{'_token':'<?php echo csrf_token();?>','level':level,'class_id':class_id},
                                    success:function(data){
                                      $(".level-class").append(data);                                        
                                    }
                                });
                              }
                              targetBox = $(".red");
                              hideBox = $(".green");
                           }
                           else
                           {
                              $( "#f-option" ).prop( "checked", false );
                              $( "#s-option" ).prop( "checked", true );

                              targetBox = $(".green");
                              hideBox = $(".red");
                           }
                           $(targetBox).show();
                           $(hideBox).hide();
                        
                           $("#event_id").val(event.id);
                           $("#title").val(event.title);
                           $('#update_title').val(event.title);
                           $('#start').val(event.start);

                           var end ='';
                           if(event.end != null){

                             endDate = new Date(event.end);
                             var month = endDate.getMonth()+1;
                             var  endDate = endDate.getFullYear()+'-'+month+'-'+endDate.getDate(); 

                             $("#end_date").datepicker('setStartDate','2018-07-03');

                             if(endDate==newdate){
                                end = new Date(event.start);
                             }
                             else{
                                end = new Date(event.end);
                             }
                           }
                           else{
                              end = new Date(event.start);
                           }
                           end = end.getFullYear()+'-'+(end.getMonth()+1)+'-'+end.getDate();
                           
                           if(end!=newdate){
                            end = new Date(event.end);
                            end = end.getFullYear()+'-'+(end.getMonth()+1)+'-'+end.getDate(); 
                           }
                           $('#end_date').val(end);
                           $('#end').val(event.end);
                           $('#allDay').val(event.allDay);
                           $('#description').val(event.event_description);


                           if(event.end != null){

                              endDate = new Date(event.end);
                             var month = endDate.getMonth()+1;
                             var  endDate = endDate.getFullYear()+'-'+month+'-'+endDate.getDate(); 

                              $(".date-label").text("{{translation('time').' :'}}");
                               var start_time = new Date(event.start);

                                start_time = start_time.getHours()+':'+start_time.getMinutes(); 
                                var start_time = start_time.split(':');
                                
                                hour = start_time[0];
                                min = start_time[1];
                                

                                min = (min+'').length == 1 ? '0'+min : min;
                                hour = (hour+'').length == 1 ? '0'+hour : hour;


                                start_time = hour+":"+min;
                               
                               var end_time = '';
                                
                                end_time = new Date(event.end);
                                end_time = end_time.getHours()+':'+end_time.getMinutes(); 
                               var end_time = end_time.split(':');
                                var hour = end_time[0];
                                var min = end_time[1];
                                
                                min = (min+'').length == 1 ? '0'+min : min;
                                hour = (hour+'').length == 1 ? '0'+hour : hour;

                                end_time = hour+":"+min;

                                var model_date = newdate;
                                model_date += ' ';
                                if(start_time!='00:00')
                                  model_date += start_time;
                                model_date += ' - ';
                                model_date += endDate;
                                model_date += ' ';
                                if(end_time!='00:00')
                                  model_date += end_time;

                                 $("#model_date").text(model_date);
                           }
                           else{
                              $(".date-label").text("{{translation('start_date').' :'}}");
                              $("#model_date").text(newdate);
                           }
                            $('#myModal').modal({
                                show: 'true'
                            });
                          }  
                          @endif
                    },
                    axisFormat: 'HH:mm',
                    timeFormat: {
                        agenda: 'H:mm{ - h:mm}'
                    },
                    buttonText :
                      {
                       
                        prev: '{{translation('prev')}}',
                        next: '{{translation('next')}}',
                        today: '{{translation('today')}}',
                        month: '{{translation('month')}}',
                        week: '{{translation('week')}}',
                        day: '{{translation('day')}}',

                        
                      },

                    @if(\Session::get('locale')=='fr')
                      monthNames:"janvier_février_mars_avril_mai_juin_juillet_août_septembre_octobre_novembre_décembre".split("_"),
                      monthNamesShort:"janv_févr_mars_avr_mai_juin_juil_août_sept_oct_nov_déc".split("_"),
                      dayNames : "dimanche_lundi_mardi_mercredi_jeudi_vendredi_samedi".split("_"),
                      dayNamesShort : "dim_lun_mar_mer_jeu_ven_sam".split("_"),
                    @endif     
                                          
                    selectable: true,
                    selectHelper: true,
                    select: function(start, end, allDay) 

                    {
                      @if(array_key_exists('calendar.create', $arr_current_user_access))
                      $(".chk").each(function() {
                        console.log("here");
                              $(this).removeAttr("checked");
                              $(this).prop( "checked", false );
                            });

                      $("input[type='radio']").each(function() {
                          $(this).removeAttr("checked");
                          $(this).prop( "checked", false );
                      });
                      $("#f-option").attr("checked",true);
                      $("#f-option").prop("checked",true);
                      $(".red").show();  
                     /* if(start.isBefore(moment())) {
                        $('#calendar').fullCalendar('unselect');
                        return false;
                      } */

                      var date = new Date(start);
                      var month = date.getMonth()+1;
                      var newdate = date.getFullYear()+'-'+month+'-'+date.getDate();
                      $("#end_date").datepicker('setStartDate',newdate);

                      if(allDay){
                        $(".date-label").html("{{translation('start_date').' :'}}");
                              
                              $("#model_date").text(newdate);
                      }
                      else{

                         $(".date-label").text("{{translation('time').' :'}}");
                         var start_time = new Date(start);

                          start_time = start_time.getHours()+':'+start_time.getMinutes(); 
                          var start_time = start_time.split(':');
                          
                          hour = start_time[0];
                          min = start_time[1];
                          var part = hour > 12 ? 'pm' : 'am';

                          min = (min+'').length == 1 ? '0'+min : min;
                          hour = hour > 12 ? hour - 12 : hour;
                          hour = (hour+'').length == 1 ? '0'+hour : hour;


                         start_time = hour+":"+min+" "+part;
                         
                         var end_time = new Date(end);
                         
                         end_time = end_time.getHours()+':'+end_time.getMinutes(); 
                         var end_time = end_time.split(':');
                          var hour = end_time[0];
                          var min = end_time[1];
                          
                          var part = hour > 12 ? 'pm' : 'am';
                          
                          min = (min+'').length == 1 ? '0'+min : min;
                          hour = hour > 12 ? hour - 12 : hour;
                          hour = (hour+'').length == 1 ? '0'+hour : hour;

                          end_time = hour+":"+min+" "+part;

                          $("#model_date").text(newdate+' ('+start_time+'-'+end_time+')');
                      }
                      $('#type').val('');
                      $('#title').val('');
                      $('#level').val('');
                      $('#end_date').val('');
                      $('.level-class').val('');
                      $('#description').val('');
                      $('#title').next().html('');
                      $('#type').next().html('');
                      $('#level').next().html('');
                      $('#level_class').next().html('');
                      $('#description').next().html('');
                      $('.err-users').html('');
                      var date = new Date(start);
                      var month = date.getMonth()+1;
                      if(month<10)
                      {
                        month = '0'+month;
                      }
                      var day = date.getDate();
                      if(day<10)
                      {
                        day = '0'+day;
                      }
                      var newdate = date.getFullYear()+'-'+month+'-'+day;
                      $("#model_date").text(newdate);
                      $("#add").css("display","inline-block");
                      $("#update").css("display","none");
                      $("#btn_delete").css("display","none");
                      $(".modal-title").html("{{translation('add_event')}}");
                      $("#add").html('{{translation('add')}}');
                      $('#myModal').modal({
                              show: 'true'
                          });
                      $("#start").val(start);

                      if(!allDay){
                          $("#end_date").attr('disabled','true');
                          $("#end_date").val(newdate);
                          $("#end").val(end);
                       }
                       else{
                        $("#end_date").removeAttr('disabled','true');
                       }
                      
                      $("#allDay").val(allDay);
                      @endif
                      },
                      
                    eventDrop: function(event, delta, revertFunc) {

                          $.ajax({
                            url  :"{{ $module_url_path }}/update",
                            type :'post',
                            data :{'start':event.start ,'end':event.end ,'title':event.title,'id':event.id,'_token':'<?php echo csrf_token();?>','all_day':event.allDay,'level':event.level,'event_type':event.type,'user_type': event.user_type,'individual':event.is_individual,'description':event.event_description,class_id:event.level_class},
                            success:function(data){
                              
                            }
                          });
                    },
                    eventResize: function( event, jsEvent, ui, view ) { 
                     
                      $.ajax({
                            url  :"{{ $module_url_path }}/update",
                            type :'post',
                            data :{'start':event.start ,'end':event.end,'title':event.title,'id':event.id,'_token':'<?php echo csrf_token();?>','all_day':event.allDay,'level':event.level,'event_type':event.type,'user_type': event.user_type,'individual':event.is_individual,'description':event.event_description,class_id:event.level_class},
                            success:function(data){
                             
                            }
                          });
                    },
                    events:events,
                  });

                  $('#myModal .close').click(function(){
                 
                    calendar.fullCalendar('unselect');
                  });
                  $("#btn_delete").click(function(){
                    
                    var id = $("#event_id").val();
                          $.ajax({
                          url  :"{{ $module_url_path }}/delete",
                          type :'post',
                          data :{'id':id,'_token':'<?php echo csrf_token();?>'},
                          success:function(data){
                            $('.calender-modal').css('display','none');  
                            calendar.fullCalendar('removeEvents',id);
                            $('#myModal').modal("hide");
                          }
                        });
                    location.reload();      
                   
                     });
                  $("#add").click(function(){
                                                  
                        var title = $('#title').val();
                        var type = $('#type').val();
                        var level = $('#level').val();
                        var level_class = $('#level_class').val();
                        var individual = $('input[name="individual"]:checked').val();
                        var start = $("#start").val();
                        var end='';
                        var allDay = $("#allDay").val();
                        if(allDay=="true"){
                          end = $("#end_date").val();  
                        }
                        else{
                          end = $("#end").val();
                        }
                        console.log(allDay+' '+end);
                        var chkArray = [];
                        $(".chk:checked").each(function() {
                          chkArray.push($(this).val());
                        });
                        
                        var description = $('#description').val();
                        var flag=0;
                        
                        if(title=='')
                        {
                          $('#title').next().html('{{translation('this_field_is_required')}}');
                          flag=1;
                        }
                        else
                        {
                          $('#title').next().html('');
                        }
                        if(type=='')
                        {
                          $('#type').next().html('{{translation('this_field_is_required')}}');
                          flag=1;
                        }
                        else
                        {
                          $('#type').next().html('');
                        }
                        if(individual=='red' && individual!='green')
                        {
                          if(level=='')
                          {
                            $('#level').next().html('{{translation('this_field_is_required')}}');
                            flag=1;
                          }
                          else
                          {
                            $('#level').next().html('');
                          }
                          if(level_class=='')
                          {
                            $('#level_class').next().html('{{translation('this_field_is_required')}}');
                            flag=1;
                          }  
                          else
                          {
                            $('#level_class').next().html('');                           
                          }
                        }
                        else
                        {
                          $('#level').next().html('');
                          $('#level_class').next().html('');
                        }
                        if(chkArray.length==0)
                        {
                            $(".err-users").html("{{translation('this_field_is_required')}}");
                            $flag=1;
                        }
                        else
                        {
                            $(".err-users").html("");

                        }
                        
                        if(flag==1)
                        {
                          return false;
                        }
                        if(flag==0) 
                        {

                            $.ajax({
                              url  :"{{ $module_url_path }}/store",
                              type :'post',
                              data :{'level':level,'event_type':type,'user_type': chkArray, 'all_day':allDay,'start':start ,'end':end,'title':title,'_token':'<?php echo csrf_token();?>','class':level_class,'individual':individual,'description':description},
                                success:function(data){
                                location.reload();

                              }
                            });
                        }
                        
                      });
                      $("#update").click(function(){

                                var id = $('#event_id').val();
                                var title = $('#title').val();            
                                var type = $('#type').val();
                                var level = $('#level').val();
                                var level_class = $('#level_class').val();
                                var individual = $('input[name="individual"]:checked').val();
                                var description = $('#description').val();
                                var allDay = $('#allDay').val();
                                var start = $('#start').val();
                                var end='';
                                
                                if(allDay!='false'){
                                  
                                  end = $("#end_date").val();  
                                }
                                else{
                                  
                                  end = $("#end").val();
                                }
                                
                                var flag=0;
                                var chkArray = [];
                                $(".chk:checked").each(function() {
                                  chkArray.push($(this).val());
                                });
                                if(title=='')
                                {
                                  $('#title').next().html('{{translation('this_field_is_required')}}');
                                  flag=1;
                                }
                                else
                                {
                                  $('#title').next().html('');
                                }
                                if(type=='')
                                {
                                  $('#type').next().html('{{translation('this_field_is_required')}}');
                                  flag=1;
                                }
                                else
                                {
                                  $('#type').next().html('');
                                }
                                if(individual=='red' && individual!='green')
                                {
                                  if(level=='')
                                  {
                                    $('#level').next().html('{{translation('this_field_is_required')}}');
                                    flag=1;
                                  }
                                  else
                                  {
                                    $('#level').next().html('');
                                  }
                                  if(level_class=='')
                                  {
                                    $('#level_class').next().html('{{translation('this_field_is_required')}}');
                                    flag=1;
                                  }  
                                  else
                                  {
                                    $('#level_class').next().html('');                           
                                  }
                                }
                                else
                                {
                                  $('#level').next().html('');
                                  $('#level_class').next().html('');
                                }
                                if(chkArray.length==0)
                                {
                                    $(".err-users").html("{{translation('this_field_is_required')}}");
                                    $flag=1;
                                }
                                else
                                {
                                    $(".err-users").html("");

                                }
                                if(flag==1)
                                {
                                  return false;
                                }
                                if(flag==0) 
                                {
                                    $.ajax({
                                        url  :"{{ $module_url_path }}/update",
                                        type :'post',
                                        data :{'id':id,'level':level,'event_type':type,'user_type': chkArray, 'all_day':allDay,'start':start ,'end':end,'title':title,'_token':'<?php echo csrf_token();?>','class':level_class,'individual':individual,'description':description},
                                        success:function(data){
                                        
                                          location.reload();
                                        }
                                    
                                     });
                                    
                                }
                                $('#myModal').modal('hide');
                          });
                    } 
                }
            });
  });

$('input[type="radio"]').click(function(){
    var inputValue = $(this).attr("value");

    var targetBox = $("." + inputValue);
    $(".box-new").not(targetBox).hide();
    $(targetBox).show();
});

$(".level").on('change',function(){
  var level = $('.level').val();
  if(level!='')
  {
      $(".level-class").empty().not('select-class');
      $.ajax({
        url  :"{{ $module_url_path }}/get_classes",
        type :'get',
        data :{'_token':'<?php echo csrf_token();?>','level':level},
        success:function(data){
          $(".level-class").append(data);
            
        }
      });  
  }
});

function goToMonth(date){

  var newdate = new Date(date);
  
  $('#calendar').fullCalendar('gotoDate',newdate.getFullYear(),newdate.getMonth(),newdate.getDate());
}
  
</script>
 <!-- To Export The Data -->
<script type="text/javascript">
  function exportForm(file_format)
  {
    window.location.href = '{{ $module_url_path }}/export?file_format='+file_format+'&export=true';
  }
  $(document).on("change","[type='search']",function(){
      var search_hidden = $(this).val();
      document.getElementById('search').value = search_hidden;
   });

  $('#add').on('click',function(){
      var start_date = new Date($('#model_date').text());
      var end_date   = new Date($('#end_date').val());
      if(start_date > end_date)
      {
        $('#err_end_date').html('{{translation('end_date_must_be_greater_than_start_date')}}');
        return false;
      }
      else
      {
        $('#err_end_date').text('');
        return true;
      }
  });
</script>
{{-- <script type="text/javascript" src="{{ url('/') }}/js/moment.js"></script> --}}
<!-- To Export The Data -->
@endsection