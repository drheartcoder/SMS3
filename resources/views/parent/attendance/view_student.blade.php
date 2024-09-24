@extends('parent.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{url('/')}}/parent/dashboard">{{translation('dashboard')}}</a>
        </li>
        <span class="divider">
          <i class="fa fa-angle-right"></i>
        </span>
            <i class="fa fa-eye"></i>
            <li class="active">{{$module_title}}</li>
    </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="fa fa-cc-diners-club"></i> {{$module_title}}</h1>

    </div>
</div>
<!-- END Page Title -->
   
  <div class="row">
<div class="col-md-12">
   <div class="box {{ $theme_color }}">
      <div class="box-title pr0">
         <h3>
            <i class="fa fa-eye"></i>
            {{ isset($page_title)?$page_title:"" }}
         </h3>
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
            
               <a class="icon-btns-block" 
                  title="{{translation('refresh')}}" 
                  href="{{ $module_url_path }}"
                  style="text-decoration:none;">
               <i class="fa fa-repeat"></i>
               </a> 
         </div>
      </div>
      <div class="box-content">
         @include('parent.layout._operation_status')  
         {!! Form::open([ 'url' => $module_url_path.'/multi_action',
         'method'=>'POST',
         'enctype' =>'multipart/form-data',   
         'class'=>'form-horizontal', 
         'id'=>'validation-form1' 
         ]) !!}
         {{ csrf_field() }}
          <input type="hidden" name="multi_action" value="" />
          <input type="hidden" name="search" id="search" value="" />
          <input type="hidden" name="file_format" id="file_format" value="" />
         <div class="col-md-10">
         </div><br/><br/>
         <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-6">
              <div class="form-group">
               <div class="row">
                <label class="col-sm-3 col-lg-2 control-label">{{translation('start_date')}}</label>
                <div class="col-sm-4 col-lg-8">
                  <input type="text" name="start_date"  id="datepicker" class="form-control datepikr" data-rule-required='true' value="" data-rule-date="true" readonly style="cursor: pointer;" placeholder="{{translation('select_date')}}">
                  <span class="help-block">{{ $errors->first('start_date') }}</span>
                </div>
                  </div>
              </div>
            </div>

            <div class="col-sm-12 col-md-12 col-lg-6">
              <div class="form-group">
               <div class="row">
                <label class="col-sm-3 col-lg-2 control-label">{{translation('end_date')}}</label>
                <div class="col-sm-4 col-lg-8">
                  <input type="text" name="end_date"  id="datepicker2" class="form-control datepikr" data-rule-required='true' value="" data-rule-date="true" readonly style="cursor: pointer;" placeholder="{{translation('select_date')}}">
                  <span class="help-block">{{ $errors->first('end_date') }}</span>
                </div>
                  </div>
              </div>
            </div>

            <div class="col-sm-12 col-md-12 col-lg-6">
              <div class="form-group">
                <div class="row">
                <label class="col-sm-3 col-lg-2 control-label"></label>
                <div class="col-sm-4 col-lg-4">
                  <div id="button">
                    <input type="button" name="show"  id="show" class="form-control btn btn-primary" value="{{translation('show')}}" onClick="getData();">
                  </div>
                </div>
                  </div>
              </div>
            </div>
         </div>
         <br/>
         <div class="clearfix"></div>
         <div class="table-responsive  attendance-create-table-section" style="border:0" id="table_body1">
            <input type="hidden" name="multi_action" value="" />
            <table class="table table-advance"  id="table_module1">
              
            </table>
         </div>
         <div class="table-responsive  attendance-create-table-section" style="border:0" id="table_body" hidden="true">
            <input type="hidden" name="multi_action" value="" />
            <table class="table table-advance"  id="table_module">
              
            </table>
         </div>
         {!! Form::close() !!}
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
          autoclose:true
      });

      $('#datepicker2').datepicker({
        format:'yyyy-mm-dd',
        autoclose:true
      });

      $("#datepicker").on('change',function(){
          var newdate = $("#datepicker").val();
          $('#datepicker2').datepicker('setStartDate',newdate);
      })

  });

 function getData()
 {
    var date1 = $('#datepicker').val();
    var date2 = $('#datepicker2').val();
    $('#table_module').empty();
    $('#table_module1').empty();
    if(date1 != '' || date2 != '')
    {
      /*$('#button').html("<a class='form-control btn btn-primary'><i class='fa fa-spinner fa-spin'></i> {{translation('processing')}}...</a>");*/
      $('#button').attr('disabled', true);
      $('#loader').fadeIn('slow');
      $('body').addClass('loader-active');

      $.ajax({
              url  :"{{ $module_url_path }}/getStudentData",
              type :'POST',
              data :{'start_date':date1 ,'end_date':date2 ,'_token':'<?php echo csrf_token();?>'},
              success:function(data){

                $('#button').attr('disabled','none');
                $('#loader').fadeOut('slow');
                $('body').removeClass('loader-active');
                $('#table_body1').css('display','none');
                $('#table_body').show();
                $('#table_module').append(data);
              }
            });
      }
 }
  

function exportForm(file_format)
  {
    document.getElementById('file_format').value = file_format;
    var serialize_form   = $("#validation-form1").serialize();
    window.location.href = '{{ $module_url_path }}/export?'+serialize_form+'&export=true';
  }
  $(document).on("change","[type='search']",function(){
      var search_hidden = $(this).val();
      document.getElementById('search').value = search_hidden;
   });
</script>
 
@endsection