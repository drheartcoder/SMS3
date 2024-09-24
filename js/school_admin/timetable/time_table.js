
/*Getting current and active academic year*/

var curr_academic_year_id = $("#curr_academic_year_id").val();
var active_academic_year_id = $("#active_academic_year_id").val();

var class_id        = $("#session_class_id").val();
var level_id        = $("#session_level_id").val();
var num_of_periods  = $("#session_num_of_periods").val();
var period_duration = $("#session_period_duration").val();

function change_class_section() 
{ 
     
    $('.timetable-data-new').hide();
    $('.teachers-list').hide();
    $('.mainTable').hide();

    $('#class_id').removeAttr('disabled',false);
    $('#level_id').removeAttr('disabled',false);
    $('#num_of_periods').removeAttr('disabled',false);

    

    $('#class_id').val('');
    $('#level_id').val('');
    $('.section_class').hide();
    $('#num_of_periods').val('');
    $('#school_start_time').val('');
    $('#school_end_time').val('');

     $('#school_start_time').removeAttr('disabled',false);
     $('#school_end_time').removeAttr('disabled',false);

     $('#school_start_time').attr('readonly',true);
     $('#school_end_time').attr('readonly',true);
   
    $('.common-btn').hide();
    $('.save_set_timetable').show();
    $("#weekly_off").select2("val", "");
    $('#weekly_off').removeAttr('disabled',false);
    
    $('#display_school_timimgs').hide();
    $("#validation-form2").hide();
}




$(document).ready(function () 
  {
    if(class_id       && class_id!=''       && 
      level_id      && level_id!=''     && 
      num_of_periods  && num_of_periods!='' )
    {
        $('.timetable-data-new').show();  
    }
    else
    {

        $('.timetable-data-new').hide();
    }


    /* Allowed to edit the period number start time end time as well as weekly off for perticular class Only */
    
    $('body').on('click','.update_time_table',function(event){
          $('#num_of_periods').removeAttr('disabled',false);
          $('#weekly_off').removeAttr('disabled',false);

          $('#school_start_time').removeAttr('disabled',false);
          $('#school_end_time').removeAttr('disabled',false);

          $('#school_start_time').attr('readonly',true);
          $('#school_end_time').attr('readonly',true);
          $('.save_set_timetable').show();
          $(this).hide()

    });
    /* Allowed to edit the period number start time end time as well as weekly off for perticular class Only */
 
  });

 
 function get_period_details(ref) 
 {
    var level_id          = $("#level_id").val();
    var class_id  = $(ref).val();
    $.ajax({
       url:site_url+'/school_admin/timetable/get_period_details',
       data:{class_id:class_id,level_id:level_id},
       dataType:'JSON',
       beforeSend: '',
       success: function(response)
       {
     
         var section_html  = "";
         console.log(response);
         if(response != null && response != "" && typeof(response)=='object')   
         {
           
           $("#num_of_periods").val(response.num_of_periods);
           $("#num_of_periods").attr('selected', true);

           $("#school_start_time").val(response.school_start_time);
           $("#school_end_time").val(response.school_end_time);

           $("#school_start_time").attr('readonly',true);
           $("#school_end_time").attr('readonly',true);
           
           window.location.reload();

         }
         else
         {
              
              $("#num_of_periods").val('');
              $("#period_duration").val('');
              $("#num_of_periods").removeAttr('disabled',false);
              
              $("#school_start_time").attr('readonly',true);
              $("#school_end_time").attr('readonly',true);

              $( ".draggable" ).draggable( "disable" );
              $('.delete_period').css({cursor:"default"});
              $('.delete_period').attr('onclick',"");
              $('.timetable-data-new').hide();
         }
        
       },  
     });
 }
 
    $('.subsd').hide();
     $('.subs').click(function(){
       event.stopPropagation();
         $(this).hide();
          $('.subsd').show();
     });
 
     $('.subsd').click(function(){
         $(this).hide();
          $('.subs').show();
     });

  var csrf_token = $("input[name=_token]").val();

  $(document).ready(function () 
  { 
      //alert(site_url);
      var url = site_url+'/school_admin/timetable/create';

      /*Hide Peiod defined section*/

      var num_of_periods = $("#num_of_periods").val();
      var period_duration = $("#period_duration").val();

      if(num_of_periods!='' && period_duration!='')
      {
          $("#class_id").attr('disabled',true);
          $("#level_id").attr('disabled',true);
          $("#num_of_periods").attr('disabled',true);
          $("#period_duration").attr('disabled',true);


          $("#school_start_time").attr('readonly',false);
          $("#school_end_time").attr('readonly',false);

          $("#school_start_time").attr('disabled',true);
          $("#school_end_time").attr('disabled',true);
          $("#weekly_off").attr('disabled',true);




          $("#btn-update-period").html('');

          var html = "";

          if(active_academic_year_id==curr_academic_year_id)
          {
            html+= '<button type="button" class="btn btn-primary btn-addon btn-sm m-r" onclick="updatePeriod(false);">Edit</button>';
          }

          $("#btn-update-period").html(html);
      }
      else
      {
          $("#num_of_periods").removeAttr('disabled',false);
          $("#period_duration").removeAttr('disabled',false);
          $("#weekly_off").attr('disabled',false);
          $("#school_start_time").attr('readonly',true);
          $("#school_end_time").attr('readonly',true);


          $("#btn-update-period").html('');

          var html = "";

          html+= '<button type="submit" class="btn btn-primary btn-addon btn-sm m-r">Update Period Mapping</button>';

          $("#btn-update-period").html(html);      
      }
     
    if(curr_academic_year_id && curr_academic_year_id==active_academic_year_id)
    {
        
       $( ".droppable_td" ).droppable({
           drop: function(event, ui) {
          
            
            var name_of_teacher = $(ui.draggable).attr('data-original-title');
          
            var subject_name = $(ui.draggable).attr('data-subject-name');

            var assign_class_id = $(ui.draggable).attr('data-class-id');
            var assign_level_id = $(ui.draggable).attr('data-level-id');

            /*Get remaining hours for if remaining hours zero*/
            var remaining_hours = $(ui.draggable).attr('data-remaining-periods');
            var subject_id = $(ui.draggable).find('#subject_id').val();
            var professor_id = $(ui.draggable).find('#professor_id').val();

            var period_num =  $(event.target).attr('period-id');
            /*var day        =  $(event.target).closest('td').find('td:first').html();*/
             var day  = $(event.target).attr('period-day');

            var period_start_time  = $(event.target).attr('period-start-time');
            var period_end_time    = $(event.target).attr('period-end-time');

            var arr_remaining_hours = remaining_hours.split(':'); // split it at the colons
            
             if(arr_remaining_hours && arr_remaining_hours>1)
            {
              /* check this code */
                $(event.target).html('<span data-professor-id="'+professor_id+'" data-period-num="'+period_num+'"  data-day="'+day+'"'+ 
                  ' class="removePeriod glyphicon glyphicon-trash pull-right delete_period" style="cursor:pointer;" onclick="delete_Assign_Teacher(this);"></span>'+name_of_teacher+'/ <br/>'+subject_name);
                console.log('html placed');

            }
            
            // ajax call to save record

            var subject_key     = $(ui.draggable).attr('data-sub-key');
            var teacher_subject = $(ui.draggable).parent('li').parent('ul').find('.sub_key_'+subject_key);

            $.ajax({
              headers: {
                     'X-CSRF-TOKEN': csrf_token
                   },
              url:url,
              type:'post',
              dataType:'json',
              data:{
                    class_id:class_id,
                    level_id:level_id,
                    professor_id:professor_id,
                    subject_id:subject_id,
                    period_num:period_num,
                    period_day:day,
                    period_start_time:period_start_time,
                    period_end_time:period_end_time
                   },
              success:function (response)
              {
                   

                  if(response && response.status=="Success" && typeof(response)=="object")
                  {

                    var success_msg = '<div class="alert alert-success">'+
                                      '<a aria-label="close" data-dismiss="alert" class="close" href="#">'+'×</a>'+
                                        response.msg+
                                      '</div>';
                    $("#timetable_msg_div").html(success_msg);

                    $(teacher_subject).each(function () 
                    {
                        if(response.remaining_periods!=null)
                        {
                            if(response.remaining_periods=="0")
                            {
                                var remain_hours = response.remaining_periods;
                                $(this).parent('span').parent('li').find('div').draggable( "disable" );
                            }
                            else
                            {
                                var remain_hours = response.remaining_periods;
                            }
                        }
                        /*While allready assigned class teacher for class-section*/
                       
                        $(this).html('Periods Reamining: '+remain_hours+' ');
                         
                        $(ui.draggable).attr('data-remaining-periods',remain_hours);
                        
                    });

                    /*When remaining hours not match with defined period minutes then not draggable*/
                    /*if(response.remaining_minutes!='' &&  period_duration>response.remaining_minutes)
                    {
                         $('.t-list').each(function () 
                      {
                          console.log($(this).find('.teacher_key_'+teacher_id).draggable('disable'));
                      })
                    }*/
                   
                  }
                  else if(response.msg!="" && response.status == "HOLIDAY_ERROR")
                  {
                    
                    var error_msg = '<div class="alert alert-danger">'+
                              '<a aria-label="close" data-dismiss="alert" class="close" href="#">'+'×</a>'+
                                response.msg+
                              '</div>';

                    $("#timetable_msg_div").html(error_msg);
                  }
                  else
                  {
                    
                    var error_msg = '<div class="alert alert-danger">'+
                              '<a aria-label="close" data-dismiss="alert" class="close" href="#">'+'×</a>'+
                                response.msg+
                              '</div>';

                    $("#timetable_msg_div").html(error_msg);
                  }
              }
            
            });

            /*Hide Peiod defined section*/

            $("#class_id").attr('disabled',true);
            $("#level_id").attr('disabled',true);
            $("#num_of_periods").attr('disabled',true);
            $("#period_duration").attr('disabled',true);
            $("#btn-update-period").html('');

            var html = "";

            html+= '<button type="button" class="btn btn-primary btn-addon btn-sm m-r" onclick="updatePeriod(true);">Edit</button>';

            $("#btn-update-period").html(html);

           },
           tolerance:"pointer",
           
       }); 
    }
 
       $( ".draggable" ).draggable({
 
           appendTo: "body",
           helper: 'clone', //original
           containment: ".timetable-data-new", // draggable html cannot be gragged out of this class
       });
  });
   
 
   $(function () {
   $('[data-toggle="popover"]').popover()
 
 })
  /*Delete assign teacher period */
function delete_Assign_Teacher(elem) 
{
   var url = site_url+'/school_admin/timetable/delete';

   var professor_id = $(elem).attr('data-professor-id');
   var period_num = $(elem).attr('data-period-num');
   var day        = $(elem).attr('data-day');

   $remove_elem = $(elem).parent('td').html('');

   $.ajax({
    headers: {
                'X-CSRF-TOKEN': csrf_token
            },
    url:url,
    type:'POST',
    data:{professor_id:professor_id,period_num:period_num,day:day},
    success:function (response) 
    {
        if(response && response.status=="Success")
        {
            location.reload();
        }
    }
   });
}
/*When existing period duration minutes change then that function call.*/
function set_Period_Duration(elem) 
{
  var edit_period_duration = $(elem).val();

  if(edit_period_duration  && edit_period_duration!='0' && period_duration!=edit_period_duration)
  {
      updatePeriod(true);  // If period duration change then return true
  }
  else
  {
      updatePeriod(false); // If period duration not change then return false
  }
}

function updatePeriod(period) /* =false */
{
    // period == period || false;
    $("#class_id").attr('disabled',true);
    $("#level_id").attr('disabled',true);
    $("#num_of_periods").removeAttr('disabled',false);
    $("#period_duration").removeAttr('disabled',false);

    if(period==false)
    {
        $("#btn-update-period").html('');

        var html = "";

        html+= '<button type="submit" class="btn btn-primary btn-addon btn-sm m-r">Update Period Mapping</button>';
    }
    else
    {
        $("#btn-update-period").html('');

        var html = "";

        html+= '<button type="button" class="btn btn-primary btn-addon btn-sm m-r" data-toggle="modal" data-target="#myModal_update">Update Period Mapping</button>';
    }

    $("#btn-update-period").html(html);

    $( ".draggable" ).draggable( "disable" );

    $('.delete_period').css({cursor:"default"});
    $('.delete_period').attr('onclick',"");
}
function submitForm()
{
   $('#frm_period_mapping').submit();
   return true;
}
/*If time table has no any kind of record and change period minute then call*/

var arr_time_table = $("#arr_time_table").val();

var arr_time_table = JSON.parse(arr_time_table);

if(arr_time_table.length==0)
{
    $("#period_duration").attr('onblur','');
}