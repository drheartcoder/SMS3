@extends('schooladmin.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
      <li>
          <i class="fa fa-home"></i>
          <a href="{{ url($school_admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
      </li>
      <span class="divider">
          <i class="fa fa-angle-right"></i>
          <i class="fa fa-thumbs-up"></i>                
      </span> 
      <li class="active">{{ isset($module_title)?$module_title:"" }}</li>
  </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
<div class="page-title new-agetitle">
    <div>
        <h1><i class="{{$module_icon}}"></i> {{ isset($module_title)?$module_title:"" }}</h1>
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
            <a title="Refresh" 
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
         'id'=>'validation-form1' 
         ]) !!}
         {{ csrf_field() }}
         <div class="col-md-10">
            <div id="ajax_op_status">
            </div>
            <div class="alert alert-danger" id="no_select" style="display:none;"></div>
            <div class="alert alert-warning" id="warning_msg" style="display:none;"></div>
         </div>
         <div class="btn-toolbar pull-right clearfix">
          
            <br>
         </div>
         <br/>
         <div class="clearfix"></div>
         <div class="table-responsive" style="border:0">
            <input type="hidden" name="multi_action" value="" />
            <table class="table table-advance"  id="table_module">
               <thead>
                  <tr>
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('suggestion_subject')}} </a><br />
                     </th>
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('name')}} </a><br />
                     </th>
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('suggestion_from')}}</a><br />
                     </th>
                      <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('suggestion_category')}}</a><br />
                     </th>
                      <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('suggestion_date')}}</a><br />
                     </th>
                    

                     @if($status == 'approved')
                      <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('is_poll_raised')}}</a><br />
                     </th>
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('poll_count')}}</a><br />
                      <div class="school-like-dislike">
                            <a href="javascript:void(0)" class="like-ic-sgn"><i class="fa fa-thumbs-up"></i></a>
                            <a href="javascript:void(0)" class="dilike-ic-sgn"><i class="fa fa-thumbs-down"></i></a>
                        </div>
                     </th>
                     @endif

                     @if($status == 'poll_raised'  || $status == 'polled_requests')
                      <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('poll_duration')}}</a><br />
                     </th>
                     @endif

                      @if($status == 'poll_raised' )
                      <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('poll_count')}}</a><br />
                        <div class="school-like-dislike">
                            <a href="javascript:void(0)" class="like-ic-sgn"><i class="fa fa-thumbs-up"></i></a>
                            <a href="javascript:void(0)" class="dilike-ic-sgn"><i class="fa fa-thumbs-down"></i></a>
                        </div>
                      </th>
                      
                     @endif

                     @if($status == 'requested' || $status == 'poll_raised' || $status =='created' || $status == 'polled_requests')
                      <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('status')}}</a><br />
                     </th>
                     @endif


                     @if(array_key_exists('suggestions.list', $arr_current_user_access))     
                        @if($status == 'requested' || $status == 'approved' || $status =='created' || $status == 'polled_requests')
                          <th>{{translation('action')}}</th>
                        @endif
                     @endif
                  </tr>
               </thead>
            </table>
         </div>


          <!-- Modal -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">{{translation('raise_poll')}}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6 col-md-12 col-lg-12">
                                <div class="row">
                                  <input type="hidden" name="suggestion_id" id="suggestion_id">
                                    <div class="form-group">
                                      <label class="col-sm-6 col-lg-3 control-label">{{translation('user_type')}}</label>
                                      <div class="col-sm-6 col-lg-9">
                                      <div class="row">
                                      <div class="col-sm-4 col-lg-4 col-md-4">
                                        <div class="controls">
                                              <div class="check-box">
                                                <input type="checkbox" class="filled-in case chk" name="user_type[]" id="employee" value="employee" checked/>
                                                <label for="employee">{{translation('employee')}}</label>
                                              </div>                                          
                                              
                                        </div>
                                      </div>
                                      <div class="col-sm-4 col-lg-4 col-md-4">
                                      <div class="controls">
                                              <div class="check-box">
                                                <input type="checkbox" class="filled-in case chk" name="user_type[]" id="professor" value="professor" />
                                                <label for="professor">{{translation('professor')}}</label>
                                              </div>                                          
                                              
                                        </div>
                                      </div>
                                      </div>
                                      <div class="row">
                                      <div class="col-sm-4 col-lg-4 col-md-4">
                                      <div class="controls">
                                              <div class="check-box">
                                                <input type="checkbox" class="filled-in case chk" name="user_type[]" id="student" value="student" />
                                                <label for="student">{{translation('student')}}</label>
                                              </div>                                          
                                              
                                        </div>
                                      </div>
                                   
                                      <div class="col-sm-4 col-lg-4 col-md-4">
                                      <div class="controls">
                                              <div class="check-box">
                                                <input type="checkbox" class="filled-in case chk" name="user_type[]" id="parent" value="parent" />
                                                <label for="parent">{{translation('parent')}}</label>
                                              </div>                                          
                                              
                                        </div>
                                      </div>
                                      </div>
                                    </div>
                                      <span class='help-block err-users'></span>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-6 col-lg-3 control-label">{{translation('poll_duration')}}</label>
                                        <div class="col-sm-6 col-lg-9 controls">
                                            <select name="poll_duration" id="poll_duration" class="form-control" data-rule-required="true" onChange="hideError();">
                                            <option value=''>{{translation('select_duration')}}</option>
                                            <option value="5">5 {{translation('days')}}</option>   
                                            <option value="7">7 {{translation('days')}}</option>   
                                            <option value="10">10 {{translation('days')}}</option>   
                                            <option value="15">15 {{translation('days')}}</option>   
                                            </select>
                                            <span class='help-block'>{{ $errors->first('poll_duration ') }}</span>
                                            <span class='help-block' id="err_duration"></span>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">                        
                        <button type="button" class="btn btn-primary" id="poll_raised">{{translation('raise_poll')}}</button>
                    </div>
                </div>
            </div>
        </div>
        </div>
        {{-- modal --}}
         {!! Form::close() !!}
      </div>
   </div>
</div>

@if(array_key_exists('suggestions.list', $arr_current_user_access))  
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
              'url':'{{ $module_url_path.'/get_records/'.$status}}',
              'data': function(d)
                {
                }
              },
           columns: [
           /*{
              render : function(data, type, row, meta) 
               {
                 return row.build_checkbox;
         
               },
               "orderable": false, "searchable":false
           },*/
          {data: 'subject', "orderable": false, "searchable":false},
          {data: 'user_name', "orderable": false, "searchable":false},
          {data: 'user_role', "orderable": false, "searchable":false},
          {data: 'category', "orderable": false, "searchable":false},
          {data: 'suggestion_date', "orderable": false, "searchable":false},
          
          @if($status == 'approved')
            {data: 'poll_raised', "orderable": false, "searchable":false},
            {data: 'poll_count', "orderable": false, "searchable":false},
          @endif

          @if($status == 'poll_raised'  || $status == 'polled_requests')
            {data: 'duration', "orderable": false, "searchable":false},
          @endif

           @if($status == 'poll_raised' )
            {data: 'poll_count', "orderable": false, "searchable":false},
          @endif
          @if($status == 'requested' || $status == 'poll_raised' || $status =='created' || $status == 'polled_requests')
            {data: 'status', "orderable": false, "searchable":false},
          @endif
          @if($status == 'requested' || $status == 'approved' || $status =='created' || $status == 'polled_requests')
          {
            render : function(data, type, row, meta) 
            {
              return row.build_action_btn;
            },
            "orderable": false, "searchable":false
          }
          @endif
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
 


  function updateStatus(id,status)
  {
    if(status == "POLL_RAISED")
    {
      
      $('#suggestion_id').val(id);
      $('#status_'+id).attr('data-toggle','modal');
      $('#status_'+id).attr('data-target','#myModal');
    }
    else
    {

      $('#loader').fadeIn('slow');
      $('body').addClass('loader-active');
        $.ajax({
                  url  :"{{ $module_url_path }}/change_status",
                  type :'POST',
                  data :{'status': status , 'id' : id , '_token':'<?php echo csrf_token();?>'},
                  success:function(){
                    location.reload();
                  }
                }); 
    }
    
  }

  $('#poll_raised').on('click',function(){
    var users   = [];
    var duration      = $('#poll_duration').val();
    var suggestion_id = $('#suggestion_id').val();

    $(".chk:checked").each(function() {
      users.push($(this).val());
    });

    if(duration == '')
    {
      $('#err_duration').text('this_field_is_required');
    }
    else
    {
      $('#err_duration').hide(''); 
      $('#myModal').hide();
      $('#loader').fadeIn('slow');
      $('body').addClass('loader-active');
      alert('s'+"{{ $module_url_path }}/raise_poll");
      $.ajax({
                url  :"{{ $module_url_path }}/raise_poll",
                type :'POST',
                data :{'duration': duration , 'id' : suggestion_id, 'users' : users , '_token':'<?php echo csrf_token();?>'},
                success:function(){
                  location.reload();
                }
              });
    }
    
  });

  function hideError()
  {
    if(($('#poll_duration').val()) != '')
    {
      $('#err_duration').hide('');  
    }

  }

  function addVote(id,status)
 {
    $('#loader').fadeIn('slow');
    $('body').addClass('loader-active');
    $.ajax({
                url  :"{{ $module_url_path }}/add_vote",
                type :'POST',
                data :{'status': status , 'id' : id , '_token':'<?php echo csrf_token();?>'},
                success:function(){
                  filterData();
                  $('#loader').hide();
                  $('body').removeClass('loader-active');
                }
              }); 
 }
</script>
@stop