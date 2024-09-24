@extends('professor.layout.master')                
@section('main_content')
<style>
    .sweet-alert .sa-icon.sa-error .sa-x-mark{ left: 5px;top: 1px;}
    .sweet-alert .sa-icon.sa-error .sa-line.sa-left{left: 7px;}
    .sweet-alert .sa-icon.sa-error .sa-line{top: 22px;}
</style>
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
      <a href="{{$module_url_path}}">{{str_plural($page_title)}}</a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right"></i>
    </span>
    <i class="{{$create_icon}}"></i>
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
            <h3><i class="{{$create_icon}}"></i>{{$module_title}}</h3>
            <div class="box-tool">
            </div>
         </div>
         <div class="box-content">
            @include('professor.layout._operation_status')
            <form method="post" onsubmit="return addLoader()"  enctype="multipart/form-data"  action="{{$module_url_path}}/store"  class="form-horizontal" id="validation-form1">
               {{ csrf_field() }} 
               <input type="hidden" name="count" id="count" value="">
               <input type="hidden" name="count_doc" id="count_doc" value="">
               <br>
               <div class="form-group">
                  <label class="col-sm-3 col-md-3 col-lg-2 control-label">{{translation('level')}}<i class="red">*</i></label>
                  <div class="col-sm-8 col-md-8 col-lg-4 controls">
                     <select name="level" id="level" class="form-control level" data-rule-required="true">
                        <option value="">{{translation('select_level')}}</option>
                        @if(isset($arr_levels) && count($arr_levels)>0)
                        @foreach($arr_levels as $levels)
                        <option value="{{$levels['level_id']}}">{{$levels['level_details']['level_name']}}</option>
                        @endforeach
                        @endif
                     </select>
                     <span class='help-block'>{{ $errors->first('level')}}</span>
                  </div>
               </div>
               <div class="form-group">
                  <label class="col-sm-3 col-md-3 col-lg-2 control-label">{{translation('class')}}<i class="red">*</i></label>
                  <div class="col-sm-8 col-md-8 col-lg-4 controls">
                     <select name="class" id="class" class="form-control level-class" onChange="getCourses();">
                        <option value="" >{{translation('select_class')}}</option>
                     </select>
                     <span class='help-block'>{{ $errors->first('class')}}</span>
                  </div>
               </div>
               <div class="form-group">
                  <label class="col-sm-3 col-md-3 col-lg-2 control-label">{{translation('course')}}<i class="red">*</i></label>
                  <div class="col-sm-8 col-md-8 col-lg-4 controls">
                     <select name="course" id="course" class="form-control">
                        <option value="">{{translation('select_course')}}</option>
                        {{-- @if(isset($arr_courses) && count($arr_courses)>0)
                        @foreach($arr_courses as $course)
                        <option value="{{$course['course_id']}}">{{$course['get_course']['course_name']}}</option>
                        @endforeach
                        @endif --}}
                     </select>
                     <span class='help-block'>{{ $errors->first('course')}}</span>
                  </div>
               </div>
               <div class="upload-section-block">
                  <div class="main-col-block">
                     <div class="form-group">
                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">{{translation('document')}}</label>
                        <div class="col-sm-8 col-md-8 col-lg-4 controls input-group-block">
                           <!--                                              <input type="file" class="validateDoc form-control" id="validateDoc" name="arr_document[]" id="arr_document_0">-->
                           <div class="upload-block-clone">
                              <input type="file" id="pdffile_0" class="hidden-input-block" name="arr_document[]" onchange="Changefilename(this)" >
                              <div class="input-group">
                                 <input type="text" class="form-control file-caption  kv-fileinput-caption" id="subfile" readonly />
                                 <div class="btn btn-primary btn-file" ><a class="file" onclick="$('#pdffile_0').click();">Browse...</a></div>
                              </div>
                           </div>
                           <span class="help-block"></span>
                           <button class="btn btn-success add-remove-btn" type="button" onclick="document_url();"> <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> </button>
                        </div>
                     </div>
                     <div class="clearfix"></div>
                  </div>
                  <div id="document_url" class="main-col-block">
                  </div>
                  <div class="clearfix"></div>
               </div>
               <div class="main-col-block">
                  <div class="form-group">
                     <label class="col-sm-3 col-md-3 col-lg-2 control-label">{{translation('video_url')}}<i class="red">*</i></label>
                     <div class="col-sm-8 col-md-8 col-lg-4 controls input-group-block">
                        <input class="form-control" name="matrial_url[]" id="matrial_url_0" type="text" placeholder="URL">
                        <span class="help-block"></span>
                        <button class="btn btn-success add-remove-btn" type="button" onclick="material_url();">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> </button>
                     </div>
                  </div>
                  <div class="clearfix"></div>
               </div>
               <div id="material_url">
               </div>
               <div class="clearfix"></div>
               <div class="form-group">
                  <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                     <a href="{{$module_url_path}}" class="btn btn-primary">{{translation('back')}}</a>
                     <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('save')}}</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
<script type="text/javascript">

                 function Changefilename(event){
                      var file = event.files;
                      validateDocument(event.files,'Doc',null);
                      $(event).next().children('input').val($(event).val());
                  }

$("#level").on('change',function(){
    $("#level").next('span').html('');
})
$("#class").on('change',function(){
    $("#class").next('span').html('');
})
$("#course").on('change',function(){
    $("#course").next('span').html('');
})

$("#submit_button").click(function(){

  var flag = 0; 
  var url_ereg =/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/;
  
  var count = $("#count").val();

  if($("#level").val()=='')
  {  
      $("#level").next('span').html("{{translation('this_field_is_required')}}");
      flag=1;
  }
  else
  {
      $("#level").next('span').html();
  }
  if($("#class").val()=='')
  {  
      $("#class").next('span').html("{{translation('this_field_is_required')}}");
      flag=1;
  }
  else
  {
      $("#class").next('span').html();
  }
  if($("#course").val()=='')
  {  
      $("#course").next('span').html("{{translation('this_field_is_required')}}");
      flag=1;
  }
  else
  {
      $("#course").next('span').html();
  }

  for(var i=0 ; i<=count ; i++)
  {
      var url = $("#matrial_url_"+i).val();
      
      if(url != undefined)
      {
        if($("#matrial_url_"+i).val()=='')
        {  
            $("#matrial_url_"+i).next('span').html("{{translation('this_field_is_required')}}");
            flag=1;
        }
        else if(!(url_ereg.test(url)))
        {
       
            $("#matrial_url_"+i).next('span').html("{{translation('enter_valid_url')}}");
            flag=1;
        }
        else
        {
            $("#matrial_url_"+i).next('span').html();
        }  
      }
        
  }

  if(flag==1)
  {
    return false;
  }

});

var room = 1;
var count = 1;

$(document).ready(function() {
$('#count').val('0');
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
                if(ext=="pdf" || ext=='doc' || ext=='docx' || ext=="PDF" || ext=='DOC' || ext=='DOCX' || ext=='xlsx' || ext=='xls')
                {
                    blnValid = true; 
                }  
            }
            else
            {
                if(ext=="pdf" || ext=='doc' || ext=='docx' || ext=="PDF" || ext=='DOC' || ext=='DOCX' || ext=='xlsx' || ext=='xls' )
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
                showAlert("Sorry, " + files[0]['name'] + " is invalid, allowed extensions are: pdf, doc, txt,docx,excel","error");
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
      divtest.innerHTML = '<label class="col-sm-3 col-md-3 col-lg-2 control-label"></label><div class="col-sm-8 col-md-8 col-lg-4 controls input-group-block"><input class="form-control" name="matrial_url[]" id="matrial_url_'+count+'" type="text" placeholder="URL"><span class="help-block"></span><button class="btn btn-danger remove-btn-block" type="button" onclick="remove_material_url('+ room +');"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button></div></div><div class="clear">';

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

          divtest.innerHTML = '<label class="col-sm-3 col-md-3 col-lg-2 control-label"></label><div class="col-sm-8 col-md-8 col-lg-4 controls input-group-block"><div class="upload-block-clone"><input type="file" id="pdffile_'+count_doc+'" onchange="Changefilename(this)" class="hidden-input-block" name="arr_document[]"><div class="input-group"><input type="text" class="form-control file-caption  kv-fileinput-caption" id="subfile_'+count_doc+'" readonly=""/><div class="btn btn-primary btn-file"><a class="file" onclick="$(\'#pdffile_'+count_doc+'\').click();">Browse...</a></div></div></div><button class="btn btn-danger remove-btn-block" type="button" onclick="remove_document_url('+ room_doc +');"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button></div></div><div class="clear">';

          objTo.appendChild(divtest)
           count_doc++;
          $("#count_doc").val(count_doc);
      }
         
      function remove_document_url(rid) 
      {
        $('.removeUrl'+rid).remove();
      }

         
      
</script>
<script>
  function getCourses() {
    var level      =   $('#level').val();
    var cls_name   =   $('#class').val();

    $('#course').empty();
    $.ajax({
              url  :"{{ $module_url_path }}/get_courses",
              type :'POST',
              data :{'level':level ,'class':cls_name ,'_token':'<?php echo csrf_token();?>'},
              success:function(data){
                $('#course').empty();
                $('#course').append(data);
              }
            });
  }
</script>
@endsection
