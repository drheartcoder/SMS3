@extends('professor.layout.master')                
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
            <a href="{{url('/')}}/professor/dashboard">{{translation('dashboard')}}</a>
        </li>
        <span class="divider">
          <i class="fa fa-angle-right"></i>
        </span>
        <li>
            <a href="{{$module_url_path}}/professor">{{$module_title}}</a>
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
        <h1><i class="fa fa-cc-diners-club"></i> {{str_singular($module_title)}}</h1>

    </div>
</div>
<!-- END Page Title -->
   
   <link rel="stylesheet" href="{{url('/')}}/assets/fullcalendar/fullcalendar/fullcalendar.css" />

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-title">
                <h3><i class="fa fa-calendar"></i> {{isset($page_title)?$page_title:''}}</h3>
                <div class="box-tool">
                </div>
                 
            </div>  
            <div class="row" style="background-color: ">
                        
                       <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="cal-wrapper">
                                <div class="box-content">
                                    <div id='calendar'></div>
                                    <div style='clear:both'></div>                               
                                </div>
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

    var events = '';
    $.ajax({ 
              url  :"{{$module_url_path}}/get_events",
              type :'post',
              data :{'_token':'<?php echo csrf_token();?>'},
              success:function(data){
               if(data)
                {
                  events = JSON.parse(data);
                  
                  var date = new Date();
                  var d = date.getDate();
                  var m = date.getMonth();
                  var y = date.getFullYear();

                  var calendar = $('#calendar').fullCalendar({
                    eventLimit:true,
                    editable:false,
                    selectable:false,
                    axisFormat: 'HH:mm',
                    timeFormat: {
                        agenda: 'H:mm{ - h:mm}'
                    },
                    header: {
                      left: 'prev,today,next',
                      center: 'title',
                      right: 'month,agendaWeek,agendaDay'
                    },
                    buttonText :
                    {
                     
                      prev: '{{translation('prev')}}',
                      next: '{{translation('next')}}',
                      today: '{{translation('today')}}',
                      month: '{{translation('month')}}',
                      week: '{{translation('week')}}',
                      day: '{{translation('day')}}' 
                    },

                    eventRender: function (event, element, view) {
                        // event.start is already a moment.js object
                        // we can apply .format()
                        

                        $('.fc-day[data-date=' + event.event_date + ']').css('background-color',event.color);
                        $('.fc-day[data-date=' + event.event_date + ']').css('font-weight',event.bold);
                    }, 
                    @if(\Session::get('locale')=='fr')
                      monthNames:['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet',
                                    'août', 'septembre', 'octobre', 'novembre', 'décembre'],
                    @endif     
                    
                    events:events
                    
                  });
                }
              }
            });
  
</script>
 
@endsection