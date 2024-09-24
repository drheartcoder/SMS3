@extends('student.layout.master')    
@section('main_content') 
        <!-- BEGIN Breadcrumb -->
        <div id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                    <a href="{{ url($student_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
                </li>
                <span class="divider">
                    <i class="fa fa-angle-right"></i>
                </span>
                <li class="active">{{ isset($module_title)?$module_title:"" }}</li>                
            </ul>
        </div>
        <!-- END Breadcrumb -->
        
        <div class="page-title new-agetitle">
            <div>
                <h1><i class="fa fa-book"></i> {{ translation('course_material') }}</h1>
            </div>
        </div>
        
        <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">

        <!-- BEGIN Tiles -->
        <div class="row">
            <div class="col-md-12">
                <div class="box  box-navy_blue">
                    
                    <div class="box-title">
                       <h3>
                          <i class="fa fa-list"></i>
                          {{ isset($module_title)?$module_title:"" }}
                       </h3>
                       <div class="box-tool">
                             <a 
                                title="{{translation('refresh')}}" 
                                href="{{ $module_url_path }}"
                                style="text-decoration:none;">
                             <i class="fa fa-repeat"></i>
                             </a> 
                             
                          </div>
                    </div>
                    <div class="box-content studt-padding">
                     @include('student.layout._operation_status')

                    
                        {!! Form::open([ 'url' => $module_url_path.'/multi_action',
                         'method'=>'POST',
                         'enctype' =>'multipart/form-data',   
                         'class'=>'form-horizontal', 
                         'id'=>'frm_manage' 
                         ]) !!}
                         {{ csrf_field() }}
                            
                            
                          
                            <div class="clearfix"></div>                            
                            <div class="border-box table-action-btns">
                                                            
                                <div class="table-responsive" style="border:0">
                                    <div class="col-lg-12">
                                    <input type="hidden" name="multi_action" value="" />
                                    <table class="table table-advance" id="table_module">
                                        <thead>
                                            <tr>

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
                                          <td>{{$course_data['get_level_class']['level_details']['level_name']}}</td> 
                                          <td>{{$course_data['get_course']['course_name']}}</td>
                                          <td>{{isset($course_data['created_at']) ? getDateFormat($course_data['created_at']) : '-'}}</td>
                                          <td>
                                             @foreach($course_data['get_material_details'] as $detail)
                                                @if($detail['type'] == "Document")
                                               
                                               <a href='{{$module_url_path.'/download_document/'.base64_encode($detail['id'])}}'>{{$detail['path']}}</a> 

                                                <br>
                                                @endif
                                             @endforeach
                                           </td> 
                                           <td>
                                             @foreach($course_data['get_material_details'] as $detail)
                                                @if($detail['type'] == "Video")
                                                
                                                <a href="{{$detail['path']}}" target="_blank">{{$detail['path']}}</a> 
                                                <br>
                                                @endif
                                             @endforeach
                                           </td> 
                                           <td>
                                            <a class="green-color" href="{{$module_url_path.'/view/'.base64_encode($course_data['id'])}}" title="{{translation('view')}}"><i class="fa fa-eye" ></i></a>
                                           </td>
                                          </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                            </div>
                         {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
<script type="text/javascript">
var room = 1;
var count = 1;

$(document).ready(function() {

$(".level").on('change',function(){
    var level = $('.level').val();
    $(".level-class").empty();
       $.ajax({
          url  :"{{ $module_url_path }}/get_classes",
          type :'get',
          data :{'_token':'<?php echo csrf_token();?>','level':level},
          success:function(data){
            
                 $(".level-class").append(data);
              
          }
    });

});

$(document).on("change",".validateDoc", function()
 {       
    var file = this.files;
    validateDocument(this.files,'Doc',null);

 });
  
var oTable = $('#table_module').dataTable({
          "pageLength": 10,
          "searching":false,      
          "aoColumnDefs": [
                          { 
                            "bSortable": false, 
                            "aTargets": [0,1,3,4,5,6] // <-- gets last column and turns off sorting
                           } 
                        ]
            });  
            $.fn.dataTable.ext.errMode = 'none';
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
                if(ext=="pdf" || ext=='doc' || ext=='docx' || ext=="PDF" || ext=='DOC' || ext=='DOCX')
                {
                    blnValid = true; 
                }  
            }
            else
            {
                if(ext=="pdf" || ext=='doc' || ext=='docx' || ext=="PDF" || ext=='DOC' || ext=='DOCX')
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
                showAlert("Sorry, " + files[0]['name'] + " is invalid, allowed extensions are: pdf, doc, txt,docx","error");
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

function material_url() 
  {

      room++;
      var objTo = document.getElementById('material_url')
      var divtest = document.createElement("div");
      divtest.setAttribute("class", "form-group removeclass"+room);
      var rdiv = 'removeclass'+room;
      divtest.innerHTML = '<div class="form-group"><label class="col-sm-3 col-md-4 col-lg-3 control-label">Video URL<i class="red">*</i></label><div class="col-sm-9 col-md-8 col-lg-9 controls input-group-block"><input class="form-control" name="matrial_url[]" id="matrial_url_'+count+'" type="text" placeholder="URL" data-rule-required="true"><span class="help-block"></span><button class="btn btn-danger remove-btn-block" type="button" onclick="remove_material_url('+ room +');"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button></div></div><div class="clear"></div>';

      objTo.appendChild(divtest)
       count++;
      $("#count").val(count);
  }
         
  function remove_material_url(rid) 
  {
         $('.removeclass'+rid).remove();
  }  
</script>
<script type="text/javascript">
      var room_doc = 1;
      var count_doc = 1;
      function document_url() 
      {

          room_doc++;
          var objTo = document.getElementById('document_url')
          var divtest = document.createElement("div");
          divtest.setAttribute("class", "form-group removeUrl"+room_doc);
          var rdiv = 'removeUrl'+room_doc;
          divtest.innerHTML = '<div class="form-group"><label class="col-sm-3 col-md-4 col-lg-3 control-label">Document</label><div class="col-sm-9 col-md-8 col-lg-9 controls input-group-block"><input type="file" required="" class="validateDoc" id="validateDoc" name="arr_document[]" id="arr_document_'+count_doc+'" ><button class="btn btn-danger remove-btn-block" type="button" onclick="remove_document_url('+ room_doc +');"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button></div></div><div class="clear"></div>';

          objTo.appendChild(divtest)
           count_doc++;
          $("#count_doc").val(count_doc);
      }
         
      function remove_document_url(rid) 
      {
        $('.removeUrl'+rid).remove();
      }

         
      
</script>
@endsection