@extends('professor.layout.master')                
@section('main_content')

<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
      <li>
          <i class="fa fa-home"></i>
          <a href="{{ url($professor_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
      </li>
      <span class="divider">
          <i class="fa fa-angle-right"></i>
          <i class="fa {{$module_icon}}"></i>                
      </span> 
      <li class="active">{{ isset($module_title)?$module_title:"" }}</li>
  </ul>
</div>
<!-- END Breadcrumb -->


<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="fa fa-cc-diners-club"></i>{{str_singular($module_title)}}</h1>
    </div>
</div>
<!-- END Page Title -->


<!-- BEGIN Main Content -->
<div class="row">
<div class="col-md-12">
   <div class="box {{ $theme_color }}">
      <div class="box-title pr0">
         <h3>
            <i class="fa fa-list"></i>
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
          @if($role == 'student')
            <a href="{{$module_url_path}}/create/{{$role}}" >{{translation('create')}} {{translation('attendance')}}
          @else
            <a href="{{$module_url_path}}/view_professor/{{$enc_id}}" >{{translation('view')}} {{translation('attendance')}}
          @endif
            </a>
               <a class="icon-btns-block" 
                  title="{{translation('refresh')}}" 
                  href="{{ $module_url_path.'/professor' }}"
                  style="text-decoration:none;">
               <i class="fa fa-repeat"></i>
               </a> 
         </div>
      </div>
      <div class="box-content">
          
         @include('professor.layout._operation_status')  
         {!! Form::open([ 'url' => $module_url_path.'/multi_action',
         'method'=>'POST',
         'enctype' =>'multipart/form-data',   
         'class'=>'form-horizontal', 
         'id'=>'frm_manage' 
         ]) !!}
         {{ csrf_field() }}

        <input type="hidden" name="multi_action" value="" />
        <input type="hidden" name="search" id="search" value="" />
        <input type="hidden" name="file_format" id="file_format" value="" />
         <div class="col-md-10">
            <div class="alert alert-danger" id="no_select" style="display:none;"></div>
            <div class="alert alert-warning" id="warning_msg" style="display:none;"></div>
         </div>
         <div class="row">
              <div class="col-md-6">
                   <div class="form-group">
                        <div class="row">
                        <label class="col-sm-3 col-lg-4 control-label">{{translation('level')}}</label>
                        <div class="col-sm-9 col-lg-8 controls">
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
              </div> 
              <div class="col-md-6">
                   <div class="form-group">
                        <div class="row">
                        <label class="col-sm-3 col-lg-4 control-label">{{translation('class')}}</label>
                        <div class="col-sm-9 col-lg-8 controls">
                           <select name="class" id="class" class="form-control" data-rule-required="true">
                              <option value="">{{translation('select_class')}}</option>
                          </select>
                        </div>
                       </div>
                    </div>
              </div>
            <div class="col-md-6">
              <div class="form-group">
                <div class="row">
                <label class="col-sm-3 col-lg-4 control-label">{{translation('start_date')}}</label>
                <div class="col-sm-9 col-lg-8 controls">
                  <input type="text" name="start_date"  id="datepicker" class="form-control datepikr" data-rule-required='true' value="" data-rule-date="true" readonly style="cursor: pointer;">
                  <span class="help-block">{{ $errors->first('start_date') }}</span>
                </div>
                  </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <div class="row">
                <label class="col-sm-3 col-lg-4 control-label">{{translation('end_date')}}</label>
                <div class="col-sm-9 col-lg-8 controls">
                  <input type="text" name="end_date"  id="datepicker2" class="form-control datepikr" data-rule-required='true' value="" data-rule-date="true" readonly style="cursor: pointer;">
                  <span class="help-block">{{ $errors->first('end_date') }}</span>
                  <span id="err_end_date" style="color: red"></span>
                </div>
                  </div>
              </div>
            </div>

            <div class="col-sm-12 col-md-12 col-lg-12" >
              <div class="form-group">
                <div class="row">
                <div class="col-sm-4 col-md-4 col-lg-2 col-lg-offset-2">
                  <div id="button">
                    <input type="button" name="show" id="show" value="{{translation('show')}}" class="form-control btn btn-primary" onClick="getRecords();"> 
                  </div>
                </div>
                  </div>
              </div>
            </div>
         </div>
         <br/>
         <div class="clearfix"></div>
         <div class="table-responsive  attendance-create-table-section" style="border:0">
            <input type="hidden" name="multi_action" value="" />
            <table class="table table-advance"  id="table_module">
               <thead>
                  <tr>
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('name')}} </a><br/></th>
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('id')}} </a><br/></th>
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('total')}} </a><br/></th>
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('total_percentage')}}</a></th>
                  </tr>
               </thead>
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
         todayHighlight: true,
            autoclose: true,
            format:'yyyy-mm-dd'
      });

      $("#datepicker2").datepicker({
          todayHighlight: true,
            autoclose: true,
            format:'yyyy-mm-dd'
      });
      $("#datepicker").on('change',function(){
        var newdate = $("#datepicker").val();
        $("#datepicker2").datepicker('setStartDate',newdate);
      })
    });
</script>

 <script type="text/javascript">


      function getRecords()
      {
          $('#button').attr('disabled', true);
          $('#loader').fadeIn('slow');
          $('body').addClass('loader-active');  
          $("#show").hide();
          $('#err_end_date').text(''); 
          var date  = $('#datepicker').val();
          var date2 = $('#datepicker2').val();
          var level = $('#level').val();
          var cls   = $('#class').val();
          $('#table_module').empty();
          $.ajax({
                url  :"{{ $module_url_path }}/getRecords/student",
                type :'POST',
                data :{'start_date':date ,'end_date':date2 ,'level':level,'class':cls,'_token':'<?php echo csrf_token();?>'},
                success:function(data){

                    $('#loader').hide();
                    $('body').removeClass('loader-active');
                    $('#button').attr('disabled','none');

                    $("#show").show();
                    $('#table_module').append(data);

                    table_module = $('#table_module').DataTable({
                          <?php  if(Session::get('locale') == 'fr'){ ?>  
                               language: {
                                 "sProcessing": "Traitement en cours ...",
                                 "sLengthMenu": "Afficher _MENU_ lignes",
                                 "sZeroRecords": "Aucun résultat trouvé",
                                 "sEmptyTable": "Aucune donnée disponible",
                                 "sInfo": "Lignes _START_ à _END_ sur _TOTAL_",
                                 "sInfoEmpty": "Aucune ligne affichée",
                                 "sInfoFiltered": "(Filtrer un maximum de_MAX_)",
                                 "sInfoPostFix": "",
                                 "sSearch": "Chercher:",
                                 "sUrl": "",
                                 "sInfoThousands": ",",
                                 "sLoadingRecords": "Chargement...",
                                 "oPaginate": {
                                   "sFirst": "Premier", "sLast": "Dernier", "sNext": "Suivant", "sPrevious": "Précédent"
                                   },
                                 "oAria": {
                                   "sSortAscending": ": Trier par ordre croissant", "sSortDescending": ": Trier par ordre décroissant"
                                   }
                               } ,
                          <?php } ?>
                          processing: true,
                          bFilter: false,
                          ordering:false,
                    });
                    $.fn.dataTable.ext.errMode = 'none';
                }
              });
      }
      
     function getClasses()
      {
          var level   =   $('#level').val();
          if(level != '')
          {
            $('#loader').fadeIn('slow');
            $('body').addClass('loader-active');
            
            $('#class').empty();
             $.ajax({
                    url  :"{{ $module_url_path }}/getClasses",
                    type :'POST',
                    data :{'level':level ,'_token':'<?php echo csrf_token();?>'},
                    success:function(data){
                      if(data!=''){
                        $('#class').append(data);
                        $('#loader').hide();
                        $('body').removeClass('loader-active');  
                      }
                      
                    }
                  });
          }
      }


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
<!-- END Main Content -->
@stop