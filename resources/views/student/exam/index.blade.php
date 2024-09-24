@extends('student.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
      <li>
          <i class="fa fa-home"></i>
          <a href="{{ url($student_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
      </li>
      
      <span class="divider">
          <i class="fa fa-angle-right"></i>
          <i class="{{$module_icon}}"></i>                
      </span> 
      <li class="active">{{ isset($module_title)?str_plural($module_title):"" }}</li>
  </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
 <div class="page-title new-agetitle">
    <div>
        <h1><li class="fa fa-book"></li> {{$module_title}}</h1>
    </div>
</div>
<!-- END Page Title -->
  
<!-- BEGIN Main Content -->
<div class="row">
<div class="col-md-12">
   <div class="box {{ $theme_color }}">
      <div class="box-title">
         <h3>
            <i class="fa fa-list"></i>
            {{ isset($page_title)?$page_title:"" }}
         </h3>
         <div class="box-tool">   
               <a 
                  title="{{translation('refresh')}}" 
                  href="{{ $module_url_path }}"
                  style="text-decoration:none;">
               <i class="fa fa-repeat"></i>
               </a> 
               
            </div>
      </div>
      <div class="box-content">
         @include('student.layout._operation_status')  
         {!! Form::open([ 'url' => $module_url_path.'/multi_action',
         'method'=>'POST',
         'enctype' =>'multipart/form-data',   
         'class'=>'form-horizontal', 
         'id'=>'frm_manage' 
         ]) !!}
         {{ csrf_field() }}
         <div class="col-md-10">
            <div id="ajax_op_status">
            </div>
            <div class="alert alert-danger" id="no_select" style="display:none;"></div>
            <div class="alert alert-warning" id="warning_msg" style="display:none;"></div>
         </div>
         <div class="btn-toolbar pull-right clearfix">
            </div>
         <div class="clearfix"></div>
         <div class="table-responsive" style="border:0">
            <input type="hidden" name="multi_action" value="" />
            <table class="table table-advance"  id="table_module">
               <thead>
                  <tr>
                     <th>{{translation('exam_number')}} </th>
                     <th>{{translation('exam_period')}} </th>
                     <th>{{translation('exam_type')}}   </th>
                     <th>{{translation('course')}}      </th>
                     <th>{{translation('exam_date')}}   </th>
                     <th>{{translation('exam_time')}}   </th>
                     <th>{{translation('assessment_scale')}}</th>
                     <th>{{translation('result')}}      </th>
                     <th>{{translation('action')}}      </th>

                  </tr>
               </thead>
            </table>
         </div>
         {!! Form::close() !!}
      </div>
   </div>
</div>
</div>
<script type="text/javascript">
   function show_details(url)
   {
     window.location.href = url;
   }
   var table_module = false;
   $(document).ready(function()
   { 
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
       serverSide: true,
       autoWidth: false,
       ordering:false,
       bFilter: true,
       ajax: {
       'url':'{{ $module_url_path.'/get_records'}}',
       'data': function(d)
         {
        
         }
       },
       columns: [
       {data: 'exam_no', "orderable": false, "searchable":false},
       {data: 'exam_name', "orderable": false, "searchable":false},
       {data: 'exam_type', "orderable": false, "searchable":false},
       {data: 'course_name', "orderable": false, "searchable":false},
       {render : function(data, type, row, meta) 
         {
           return row.exam_date;
   
         },
         "orderable": false, "searchable":false
       },
       {
         render : function(data, type, row, meta) 
         {
           return row.exam_time;
   
         },
         "orderable": false, "searchable":false
       },

       {data: 'scale', "orderable": false, "searchable":false},
       {
         render : function(data, type, row, meta) 
         {
           return row.result;
   
         },
         "orderable": false, "searchable":false
       },
       {
         render : function(data, type, row, meta) 
         {
           return row.build_action_btn;
   
         },
         "orderable": false, "searchable":false
       }]
     });
     $('input.column_filter').on( 'keyup click', function () 
     {
         filterData();
     });
   
     $('#table_module').on('draw.dt',function(event)
     {
       var oTable = $('#table_module').dataTable();
       var recordLength = oTable.fnGetData().length;
       $('#record_count').html(recordLength);
     });
      $.fn.dataTable.ext.errMode = 'none';
   });
   
   function filter_result_by_type()
   {
     filterData();
   }
   
   function filterData()
   {
     table_module.draw();
   }
   
</script>
@stop

