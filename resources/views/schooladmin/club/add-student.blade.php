@extends('schooladmin.layout.master') @section('main_content')
<!-- BEGIN Breadcrumb -->

<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{ url($school_admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        <li>
            <a href="{{ url($module_url_path) }}">{{$page_title}}</a>
        </li>
        <span class="divider">
        <i class="fa fa-angle-right"></i>
        </span>
        <li class="active">{{ isset($module_title)?$module_title:"" }}</li>
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
            <h3><i class="{{$create_icon}}"></i>{{ isset($module_title)?$module_title:"" }}</h3>
            <div class="box-tool">
            </div>
         </div>
         <div class="box-content studt-padding">
            @include('professor.layout._operation_status')   
            <div class="row ">
               <div class="details-infor-section-block">
                  {{$page_title}}
               </div>
               <br>
               <div class="form-group">
                  <label class="col-sm-2 col-lg-2 control-label" style="text-align:left"><b> {{translation('club_name')}}  </b>: </label>
                  <label class="col-sm-2 col-lg-2 control-label" style="text-align:left"> {{$obj_club->club_name}} </label>
                  <div class="clearfix"></div>
               </div>
               <div class="form-group">
                  <label class="col-sm-2 col-lg-2 control-label" style="text-align:left"><b> {{translation('description')}}  </b>: </label>
                  <label class="col-sm-2 col-lg-2 control-label" style="text-align:left">{{$obj_club->description}} </label>
                  <div class="clearfix"></div>
               </div>
               <div class="form-group">
                  <label class="col-sm-2 col-lg-2 control-label" style="text-align:left"><b> {{translation('level')}}</b><i class="red">*</i></label>
                  <div class="col-sm-2 col-lg-2 controls">
                     <select name="level" id="level" class="form-control level" data-rule-required="true">
                        <option value="">{{translation('select_level')}}</option>
                        @if(isset($arr_levels) && count($arr_levels)>0)
                        @foreach($arr_levels as $value)
                        <option value="{{$value['level_id']}}" >{{$value['level_details']['level_name']}}</option>
                        @endforeach
                        @endif    
                     </select>
                     <span class='help-block'>{{ $errors->first('level') }}</span>    
                  </div>
               </div>
               
               <div class="form-group">
                  <label class="col-sm-2 col-lg-2 control-label" style="text-align:left">{{translation('class')}}<i class="red">*</i></label>
                  <div class="col-sm-2 col-lg-2 controls">
                     <select name="class" id="class" class="form-control level-class" data-rule-required='true'>
                        <option value="">{{translation('select_class')}}</option>
                     </select>
                     <span class='help-block'>{{ $errors->first('class')}}
                  </div>
               </div>
            </div>
            <br>
            @include('schooladmin.layout._operation_status')  
            {!! Form::open([ 'url' => $module_url_path.'/store_student/'.$obj_club->id,
            'method'=>'POST', 
            'class'=>'form-horizontal'
            ]) !!}
            {{ csrf_field() }}
            <input type="hidden" name="level_class_id" id="level_class_id"/>
            <div class="display-table" style="display:none ">
               <div class="filter-section">
                  <div class="row">
                     <div class="col-md-4">
                        <div class="form-group">                                                                                        
                           <input class="form-control" name="search_key" id="search_key" type="text" placeholder="{{translation('search')}}...">
                        </div>
                     </div>
                  </div>
               </div>
               <table class="table table-advance" id="table_module">
                  <thead>
                     <tr>
                        <th></th>
                        <th class="sorting_disabled">
                           <a class="sort-descs" >{{translation('name')}}</a><br>
                        </th>
                        <th class="sorting_disabled">
                           <a class="sort-descs" >{{translation('national_id')}}</a><br>
                        </th>
                     </tr>
                  </thead>
                  <tbody>
                  </tbody>
               </table>
            </div>
            <div id="hide_row" class="alert alert-danger" style="text-align:center" hidden>{{translation('no_data_available')}}   
            </div>
            <div style="float:right">
               <div class="form-group back-btn-form-block" style="display:inline-block">
                  <div class="controls">
                     <button class="btn btn-primary" style="float: right;margin-top: 20px;" >{{translation('update')}} </button>
                  </div>
               </div>
               <div class="form-group back-btn-form-block" style="display:inline-block">
                  <div class="controls">
                     <a href="{{ $module_url_path }}" class="btn btn-primary" style="float: right;margin-top: 20px;" > <i class="fa fa-arrow-left"></i> {{translation('back')}} </a>
                  </div>
               </div>
            </div>
               
            {!! Form::close() !!}
         </div>
      </div>
   </div>
</div>    <!-- END Main Content -->
<script>


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

  $(".level-class").on('change',function(){
    var level_class = $('.level-class').val();
 
       $.ajax({
          url  :"{{ $module_url_path }}/get_student",
          type :'get',
          data :{'_token':'<?php echo csrf_token();?>','level_class':level_class,'club_id':"{{$obj_club->id}}"},
          success:function(data){
                 $("#level_class_id").val(level_class);
                 $("tbody").html(data);
                 if(data==''){
                    $(".display-table").hide();
                 }
                 else{
                    $(".display-table").show();
                 }
              
          }
    });

  });


  $("#search_key").keyup(function(){
    var flag=0;
        $("tbody tr").each(function(){
          
            var td = $(this).find("td");
            $(td).each(function(){
              var data = $(this).text().trim();
              data = data.toLowerCase();

              var search_key = $("#search_key").val();
              search_key = search_key.toLowerCase();
              search_key = new RegExp(search_key) ; 
                console.log(search_key.test(data));
                if(search_key.test(data)){
                  flag=1;
                  $(this).parent().show();
                  return false;
                }
                else{
                  $(this).parent().hide();
                }
                console.log(data);
                

            });
         })
         if(flag==0)
          {
            $("#hide_row").show();
          }
          else
          {
            $("#hide_row").hide();
          }  
      })
  
</script>

@endsection