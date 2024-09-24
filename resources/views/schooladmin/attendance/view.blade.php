@extends('schooladmin.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{url('/')}}/schooladmin/dashboard">{{translation('dashboard')}}</a>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        <li>
          <i class="{{$module_icon}}"></i>
          <a href="{{$module_url_path.'/'.$role}}">{{$module_title}}</a>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        <i class="fa fa-eye"></i>
        <li class="active">{{$view_page_title}}</li>
    </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1>{{$module_title}}</h1>

    </div>
</div>
<!-- END Page Title -->

<!-- BEGIN Main Content -->
<div class="row">
<div class="col-md-12">
   <div class="box {{ $theme_color }}">
      <div class="box-title">
         <h3>
            
         </h3>
      </div>
      <div class="box-content">
         @include('schooladmin.layout._operation_status')  

            <div class="row">
             
            </div>
            <br/>
            <form class="form-horizontal" id="validation-form1">
                {{ csrf_field() }}
                <div class="row">
                  <div class="row">
                              <div class="col-md-6">
                                   <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('select_level')}}</label>
                                        <div class="col-sm-9 col-md-8 col-lg-9 controls">
                                           <select name="level" id="level" class="form-control" data-rule-required="true" onChange="getClasses();">
                                              <option value="">{{translation('select_level')}}</option>
                                              @if(isset($levels) && !empty($levels))
                                                @foreach($levels as $key => $level)
                                                  <option value="{{$level['level_id']}}">{{$level['level_details']['level_name']}}</option>
                                                @endforeach
                                              @endif
                                          </select>
                                        </div>
                                    </div>
                              </div> 
                              <div class="col-md-6">
                                   <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('select_class')}}</label>
                                        <div class="col-sm-9 col-md-8 col-lg-9 controls">
                                           <select name="class" id="class" class="form-control" data-rule-required="true" onChange="getPeriods();">
                                              <option value="">{{translation('select_class')}}</option>
                                          </select>
                                        </div>
                                    </div>
                              </div>
                              <div class="col-md-6">
                                  <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('select_date')}}</label>
                                        <div class="col-sm-9 col-md-8 col-lg-9 controls">
                                           <input class="form-control datepikr" name="date" id="datepicker" placeholder="{{translation('select_date')}}" type="text" readonly style="cursor: pointer;">
                                        </div>
                                    </div>
                              </div> 
                              <div class="col-md-6">
                                  <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('select_period')}}</label>
                                        <div class="col-sm-9 col-md-8 col-lg-9 controls">
                                           <select name="period" id="period" class="form-control">
                                              <option value="">{{translation('select_period')}}</option>
                                              
                                          </select>
                                        </div>
                                    </div>
                              </div>

                              <div align="center">
                                 <a href="javascript:void(0)" class="btn btn btn-primary sv-space" onClick="getData();">{{translation('submit')}}</a>
                              </div>
                  </div>
                  <br/><br/>

                <div id="table_div2" hidden="true">
                  <br>
                  <div class="filter-section">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">                            
                                <input class="form-control" name="search_key" id="search_key" type="text" placeholder="{{translation('search')}}...">
                            </div>
                        </div>
                    </div>
                  </div>
                 <div class="table-responsive  attendance-create-table-section" style="border:0">
                    <input type="hidden" name="role" value="{{$role}}" />
                    <table class="table table-advance"  id="table_module2">
                       
                    </table>
                 </div><br/>
                </div>
               </div>
               </div>
              </form>
      </div>
    </div>
   </div>
</div>
<script>
    $(function () {
      var date  = new Date();
      var today = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate();
      $("#datepicker").datepicker({
          format:'yyyy-mm-dd',
          autoclose:true,
          startDate: "{{\Session::get('start_date')}}",
          endDate: "{{\Session::get('end_date')}}",
          todayHighlight: true,
      });

      $('#datepicker').val(today);
    });
</script>

<script>
  function getData()
  {
    var level   =   $('#level').val();
    var cls     =   $('#class').val();
    var period  =   $('#period').val();
    var date    =   $('#datepicker').val();
      $('#table_div').show();
      $('#table_body').empty();
      $('#table_module2').empty();
      $.ajax({
              url  :"{{ $module_url_path }}/getData",
              type :'POST',
              data :{'level':level ,'cls':cls ,'period':period ,'date':date ,'_token':'<?php echo csrf_token();?>'},
              success:function(data){
                $('#table_module2').empty();
                  $('#table_div2').show();
                  $('#table_module2').append(data);
              }
            });
 
  }

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

 function getPeriods()
 {
   var level   =   $('#level').val();
   var cls     =   $('#class').val();
   $('#period').empty();

   $.ajax({
              url  :"{{ $module_url_path }}/getPeriods",
              type :'POST',
              data :{'level':level ,'class':cls ,'_token':'<?php echo csrf_token();?>'},
              success:function(data){
                $('#period').append(data);
              }
            });
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
<!-- END Main Content --> 
@endsection