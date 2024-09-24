@extends('schooladmin.layout.master')                
@section('main_content')
<style>
.user-td{
  position:relative;
}
</style>
<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
      <li>
          <i class="fa fa-home"></i>
          <a href="{{ url($school_admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
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
<link rel="stylesheet" type="text/css" href="" ref="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
 <div class="page-title new-agetitle">
    <div>
        <h1><i class="{{$module_icon}}"></i>{{str_plural($module_title)}}</h1>
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
            @if(array_key_exists('exam.create', $arr_current_user_access))
              <a href="{{$module_url_path.'/download/xls'}}" title="{{translation('download_csv_format')}}"><i class="fa fa-file-excel-o"></i> {{translation('download')}} </a>
            @endif
            @if(array_key_exists('exam.create', $arr_current_user_access))
              <a href="{{$module_url_path}}/create" >{{translation('add')}} {{translation('exam')}}</a> 
            @endif  

            @if(array_key_exists('exam.delete', $arr_current_user_access))     
               <a title="{{translation('multiple_delete')}}" 
                  href="javascript:void(0);" 
                  onclick='javascript : return check_multi_action("frm_manage","{{translation('are_you_sure')}}","{{translation('do_you_really_want_to_delete_this_record').'?'}}","{{translation('yes')}}","{{translation('no')}}","{{translation('oops')}}","{{translation('please_select_the_record_to_perform_this_action')}}","delete");'
                  style="text-decoration:none;">
               <i class="fa fa-trash-o">
               </i>
               </a>
             @endif  
               <a 
                  title="{{translation('refresh')}}" 
                  href="{{ $module_url_path }}"
                  style="text-decoration:none;">
               <i class="fa fa-repeat"></i>
               </a> 
               
            </div>
      </div>
      <div class="box-content">
         @include('admin.layout._operation_status')  
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
                     @if(array_key_exists('exam.update', $arr_current_user_access) || array_key_exists('exam.delete', $arr_current_user_access))                             
                     <th style="width: 18px; vertical-align: initial;">
                        <div class="check-box">
                            <input type="checkbox" class="filled-in" name="selectall" id="selectall" />
                            <label for="selectall"></label>
                        </div>
                     </th>
                     @endif
                     <th>{{translation('exam_number')}}</th>
                     <th>{{translation('level')}}</th>
                     <th>{{translation('class')}}</th>
                     <th>{{translation('exam_period')}}</th>
                     <th>{{translation('exam_type')}}</th>
                     <th>{{translation('course')}}</th>
                     <th>{{translation('exam_time')}}</th>
                     <th>{{translation('status')}}</th>
                     <th>{{translation('action')}}</th>
                  </tr>
               </thead>
            </table>
         </div>
         {!! Form::close() !!}
      </div>
   </div>
</div>
</div>
<div id="import_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{translation("import")}}</h4>
      </div>
      <div class="modal-body">

      <form id="form-avlidation1" method="post" action="{{ $module_url_path.'/upload' }}"   enctype="multipart/form-data">  
      {{csrf_field()}}
      
         <div class="upload-section-block">
                  <div class="main-col-block">
                     <div class="form-group">
                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">{{translation('document')}}</label>
                        <div class="col-sm-8 col-md-8 col-lg-10 controls input-group-block">
                                                                      
                           <div class="upload-block-clone">
                              <input type="file" id="pdffile_0" class="hidden-input-block" name="upload_file" onchange="Changefilename(this)" >
                              <div class="input-group">
                                 <input type="text" data-rule-required="true" class="form-control file-caption  kv-fileinput-caption" id="subfile" readonly />
                                 <div class="btn btn-primary btn-file" ><a class="file" onclick="$('#pdffile_0').click();">Browse...</a></div>
                              </div>
                           </div>
                           <span class="help-block" id="err_file"></span>
                           <input type="hidden" id="exam_id" name="exam_id">
                        </div>
                     </div>
                     <div class="clearfix"></div>
                  </div>
                  <div id="document_url" class="main-col-block">
                  </div>
                  <div class="clearfix"></div>
               </div>
        
      <div class="modal-footer bulk-import-form-footer-section">
      
      <div style="float:right">
               <div class="form-group back-btn-form-block" style="display:inline-block">
                  <div class="controls">
                     <button type="button" class="btn btn-primary" data-dismiss="modal" style="float: right;margin-top: 20px;" >{{translation('cancel')}} </button>
                  </div>
               </div>
               <div class="form-group back-btn-form-block" style="display:inline-block">
                  <div class="controls">
                    <button class="btn btn-primary" type="button" id="upload_button" style="float: right;margin-top: 20px;"> {{translation("upload")}}</button>
                     
                  </div>
               </div>
            </div>
      </div>

      </form>
    </div>

  </div>
</div>
</div>
<script>

function setID(id){
  $("#exam_id").val(id);
}

$("#upload_button").click(function(){
  var file_name = $("#subfile").val();

    if(file_name){
      $("#form-avlidation1").submit();
      $("#err_file").text("");
    }
    else{
      $("#err_file").text("{{translation("this_field_is_required")}}");
    }
});

function Changefilename(event){
                      var file = event.files;
                      name = file[0].name;
                      validateDocument(file,'Doc',null);
                      $(event).next().children('input').val(name);
                  }
$(document).on("change",".validateDoc", function()
 {       
    var file = this.files;
    validateDocument(this.files,'Doc',null);

 });                  
function validateDocument(files,type,element_id) 
 {
    //var default_img_path = site_url+'/front/images/uploadimg.png';

    if (typeof files !== "undefined") 
    {
      for (var i=0, l=files.length; i<l; i++) 
      {
            var blnValid = false;
            var ext = files[i]['name'].substring(files[i]['name'].lastIndexOf('.') + 1);
            if(type=='Doc')
            {
                if(ext=='xlsx' || ext=='xls' || ext=='xltm' || ext=='xltx' || ext=='xlsm')
                {
                    blnValid = true; 
                }  
            }
            else
            {
                if(ext=='xlsx' || ext=='xls' || ext=='xltm' || ext=='xltx' || ext=='xlsm')
                {
                      blnValid = true;
                }  
            }
            
            if(blnValid ==false) 
            {
              if(type=='Doc')
              {
                showAlert("Sorry, " + files[0]['name'] + " is invalid, allowed extensions are: Pdf,Doc","error");
              }
              else
              {
                showAlert("Sorry, " + files[0]['name'] + " is invalid, allowed extensions are: xls,xlsx,xltm,xltx,xlsm","error");
              }
                return false;
            }
            else
            {              
                if(type=='Doc')
                {
                  if(files[0].size>10485760)
                  {
                     showAlert("File size should be less than 10 MB","error");
                  }
                }       
            }                
        }
    }
    else
    {
      showAlert("No support for the File API in this web browser" ,"error");
    } 
  }
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
       bFilter: true,
       ordering: false,
       ajax: {
       'url':'{{ $module_url_path.'/get_records'}}',
       'data': function(d)
         {
        
         }
       },
    
       columns: [
       @if(array_key_exists('exam.update',$arr_current_user_access) || array_key_exists('exam.delete',$arr_current_user_access) )
       {
         render : function(data, type, row, meta) 
         {
           return row.build_checkbox;
           $(this).addClass('user-td');
   
         },
         "orderable": false, "searchable":false
       },
       @endif
       {data: 'exam_no', "orderable": false, "searchable":false},
       {data: 'level_name', "orderable": false, "searchable":false},
       {data: 'class_name', "orderable": false, "searchable":false},
       {data: 'exam_name', "orderable": false, "searchable":false},
       {data: 'exam_type', "orderable": false, "searchable":false},
       {data: 'course_name', "orderable": false, "searchable":false},
       {
         render : function(data, type, row, meta) 
         {
           return row.exam_time;
   
         },
         "orderable": false, "searchable":false
       },
       {
         render : function(data, type, row, meta) 
         {
           return row.build_status;
   
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

