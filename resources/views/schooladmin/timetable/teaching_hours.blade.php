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
                    <i class="{{$module_icon}}"></i>
                </span>
                <li><a href="{{$module_url_path}}">{{$page_title}}</a></li>
                <span class="divider">
                    <i class="fa fa-angle-right"></i>
                    <i class="{{$module_icon}}"></i>
                </span>
                <li class="active">{{$module_title}}</li>                
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


            <!-- BEGIN Tiles -->
           <div class="row">
                <div class="col-md-12">
                    <div class="box  box-navy_blue">
                        <div class="box-title">
                            <h3><i class="fa fa-list"></i>{{$module_title}}</h3>
                            <div class="box-tool">
                            </div>
                        </div>
                        
                        <div class="box-content">
                        @include('schooladmin.layout._operation_status')  
 
                            @if(count($arr_edit_professor_hours)>0)
                                <form method="post" onsubmit="return addLoader()" action="{{$module_url_path}}/update_teaching_hours/{{$enc_id}}"  class="form-horizontal" id="validation-form1">
                            @else
                                <form method="post" onsubmit="return addLoader()" action="{{$module_url_path}}/store_teaching_hours"  class="form-horizontal" id="validation-form1">
                            @endif    
                                {{ csrf_field() }}
                                <div class="row">
                                <br><br>
                                  
                                <div class="col-sm-6 col-md-6 col-lg-6">
                                    <div class="row">                                        
                                        <div class="form-group">
                                            <label class="col-sm-3 col-lg-4 control-label">{{translation('select')}} {{translation('professor')}} <i class="red">*</i></label></label>
                                            <div class="col-sm-9 col-lg-8 controls">
                                                <select name="professor_id" id="professor_id" class="form-control"  data-rule-required='true' >
                                                    @if(isset($arr_professor) && count($arr_professor)>0)
                                                    <option value="">{{translation('select')}} {{translation('professor')}}</option>
                                                        @foreach($arr_professor as $arr_professor)
                                                            <option value="{{$arr_professor->user_id}}" @if(!empty($arr_edit_professor_hours))  @if($arr_edit_professor_hours['professor_id']== $arr_professor->user_id) selected @endif @endif
                                                            >{{$arr_professor->user_name}}</option>
                                                        @endforeach
                                                    @endif    
                                                </select>
                                                <span class='help-block'>{{ $errors->first('professor_id') }}</span>    
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6 col-lg-6">
                                    <div class="row">
                                        <div class="form-group">
                                            <label class="col-sm-3 col-lg-4 control-label">{{translation('total')}} {{translation('periods')}}<i class="red">*</i></label>
                                            <div class="col-sm-9 col-lg-8 controls">
                                               <?php 
                                                 $total_hours = !empty($arr_edit_professor_hours) && isset($arr_edit_professor_hours['total_periods']) && $arr_edit_professor_hours['total_periods']!=''?$arr_edit_professor_hours['total_periods']:'';

                                                  $min = !empty($arr_edit_professor_hours) && isset($arr_edit_professor_hours['total_periods']) && $arr_edit_professor_hours['total_periods']!=''?$arr_edit_professor_hours['total_periods']:1;
                                                ?>

                                               <input type="hidden" name="old_total_periods" id="old_total_periods" value="{{$total_hours}}">
                                               <input class="form-control" name="total_periods" id="total_periods" data-rule-required="true"  placeholder="{{ translation('enter') }} {{translation('total')}} {{translation('periods')}}" type="text" data-rule-number="true"  value="{{$total_hours}}"  min="{{$min}}"  />
                                                <span class='help-block'>{{ $errors->first('total_periods') }}</span>    

                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                <div class="row">
                                <div class="form-group">
                                    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                                        @if(count($arr_edit_professor_hours)>0)
                                            <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('update')}}</button>
                                        @else
                                            <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('save')}}</button>
                                        @endif    
                                    </div>
                                </div>
                                    </div>
                            </form>
                             
                            
                            <div class="table-responsive" style="border:0">
                                <input type="hidden" name="multi_action" value="" />
                                <table class="table table-advance" id="table_module">
                                    <thead>
                                        <tr>
                                            
                                            <th>{{translation('professor')}}</th>
                                            <th>{{translation('total_periods')}}</th>
                                            <th>{{translation('assigned_periods')}}</th>
                                            <th>{{translation('subject')}}</th>
                                            <th>{{translation('professor')}} {{translation('timetable')}}</th>
                                            <th>{{translation('action')}}</th>                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($arr_teacher as $value)
                                    
                                        <tr role="row">
                                            <td>{{$value->user_name or '-'}}</td>
                                            <td>{{$value->total_periods or '-'}}</td>
                                            <td>{{ $value->assigned_periods or '-'}}</td>
                                            <td><?php

                                            $strSubjects = getProfessorTeachingSubject($value->professor_id,$academic_year);
                                            if($strSubjects){
                                            ?>
                                              <span class="subjects-teaching-hrs">{{ getProfessorTeachingSubject($value->professor_id,$academic_year) }}</span>
                                              <?php }else{ echo '-'; } ?>
                                            </td>
                                            <td>
                                              <a  href="#" title="Timetable" class="download-template-link-section" data-toggle="modal" data-target="#teacher_lecture_info_{{ $value->professor_id }}">
                                                    {{translation('timetable')}}
                                                </a>    <!--  data-target="#teacher_lecture_info_{{ $value->professor_id }}" -->
                                            </td>

                                            <td width="100px">                                                    
                                                <a  href="{{ $module_url_path.'/teaching_hours/'.base64_encode($value->id) }}" title="{{translation('edit')}}" class="orange-color">
                                                    <i class="fa fa-pencil" ></i>
                                                </a>
                                            </td>
                                        </tr>                                       
                                    @endforeach    
                                    </tbody>
                                </table>
                            </div>                            
                        </div>
                    </div>
                </div>
               </div> 
         <!-- END Main Content -->


 
@if(isset($arr_teacher_timetable) && sizeof($arr_teacher_timetable)>0)
  @foreach($arr_teacher_timetable as $key => $timetable)
  
<div class="modal fade teacherTimetableModel" id="teacher_lecture_info_{{ $timetable['professor_id'] }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">{{translation('professor_lecture_information')}}</h4>
        <h4 class="modal-title" id="myModalLabel"></h4>         
      </div>
      <div class="modal-body">
           <div class="clearfix"></div>
            <div class="panel-body table-responsive">
                <div class="professor-info-section">
                    <label class="">
                        <span class="professor-info-head"><b>{{translation('professor')}} {{translation('name')}} </b> </span>  
                        <span class="professor-label-content">: {{ isset($timetable['teacher_name'])?$timetable['teacher_name']:'' }}</span>
                    </label>
                </div>
                <div class="professor-info-section">
                    <label class="">
                        <span class="professor-info-head"><b>{{translation('total_periods')}}</b></span>
                        <span class="professor-label-content">: {{ isset($timetable['total_periods'])?$timetable['total_periods']:'' }}</span> 
                    </label>
                </div>
                <div class="professor-info-section">
                    <label class="">
                        <span class="professor-info-head"><b>{{translation('assigned_periods')}}</b></span> 
                        <span class="professor-label-content">: {{ isset($timetable['assigned_periods'])?$timetable['assigned_periods']:'' }}</span>
                    </label>
                </div>

                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                      <th>{{translation('sr_no')}}</th>
                                      <th>{{translation('subject')}}</th>
                                      <th>{{translation('class_details')}}</th>
                                      <th>&nbsp;&nbsp;{{translation('day')}}</th>
                                      <th>{{translation('period_number')}}</th>
                                      <th>&nbsp;&nbsp;{{translation('time')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                            @if(isset($timetable['teacher_timetable']) && sizeof($timetable['teacher_timetable'])>0)        <?php $k = 0; ?>
                                      @foreach($timetable['teacher_timetable'] as $key123 => $teacher_subject)

                                          @foreach($teacher_subject as $key=>$teacher_timetable)


                                          @if($teacher_timetable['professor_id']==$timetable['professor_id'])
                                            <?php $k++; 
                                                $period_start_time = $period_end_time = '00:00:00'; 
                                                $period_start_time = isset($teacher_timetable['period_start_time'])?$teacher_timetable['period_start_time']:'00:00:00';
                                                $period_end_time = isset($teacher_timetable['period_end_time'])?$teacher_timetable['period_end_time']:'00:00:00';
                                              ?>
                                            <tr>
                                              <td style="padding-left: 3% "><?php echo $k; ?></td>
                                               <td style="padding-left:3%">
                                                    {{ isset($teacher_timetable['professor_subjects']['course_name'])?$teacher_timetable['professor_subjects']['course_name']:'' }} 
                                               </td>
                                               <td style="padding-left:3%">
                                                 
                                                 {{ isset($teacher_timetable['class_details']['class_name'])?$teacher_timetable['class_details']['class_name']:'' }} - {{ isset($teacher_timetable['level_details']['level_name'])?$teacher_timetable['level_details']['level_name']:'' }}
                                                    
                                               </td>
                                               <td style="padding-left:3%"> 
                                                    {{ isset($teacher_timetable['day'])?translation(strtolower($teacher_timetable['day'])):'' }} 
                                               </td>
                                               <td style="padding-left:3%">
                                                    {{ isset($teacher_timetable['periods_no'])?$teacher_timetable['periods_no']:'' }} 
                                               </td>

                                               <td style="padding-left:3%">
                                                    {{getTimeFormat($period_start_time)}} - {{getTimeFormat($period_end_time)}}
                                               </td>
                                                
                                            </tr>
                                          @endif
                                          @endforeach
                                      @endforeach 
                        @endif
                        <tbody>
                           </table>   
              
           </div>     
      </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">{{translation('close')}}</button>
    </div>
  </div>
 </div>
 </div>
@endforeach
@endif


        <script type="text/javascript">
        $(document).ready(function() {
         var oTable = $('#table_module').dataTable({
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
          "pageLength": 5,
          "searching":false,      
          "aoColumnDefs": [
                          { 
                            "bSortable": false, 
                            "aTargets": [0,1,3,4,5,2] // <-- gets last column and turns off sorting
                           } 
                        ]
            });  
           /* $.fn.dataTable.ext.errMode = 'none';      */
         
        });
        </script>
 

@endsection