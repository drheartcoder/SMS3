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
             <div class="col-sm-9 col-md-8 col-lg-9">
                
                </div>
            </div>
            <br/>
            <form class="form-horizontal" id="validation-form1">
                {{ csrf_field() }}
                <div class="row">
                  <div class="row">
                              
                              <div class="col-md-6">
                                   <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('start_date')}}</label>
                                        <div class="col-sm-9 col-md-8 col-lg-9 controls">
                                           <input class="form-control datepikr" name="start_date" id="datepicker" placeholder="{{translation('select_date')}}" type="text" readonly style="cursor: pointer;">
                                        </div>
                                    </div>
                              </div>
                  </div>
                  <br/><br/>
              
                <div id="table_div" style="display: none;">
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
                
                 <div class="table-responsive attendance-create-table-section " style="border:0">
                    <input type="hidden" name="role" value="{{$role}}" />
                    <table class="table table-advance"  id="table_module">
                       <thead>
                          <tr>
                            <th>{{translation('sr_no')}}.</th>
                             <th>{{translation($role)}} {{translation('name')}}</th>
                             <th>{{translation('national_id')}}</th>
                             <th>{{translation('attendance')}}</th>
                          </tr>
                       </thead>
                       <tbody id="table_body">                            
                       </tbody>
                    </table>
                 </div><br/>
                </div>
                <div id="table_div2" style="display: none;">
                 <div class="table-responsive" style="border:0">
                    <input type="hidden" name="role" value="{{$role}}" />
                    <table class="table table-advance"  id="table_module2">
                       <tr>
                        <td>
                          <div class="alert alert-danger" style="text-align:center">{{translation('no_data_available')}}</div>
                        </td>
                      </tr>
                    </table>
                 </div><br/>
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
          todayHighlight: true
      });

      $('#datepicker').val(today);
      getStaffData();
    });
</script>

<script>
  

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


$('#datepicker').on('changeDate',function(){
   getStaffData();
});

function getStaffData()
{
  $('#loader').show();
  $('body').addClass('loader-active'); 
  
  var date    =   $('#datepicker').val();
  $.ajax({
          url  :"{{ $module_url_path }}/getStaffData/{{$role}}",
          type :'POST',
          data :{'start_date':date ,'_token':'<?php echo csrf_token();?>'},
          success:function(data){
              if(data.flag == 'true')
                {
                  $('#table_div2').hide();
                  $('#table_div').show();
                  $('#table_body').empty();
                  $('#table_body').append(data.data);
                }
                else
                {
                  $('#table_div').hide();
                  $('#table_div2').show();
                }
          }
        });
}
</script>
<!-- END Main Content --> 
@endsection