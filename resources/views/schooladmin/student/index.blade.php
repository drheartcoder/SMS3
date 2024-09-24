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
        <h1><i class="{{$module_icon}}"></i>{{$page_title}}</h1>
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
            {{ isset($module_title)?$module_title:"" }}
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
            @if(array_key_exists('student.update', $arr_current_user_access))  
            <a title="{{translation('promote')}}" 
                href="javascript:void(0);" 
                onclick='javascript : return check_multi_action("frm_manage","{{translation('are_you_sure')}}","{{translation('do_you_really_want_to_promote_these_students').'?'}}","{{translation('yes')}}","{{translation('no')}}","{{translation('oops')}}","{{translation('please_select_the_record_to_perform_this_action')}}","promote");'
                style="text-decoration:none;"><i class="fa fa-graduation-cap"></i>{{translation('promote')}} </a>
            @endif    
            @if(array_key_exists('student.update', $arr_current_user_access))  
            <a title="{{translation('multiple_activate')}}" 
                href="javascript:void(0);" 
                onclick='javascript : return check_multi_action("frm_manage","{{translation('are_you_sure')}}","{{translation('do_you_really_want_to_activate_this_record').'?'}}","{{translation('yes')}}","{{translation('no')}}","{{translation('oops')}}","{{translation('please_select_the_record_to_perform_this_action')}}","activate");'
                style="text-decoration:none;">
               <i class="fa fa-unlock"></i>
            </a> 
            <a title="{{translation('multiple_deactivate')}}" 
              href="javascript:void(0);" 
              onclick='javascript : return check_multi_action("frm_manage","{{translation('are_you_sure')}}","{{translation('do_you_really_want_to_deactivate_this_record').'?'}}","{{translation('yes')}}","{{translation('no')}}","{{translation('oops')}}","{{translation('please_select_the_record_to_perform_this_action')}}","deactivate");'
              style="text-decoration:none;">
            <i class="fa fa-lock"></i>
            </a> 
            @endif
            @if(array_key_exists('student.delete', $arr_current_user_access))     
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
         @include('schooladmin.layout._operation_status')  
         {!! Form::open([ 'url' => $module_url_path.'/multi_action',
         'method'=>'POST',
         'enctype' =>'multipart/form-data',   
         'class'=>'form-horizontal', 
         'id'=>'frm_manage' 
         ]) !!}
         {{ csrf_field() }}
         <div class="col-md-12 ajax_messages">
            <div class="alert alert-danger" id="error" style="display:none;"></div>
            <div class="alert alert-success" id="success" style="display:none;"></div>
         </div>
         <div class="btn-toolbar pull-right clearfix">
            @if($role == 'school_admin')
            <div class="btn-group">
               
            </div>
            @endif
            <div class="btn-group"> 
               
            </div>
            <br>
         </div>
         <br/>
         <div class="clearfix"></div>
         <div class="table-responsive" style="border:0">
            <input type="hidden" name="multi_action" value="" />
            <input type="hidden" name="search" id="search" value="" />
            <input type="hidden" name="file_format" id="file_format" value="" />
            <table class="table table-advance"  id="table_module">
               <thead>
                  <tr>
                    @if(array_key_exists('student.update', $arr_current_user_access) || array_key_exists('student.delete', $arr_current_user_access) )                             
                     <th style="width: 18px; vertical-align: initial;">
                        <div class="check-box">
                            <input type="checkbox" class="filled-in" name="selectall" id="selectall" />
                            <label for="selectall"></label>
                        </div>
                     </th>
                     @endif
                     <!--  <th>Sr no</th>  --> 
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('name')}} </a><br />
                        
                     </th>
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('email')}} </a><br />
                        
                     </th>
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('national_id')}} </a><br />
                        
                     </th>
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('level')}} </a><br />
                        
                     </th>
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('class')}} </a><br />
                        
                     </th>
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('has_left')}} </a><br />

                     </th>
                         
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
@if(array_key_exists('student.list', $arr_current_user_access))  
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
              'url':'{{ $module_url_path.'/get_records/'.$role}}',
              'data': function(d)
                {
                 
                }
              },
           columns: [
            @if(array_key_exists('student.update', $arr_current_user_access) || array_key_exists('student.delete', $arr_current_user_access) )
           {
              render : function(data, type, row, meta) 
               {
                 return row.build_checkbox;
         
               },
               "orderable": false, "searchable":false
           },
           @endif
          {data: 'user_name', "orderable": true, "searchable":false},
          {data: 'email', "orderable": true, "searchable":false}, 
          {data: 'national_id', "orderable": true, "searchable":false}, 
          {data: 'level_name', "orderable": true, "searchable":false},
          {data: 'class_name', "orderable": true, "searchable":false},
          {
              render : function(data, type, row, meta) 
              {
                return row.has_left;
              },
              "orderable": false, "searchable":false
          },
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
@endif
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
  function exportForm()
  {
    var serialize_form   = $("#frm_manage").serialize();
    window.location.href = '{{ $module_url_path }}/export?'+serialize_form+'&export=true';
  }
  $(document).on("change","[type='search']",function(){
      var search_hidden = $(this).val();
      document.getElementById('search').value = search_hidden;
   });
</script>
<!-- To Export The Data -->
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

  function checkExistance(user_id,id)
  {
      $.ajax({
                url  :"{{ $module_url_path }}/check_existance",
                type :'POST',
                data :{'user_id':user_id ,'_token':'<?php echo csrf_token();?>'},
                success:function(data){

                  if (data == 'true')
                  {
                    swal({
                          title: "{{translation('alert')}}",
                          text: 'this user already registered with another school',
                          icon: "warning",
                          confirmButtonText: '{{translation('ok')}}',
                          closeOnConfirm: true,
                          dangerMode: true,
                        });
                  }
                  else if(data == 'false')
                  {
                    swal({
                          title: "{{translation('warning')}}",
                          text: "{{translation('do_you_really_wanted_to_reassign_this_user_to_school')}}?\n {{translation('are_you_sure')}}",
                          icon: "warning",
                          confirmButtonText: '{{translation('yes')}}',
                          closeOnConfirm: true,
                          dangerMode: true,
                          showCancelButton: true,
                          cancelButtonText: '{{translation('no')}}',
                          },
                          function(isConfirm)
                          {

                            if(isConfirm)
                            {
                              $('#loader').show();
                              $('body').addClass('loader-active');  
                              $.ajax({
                                            url  :"{{$module_url_path}}/not_left",
                                            type :'POST',
                                            data :{'enc_id':id,'_token':'<?php echo csrf_token();?>'},
                                            success:function(data){
                                              if(data.status =='success')
                                              {
                                                location.reload(true);
                                              }
                                              else
                                              {
                                                $('.ajax_messages').show();
                                                $('#success').css('display','none');
                                                $('#success').text('');
                                                $('#error').css('display','block');
                                                $('#error').text(data.msg);
                                                setTimeout(function(){
                                                    $('.ajax_messages').hide();
                                                }, 3000);
                                              }
                                            }
                                      });
                            }
                            else
                            {
                              location.reload(true);
                            }
                          });
                  }
                }
            });
  }
</script>
<!-- To Export The Data -->
@stop