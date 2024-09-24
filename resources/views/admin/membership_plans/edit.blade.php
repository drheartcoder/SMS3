@extends('admin.layout.master')    
@section('main_content')

<style>
    .check-box.position-div .help-block {position: absolute;bottom: -9px;}
    .radio-btn.replay-positions .help-block {position: absolute;bottom: 10px;}
    .col-lg-12{position: static;}
    .panel-body{position: relative;}
    .check-box.position-div{position: static;}
    .check-box.position-div .help-block {bottom: 10px;}
    .carousel-inner{text-align: center;}
    .carousel-inner>.item>img{margin: 0 auto;}
</style>
<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home">
      </i>
      <a href="{{ url($admin_panel_slug.'/dashboard') }}"> {{translation('dashboard')}}
      </a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa fa-bar-chart">
      </i>
    </span> 
    <li >  <a href="{{$module_url_path}}"> {{ isset($module_title)?$module_title:"" }}</a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa fa-plus">
      </i>
    </span> 
    <li class="active">  {{ isset($page_title)?$page_title:"" }}
    </li>
  </ul>
</div>
<!-- END Breadcrumb -->


<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="fa fa-bar-chart"></i> {{ isset($module_title)?$module_title:"" }} </h1>
    </div>
</div><!-- END Page Title -->
<!-- BEGIN Main Content -->
<div class="row">
  <div class="col-md-12">
    <div class="box {{ $theme_color }}">
      <div class="box-title">
        <h3>
          <i class="fa fa-edit">
          </i> {{ isset($page_title)?$page_title:"" }} 
        </h3>
        <div class="box-tool">
        </div>
      </div>
      <div class="box-content">
        @include('admin.layout._operation_status')
          <div class="tabbable">
          <form method="POST" id="validation-form1"  onsubmit="return addLoader()"  class="form-horizontal" action="{{ $module_url_path}}/update/{{$enc_id}}" enctype ='multipart/form-data'>
                {{ csrf_field() }}              
                
                <div style="border: none;" class="tab-content">
                             <div class="row">
                             <div class="col-lg-6">
                             <div class="row">
                              <div class="form-group">
                                <label class="col-sm-3 col-lg-4 control-label">{{translation('plan_name')}} 
                                    <i class="red">*</i> 
                                </label>
                                <div class="col-sm-4 col-lg-8 controls">
                                    <input type="text" name="plan_name" data-rule-required='true' class="form-control" value="{{isset($arr_data['plan_name']) && $arr_data['plan_name']!='' ? $arr_data['plan_name']:'-' }}" placeholder="{{translation('enter')}} {{translation('plan_name')}}"/>
                                    <span class='help-block'>{{ $errors->first('plan_name')}}</span>
                                </div>
                              </div>
                              <div class="clearfix"></div>                              
                        
                              <div class="form-group">
                                <label class="col-sm-3 col-lg-4 control-label" >{{translation('duration_type')}}  
                                  <i class="red">*</i> 
                                </label>
                                <div class="col-sm-4 col-lg-8 controls">
                                    <div class="radio-btns">  
                                        <div class="radio-btn">
                                            <input type="radio" id="f-option" value="{{translation('year')}}"" {{isset($arr_data['duration_type']) && $arr_data['duration_type']=='year' ?'checked=""':''}}" name="duration_type" value="red">
                                            <label for="f-option">{{translation('in_year')}}</label>
                                            <div class="check"></div>
                                        </div>
                                        <div class="radio-btn">
                                            <input type="radio" id="s-option" value="{{translation('month')}}" {{isset($arr_data['duration_type']) && $arr_data['duration_type']=='month' ?'checked=""':''}}" name="duration_type" value="green">
                                            <label for="s-option">{{translation('in_month')}} </label>
                                            <div class="check"><div class="inside"></div></div>
                                        </div>
                                    </div>
                                </div> 
                              </div>                      
                              <div class="clearfix"></div>
                             
                              <div class="form-group">
                                <label class="col-sm-3 col-lg-4 control-label">{{translation('duration_value')}} {{translation('monthyear')}}
                                  <i class="red">*</i> 
                                </label>
                                <div class="col-sm-4 col-lg-8 controls">        
                                    <input type="text" name="duration_value" class="form-control commonNumber"  value="{{isset($arr_data['duration_value']) && $arr_data['duration_value']!=0 ? $arr_data['duration_value']:'0' }}" placeholder="{{translation('enter')}} {{translation('duration_value')}}"  data-rule-required="true" data-rule-minlength='1' data-rule-maxlength='2'  data-msg-minlength="{{translation('please_enter_at_least_1_digits')}}" data-msg-maxlength="{{translation('please_enter_no_more_than_2_digits')}}" min="1"/>  
                                    <span class='help-block'>{{ $errors->first('duration_value') }}</span>
                                </div> 
                              </div>
                     
                              <div class="clearfix"></div>
                              <div class="form-group">
                                <label class="col-sm-3 col-lg-4 control-label">{{translation('price')}} {{translation('mad')}}
                                  <i class="red">*</i> 
                                </label>
                                <div class="col-sm-4 col-lg-8 controls">        
                                    <input type="text" name="price" class="form-control"  value="{{isset($arr_data['price']) && $arr_data['price']!=0 ? $arr_data['price']:'0' }}" placeholder="{{translation('enter')}} {{translation('price')}}"  data-rule-required="true"  data-rule-minlength='1' data-rule-maxlength='9' data-msg-minlength="{{translation('please_enter_at_least_1_digits')}}" data-msg-maxlength="{{translation('please_enter_no_more_than_9_digits')}}" min="1"/>  
                                    <span class='help-block'>{{ $errors->first('price') }}</span>
                                </div> 
                              </div>
                                    
                              <div class="clearfix"></div>
                              <div class="form-group">
                                <label class="col-sm-3 col-lg-4 control-label">{{translation('stackholder')}} 
                                  <i class="red">*</i> 
                                </label>
                               <div class="col-sm-4 col-lg-8 controls">    
                              <div >
                              <?php 
                                    $str_stack = isset($arr_data['stackholders']) && $arr_data['stackholders']!='' ? $arr_data['stackholders']:''; 
                                    $arr_stack = get_stackholders($str_stack);                                    
                               ?>

                               @if(count($arr_stack)>0)
                                  
                              @foreach($arr_roll as $key=>$roll )
                               <input   id="{{$key}}"
                              name="stackholders[]" data-rule-required='true'   value="{{$roll['id']}}" <?php  echo (in_array($roll['id'], $arr_stack))? "checked='checked'":''; ?>  type="checkbox" data-rule-required='true'>
                              {{$roll['name']}} <br/>
                                @endforeach
                               
                         
                            <span id="err" style="color: red"></span>
                             
                            
                            @endif

                        </div>
                        </div>
                                 </div>
                                 </div>
                    </div>

                </div>
                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">    
                    <a href="{{$module_url_path}}" class="btn btn-primary">{{translation('back')}}</a>
                               <button type="submit"  id="submit_button" class="btn btn-primary"> {{translation('update')}}</button>               
                    </div>
                </div> 
              </form>     
            </div>  
      </div>
    </div>
  </div>
  <!-- END Main Content -->  
 <script>
  $(document).on('keydown', '.commonNumber', function(e){
        if(e.shiftKey === true)
        {
          e.preventDefault();
        }
        if ($.inArray(e.keyCode, [86, 17, 46, 8, 9, 27, 13, 190]) !== -1 ||
         // Allow: Ctrl+A
        (e.keyCode == 65 && e.ctrlKey === true) ||
         // Allow: Ctrl+V
        (e.keyCode == 86 && e.ctrlKey === true) ||
         // Allow: home, end, left, right
        (e.keyCode >= 35 && e.keyCode <= 39)) {
           // let it happen, don't do anything
           return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57) ) && (e.keyCode < 96 || (e.keyCode > 105 && e.keyCode==110))) {
           e.preventDefault();
        }
    });



</script>
 
@endsection
