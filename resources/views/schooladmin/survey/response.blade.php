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
      </span>
      <li> 
        <i class="{{$module_icon}}"></i>
        <a href="{{$module_url_path}}">{{ isset($module_title)?$module_title:"" }}</a>
      </li>
      <span class="divider">
          <i class="fa fa-angle-right"></i>
          <i class="fa {{$module_icon}}"></i>                
      </span> 
      <li class="active">{{$page_title}}</li>
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
          
          @if(\Session::get('role')!='school_admin')
          @if($give_reply)    
          <a href="{{$module_url_path}}/reply_survey/{{$enc_id}}" >{{translation('reply')}}</a> 
          @endif
          @endif
               <a 
                  title="{{translation('refresh')}}" 
                  href="window.location.reload();"
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
         
        
         <div class="form-group">
           <label class="col-sm-2 col-lg-2 control-label" style="text-align:left"><b> {{translation('title')}}</b>:</label>
           <label class="col-sm-8 col-lg-8 control-label" style="text-align:left">{{$survey_title}}</label>
           <div class="col-sm-1 col-lg-1 dropup-down-uls" style="float:right !important">
                <a href="javascript:void(0)" class="export-lists" ><i class="fa fa-upload"></i> {{ translation('export')}} </a>
                <div class="export-content-links">
                    <div class="li-list-a">
                        <a href="javascript:void(0)" onclick="exportForm('csv');">CSV</a>
                    </div>
                     
                </div>
            </div>
           <div class="clearfix"></div>
        
      </div>
        <input type="hidden" name="file_format" id="file_format" value="" />
        <input type="hidden" name="survey_id" id="survey_id" value="{{$enc_id}}" />
        <div class="form-group">
           <label class="col-sm-2 col-lg-2 control-label" style="text-align:left"><b> {{translation('start_date')}}</b>:</label>
           <label class="col-sm-9 col-lg-9 control-label" style="text-align:left">{{$start_date}}</label>
           <div class="clearfix"></div>
        </div>
        <div class="form-group">
           <label class="col-sm-2 col-lg-2 control-label" style="text-align:left"><b> {{translation('end_date')}}</b>:</label>
           <label class="col-sm-9 col-lg-9 control-label" style="text-align:left">{{$end_date}}</label>
           <div class="clearfix"></div>
        </div>

         <div class="col-md-10">
            <div class="alert alert-danger" id="no_select" style="display:none;"></div>
            <div class="alert alert-warning" id="warning_msg" style="display:none;"></div>
         </div>
          


         <br/>
         <div class="clearfix"></div>
         <div class="table-responsive" style="border:0">
            <input type="hidden" name="multi_action" value="" />
            <table class="table table-advance"  id="table_module">
               <thead>
                  <tr>
                     <th><a  class="sort-descs" href="#" >{{translation('user_name')}} </a></th>
                     <th><a  class="sort-descs" href="#" >{{translation('user_type')}} </a></th>
                     <th><a  class="sort-descs" href="#" >{{translation('response_date')}} </a></th>
                     <th><a  class="sort-descs" href="#" >{{translation('action')}}</a></th>
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

      /*Script to show table data*/
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
              bFilter: true,
              ordering:false,
              ajax: {
              'url':'{{ $module_url_path.'/get_response_records/'.$enc_id}}',
              },
           columns: [
           
          {data: 'user_name', "orderable": false, "searchable":false},
          {data: 'role', "orderable": false, "searchable":false},
          {data: 'created_at', "orderable": false, "searchable":false},
          {
              render : function(data, type, row, meta) 
               {
                 return row.build_action_btn;
         
               },
               "orderable": false, "searchable":false
           }
          ]
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
 </script> 

<!-- END Main Content -->
<script type="text/javascript">
  function show_details(url)
  {  
      window.location.href = url;
  }
 function filterData()
  {
    table_module.draw();
  }
</script>
<!-- To Export The Data -->
<script type="text/javascript">
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
<!-- To Export The Data -->
@stop