@extends('schooladmin.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{url('/')}}/school_admin/dashboard">{{translation('dashboard')}}</a>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        <li>
          <i class="{{$module_icon}}"></i>
          <a href="{{$module_url_path.'/'.$role}}">{{$module_title}}</a>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        <i class="{{$create_icon}}"></i>
        <li class="active">{{$page_title}}</li>
    </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1>{{$module_title}}</h1>

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
            {{ $page_title}}
         </h3>
      </div>
      
      <div class="box-content">
         @include('schooladmin.layout._operation_status')  
            <div class="row">
             <div class="col-sm-9 col-md-8 col-lg-9">
                @if($role == 'professor' || $role == 'employee')
                   <a href="{{$module_url_path.'/edit/'.$role}}" class="btn btn active"><i class="fa fa-plus-circle"></i> {{ $page_title}}</a>
              
                    <a href="{{$module_url_path.'/view_staff/'.$role}}" class="btn btn" id="view"><i class="fa fa-eye"></i> {{ $view_page_title}}</a>
                @endif
                </div>
            </div>
            <form action="{{$module_url_path}}/update/{{$enc_id}}" method="POST" enctype="multipart/form-data" class="form-horizontal" id="validation-form1">
                {{ csrf_field() }}
                <div class="row">

                 <div class="col-sm-12 col-md-12 col-lg-6"></div>
                  <div class="col-sm-12 col-md-12 col-lg-6" align="right">
                     <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 control-label">
                        <label>{{translation('select_date')}}:</label>
                     </div>
                     <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                      <input type="text" name="attendance_date" class="form-control datepikr" id="datepicker" data-rule-required="true" readonly style="cursor: pointer;" onBlur="getStaffData();">
                      <span class='help-block' style="text-align: left">{{ $errors->first('attendance_date')}} </span>
                     </div>
                  </div>
                  <div class="clearfix"></div><br>
                 <div class="clearfix"></div>
                 <br>
                <div class="filter-section">
                  <div class="row">
                      <div class="col-md-4">
                          <div class="form-group">                            
                              <input class="form-control" name="search_key" id="search_key" type="text" placeholder="{{translation('search')}}...">
                          </div>
                      </div>
                  </div>
                </div>

                 <div class="table-responsive attendance-create-table-section" style="border:0">
                    <input type="hidden" name="role" value="{{$role}}" />
                    <table class="table table-advance"  id="table_module">
                       <thead>
                          <tr>
                            <th>{{translation('sr_no')}}.</th>
                             <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation($role)}} {{translation('name')}} </a><br/></th>
                             <th>{{translation('national_id')}}</th>
                             <th></th>
                             <th></th>
                             <th></th>
                          </tr>
                       </thead>
                       <tbody id="table_body">
                        <?php $no=0;?>
                         @if(isset($attendance['attendance']) && !empty($attendance['attendance']))
                            <?php 
                                $attendance_data =  json_decode($attendance['attendance'],true);
                            ?>
                         @endif

                         @if(isset($arr_data) && !empty($arr_data))
                            @foreach($arr_data as $key => $arr_data)
                              @if(isset($arr_data['get_user_details']['first_name']) && !empty($arr_data['get_user_details']['first_name']))
                               <tr>
                                <td>{{++$no}}</td>
                                <td>
                                    @if(isset($arr_data['get_user_details']['first_name']))
                                      {{ucfirst($arr_data['get_user_details']['first_name'])}}
                                    @endif

                                    @if(isset($arr_data['get_user_details']['last_name']))
                                      {{ucfirst($arr_data['get_user_details']['last_name'])}}
                                    @endif
                                </td>

                                <td>
                                    @if(isset($arr_data['get_user_details']['national_id']) && !empty($arr_data['get_user_details']['national_id']))
                                        {{$arr_data['get_user_details']['national_id'] or '-'}}
                                    @endif
                                </td>
                                <td>
                                  <div class="radio-btns">  
                                      <div class="radio-btn">
                                          <input type="radio" id="f-option{{$key}}" name="arr_attendance[{{$arr_data['user_id']}}]" value="present" @if(array_key_exists($arr_data['user_id'],$attendance_data)) @if($attendance_data[$arr_data['user_id']] == 'present') checked @endif @endif>
                                          <label for="f-option{{$key}}">{{translation('present')}}</label>
                                          <div class="check"></div>
                                      </div>
                                              
                                  </div> 
                                </td>

                                <td>
                                  <div class="radio-btns">  
                                      <div class="radio-btn">
                                          <input type="radio" id="s-option{{$key}}" name="arr_attendance[{{$arr_data['user_id']}}]" value="absent" @if(array_key_exists($arr_data['user_id'],$attendance_data)) @if($attendance_data[$arr_data['user_id']] == 'absent') checked @endif @endif>
                                          <label for="s-option{{$key}}">{{translation('absent')}}</label>
                                          <div class="check"><div class="inside"></div></div>
                                      </div>
                                  </div>
                                </td>

                                <td>
                                  <div class="radio-btns">  
                                      <div class="radio-btn">
                                          <input type="radio" id="t-option{{$key}}" name="arr_attendance[{{$arr_data['user_id']}}]" value="late" @if(array_key_exists($arr_data['user_id'],$attendance_data)) @if($attendance_data[$arr_data['user_id']] == 'late') checked @endif @endif>
                                          <label for="t-option{{$key}}">{{translation('late')}}</label>
                                          <div class="check"><div class="inside"></div></div>
                                      </div>
                                  </div>
                                  
                                </td>
                               </tr>
                              @endif
                              @endforeach
                         @endif
                       </tbody>
                    </table>
                 </div><br/>
                 <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                       <a href="{{ url($module_url_path.'/'.$role) }}" class="btn btn-primary">{{translation('back')}}</a> 
                       <input type="submit" id="submit_button" name="update" value="{{translation('update')}}" class="btn btn-primary">
                    </div>
                  </div>
               </div>
              </form>
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
          autoclose: true
      });

      $('#datepicker').val(today);
    });
</script>

<script>

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
              bFilter: false,
              bPaginate: false
          });
            $.fn.dataTable.ext.errMode = 'none';
          });

          function getStaffData()
  {
    var date    =   $('#datepicker').val();
      $('#table_div').show();
      $('#table_body').empty();
      $('#table_module2').empty();
      $.ajax({
              url  :"{{ $module_url_path }}/getStaffData/{{$role}}",
              type :'POST',
              data :{'start_date':date ,'_token':'<?php echo csrf_token();?>'},
              success:function(data){
                  $('#table_div').show();
                  $('#table_div2').css('display','none');
                  $('#table_body').append(data);

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
                    bFilter: false,
                    bPaginate: false
                  });
                  $.fn.dataTable.ext.errMode = 'none';
              }
            });
 
  }

  $("#search_key").keyup(function(){
    var flag=0;
        $("tbody tr").each(function(){
          
            var td = $(this).find("td");
            $(td).each(function(){
              var data = $(this).text().trim();
              data = data.toLowerCase();

              var search_key = $("#search_key").val();
              search_key = search_key.toLowerCase();
              search_key = new RegExp(search_key) ; 
                console.log(search_key.test(data));
                if(search_key.test(data)){
                  flag=1;
                  $(this).parent().show();
                  return false;
                }
                else{
                  $(this).parent().hide();
                }
                console.log(data);
                

            });
         })
         if(flag==0)
          {
            $("#hide_row").show();
          }
          else
          {
            $("#hide_row").hide();
          }  
      })
</script>
<!-- END Main Content --> 
@endsection