@extends('schooladmin.layout.master')                
@section('main_content')

<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
      <li>
          <i class="fa fa-home"></i>
          <a href="{{ url($school_admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
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
        <h1><i class="{{$module_icon}}"></i>{{$module_title}}</h1>
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
          @if($role == 'student')
            <a href="{{$module_url_path}}/view/{{$role}}" >{{translation('view')}} {{translation('attendance')}}
          @endif
            </a><a href="javascript:void(0)"><i class="fa fa-upload"></i> Export </a>
               <a class="icon-btns-block" 
                  title="{{translation('refresh')}}" 
                  href="{{ $module_url_path.'/professor' }}"
                  style="text-decoration:none;">
               <i class="fa fa-repeat"></i>
               </a> 
         </div>
      </div>
      <div class="box-content">
         @include('schooladmin.layout._operation_status')  
         {!! Form::open([ 'url' => $module_url_path.'/multi_action',
         'method'=>'POST',
         'enctype' =>'multipart/form-data',   
         'class'=>'form-horizontal', 
         'id'=>'frm_manage' 
         ]) !!}
         {{ csrf_field() }}
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
                  <input type="text" name="start_date" placeholder="{{translation('select_start_date')}}" id="datepicker" class="form-control datepikr" data-rule-required='true' value="" data-rule-date="true" readonly style="cursor: pointer;">
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
                  <input type="text" name="end_date" placeholder="{{translation('select_end_date')}}"  id="datepicker2" class="form-control datepikr" data-rule-required='true' value="" data-rule-date="true" readonly style="cursor: pointer;">
                  <span class="help-block">{{ $errors->first('end_date') }}</span>
                  <span id="err_end_date" style="color: red"></span>
                </div>
                  </div>
              </div>
            </div>

            <div class="col-sm-12 col-md-12 col-lg-12" >
              <div class="form-group">
                <div class="row">
                <div class="col-sm-4 col-lg-2 col-lg-offset-2">
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
         
         <div class="table-responsive" style="border:0">
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
          todayHighlight: true,
          autoclose:true,
          startDate: "{{\Session::get('start_date')}}",
          endDate: today,
          todayHighlight: true
         
      });

      $("#datepicker2").datepicker({
          format:'yyyy-mm-dd',
          todayHighlight: true
      });

      $("#datepicker").on('change',function(){
          var newdate = $("#datepicker").val();
          $('#datepicker2').datepicker('setStartDate',newdate);

      })

      /*$('#datepicker').val(today);
      $('#datepicker2').val(today);*/
    });
</script>
 
 <script type="text/javascript">


      function getRecords()
      {

        var startDate = new Date($('#datepicker').val());

        var endDate = new Date($('#datepicker2').val());

        if(startDate > endDate)
        {
          $('#err_end_date').text('End date must be greater than start date');
        }
        else
        {
          $('#loader').show();
          $('body').addClass('loader-active');          
          $('#button').attr('disabled', true);

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
                    $('#table_module').append(data);
                    
                    $('#loader').hide();
                    $('body').removeClass('loader-active');
                    $('#button').attr('disabled', false);

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
 </script> 
<!-- END Main Content -->

@stop