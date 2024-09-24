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
        <h1><i class="fa {{$module_icon}}"></i>{{$module_title}}</h1>
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
            @if(array_key_exists('canteen.create', $arr_current_user_access))
            <a href="{{$module_url_path}}/create" >{{translation('add')}} {{translation('weekly_meal')}}</a> 
            @endif
            @if(array_key_exists('canteen.delete', $arr_current_user_access))     
               <a title="{{translation('multiple_delete')}}" 
                  href="javascript:void(0);" 
                  onclick='javascript : return check_multi_action("frm_manage","{{translation('are_you_sure')}}","{{translation('do_you_really_want_to_delete_this_record').'?'}}","{{translation('yes')}}","{{translation('no')}}","{{translation('oops')}}","{{translation('please_select_the_record_to_perform_this_action')}}","delete");'
                  style="text-decoration:none;">
               <i class="fa fa-trash-o">
               </i>
               </a>

               <a 
                  title="{{translation('refresh')}}" 
                  href="{{ $module_url_path }}"
                  style="text-decoration:none;">
               <i class="fa fa-repeat"></i>
               </a> 
               @endif
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
            <div class="alert alert-success" id="success" style="display:none;">
            </div>
            <div class="alert alert-danger" id="error" style="display:none;">
            </div>
         </div>

         <div class="row">

            <div class="form-group">
              <div class="col-sm-9 col-md-8 col-lg-4 controls">
                <select name="q_day" class="form-control column_filter">
                  <option>{{translation('select')}} {{translation('day')}}</option>
                  @if(isset($arr_days) && count($arr_days)>0)
                    @foreach($arr_days as $key => $day)
                      <option value="{{isset($day)?$day:'-'}}" @if($day == 'Monday') selected @endif>{{isset($day)?$day:'-'}}</option>
                    @endforeach
                  @endif
                </select>
              </div>
            </div>
          </div>
         
         <div class="clearfix"></div>
         <div class="table-responsive" style="border:0">
            <input type="hidden" name="multi_action" value="" />
            <table class="table table-advance"  id="table_module">
               <thead>
                  <tr>

                    
                     <!--  <th>Sr no</th>  --> 
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('product_image')}} </a><br />
                       
                     </th>
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('product_type')}} </a><br />
                       
                     </th>
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('product_id')}} </a><br />
                     </th>
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('product_name')}} </a><br />
                     </th>
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('price')}} (MAD)</a><br />
                       
                     </th>
                     @if(array_key_exists('canteen.update', $arr_current_user_access))
                      <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('quantity')}} </a><br />
                     </th>
                     @endif
                     @if(array_key_exists('canteen.delete', $arr_current_user_access))     
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('action')}} </a><br />
                     </th>
                     @endif
                  </tr>
               </thead>
            </table>
         </div>
         {!! Form::close() !!}
      </div>
   </div>
 </div>
</div>

@if(array_key_exists('canteen.list', $arr_current_user_access))  
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
              'url':'{{ $module_url_path.'/get_records'}}',
              'data': function(d)
                {
                  d['column_filter[q_day]']= $("select[name='q_day']").val() 
                }
              },
           columns: [
          
          {data: 'product_image', "orderable": false, "searchable":false},
          {data: 'type', "orderable": false, "searchable":false},
          {data: 'product_id', "orderable": false, "searchable":false},
          {data: 'product_name', "orderable": false, "searchable":false},
          {data: 'price', "orderable": false, "searchable":false},
          @if(array_key_exists('canteen.update', $arr_current_user_access))
          {data: 'quantity', "orderable": false, "searchable":false}
          @endif
          @if(array_key_exists('canteen.delete', $arr_current_user_access) )
          ,
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

        $('select.column_filter').on( 'change', function () 
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

  function updateQuantity(obj)
  {
    var stock   =   $(obj).val();
    var day     =   $('[name=q_day]').val();
    var id      =   $(obj).parent().parent().find('td:eq(0)').find('div').find('[type=checkbox]').val();
   
    if(stock != '' && stock > 0)
    {
      $('#loader').fadeIn('slow');
      $('body').addClass('loader-active');
       $.ajax({
              url  :"{{ $module_url_path }}/update_stock",
              type :'POST',
              data :{'stock':stock ,'day':day ,'enc_id':id ,'_token':'<?php echo csrf_token();?>'},
              success:function(data){
                  if(data.status=='success')
                  {
                    $(obj).val(data.stock);
                    $('.ajax_messages').show();
                    $('#success').show();
                    $('#success').text(data.msg);
                    setTimeout(function(){
                        $('.ajax_messages').hide();
                    }, 3000);
                  }
                  if(data.status=='error')
                  {
                    $(obj).val(data.stock);
                    $('.ajax_messages').show();
                    $('#error').css('display','block');
                    $('#error').text(data.msg);
                    setTimeout(function(){
                        $('.ajax_messages').hide();
                    }, 3000);
                  }
                  $('#loader').hide();
                  $('body').removeClass('loader-active');
              }
            });
    }else{
      $(obj).val('');
      swal('{{translation("positive_numbers_only")}}');
    }
  }
</script>
@stop