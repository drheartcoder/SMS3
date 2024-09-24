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
      <a href="{{ $module_url_path }}">{{ $module_title or ''}}</a>
      </span> 
      <span class="divider">
      <i class="fa fa-angle-right"></i>
      <i class="fa fa-plus-circle"></i>
      </span>
      <li class="active">{{ $page_title or ''}}</li>
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
<!-- BEGIN Main Content -->
<div class="row">
   <div class="col-md-12">
      <div class="box">
         <div class="box-title">
            <h3>
               <i class="fa fa-plus-circle"></i>
               {{ isset($page_title)?$page_title:"" }}
            </h3>
            <div class="box-tool">
            </div>
         </div>
         <div class="box-content edit-space">
            @include('schooladmin.layout._operation_status')  
            <div class="tabbable">
               <form class="form-horizontal" onsubmit="return addLoader()" id="validation-form1" method="POST" action="{{ $module_url_path }}/store">
                  {{ csrf_field() }}
                  <div class="row">
                     <div class="col-lg-6">
                        <div class="row">
                           <div class="form-group">
                              <label class="col-sm-3 col-lg-4 control-label" for="state"> 
                              {{translation('school_level')}}<i class="red">*</i> 
                              </label>
                              <div class="col-sm-6 col-lg-8 controls">
                                 <select class="form-control chosen" name="school_level_id" data-rule-required="true">
                                    <option value="">{{translation('select_school_level')}}</option>
                                    @if(isset($arr_level) && sizeof($arr_level))
                                    @foreach($arr_level as $level_key => $level)
                                      @if(isset($level['level_details']['level_name']))
                                        <option value="{{$level['level_id']}}">{{$level['level_details']['level_name']}}</option>
                                      @endif  
                                    @endforeach     
                                    @endif
                                 </select>
                                 <span class='help-block'>{{ $errors->first('school_level_id') }}</span> 
                                 <span id='err_exam_type' style="color: red"></span> 
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-sm-3 col-lg-4 control-label" for="state"> 
                              {{translation('course_name')}}<i class="red">*</i> 
                              </label>
                              <div class="col-sm-6 col-lg-8 controls">
                                 <input type="text" name="course_name" data-rule-required='true'  id="search-box" value="{{old('course_name')}}" class="form-control" pattern="[a-zA-Z0-9\- àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$"  placeholder="{{translation('course_name')}}">
                                 <div id="suggesstion-box" name="suggesstion-box" ></div>
                                 <span class='help-block'>{{ $errors->first('course_name') }}</span> 
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-sm-3 col-lg-4 control-label" for="state"> 
                              {{translation('coefficient')}}<i class="red">*</i> 
                              </label>
                              <div class="col-sm-6 col-lg-8 controls">
                                 <input type="text" name="coefficient" 
                                    data-rule-required='true' data-rule-digits='true' id="coefficient" data-rule-minlength='1' data-rule-maxlength='4' value="{{old('coefficient')}}" class="form-control" placeholder="{{translation('coefficient')}}">
                                 <span class='help-block'>{{$errors->first('coefficient')}}</span> 
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <br/>
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
             url: "{{$module_url_path.'/'.'get_course_name_suggession?keyword='}}"+key,
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
                     str =' <li onclick="selectCourse(\''+val.get_course.course_name+'\')">'+val.get_course.course_name+'</li>';
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
   
</script>
<script>
   function selectCourse(val) 
   {
     $("#search-box").val(val);
     $("#suggesstion-box").hide();
   }
     
   
     
</script>
@endsection