@extends('schooladmin.layout.master')                
@section('main_content')

<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
<style>
.chosen-container-single .chosen-single div b{width: 9px;background: url(select-arrow.png) no-repeat 100% 11px;height: 22px;}
    .blackdivs{ width: 20.7%;}
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
          <i class="fa {{$module_icon}}"></i>                
      </span> 
      <li>
        <a href="{{ url($school_admin_panel_slug.'/library/manage_library_contents') }}">{{ isset($module_title)?$module_title:"" }}</a></li>
      <span class="divider">
          <i class="fa fa-angle-right"></i>
          <i class="fa {{$module_icon}}"></i>                
      </span> 
      <li class="active">{{translation('issue_book')}}</li>
  </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="{{$module_icon}}"></i> {{str_plural(translation('issue_book'))}}</h1>
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
            {{translation('manage')}} {{str_plural(translation('issue_book'))}}
         </h3>
         <div class="box-tool">  
               <a 
                  title="{{translation('refresh')}}" 
                  href="{{ $module_url_path.'/issue_book'}}"
                  style="text-decoration:none;">
               <i class="fa fa-repeat"></i>
               </a> 
         </div>
      </div>
      <div class="box-content">
         @include('schooladmin.layout._operation_status')  
         {!! Form::open([ 'url' => $module_url_path.'/store_issue_book',
         'method'=>'POST',
         'enctype' =>'multipart/form-data',   
         'class'=>'form-horizontal', 
         'id'=>'validation-form1'
         ]) !!}
         {{ csrf_field() }}
         <div class="col-md-10">
            <div class="alert alert-danger" id="no_select" style="display:none;"></div>
            <div class="alert alert-warning" id="warning_msg" style="display:none;"></div>
         </div>
         <br/>
         <div class="clearfix"></div>
         <div class="table-responsive" style="border:0">
            <input type="hidden" name="multi_action" value="" />
            @if(isset($year) && !empty($year))
              <input type="hidden" name="start_date" id="start_date" value="{{$year->start_date}}" />
              <input type="hidden" name="end_date" id="end_date" value="{{$year->end_date}}" />
            @endif
            <table class="table table-advance"  id="table_module">
               <thead>
                  <tr>
                      
                      <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('number')}} </a></th>
                      <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('title')}} </a></th>
                      <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('author')}} </a></th>
                      <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('available')}} </a></th>
                      <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('status')}} </a>
                     </th>    
                      @if(array_key_exists('library.update', $arr_current_user_access))
                      <th>{{translation('action')}}</th>
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
                        <h4 class="modal-title" id="myModalLabel">{{translation('issue_book')}}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                          <input type="hidden" name="book_id" id="book_id">
                           
                            <div class="form-group">
                                        <label class="col-sm-4 col-md-2 col-lg-3 control-label">{{translation('user_type')}}</label>
                                        <div class="col-sm-9 col-md-8 col-lg-6 controls">

                                          <select name="user" id="user_type" class="form-control" data-rule-required="true">
                                              <option value="">
                                                {{translation('select')}}
                                              </option>  

                                              <option value="{{config('app.project.role_slug.employee_role_slug')}}">{{translation('employee')}}</option>
                                              <option value="{{config('app.project.role_slug.professor_role_slug')}}">{{translation('professor')}}</option>
                                              <option value="{{config('app.project.role_slug.student_role_slug')}}">{{translation('student')}}</option>
                                              <option value="{{config('app.project.role_slug.parent_role_slug')}}">{{translation('parent')}}</option>
                                          </select>
                                          <span class='help-block'>{{ $errors->first('user') }}
                                           </span>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                           
                           {{--  <div class="col-sm-6 col-md-6 col-lg-6">
                                <div class="row"> --}}
                                    <div class="form-group">
                                        <label class="col-sm-4 col-md-2 col-lg-3 control-label">{{translation('user_name')}}</label>
                                        <div class="col-sm-9 col-md-8 col-lg-6 controls">
                                          <select name="user_name" id="user_name" class="form-control chosen" onChange="getUserNo();" >
                                             <option value="">{{translation('select')}} {{translation('user')}}</option>
                                          </select>
                                         
                                            <span id="err_start_date" style="color: red"></span>
                                        </div>
                                        <div class="clearfix"></div>
                                       {{--  <div class="usr-or-num">OR</div> --}}
                                    </div>
                                    {{-- </div>
                                </div>    --}} 
                                 {{-- <div class="col-sm-6 col-md-6 col-lg-6">
                                  <div class="row"> --}}
                                    <div class="form-group">
                                        <label class="col-sm-4 col-md-2 col-lg-3 control-label ">{{translation('user_no')}}</label>
                                        <div class="col-sm-9 col-md-8 col-lg-6 controls">
                                            <input class="form-control" name="user_id" id="user_id" type="text" placeholder="{{translation('user_no')}}" readonly />
                                            <span class='help-block'>{{ $errors->first('user_id') }}
                                              
                                           </span>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    {{-- </div>
                                </div> --}}
                               {{-- <div class="col-sm-6 col-md-6 col-lg-6">
                                <div class="row"> --}} 
                                    <div class="form-group">
                                        <label class="col-sm-4 col-md-2 col-lg-3 control-label ">{{translation('issue_date')}}</label>
                                        <div class="col-sm-9 col-md-8 col-lg-6 controls">
                                            <input class="form-control datepikr" name="issue_date" id="datepicker" placeholder="{{translation('issue_date')}}" type="text" data-rule-required="true" data-rule-date="true" readonly style="cursor: pointer;" />
                                            <span class='help-block'>{{ $errors->first('issue_date') }}</span>
                                            <span id="err_start_date" style="color: red"></span>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                   {{--  </div>
                                </div>
                              
                                   <div class="col-sm-6 col-md-6 col-lg-6">
                                        <div class="row"> --}}
                                            <div class="form-group">
                                                <label class="col-sm-4 col-md-2 col-lg-3 control-label ">{{translation('due_date')}}</label>
                                                <div class="col-sm-9 col-md-8 col-lg-6 controls">
                                                    <input class="form-control datepikr" name="due_date" id="datepicker2" placeholder="{{translation('due_date')}}" type="text" data-rule-required="true" data-rule-date="true" readonly style="cursor: pointer;"/>
                                                    <span class='help-block'>{{ $errors->first('due_date') }}</span>
                                                    <span id="err_end_date" style="color: red"></span>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                       {{--  </div>
                                    </div> --}}
                        </div>
                    </div>
                    <div class="modal-footer">                        
                        <button type="submit" class="btn btn-primary">{{translation('issue_book')}}</button>
                    </div>
                </div>
            </div>
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
              'url':'{{ $module_url_path.'/get_records/status=true'}}',
              'data': function(d)
                {
                }
              },
           columns: [
          {data: 'book_no', "orderable": false, "searchable":false},
          {data: 'title', "orderable": false, "searchable":false},
          {data: 'get_author', "orderable": false, "searchable":false},
          {data: 'available_books', "orderable": false, "searchable":false},
          {data: 'availability', "orderable": false, "searchable":false},
          @if(array_key_exists('library.update', $arr_current_user_access))
          {
            render : function(data, type, row, meta) 
            {
              return row.issue_book;
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

<script>

    $(function () {
        $("#datepicker").datepicker({
            todayHighlight: true,
            autoclose: true,
            format:'yyyy-mm-dd',
            endDate: "{{\Session::get('end_date')}}",
            startDate: "{{\Session::get('start_date')}}"
        });
        $("#datepicker2").datepicker({
            todayHighlight: true,
            autoclose: true,
            format:'yyyy-mm-dd',
            endDate: "{{\Session::get('end_date')}}",
            startDate: "{{\Session::get('start_date')}}"
        });
    });

    $("#datepicker").on('blur',function(){

      var current    = new Date($('#datepicker').val());
      var start_date = new Date($('#start_date').val());
      if(current < start_date)
      {
        $('#err_start_date').text('issue date must be in current academic year');
        return false;
      }
      else
      {
        $('#err_start_date').text('');
      }

    });

    $("#datepicker2").on('blur',function(){

      var current    = new Date($('#datepicker2').val());
      var end_date = new Date($('#end_date').val());
      if(current > end_date)
      {
        $('#err_end_date').text('due date must be in current academic year');
        return false;
      }
      else
      {
        $('#err_end_date').text('');
      }

    });

  $("#validation-form1").on('submit',function(){
     
      var startDate = new Date($('#datepicker').val());
      var start_date = new Date($('#start_date').val());
      var end_date = new Date($('#end_date').val());
      var endDate = new Date($('#datepicker2').val());

      if(startDate >= endDate)
      {
        $('#err_end_date').text('due date must be greater than issue date');
        return false; 
      }
      else
      {
        $('#err_end_date').text(''); 
        
      }

      if(startDate < start_date)
      {
        $('#err_start_date').text('issue date must be in current academic year');
        return false;
      }
      else
      {
        $('#err_start_date').text('');
      }

      if(endDate > end_date)
      {
        $('#err_end_date').text('due date must be in current academic year');
        return false;
      }
      else
      {
        $('#err_end_date').text('');
      }  

  });
</script>
<script>
  function addId(id)
  {
    console.log(id);
    $('#book_id').val(id);
  }

  $('#user_type').on('change',function(){
    var role  = $('#user_type').val();
    $.ajax({
                  url:"{{$module_url_path.'/get_users'}}",
                  type:'POST',
                  data:{'role':role,'_token':'<?php echo csrf_token();?>'},           
                    success:function(data)
                    {
                      $('#user_name').empty();
                      $('#user_name').append(data);
                      $("#user_name").trigger("chosen:updated");
                    }

          });
  });

  function getUserNo()
  {
    var user_id = $('#user_name').val();
    var role    = $('#user_type').val();
    $.ajax({
                  url:"{{$module_url_path.'/get_user_no'}}",
                  type:'POST',
                  data:{'user_id':user_id,'user_type':role,'_token':'<?php echo csrf_token();?>'},           
                    success:function(data)
                    {
                      $('#user_id').val(data);
                    }

          });

  }


</script>

@stop