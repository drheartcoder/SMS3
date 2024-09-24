@extends('professor.layout.master')                
@section('main_content')
<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="{{ url($professor_panel_slug.'/dashboard') }}"> {{translation('dashboard')}} </a>
    </li>
    <span class="divider">
    <i class="fa fa-angle-right"></i>
    <i class="{{$module_icon}}"></i>   
    </span> 
    <li class="active"> {{ $module_title or ''}} </li>
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
      <div class="box-title">
        <h3>
          <i class="fa fa-list"></i>
          {{ isset($module_title)?$module_title:"" }}
        </h3>
        <div class="box-tool">
          @if(array_key_exists('course_material.create', $arr_current_user_access))  
          <a href="{{$module_url_path}}/create" >{{translation("add").' '.$page_title }}</a> 
          @endif
          @if(array_key_exists('course_material.delete',$arr_current_user_access))     
          <a title="{{translation('multiple_delete')}}" 
          href="javascript:void(0);" 
          onclick='javascript : return check_multi_action("frm_manage","{{translation('are_you_sure')}}","{{translation('do_you_really_want_to_delete_this_record').'?'}}","{{translation('yes')}}","{{translation('no')}}","{{translation('oops')}}","{{translation('please_select_the_record_to_perform_this_action')}}","delete");'
          style="text-decoration:none;">
          <i class="fa fa-trash-o"> </i>
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
        @include('professor.layout._operation_status') 
        {!! Form::open([ 'url' => $module_url_path.'/multi_action',
        'method'=>'POST',
        'enctype' =>'multipart/form-data',   
        'class'=>'form-horizontal', 
        'id'=>'frm_manage' 
        ]) !!} 
        {{ csrf_field() }}
         
        <br/>
        <div class="clearfix"></div>
        <div class="table-responsive" style="border:0">
          <input type="hidden" name="multi_action" value="" />
          <div class="table-responsive" style="border:0">
            <div class="col-lg-12">
              <input type="hidden" name="multi_action" value="" />
              <table class="table table-advance" id="table_module">
                <thead>
                  <tr>
                    @if(array_key_exists('course_material.delete',$arr_current_user_access ) )
                    <th style="width: 18px; vertical-align: initial;">
                      <div class="check-box">
                        <input type="checkbox" class="filled-in" name="selectall" id="selectall" />
                        <label for="selectall"></label>
                      </div>
                    </th>
                    @endif
                    <th>{{translation('level')}}</th>
                    <th>{{translation('course')}}</th>
                    <th>{{translation('added_date')}}</th>
                    <th>{{translation('document')}}</th>
                    <th>{{translation('video_url')}}</th>
                    <th>{{translation('action')}}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($arr_data as $course_data)
                  <tr>
                    @if(array_key_exists('course_material.delete',$arr_current_user_access ))
                    <td style="position: relative">
                      @if($course_data['material_added_by']==$current_user) 
                      <div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_{{base64_encode($course_data['id'])}}" value="{{base64_encode($course_data['id'])}}" /><label for="mult_change_{{base64_encode($course_data['id'])}}"></label></div>
                      @else
                      <div class="over-patch-section"></div>
                      @endif
                    </td>
                    @endif
                    <td>{{$course_data['get_level_class']['level_details']['level_name']}}</td>
                    <td>{{$course_data['get_course']['course_name']}}</td>
                    <td>{{isset($course_data['created_at']) ? getDateFormat($course_data['created_at']) : '-'}}</td>
                    <td>
                      @foreach($course_data['get_material_details'] as $detail)
                      @if($detail['type'] == "Document")
                      <div class="close-arrow-cross">
                        <i class="fa fa-times" title="{{translation('delete')}}" onclick="deleteDoc('{{$detail['id']}}')"></i>
                        <a href='{{$module_url_path.'/download_document/'.base64_encode($detail['id'])}}' title="{{$detail['path']}}">{{ (strlen($detail['path']) > 40) ? substr($detail['path'],0,40).'...' : $detail['path']}}</a> 
                       </div>  
                      <br>
                      @endif
                      @endforeach
                    </td>
                    <td>
                      @foreach($course_data['get_material_details'] as $detail)
                      @if($detail['type'] == "Video")
                      <div class="close-arrow-cross">
                        <i class="fa fa-times" title="{{translation('delete')}}" onclick="deleteDoc('{{$detail['id']}}')"></i>
                        <a href="{{$detail['path']}}" target="_blank"  title="{{$detail['path']}}">{{ (strlen($detail['path']) > 40) ? substr($detail['path'],0,40).'...' : $detail['path']}}</a> 
                      </div>
                      <br>
                      @endif
                      @endforeach
                    </td>
                    <td style="position: relative;">
                      <a class="green-color" href="{{$module_url_path.'/view/'.base64_encode($course_data['id'])}}" title="{{translation('view')}}"><i class="fa fa-eye" ></i></a>
                      @if(array_key_exists('course_material.delete',$arr_current_user_access ) )
                      @if($course_data['material_added_by']==$current_user)
                      <a class="red-color" href="{{$module_url_path.'/delete/'.base64_encode($course_data['id'])}}" title="{{translation('delete')}}" onclick="return confirm_action(this,event,'{{translation('do_you_really_want_to_delete_this_record')}}','{{translation("are_you_sure")}}','{{translation('yes')}}','{{translation("no")}}')"><i class="fa fa-trash" ></i></a>
                      @else
                      <a style="position: relative;" class="red-color" href="javascript:void(0)" title="{{translation('access_denied')}}" ><i class="fa fa-trash" ></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>
                      @endif  
                      @endif    
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div> </div>
        {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>

<script>


function changeStatus(id)
  {
    var status = $("#status").val();
    if(status=="REJECTED")
    {
        $('#myModal').modal('show');
        $("#homework_id").val(id);
    }
    else
    {
      $.ajax({
              url  :"{{ $module_url_path }}/change_status",
              type :'POST',
              data :{'status': status , 'id' : id , '_token':'<?php echo csrf_token();?>'},
              success:function(data){
              }
            }); 
    }
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
              ordering: false
            });
          });
          
function deleteDoc(id)
{
  $.ajax({
          url  :"{{ $module_url_path}}/delete_doc",
          type :'post',
          data :{'_token':'<?php echo csrf_token();?>','id':id},
          success:function(data){
                  window.location.reload();
          }
    });
}

</script>
@stop                    


