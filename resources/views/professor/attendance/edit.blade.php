@extends('professor.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{url('/')}}/professor/dashboard">{{translation('dashboard')}}</a>
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
         @include('professor.layout._operation_status')  
            <form action="{{$module_url_path}}/store" method="POST" enctype="multipart/form-data" class="form-horizontal" id="validation-form1">
                {{ csrf_field() }}
                <div class="row">
                  <div class="row">
                              <div class="col-md-6">
                                   <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('select_level')}}</label>
                                        <div class="col-sm-9 col-md-8 col-lg-9 controls">
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
                              <div class="col-md-6">
                                   <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('select_class')}}</label>
                                        <div class="col-sm-9 col-md-8 col-lg-9 controls">
                                           <select name="class" id="class" class="form-control" data-rule-required="true">
                                              <option value="">{{translation('select_class')}}</option>
                                          </select>
                                        </div>
                                    </div>
                              </div>
                              <div class="col-md-6">
                                  <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('select_date')}}</label>
                                        <div class="col-sm-9 col-md-8 col-lg-9 controls">
                                           <input class="form-control datepikr" name="date" id="datepicker" placeholder="{{translation('select_date')}}" type="text" readonly style="cursor: pointer;">
                                        </div>
                                    </div>
                              </div> 
                              <div class="col-md-6">
                                  <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('select_course')}}</label>
                                        <div class="col-sm-9 col-md-8 col-lg-9 controls">
                                           <select name="course" id="course" class="form-control" data-rule-required="true">
                                              <option value="">{{translation('select_course')}}</option>
                                              @if(isset($courses) && !empty($courses))
                                                @foreach($courses as $key => $course)
                                                  <option value="{{$course['course_id']}}">{{$course['get_course']['course_name']}}</option>
                                                @endforeach
                                              @endif
                                          </select>
                                        </div>
                                    </div>
                              </div> 

                              <div align="center">
                                 <a href="javascript:void(0)" class="btn btn btn-primary sv-space" onClick="getData();">Submit</a>
                              </div>
                  </div>

                <div id="table_div" hidden="true">
                 <div class="table-responsive" style="border:0">
                    <input type="hidden" name="role" value="{{$role}}" />
                    <table class="table table-advance"  id="table_module">
                       <thead>
                          <tr>
                            {{-- <th>{{translation('sr_no')}}.</th> --}}
                             <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation($role)}} {{translation('name')}} </a><br/></th>
                             <th>{{translation('national_id')}}</th>
                             <th></th>
                             <th></th>
                             <th></th>
                          </tr>
                       </thead>
                       <tbody id="table_body">
                        
                       </tbody>
                    </table>
                 </div><br/>
                 <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                       <a href="{{ url($module_url_path.'/'.$role) }}" class="btn btn-primary">{{translation('back')}}</a> 
                       <input type="submit" id="submit_button" name="save" value="{{translation('save')}}" class="btn btn-primary">
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
    $(function () {
      var date  = new Date();
      var today = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate();
      $("#datepicker").datepicker({
          format:'yyyy-mm-dd',
          autoclose : true
      });

      $('#datepicker').val(today);
    });
</script>

<script>
  function getData()
  {
    var level   =   $('#level').val();
    var cls     =   $('#class').val();
    alert(level.length+'-----'+cls);
    if(level.length != 0 && cls.length !=0)
    {
      $('#table_div').show();
      $.ajax({
              url  :"{{ $module_url_path }}/getStudents",
              type :'POST',
              data :{'level':level ,'cls':cls ,'_token':'<?php echo csrf_token();?>'},
              success:function(data){
                
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
@endsection