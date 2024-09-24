@extends('schooladmin.layout.master')                
@section('main_content')
<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
   <ul class="breadcrumb">
      <li>
         <i class="fa fa-home"></i>
         <a href="{{ url($school_admin_panel_slug.'/dashboard') }}">{{ translation('dashboard') }}</a>
      </li>
      <span class="divider">
      <i class="fa fa-angle-right"></i>
      <i class="fa fa-book"></i>
      <a href="{{ $module_url_path }}">{{ $page_title or ''}}</a>
      </span> 
      <span class="divider">
      <i class="fa fa-angle-right"></i>
      <i class="fa fa fa-plus-circle"></i>
      </span>
      <li class="active">{{ $module_title or ''}}</li>
   </ul>
</div>
<!-- END Breadcrumb -->
<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
   <div>
      <h1><i class="fa fa-book"></i>{{$page_title}}</h1>
   </div>
</div>
<!-- END Page Title -->
<!-- BEGIN Main Content -->
<div class="row">
   <div class="col-md-12">
      <div class="box">
         <div class="box-title">
            <h3>
               <i class="fa fa-plus-circle"></i>
               {{ isset($module_title)?$module_title:"" }}
            </h3>
            <div class="box-tool">
            </div>
         </div>
         <div class="box-content edit-space">
         @include('schooladmin.layout._operation_status')   
            <form class="form-horizontal" onsubmit="return addLoader()" id="validation-form1" method="POST" action="{{ $module_url_path }}/store">
               {{ csrf_field() }}
               <div class="row">
                  <div class="col-sm-6 col-lg-6">
                     <div class="row">
                        <div class="form-group">
                           <label class="col-sm-3 col-lg-4 control-label" for="state">
                           {{translation('exam_type')}}<i class="red">*</i> 
                           </label>
                           <div class="col-sm-6 col-lg-8 controls">
                              <input type="text" name="exam_type" data-rule-required='true' id="search-box" value="{{old('level')}}" class="form-control" pattern="[a-zA-Z0-9\- àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" placeholder="{{translation('exam_type')}}">

                              <div id="suggesstion-box" name="suggesstion-box" ></div>
                              <span for="search-box" class='help-block'>{{ $errors->first('exam_type') }}</span> 
                           </div>

                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 col-lg-4 control-label">{{translation('include_in_gradebook')}}<i class="red">*</i></label>
                            <div class="col-sm-8 col-lg-8 controls">
                                <select name="gradebook" class="form-control" data-rule-required='true'>
                                 <option value="test" selected>{{translation('yes')}}</option>
                                 <option value="other" >{{translation('no')}}</option>
                                </select>
                                <span class='help-block'>{{ $errors->first('gradebook') }}</span>
                            </div>
                        </div>  
                     </div>
                  </div>
               </div>
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
<script>
   $(document).ready(function()
           {
           
     var search = "#search-box";
     
     var suggession = "#suggesstion-box"; 
             
     $(search).keyup(function()
     {
         var key = $(search).val();
         $.ajax
         ({
             type: "get",
             url: "{{$module_url_path.'/'.'get_exam_type_suggession?keyword='}}"+key,
             data:{keyword:$(this).val()},
             success: function(data)
             {
               if(data!="")
               {
                 var str = '';
                 var str_second ='';
                  data = JSON.parse(data);
                  $.each(data,function(key,val)
                  {
                    var str =' <li onclick="selectType(\''+val.get_exam_type.exam_type+'\')">'+val.get_exam_type.exam_type+'</li>';
                       str_second +=str;
                  });
                   str_third ='<ul id="country-list">'+str_second+'</ul>';
                   
                      $("#suggesstion-box").html(str_third);            
               }
               else{
                      $("#suggesstion-box").html('');
                    }
             }
         });
     });  
   
    });

function selectType(val) {
$("#search-box").val(val);
$("#suggesstion-box").hide();
}
</script>

@endsection