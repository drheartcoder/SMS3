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
      <a href="{{$module_url_path}}">{{$module_title}}</a>
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
    <h1><i class="{{$module_icon}}"></i>{{str_plural($module_title)}}</h1>

  </div>
</div>
<!-- END Page Title -->


<!-- BEGIN Tiles -->
<div class="row">
  <div class="col-md-12">

    <div class="box  box-navy_blue">
      <div class="box-title">
        <h3><i class="{{$create_icon}}"></i>{{$page_title}}</h3>
        <div class="box-tool">
        </div>
      </div>  
      <div class="box-content">
       @include('schooladmin.layout._operation_status')

       <form method="POST" action="{{$module_url_path}}/store" accept-charset="UTF-8" class="form-horizontal"  enctype="multipart/form-data"  id="validation-form1" onsubmit="return addLoader()" >
        {{ csrf_field() }}
        <div class="row">

          <div class="form-group">
            <label class="col-sm-3 col-lg-2 control-label">{{translation('title')}} <i class="red">*</i></label>
            <div class="col-sm-9 col-lg-4 controls">
              <input class="form-control" name="news_title" id="news_title" placeholder="{{translation('enter')}} {{translation('news_title')}}" type="text" data-rule-required='true' >
               <span class='help-block'>{{ $errors->first('news_title')}}</span>
            </div><div class="clearfix"></div>
          </div>
          <div class="form-group">
            <label class="col-sm-3 col-lg-2 control-label">{{translation('description')}} <i class="red">*</i></label>
            <div class="col-sm-9 col-lg-4 controls">
              <textarea name="description" id="description" class="form-control" placeholder="{{translation('enter')}}  {{translation('description')}}" data-rule-required='true' data-rule-maxlength="1000"  ></textarea>           
                 <span class='help-block'>{{ $errors->first('description')}}</span>                             
            </div><div class="clearfix"></div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 col-lg-2 control-label">{{translation('image')}}</label>
            <div class="col-sm-9 col-lg-4 controls">
              <span data-multiupload="3">
                <span data-multiupload-holder ></span>
                <span class="upload-photo">
                  <img src="{{url('/')}}/images/school_admin/plus-img.jpg" alt="plus img">
                  <input data-multiupload-src class="upload_pic_btn validate-image" type="file"  multiple   name="media_images[]"  >
                  <span data-multiupload-fileinputs></span>
                </span>
              </span>
            </div><div class="clearfix"></div>
          </div>


           <div class="form-group">
            <label class="col-sm-3 col-lg-2 control-label">{{translation('document')}}</label>
            <div class="col-sm-9 col-lg-4 controls input-group-block">
               <div class="upload-block-clone">
                          <input type="file" id="pdffile_0" class="hidden-input-block" name="news_media" onchange="Changefilename(this)"  >
                          <div class="input-group">
                              <input type="text" class="form-control file-caption  kv-fileinput-caption" id="subfile" readonly=""/>
                              <div class="btn btn-primary btn-file"><a class="file" onclick="$('#pdffile_0').click();">Browse...</a></div>
                           </div>
                      </div>
                <span class="help-block"></span>
                <small>{{translation('note_allowed_mp4docpdf_formate_only')}}</small>
            </div>
        </div>    

          

         <div class="form-group">
            <label class="col-sm-3 col-lg-2 control-label">{{translation('video_url')}}</label>
            <div class="col-sm-9 col-lg-4 controls">
                <input class="form-control" name="media_url" id="matrial_url_0" type="url" placeholder="{{translation('enter')}} {{translation('youtube_embedded_video_url')}}" >
                <span class='help-block'>{{ $errors->first('media_url')}}</span>                             
            </div>
        </div>               
         <div id="material_url"></div><div class="clearfix"></div>                       
        

      

        <div class="form-group">
          <label class="col-sm-3 col-lg-2 control-label">{{translation('date')}} <i class="red">*</i></label>
          <div class="col-sm-9 col-lg-4 controls">
            <div class="row">
              <div class="col-sm-6 col-lg-6 controls">
                <input class="form-control datepikr" name="publish_date" id="publish_date"  placeholder="{{ translation('enter') }} {{ translation('publish_date') }}" type="text" data-rule-required='true' />
              </div>
              <div class="col-sm-6 col-lg-6 controls">
                <input class="form-control datepikr" name="end_date" id="end_date" placeholder="{{ translation('enter') }} {{ translation('end_date') }}" type="text"  data-rule-required='true'/>
              </div>
            </div>
          </div><div class="clearfix"></div>
        </div> 



        <div class="form-group">
          <label class="col-sm-3 col-lg-2 control-label">{{translation('start_time')}} <i class="red">*</i></label>
          <div class="col-sm-9 col-lg-4 controls">
            <div class="row">
              <div class="col-sm-6 col-lg-6 controls">
                <input class="form-control timepicker-default" name="start_time" id="start_time" placeholder="{{ translation('enter')}} {{translation('start_time')}}" type="text" data-rule-required='true' />
              </div>
              <div class="col-sm-6 col-lg-6 controls">
                <input class="form-control timepicker-default" name="end_time" id="end_time" placeholder="{{translation('enter')}} {{translation('end_time')}}" type="text" data-rule-required='true'/>
              </div>
            </div>
          </div>
        </div>                                

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
  $(".js-example-basic-multiple").select2();
</script>               
<script>
  function Changefilename(event){
                      console.log($(event).next().children('input'));
                      var file = event.files;
                      validateDocument(event.files,null);
                      $(event).next().children('input').val($(event).val());
                  }
  $(document).on("change",".validateDoc", function()
 {       
    var file = this.files;
    validateDocument(this.files,null);

 });


 function validateDocument(files,element_id) 
 {
    //var default_img_path = site_url+'/front/images/uploadimg.png';

    if (typeof files !== "undefined") 
    {
      for (var i=0, l=files.length; i<l; i++) 
      {
            var blnValid = false;
            var ext = files[i]['name'].substring(files[i]['name'].lastIndexOf('.') + 1);
            
            if(ext=="pdf" || ext=='doc' || ext=='docx' || ext=='mp4' || ext=="PDF" || ext=='DOC' || ext=='DOCX' || ext=='MP4' )
            {
                  blnValid = true;
            }  
            
            
            if(blnValid ==false) 
            {
              
                showAlert("Sorry, " + files[0]['name'] + " is invalid, allowed extensions are: pdf, doc,docx,mp4","error");
                $("#pdffile_0").val('');
                return false;
            }
            else
            {              
                
                if(files[0].size>10485760)
                {
                   $("#pdffile_0").val('');
                   showAlert("File size should be less than 10 MB","error");
                }
                       
            }                
        }
    }
    else
    {
       $("#pdffile_0").val('');
      showAlert("No support for the File API in this web browser" ,"error");
    } 
  }
  
  var date = new Date();
  date.setDate(date.getDate());
  $(function() {
    $( "#publish_date" ).datepicker({
      todayHighlight: true,
      autoclose: true,
      startDate: date
    });
  }); 
  $(function() {
    $( "#end_date" ).datepicker({
      todayHighlight: true,
      autoclose: true,
      startDate: date
    });
  }); 
  $( function() {    
    $('.timepicker-default').timepicker({
         
    }); });
  $.fn.timepicker.defaults = {
        defaultTime: '00',
        disableFocus: false,
        disableMousewheel: false,
        isOpen: false,
        minuteStep: 15,
        modalBackdrop: false,
        orientation: { x: 'auto', y: 'auto'},
        secondStep: 15,
        showSeconds: false,
        showInputs: true,
        showMeridian: false,
        template: 'dropdown',
        appendWidgetTo: 'body',
        showWidgetOnAddonClick: true
      };
            </script> 
            <script type="text/javascript">
            //dropzone script with multiple files
            (function($) {
             function readMultiUploadURL(input, callback) {

                if (input.files) {
                      $.each(input.files, function(index, file) {


                      var blnValid = false;
                      var ext = input.files[0]['name'].substring(input.files[0]['name'].lastIndexOf('.') + 1);
                      if(ext == "JPEG" || ext == "jpeg" || ext == "jpg" || ext == "JPG" || ext == "png" || ext == "PNG")
                      {
                        blnValid = true;
                      }
                      
                      if(blnValid ==false) 
                      {
                          showAlert("Please select valid image to upload","error");
                          return false;
                            
                      }
                      else
                      {
                           var reader = new FileReader();
                           reader.onload = function(e) {

                               callback(false, e.target.result);
                           }
                           reader.readAsDataURL(file);

                      }


                       });

                   }
              callback(true, false);
            }


            var arr_multiupload = $("span[data-multiupload]");


            if (arr_multiupload.length > 0) {
             $.each(arr_multiupload, function(index, elem) {

               console.log(elem);
               var container_id = $(elem).attr("data-multiupload");


               var id_multiupload_img = "multiupload_img_" + container_id + "_";
               var id_multiupload_img_remove = "multiupload_img_remove" + container_id + "_";
               var id_multiupload_file = id_multiupload_img + "_file";

               var block_multiupload_src = "data-multiupload-src-" + container_id;
               var block_multiupload_holder = "data-multiupload-holder-" + container_id;
               var block_multiupload_fileinputs = "data-multiupload-fileinputs-" + container_id;


               var input_src = $(elem).find("input[data-multiupload-src]");
               $(input_src).removeAttr('data-multiupload-src')
               .attr(block_multiupload_src, "");


               var block_img_holder = $(elem).find("span[data-multiupload-holder]");
               $(block_img_holder).removeAttr('data-multiupload-holder')
               .attr(block_multiupload_holder, "");

               var block_fileinputs = $(elem).find("span[data-multiupload-fileinputs]");
               $(block_fileinputs).removeAttr('data-multiupload-fileinputs')
               .attr(block_multiupload_fileinputs, "");

               $(input_src).on('change', function(event) {

                 readMultiUploadURL(event.target, function(has_error, img_src) {


                   if (has_error == false) {
                     addImgToMultiUpload(img_src);
                   }
                 })
               });

               function addImgToMultiUpload(img_src) {
                 var id = Math.random().toString(36).substring(2, 10);

                 var html = '<div class="upload-photo" id="' + id_multiupload_img + id + '">' +
                 '<span class="upload-close">' +
                 '<a href="javascript:void(0)" id="' + id_multiupload_img_remove + id + '" ><i class="fa fa-trash-o"></i></a>' +
                 '</span>' +
                 '<img src="' + img_src + '" >' +
                 '</div>';

                 var file_input = '<input type="file" name="file[]" id="' + id_multiupload_file + id + '" style="display:none" />';
                 $(block_img_holder).append(html);
                 $(block_fileinputs).append(file_input);

                 bindRemoveMultiUpload(id);
               }

               function bindRemoveMultiUpload(id) {
                 $("#" + id_multiupload_img_remove + id).on('click', function() {
                   $("#" + id_multiupload_img + id).remove();
                   $("#" + id_multiupload_file + id).remove();
                 });
               }


             });
           }
         })(jQuery);
       </script>
       <script type="text/javascript">
        $(document).ready(function(){
                // This is the simple bit of jquery to duplicate the hidden field to subfile
                $('#pdffile').change(function(){
                  $('#subfile').val($(this).val());
                });

                // This bit of jquery will show the actual file input box
                $('#showHidden').click(function(){
                  $('#pdffile').css('visibilty','visible');
                });

                // This is the simple bit of jquery to duplicate the hidden field to subfile
                $('#pdffile1').change(function(){
                  $('#subfile1').val($(this).val());
                });

                // This bit of jquery will show the actual file input box
                $('#showHidden1').click(function(){
                  $('#pdffile1').css('visibilty','visible');
                });
              });
            </script>

<script type="text/javascript">

$(document).on("change","#publish_date",function(){ 
   var publish_date = $(this).val();  
   var end_date   = $("#end_date").val();  

   if (publish_date != ''  && (end_date!='')) {
      if(publish_date > end_date) {
        $("#publish_date").val('');
        swal("{{translation('start_date_can_not_be_greater_than_end_date')}}");
      }
  }  

});
$(document).on("change","#end_date",function(){ 
   var end_date = $(this).val();  
   var publish_date   = $("#publish_date").val();  

   if (publish_date != ''  && (end_date!='')) {
    if(publish_date > end_date) 
    {
      $("#end_date").val('');
      swal("{{translation('end_date_can_not_be_less_than_start_date')}}");
    }
  }  
});
 
</script>

<script type="text/javascript">
  $(document).ready(function(){
    // This is the simple bit of jquery to duplicate the hidden field to subfile
    $('#pdffile').change(function(){
      $('#subfile').val($(this).val());
    });

    // This bit of jquery will show the actual file input box
    $('#showHidden').click(function(){
      $('#pdffile').css('visibilty','visible');
    });

    // This is the simple bit of jquery to duplicate the hidden field to subfile
    $('#pdffile1').change(function(){
      $('#subfile1').val($(this).val());
    });

    // This bit of jquery will show the actual file input box
    $('#showHidden1').click(function(){
      $('#pdffile1').css('visibilty','visible');
    });
  });
</script>
@endsection
