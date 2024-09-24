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
            <i class="fa fa-eye"></i>
            <li class="active">{{$page_title}}</li>
    </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1>{{$page_title}}</h1>

    </div>
</div>
<!-- END Page Title -->
   
   <link rel="stylesheet" href="{{url('/')}}/assets/fullcalendar/fullcalendar/fullcalendar.css" />

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-title">
                <h3><i class="fa fa-calendar"></i> {{translation('calendar')}}</h3>
                <div class="box-tool">
                </div>
                 
            </div>   


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
                  
                  var calendar = $('#calendar').fullCalendar({
                   
                    header: {
                      left: 'prev,today,next',
                      center: 'title',
                      right: 'month,agendaWeek,agendaDay'
                    },
                    axisFormat: 'HH:mm',
                    timeFormat: {
                        agenda: 'H:mm{ - h:mm}'
                    },
                    eventLimit:true,
                    editable:false,
                    selectable:false,
                    buttonText :
                      {
                       
                        prev: '{{translation('prev')}}',
                        next: '{{translation('next')}}',
                        today: '{{translation('today')}}',
                        month: '{{translation('month')}}',
                        week: '{{translation('week')}}',
                        day: '{{translation('day')}}' 
                      },

                    @if(\Session::get('locale')=='fr')
                      monthNames:"janvier_février_mars_avril_mai_juin_juillet_août_septembre_octobre_novembre_décembre".split("_"),
                      monthNamesShort:"janv_févr_mars_avr_mai_juin_juil_août_sept_oct_nov_déc".split("_"),
                      dayNames : "dimanche_lundi_mardi_mercredi_jeudi_vendredi_samedi".split("_"),
                      dayNamesShort : "dim_lun_mar_mer_jeu_ven_sam".split("_"),
                    @endif     
                    
                    events:events
                    
                  });
                }
                
                  
              }
            });
function goToMonth(date){

  var newdate = new Date(date);
  console.log(date); 
  $('#calendar').fullCalendar('gotoDate',newdate.getFullYear(),newdate.getMonth(),newdate.getDate());
}
</script>
 
@endsection