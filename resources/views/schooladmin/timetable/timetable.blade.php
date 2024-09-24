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
          {{$module_title}}
        </li>

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


    <!-- BEGIN Tiles -->
    <div class="row">
      <div class="col-md-12">
        <div class="box  box-navy_blue">
          <div class="box-title">
            <h3><i class="{{$module_icon}}"></i>{{translation('periods_define')}}</h3>
            <div class="box-tool">
            </div>
          </div>
          @if($session_school_start_time!='' && $session_school_end_time!='')
          <div class="box-title">
            <h2 id="display_school_timimgs">{{translation('school_timing')}}  {{ $session_school_start_time }} - {{ $session_school_end_time }}</h2>
            <div class="box-tool">
            </div>
          </div>
          @endif

          <div class="box-content gray-clrs">

            <!-- tables inside this DIV could have draggable content -->
            @include('schooladmin.layout._operation_status')  

            <div id="timetable_msg_div"></div>

            <div id="timetable_err_msg_div"></div>
            <form name="frm_period_mapping" id="validation-form1"   action="{{ $module_url_path }}/update_period_mapping" method="POST" onsubmit="return addLoader()">
              {{ csrf_field() }}
              <div class="row">
                <div class="form-group-nms">
                  <div class="col-sm-3 col-lg-2"></div>
                  <div class="col-sm-12 col-lg-8">{{$module_title}}</div>
                  <div class="clearfix"></div>
                </div>


                <div class="col-sm-4 col-md-4 col-lg-6">
                  <div class="row">                                        
                    <div class="form-group">
                      <label class="col-sm-3 col-lg-4 control-label">{{translation('select')}} {{translation('level')}} <i class="red">*</i></label>
                      <div class="col-sm-9 col-lg-8 controls">
                       <select  class="form-control" name="level_id"  id="level_id" data-rule-required="true">
                        <option value="">{{ translation('select') }} {{translation('level')}}</option>    
                        @if(!empty($arr_level) && count($arr_level) > 0)
                        @foreach($arr_level as $val)
                        <option value="{{ $val['level_id'] }}" 

                        @if(isset($session_level_id) && $session_level_id==$val['level_id'])
                        {{"selected"}}
                        @endif

                        >
                        {{ ucwords($val['level_details']['level_name']) }}
                      </option>    
                      @endforeach
                      @endif
                    </select>
                    <span class='help-block'>{{ $errors->first('professor_id') }}</span>    
                  </div>
                  <div class="clearfix"></div>
                </div>
              </div>
            </div>
            <div class="col-sm-4 col-md-4 col-lg-6">
              <div class="row">
                <div class="form-group">
                  <label class="col-sm-3 col-lg-4 control-label">{{translation('select')}} {{translation('class')}}<i class="red">*</i></label>
                  <div class="col-sm-9 col-lg-8 controls">

                    <select  class="form-control" name="class_id" id="class_id"  data-rule-required="true"  @if(isset($session_class_id) && $session_class_id>0) @else disabled="" @endif onchange="javascript:return get_period_details(this);">
                      <option value="">{{ translation('select') }} {{translation('class')}}</option>   

                      @if(!empty($arr_classes) && count($arr_classes) > 0)
                      @foreach($arr_classes as $val)
                      <option class="section_class"  value="{{ $val['class_id'] }}"   @if(isset($session_class_id) && $session_class_id==$val['class_id'])
                      {{"selected"}}
                      @endif >
                      {{ ucwords($val['class_details']['class_name']) }}
                    </option>    
                    @endforeach
                    @endif
                  </select>
                </div>
                <div class="clearfix"></div>
              </div>
            </div>
          </div>

          <div class="col-sm-4 col-md-4 col-lg-6">
            <div class="row">
              <div class="form-group">
                <label class="col-sm-3 col-lg-4 control-label">{{translation('select')}} {{translation('periods')}}<i class="red">*</i></label>
                <div class="col-sm-9 col-lg-8 controls">
                 <select class="form-control" required="" name="num_of_periods" id="num_of_periods" data-rule-required="true"  >
                  <option value="">{{translation('no_of_periods')}}</option>
                  @for($i = 1; $i<= 20; $i++)
                  <option @if(isset($session_num_of_periods) && $session_num_of_periods==$i) {{'selected'}} @endif value="{{$i}}">{{ $i }}</option>
                  @endfor
                </select>
              </div>
              <div class="clearfix"></div>
            </div>
          </div>
        </div>

        <?php
        $weekly_off_123 = isset($session_weekly_off)&&!empty($session_weekly_off)?$session_weekly_off:$arr_holiday; 


        ?>
        <div class="col-sm-4 col-md-4 col-lg-6">
          <div class="row">
            <div class="form-group">
              <label class="col-sm-3 col-lg-4 control-label">{{translation('select')}} {{translation('weekly_holiday')}} <i class="red">*</i></label>
              <div class="col-sm-9 col-lg-8 controls ">
               <select class="js-example-basic-multiple form-control" multiple="multiple" name="weekly_off[]" id="weekly_off" data-rule-required='true'>

                 @foreach($weekly_days as $key => $value)
                 <option value="{{$key}}"  @if(in_array($key,$weekly_off_123)) selected="selected" @endif >{{$value}}  </option>
                 @endforeach
               </select>
             </div>
             <div class="clearfix"></div>
           </div>
         </div>
       </div>


       <div class="col-sm-4 col-md-4 col-lg-6">
        <div class="row">
          <div class="form-group">
            <label class="col-sm-3 col-lg-4 control-label"> {{translation('school_start_time')}}<i class="red">*</i></label>
            <div class="col-sm-9 col-lg-8 controls">

              <input class="form-control datepikr timepicker-default" name="school_start_time"   id="school_start_time"  placeholder="{{translation('enter')}} {{translation('school_start_time')}}" type="text" data-rule-required="true" @if(isset($session_school_start_time) && $session_school_start_time!='' && !empty($session_school_start_time)) value="{{ $session_school_start_time }}"@endif readonly style="cursor: pointer;" />
              <span for="school_start_time"></span>
            </div>
            <div class="clearfix"></div>
          </div>
        </div>
      </div>

      <div class="col-sm-4 col-md-4 col-lg-6">
        <div class="row">
          <div class="form-group">
            <label class="col-sm-3 col-lg-4 control-label">{{translation('select')}} {{translation('school_end_time')}}  <i class="red">*</i></label>
            <div class="col-sm-9 col-lg-8 controls">
             <input type="text" name="school_end_time"  id="school_end_time"  data-rule-required='true'  placeholder="{{translation('enter')}} {{translation('school_end_time')}}"  class="form-control datepikr timepicker-default" readonly value="@if(isset($session_school_end_time) && $session_school_end_time!='') {{ $session_school_end_time }} @endif" style="cursor: pointer;" >
              <span for="school_end_time"></span>
           </div>
           <div class="clearfix"></div>
         </div>
       </div>
     </div>
    </div>
    <div class="row">
      <div class="form-group">
        <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2"> 
          @if(isset($session_num_of_periods) && !empty($session_num_of_periods))         
          <?php
          $buttonName = translation('delete').' '.translation('timetable');
          $class = "delete_time_table";
          $type  = "button";
          ?>
          @if(array_key_exists('timetable.delete', $arr_current_user_access))
          <button type="{{$type}}"  id="submit_button" class="btn btn-primary {{$class}} common-btn">{{$buttonName}}</button>
          @endif
          @else
          <?php
          $buttonName =  translation('save_set_timetable');
          $class = "";
          $type  = "submit";
          ?>
          <button type="{{$type}}"  id="submit_button" class="btn btn-primary {{$class}} common-btn">{{$buttonName}}</button>
          @endif
          <button type="submit"  id="submit_button" class="btn btn-primary save_set_timetable"   style="display: none"  >{{translation('save_set_timetable')}}</button>
          &nbsp; &nbsp;
          <button type="button" class="btn btn-primary btn-addon m-r" onclick="javascript:return change_class_section();" >{{translation('change_class_section')}}</button>
        </div>
        <div class="clearfix"></div>
      </div>           
    </div>                      
    </form>
    <?php
    $weekly_days =  $weekly_days;
    $no_td = isset($session_num_of_periods) ? $session_num_of_periods : 1;

    ?>
    @if(empty($arr_periods_timing) && !empty($session_class_id))
    <div class="clearfix">&nbsp;</div>
    <form name="frm_period_mapping" id="validation-form2"   action="{{ $module_url_path }}/store_period_timimg" method="POST" onsubmit="return addLoader2()">
      {{ csrf_field() }}
     

     <div class="box-maintimetable box-maintimetable-headers">
          <div class="box-maintimetable-list">{{translation('period_no')}}</div>
          <div class="box-maintimetable-list widthsame-cols">{{translation('is_break')}} ?</div>
          <div class="box-maintimetable-list widthsame-cols">{{translation('start_time')}}</div>
          <div class="box-maintimetable-list widthsame-cols">{{translation('end_time')}}</div>
      </div>   
     
      @if($no_td!='' && $no_td>0)
        @for($j=1; $j<=$session_num_of_periods; $j++)
      <div class="box-maintimetable">
          <div class="box-maintimetable-list">P {{ $j }}</div>
          <div class="box-maintimetable-list widthsame-cols">
              <div class="radio-btns">
                  <div class="radio-btn">
                      <input id="t-option-{{$j}}" name="is_break_{{$j}}" value="1" type="radio">
                      <label for="t-option-{{$j}}">{{translation('yes')}}</label>
                      <div class="check"></div>
                  </div>
                  <div class="radio-btn">
                      <input id="f-option-{{$j}}" name="is_break_{{$j}}" value="0" type="radio"  checked="">
                      <label for="f-option-{{$j}}">{{translation('no')}}</label>
                      <div class="check">
                          <div class="inside"></div>
                      </div>
                  </div><div class="clearfix"></div>
              </div>
          </div>
          
          <div class="box-maintimetable-list widthsame-cols">
            <input class="form-control datepikr timepicker-default checkValidationStart" name="period_start_time[]"   id="period_start_time{{$j}}"  placeholder="{{translation('enter')}} {{translation('period_start_time')}}" type="text"  data-rule-required="true"  data-school-timimg-period-id = "{{ $j }}"      @if(isset($session_school_start_time) && $session_school_start_time!='' && !empty($session_school_start_time)) value="{{ $session_school_start_time }}"@endif   readonly    />
             <span for="period_start_time{{$j}}"></span>
          </div>
          <div class="box-maintimetable-list widthsame-cols">
              <input class="form-control datepikr timepicker-default checkValidationEnd" name="period_end_time[]" id="period_end_time{{$j}}" placeholder="{{translation('enter')}} {{translation('period_end_time')}}" type="text" data-rule-required="true" data-school-timimg-period-id="{{ $j }}" value="@if(isset($session_school_end_time) && $session_school_end_time!='') {{ $session_school_end_time }} @endif" readonly />
              <span for="period_end_time{{$j}}"></span>
          </div>
      </div>
        @endfor
    @endif

    
     
     
      <div class="row">
        <div class="form-group">
          <div class="col-sm-9 col-lg-10 "> 
              <button type="submit"  id="submit_button_periods" class="btn btn-primary">{{translation('save')}}</button>
          </div>
          <div class="clearfix"></div>
        </div>           
      </div>
    </form>
@endif

      <div class="clearfix"></div>




      @if($session_class_id && !empty($arr_periods_timing))
      <div class="row mainTable" >
        <div class="col-lg-9">
         <div class="teacher-list-section tm-tbl-pro">
           <div> 
             <span  style="float:left"><b>{{translation('timetable')}}</b></span>
             <div class="dropup-down-uls table-right" style="float:right">
                <a href="javascript:void(0)" class="export-lists" style="color:#fff"><i class="fa fa-upload"></i> {{ translation('export')}} </a>
                <div class="export-content-links">
                    <div class="li-list-a">
                        <a href="javascript:void(0)" onclick="exportForm('pdf');">PDF</a>
                    </div>
                    <div class="li-list-a">
                        <a href="javascript:void(0)" onclick="exportForm('csv');">CSV</a>
                    </div>
                     
                </div>
            </div>
            <div class="clearfix"></div>
           </div>

         </div>
         
         @if(Session::has('num_of_periods'))
         <div class="timetable-data-new timetable-section-main professor-timetable-section" style="display: block!important;"> 
          <div class="table-responsive" style="border:1!important;">
            <input type="hidden" name="multi_action" value="" />
            <table class="table table-advance posn-relative time-tbl-custm" id="table_module" style="border:1!important;">
             <thead>
              <tr>
               <th></th>
               @if(count($weekly_days) >0)
               @foreach($weekly_days as $day => $day_full_name)
               <th>{{ translation(strtolower($day)) }}</th>
               @endforeach
               @endif


             </tr>

           </thead>
           <tbody>
            <?php $countPeriodTimimgArray  = 0;
            $period_start_time = $period_end_time = '00:00:00'; 

            ?>
            @if(isset($session_num_of_periods) && $session_num_of_periods!="")

            @for($i=1; $i<=$session_num_of_periods; $i++)
            <?php

            if($arr_periods_timing[$countPeriodTimimgArray]['period_no'] == $i){
              $period_start_time = $arr_periods_timing[$countPeriodTimimgArray]['period_start_time'];
              $period_end_time = $arr_periods_timing[$countPeriodTimimgArray]['period_end_time'];
            }
            $isBreak = $arr_periods_timing[$countPeriodTimimgArray]['is_break'];
            ++$countPeriodTimimgArray;
            ?>


            <tr> 
              <td><div class="font-time-tb">{{ translation('period') }} {{$i}} </div>
              <div class="font-time-tm-tbl">{{getTimeFormat($period_start_time)}} - {{getTimeFormat($period_end_time)}}</div>
              </td>

              @if(count($weekly_days) >0)
              @foreach($weekly_days as $day => $day_full_name)



              @if(isset($arr_holiday) && in_array($day,$arr_holiday))

              @if($i==1)
              <td rowspan="{{$session_num_of_periods}}" class="sunday-holiday-section">{{-- other-holiday-section --}}
              {{ translation("holiday") }} </td>
              @endif
              @elseif($isBreak == 1)

              <td  class="other-holiday-section">
              {{ translation("break") }} </td>

              @else 
              <td class="droppable_td" period-id="{{$i}}" period-day="{{$day}}" period-start-time="{{$period_start_time}}" period-end-time="{{$period_end_time}}" > 

                @if(isset($arr_time_table) && sizeof($arr_time_table)>0)
                @foreach($arr_time_table as $key => $timetable)

                @if(isset($timetable['day']) && $timetable['day']==strtoupper($day) && 
                isset($timetable['periods_no']) && $timetable['periods_no']==$i
                )
                <?php 
                $middle_name = $first_name  = $last_name  ='';
                if(isset($timetable['user_details']['first_name']) && $timetable['user_details']['first_name']!="")
                {
                  $first_name = ucfirst($timetable['user_details']['first_name']);

                }

                if(isset($timetable['user_details']['last_name']) && $timetable['user_details']['last_name']!="")
                {
                  $last_name = ucfirst($timetable['user_details']['last_name']);

                }
                if(isset($timetable['professor_subjects']['course_name']) && $timetable['professor_subjects']['course_name']!="")
                {
                  $subject_name = $timetable['professor_subjects']['course_name'];
                }
                else
                {
                  $subject_name = "NA";
                }

                ?>

                <span style="color: blue">{{$first_name.' '.$last_name}} </span><br/> <span style="color: green">{{$subject_name}}</span>
                @if($curr_academic_year_id==$active_academic_year_id)
                <span class="removePeriod glyphicon glyphicon-trash pull-right delete_period time-td-close" 
                data-professor-id="{{isset($timetable['professor_id'])?$timetable['professor_id']:""}}" data-period-num="{{isset($timetable['periods_no'])?$timetable['periods_no']:""}}" data-day="{{isset($timetable['day'])?$timetable['day']:""}}" style="cursor:pointer" onclick="delete_Assign_Teacher(this);">
              </span>
              @endif
              @endif

              @endforeach
              @endif
            </td>



            @endif




            @endforeach
            @endif
          </tr>

          @endfor
          @endif

        </tbody>
      </table>
    </div>
    </div>
    @endif
    </div>
    <div class="col-lg-3">
      <div class="teacher-list-section tm-tbl-pro">
        {{translation('professor_list')}}
      </div>
      <ul class="t-list teachers-list no-teacher-available-section">

       @if(isset($arr_teachers) && sizeof($arr_teachers)>0)
       @foreach($arr_teachers as $subject_key => $subject)

       @if(isset($subject) && count($subject)>0)
       @foreach($subject as $key => $teacher)

       <?php 

       $hours = isset($teacher['total_periods']) && $teacher['total_periods']!=""?$teacher['total_periods']:'0';

       if(isset($teacher['remaingin_periods']) && $teacher['remaingin_periods']!="" && $teacher['remaingin_periods']!="0")
       {  

        $teacher_remaingin_periods = $teacher['remaingin_periods'];
      }
      else
      {
        $teacher_remaingin_periods = 0;
      }

      ?>
      <li>
       <div @if(isset($teacher['remaingin_periods']) && $teacher['remaingin_periods']!="0") class="draggable teacher_key_{{$teacher['professor_id']}}" @endif  data-container="body" data-trigger="hover" data-placement="top" data-toggle="popover" 

       title="{{isset($teacher['teacher_name'])?$teacher['teacher_name']:''}}"
       data-original-title="{{isset($teacher['teacher_name'])?$teacher['teacher_name']:''}}"
       data-content="Subject: {{isset($teacher['course_name'])?$teacher['course_name']:""}}, Total Periods:{{isset($hours)?$hours:""}}" 
       data-subject-name={{isset($teacher['course_name'])?$teacher['course_name']:""}} 

       data-sub-key="{{$subject_key}}"  
       data-remaining-periods="{{isset($teacher_remaingin_periods)?$teacher_remaingin_periods:''}}"
       data-class-id={{isset($teacher['class_id'])?$teacher['class_id']:""}}
       data-level-id={{isset($teacher['level_id'])?$teacher['level_id']:""}}> 
       <div class="timetable-img-section-block pull-left">
        <img src="{{isset($teacher['teacher_image'])?$teacher['teacher_image']:""}}" alt="" width="40">
      </div>
      <div class="time-table-techer-list">
        <span>{{isset($teacher['teacher_name'])?$teacher['teacher_name']:""}}</span> 
        <span>
          - {{isset($teacher['course_name'])?$teacher['course_name']:""}}
        </span><br/>

        <input type="hidden" id="subject_id" value="{{isset($teacher['id'])?$teacher['id']:""}}">
        <input type="hidden" id="professor_id" value="{{isset($teacher['professor_id'])?$teacher['professor_id']:""}}">
        <span>  <label class="i-checks m-b-none sub_key_{{$subject_key}}">
          {{translation('periods_remaining')}}: {{isset($teacher_remaingin_periods)?$teacher_remaingin_periods:""}} 
        </label></span>
      </div>
    </div>

    <div class="clearfix"></div>
    </li>

    @endforeach
    @endif

    @endforeach
    @else                            
    <div class="no-padding-left">
      <h5>{{translation('no_professor_available')}}</h5>
    </div>

    <div class="clearfix"></div>
    <br>
    <ul class="t-list">
    </ul>                            
    @endif

    </ul>
    </div>
    </div>
    @endif
    </div>
    </div>
    </div>
    </div>
    <!-- END Main Content -->

    <script type="text/javascript">
      $(document).ready(function(){



        $(document).on("change", "#level_id", function(){
          var _id = $(this).val();
          var token   = $("input[name=_token]").val();

          if(_id == ''){
            $('#class_id').html('<option value="">{{ translation('select') }} {{translation("class")}}</option>');
          }
          else
          {
            $.ajax({
              headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
              url : '{{$module_url_path}}/get_classes',
              type : "POST",
              dataType: 'JSON',
              data : {level_id:_id,type:'{{\Request::segment(3)}}'},
              beforeSend:function(data, statusText, xhr, wrapper){
                $('#class_id').html('<option value="">{{ translation('select') }} {{translation("class")}}</option>');

              },
              success:function(data, statusText, xhr, wrapper){
                $('#class_id').html('');
                allData = ['<option value="">{{ translation('select') }} {{translation("class")}} </option>'];
                if(data.status == 'done')
                {
                  $("#class_id").attr("disabled",false);
                  var responseArray = data.categories;
                  if(responseArray.length){
                    var obj = $.parseJSON(responseArray);
                    $.each(obj, function() {
                     allData.push('<option value="'+this['id']+'">'+this['name']+'<\/option>'); 
                    
                   });  
                  } 
                  $('#class_id').html(allData.join(''));

                }
                else
                {
                  $('#class_id').html(allData.join(''));
                }

              }
            });
            
          }
        });
      });

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
    </script>
    <!-- To Export The Data -->
    <script type="text/javascript">
      $( function() {    
        $('.timepicker-default').timepicker(); 
        //bootstrap timepicker
      });
      $(".js-example-basic-multiple").select2();



      $.fn.timepicker.defaults = {
        defaultTime: false,
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


      function addLoader2(){   
        $('#validation-form2').submit(function(event) {

            $("input[name*='period_start_time']").each(function() {
                if($(this).val()!= "" ){}else{
                  showAlert('{{translation("period_timing_can_not_be_null")}}','warning');
                  console.log('error'+$(this).val());
                }
            });

            $("input[name*='period_end_time']").each(function() {
                if($(this).val()!= "" ){}else{
                  showAlert('{{translation("period_timing_can_not_be_null")}}','warning');
                  console.log('error'+$(this).val());
                }
            });

        if($('.has-errors').length > 0){
           event.preventDefault();
         }else{

          $("#submit_button_periods").html("<b><i class='fa fa-spinner fa-spin'></i></b> Processing...");
          $("#submit_button_periods").attr('disabled', true);
        }
      });
      }


      /* VALIDATE SCHOOL START TIME AND END TIME */
       $(document).on("change","#school_start_time",function()
        { 
            var school_start_time = $("#school_start_time").val();
            var school_end_time = $("#school_end_time").val();
            
            if(school_start_time!='' && school_end_time!=''){
              //convert both time into timestamp
              var stt = new Date("November 13, 2013 " + school_start_time);
              stt = stt.getTime();

              var endt = new Date("November 13, 2013 " + school_end_time);
              endt = endt.getTime();

              //by this you can see time stamp value in console via firebug
              console.log("Time1: "+ stt + " Time2: " + endt);
              if(stt > endt) {
                  $("#school_start_time").next('span').html('{{translation("start_time_must_be_smaller_then_end_time")}}');
                  $("#school_start_time").val('');
                  $("#school_start_time").parent().parent().addClass('has-error');
                  return false;
              }else{
                  $("#school_start_time").next('span').html('');
                  $("#school_start_time").parent().parent().removeClass('has-error');
              } 
            }
               
        });

       $(document).on("change","#school_end_time",function()
        { 
            var school_start_time = $("#school_start_time").val();
            var school_end_time = $("#school_end_time").val();
            
            if(school_start_time!='' && school_end_time!=''){
              //convert both time into timestamp
              var stt = new Date("November 13, 2013 " + school_start_time);
              stt = stt.getTime();

              var endt = new Date("November 13, 2013 " + school_end_time);
              endt = endt.getTime();

              //by this you can see time stamp value in console via firebug
              console.log("Time1: "+ stt + " Time2: " + endt);
              if(stt > endt) {
                  $("#school_end_time").next('span').html('{{translation("end_time_must_be_greater_then_start_time")}}');
                  $("#school_end_time").val('');
                  $("#school_end_time").parent().parent().addClass('has-error');
                  return false;
              }else{
                  $("#school_end_time").next('span').html('');
                  $("#school_end_time").parent().parent().removeClass('has-error');
              } 
            }
               
        });


        /* Period Validation */
        var school_start_time = $('#school_start_time').val();
        var convertied_school_st =   new Date("November 13, 2013 " + school_start_time);
              convertied_school_st = convertied_school_st.getTime();



        var school_end_time   = $('#school_end_time').val();
        var convertied_school_ent =   new Date("November 13, 2013 " + school_end_time);
              convertied_school_ent = convertied_school_ent.getTime();
        $(document).on("change",".checkValidationStart",function()
        { 
            var period = $(this).attr('data-school-timimg-period-id');
            var period_start_time = $("#period_start_time"+period).val();;
            var period_end_time = $("#period_end_time"+period).val();
            if(period_start_time!='' && period_end_time!=''){
                  //convert both time into timestamp
                  var stt = new Date("November 13, 2013 " + period_start_time);
                  stt = stt.getTime();

                  var endt = new Date("November 13, 2013 " + period_end_time);
                  endt = endt.getTime();

                  //by this you can see time stamp value in console via firebug
                   
                  if(stt > endt) {
                      $("#period_start_time"+period).next('span').html('{{translation("start_time_must_be_smaller_then_end_time")}}').addClass('has-errors');
                      $("#period_start_time"+period).val('');
                      return false;
                  }else{

                      $("#period_start_time"+period).next('span').html('').removeClass('has-errors');
                  }

                  if(stt < convertied_school_st){
                      $("#period_start_time"+period).next('span').html('{{translation("period_start_time_must_be_greater_than_or_equal_to_school_start_time")}}').addClass('has-errors');
                      $("#period_start_time"+period).val('');
                      return false;
                  }else{
                      $("#period_start_time"+period).next('span').text('').removeClass('has-errors');
                  }
                   

                 if((endt <= convertied_school_st )  &&  (convertied_school_ent <= endt ) ){
                      $("#period_end_time"+period).next('span').html('{{translation("period_end_time_must_be_in_between_school_time")}}').addClass('has-errors');
                      $("#period_end_time"+period).val('');
                      return false;
                  }else{
                      $("#period_end_time"+period).next('span').html('').removeClass('has-errors');
                  }
                  

                  if((stt < convertied_school_st ) ||  (convertied_school_ent <= stt ) ){
                      $("#period_start_time"+period).next('span').html('{{translation("period_start_time_must_be_in_between_school_time")}}').addClass('has-errors');
                      $("#period_start_time"+period).val('');
                      return false;
                  }else{
                      $("#period_start_time"+period).next('span').html('').removeClass('has-errors');
                  }

 
            }
        });


         $(document).on("change",".checkValidationEnd",function()
        { 
             
            var period = $(this).attr('data-school-timimg-period-id');
            var period_start_time = $("#period_start_time"+period).val();;
            var period_end_time = $("#period_end_time"+period).val();
            if(period_start_time!='' && period_end_time!=''){
                  //convert both time into timestamp
                  var stt = new Date("November 13, 2013 " + period_start_time);
                  stt = stt.getTime();

                  var endt = new Date("November 13, 2013 " + period_end_time);
                  endt = endt.getTime();




                  //by this you can see time stamp value in console via firebug
                   
                  if(stt > endt) {

                      $("#period_end_time"+period).next('span').html('{{translation("period_end_time_must_be_greater_then_period_start_time")}}').addClass('has-errors');
                      $("#period_end_time"+period).val('');
                      return false;
                  }else{
                      $("#period_end_time"+period).next('span').html('').removeClass('has-errors');
                  }

                  if(endt > convertied_school_ent){
                       $("#period_end_time"+period).next('span').html('{{translation("period_end_time_must_be_less_than_or_equal_to_school_end_time")}}').addClass('has-errors');
                      $("#period_end_time"+period).val('');
                      return false;
                  }else{
                      $("#period_end_time"+period).next('span').html('').removeClass('has-errors');
                  }
                   

                  if((endt <= convertied_school_st )  &&  (convertied_school_ent <= endt ) ){
                      $("#period_end_time"+period).next('span').html('{{translation("period_end_time_must_be_in_between_school_time")}}').addClass('has-errors');
                      $("#period_end_time"+period).val('');
                      return false;
                  }else{
                      $("#period_end_time"+period).next('span').html('').removeClass('has-errors');
                  }


                  if((stt < convertied_school_st ) ||  (convertied_school_ent <= stt ) ){
                      $("#period_start_time"+period).next('span').html('{{translation("period_start_time_must_be_in_between_school_time")}}').addClass('has-errors');
                      $("#period_start_time"+period).val('');
                      return false;
                  }else{
                      $("#period_start_time"+period).next('span').html('').removeClass('has-errors');
                  }

 
            }
        });

    /* Allowed toDelete the timetable */
    
    $('body').on('click','.delete_time_table',function(event){
          
               
                   

                   event.preventDefault();  
                   swal({
                    title: "Are you sure ?",
                    text: '{{translation("are_you_sure_to_delete_the_timetable")}}',
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "{{translation('yes')}}",
                    cancelButtonText: "{{translation('no')}}",
                    closeOnConfirm: true,
                    closeOnCancel: true
                  },
                  function(isConfirm)
                  {
                    if(isConfirm==true)
                    {
                            window.location.href= "{{url('/')}}/school_admin/timetable/delete_time_table";
                    }
                  });
                   

    });
    /*  Allowed toDelete the timetable */

      /* Period Validation */
      /* VALIDATE SCHOOL START TIME AND END TIME */

    </script> 
    <input type="hidden" id="session_class_id"        value="{{isset($session_class_id)?$session_class_id:""}}">
    <input type="hidden" id="session_level_id"      value="{{isset($session_level_id)?$session_level_id:""}}">
    <input type="hidden" id="session_num_of_periods"  value="{{isset($session_num_of_periods)?$session_num_of_periods:""}}">
    <input type="hidden" id="session_period_duration" value="{{isset($session_period_duration)?$session_period_duration:""}}">

    <input type="hidden" id="curr_academic_year_id"   value="{{isset($curr_academic_year_id)?$curr_academic_year_id:""}}">
    <input type="hidden" id="active_academic_year_id" value="{{isset($active_academic_year_id)?$active_academic_year_id:""}}">

    <input type="hidden" id="arr_time_table" value="{{isset($arr_time_table)?json_encode($arr_time_table):""}}"> 


    <script type="text/javascript">
      site_url = "{{ url('/') }}";
    </script>
    <script src="{{ url('/') }}/js/school_admin/timetable/time_table.js"></script> 
    <script src="{{ url('/') }}/js/school_admin/timetable/jquery-ui.js"></script>
    <script src="{{ url('/') }}/js/school_admin/timetable/ui-load.js"></script>

    @endsection