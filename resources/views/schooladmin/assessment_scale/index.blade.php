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
          <li class="active">{{$module_title}}</li>
        </li>
       
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

  <!-- BEGIN Tiles -->
 <div class="row simple-table-section">
      <div class="col-md-12">
          <div class="box  box-navy_blue">
              <div class="box-title">
                  <h3><i class="fa fa-list"></i>{{$page_title}}</h3>
                  <div class="box-tool">
                  </div>
              </div>
              <div class="box-content">
              @include('schooladmin.layout._operation_status')  
              <div class="tobbable">
                  @if(array_key_exists('assessment_scale.create', $arr_current_user_access) || array_key_exists('assessment_scale.update', $arr_current_user_access))
                  @if(isset($scale_value) && count($scale_value)>0)
                      <form method="post" onsubmit="return addLoader()" action="{{$module_url_path}}/update/{{$enc_id}}"  class="form-horizontal" id="validation-form1">
                  @else
                      <form method="post" onsubmit="return addLoader()" action="{{$module_url_path}}/store"  class="form-horizontal" id="validation-form1">
                  @endif 
                      {{ csrf_field() }}

                  <div class="row">
                      <div class="col-sm-12 col-md-12 col-lg-6">
                        <div class="row">
                          <div class="form-group">
                              <label class="col-sm-3 col-lg-4 control-label">{{translation('course')}} <i class="red">*</i></label>
                              <div class="col-sm-9 col-lg-8 controls">
                                  <select name="course" id="course" data-rule-required="true" class="form-control"> 
                                    <option></option>
                                    @if(isset($arr_course) && !empty($arr_course))
                                      @foreach($arr_course as $course)
                                          <option value="{{$course['course_id'] or 0}}" @if(isset($scale_value['course_id'])) @if($course['course_id']==$scale_value['course_id']) selected @endif @endif>{{$course['get_course']['course_name'] or ''}}</option>
                                      @endforeach
                                    @endif
                                  </select>
                                  <span class='help-block'>{{ $errors->first('course') }}</span> 
                              </div>
                          </div>

                          <div class="form-group">
                              <label class="col-sm-3 col-lg-4 control-label"></label>
                              <div class="col-sm-9 col-lg-8 controls">
                                  <div class="radio-btns">  
                                      <div class="radio-btn">
                                          <input type="radio" id="f-option" name="scale_type" value="grade" checked @if(isset($scale_value['scale']) && strpos($scale_value['scale'],',')) checked @endif>
                                          <label for="f-option">{{translation('grade')}}</label>
                                          <div class="check"></div>
                                      </div>
                                      <div class="radio-btn">
                                          <input type="radio" id="s-option" name="scale_type" value="marks" @if(isset($scale_value['scale']) && strpos($scale_value['scale'],'-')) checked @endif>
                                          <label for="s-option">{{translation('marks')}}</label>
                                          <div class="check"><div class="inside"></div></div>
                                      </div>
                                  </div>    
                              </div>
                          </div>

                          <div class="form-group">
                              <label class="col-sm-3 col-lg-4 control-label">{{translation('assessment_scale')}} <i class="red">*</i></label>
                              <div class="col-sm-9 col-lg-8 controls">
                                  <input type="text" name="scale" id="scale" class="form-control" data-rule-required="true" @if(isset($scale_value['scale'])) value="{{$scale_value['scale']}}"  @endif>
                                  <small>{{translation('note')}} : {{ translation('grade')}} : a,b,c,d | A,B,C,D</small><br/>
                                  <small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ translation('marks')}} : 20 | 0-20</small>
                                  <span class='help-block'>{{ $errors->first('scale') }}</span> 
                                  <span   id="err_scale" style="color: red"></span>
                                    
                              </div>
                          </div>
                          </div>
                      </div>
                  </div>
                    <div class="row">
                  <div class="form-group">
                      <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                          @if(isset($scale_value) && count($scale_value)>0)
                              <a href="{{ url($module_url_path) }}" class="btn btn-primary">{{translation('back')}}</a>
                              <button type="submit"  class="btn btn-primary">{{translation('update')}}</button>
                          @else
                              <button type="submit" class="btn btn-primary">{{translation('save')}}</button>
                          @endif    
                          
                      </div>
                  </div>
                          </div>
                  </form>
                  @endif
              </div>


              <div class="table-responsive" style="border:0">
                  <input type="hidden" name="multi_action" value="" />
                  <table class="table table-advance" id="table_module">
                      <thead>
                          <tr>
                              <th>
                                    {{translation('sr_no')}}.                                
                              </th>
                              <th>
                                    {{translation('course')}}
                              </th>
                              <th>
                                    {{translation('assessment_scale')}}
                              </th>
                              @if(array_key_exists('assessment_scale.update', $arr_current_user_access) || array_key_exists('assessment_scale.delete', $arr_current_user_access))
                                  <th width="200px">
                                     {{translation('action')}}                   
                                  </th>
                              @endif                                            
                          </tr>
                      </thead>
                      <tbody>
                        <?php $no = 0; ?>
                          @if(isset($arr_scale) && !empty($arr_scale))
                              @foreach($arr_scale as $key =>$scale)
                                  <tr>
                                      <td width="70px">
                                          {{++$no}} 
                                      </td>
                                      <td>
                                          {{$scale['course_name']['course_name'] or ''}}
                                      </td>

                                      <td>
                                          {{$scale['scale'] or ''}}
                                      </td>
                                      @if(array_key_exists('assessment_scale.update', $arr_current_user_access) || array_key_exists('assessment_scale.delete', $arr_current_user_access))
                                      <td>
                                          
                                            @if(array_key_exists('assessment_scale.update', $arr_current_user_access))     
                                              <a class="orange-color" href="{{ $module_url_path.'/'.base64_encode($scale['id']) }}" title="{{translation("edit")}}">
                                                  <i class="fa fa-edit" >
                                                  </i>
                                              </a>  
                                            @endif

                                            @if(array_key_exists('assessment_scale.delete', $arr_current_user_access))     
                                              <a class="red-color" href="{{ $module_url_path.'/delete/'.base64_encode($scale['id']) }}" title="{{translation("delete")}}" onclick="return confirm_action(this,event,'{{translation('do_you_really_want_to_delete_this_record')}}','{{translation("are_you_sure")}}','{{translation('yes')}}','{{translation("no")}}')">
                                                  <i class="fa fa-trash" >
                                                  </i>
                                              </a>  
                                            @endif
                                            
                                         
                                      </td>
                                      @endif
                                  </tr>
                              @endforeach
                          @endif
                      </tbody>
                  </table>
              </div>                            
              </div>
          </div>
      </div>
     </div> 

</script>
<!-- END Main Content -->
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
  });        
  $.fn.dataTable.ext.errMode = 'none';
});

</script>

<script>
  $('#scale').keyup(function(){
    var scale = $(this).val();
    if($('[name=scale_type]:checked').val() == 'grade')
    {
      var reg  = /^[A-Za-z\,]{1,}$/;
 
      if(!scale.match(reg))
      {
        $('#err_scale').show();
        $('#err_scale').text('Invalid grade format');
      }
      else
      {
        $('#err_scale').css('display','none');
      }
    }

    if($('[name=scale_type]:checked').val() == 'marks')
    {
      var reg2 =/^[0-9\-]+$/;
      if(!reg2.test(scale))
      {
        $('#err_scale').show();
        $('#err_scale').text('Invalid marks format');
      }
      else
      {
        $('#err_scale').css('display','none');
      }
    }

    if(scale == '')
    {
      $('#err_scale').css('display','none');
    }

  });
</script>
<script>
  $('#validation-form1').submit(function(){
    var scale = $('#scale').val();

    if($('[name=scale_type]:checked').val() == 'grade')
    {
      var reg  = /^[A-Z\,]{1,}$/;
      if(!scale.match(reg) && scale != '')
      {
        $('#err_scale').show();
        $('#err_scale').text('Invalid grade format');
        return false;
      }
      else
      {
        $('#err_scale').css('display','none');
        return true;
      }
    }

    if($('[name=scale_type]:checked').val() == 'marks')
    {
      var reg2 =/^[\d]+[\-].[\d]*$/;
      if(!scale.match(reg2) && scale != '')
      {
        $('#err_scale').show();
        $('#err_scale').text('Invalid marks format');
        return false;
      }
      else
      {
        $('#err_scale').css('display','none');
        return true;
      }
    }

    if(scale == '')
    {
      $('#err_scale').css('display','none');
    }


  });

</script>

@endsection