@extends('schooladmin.layout.master')    
@section('main_content')
<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
   <ul class="breadcrumb">
      <li>
         <i class="fa fa-home">
         </i>
         <a href="{{ url($school_admin_panel_slug.'/dashboard') }}"> {{translation('dashboard')}}
         </a>
      </li>
      <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="{{$module_icon}}">
      </i>
      </span> 
      <li >  <a href="{{$module_url_path}}"> {{ isset($page_title)?$page_title:"" }}</a>
      </li>
      <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa {{$create_icon}}">
      </i>
      </span> 
      <li class="active">  {{ isset($module_title)?$module_title:"" }}
      </li>
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
            <i class="fa {{$create_icon}}">
            </i> {{ isset($module_title)?$module_title:"" }} 
         </h3>
         <div class="box-tool">
         </div>
      </div>
      <div class="box-content">
         @include('admin.layout._operation_status')
         <div class="tabbable">
            <form method="POST" onsubmit="return addLoader()" id="validation-form1" class="form-horizontal" action="{{ $module_url_path}}/store" >
               {{ csrf_field() }}              
               <div class="row">
                  <div class="col-sm-12 col-lg-6">
                     <div class="row">
                        <div class="form-group">
                           <label class="col-sm-3 col-lg-4 control-label" for="state">{{translation('exam_period')}}  
                           <i class="red">*</i>
                           </label>
                           <div class="col-sm-4 col-lg-8 controls">
                              <input type="text" name="exam_name" id="search-box" class="form-control add-stundt"  placeholder="{{translation('enter')}} {{translation('exam_period')}}" pattern="[a-zA-Z0-9\- àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" data-rule-required="true"/>
                              <div id="suggesstion-box" name="suggesstion-box"> </div>
                              <span class='help-block'>{{ $errors->first('exam_name') }}</span>
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
<!-- END Main Content -->  
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
                           url: "{{$module_url_path.'/'.'get_exam_period_suggession?keyword='}}"+key,
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
                                 var str ='<li onClick="selectCountry(\''+val.exam_name+'\')">'+val.exam_name+'</li>';
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
   function selectCountry(val) 
   {
     var searchs = "#search-box";
     $(searchs).val(val);
   
     var suggessions = "#suggesstion-box";
   
     var key = val;
       
     $.ajax
           ({ 
               type: "get",
               url: "{{$module_url_path.'/'.'get_exam_period_suggession'}}",
               data:{keyword:key},
               success: function(data)
               { 
                 if(data)
                  {
                      var str = '';
                     var str_second ='';
                      data = JSON.parse(data);
                      $.each(data,function(key,val)
                      {
                        var str =' <li onclick="selectType(\''+val.get_exam_period.exam_name+'\')">'+val.get_exam_period.exam_name+'</li>';
                           str_second +=str;
                      });
                       str_third ='<ul id="country-list">'+str_second+'</ul>';
                     
                        $("#suggesstion-box").html(str_third);
                  }
               }
           });
     $(suggessions).hide();
   }
   
   function selectType(val) {
$("#search-box").val(val);
$("#suggesstion-box").hide();
}
     
</script>
@endsection