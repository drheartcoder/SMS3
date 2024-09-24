@extends('schooladmin.layout.master')                
@section('main_content')
<style>
.chosen-container-single .chosen-single div b {position: absolute;right: 0px;}
</style>
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
      
    </span> 
      <i class="fa fa-phone">
      </i>
    </span> 
    <li>  <a href="{{$module_url_path}}"> {{$module_title}} </a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right"></i>
      <i class="fa fa-info-circle"></i>   
    </span> 
    <li class="active">
      {{isset($page_title)?$page_title:''}}
    </li>
  </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="fa fa-phone">
      </i>{{$module_title}}</h1>
    </div>
</div>
<!-- END Page Title -->

<!-- BEGIN Main Content -->
<div class="row">
  <div class="col-md-12">
    <div class="box {{ $theme_color }}">
      <div class="box-title">
        <h3>
          <i class="fa fa-plus-circle">
          </i> {{ isset($page_title)?$page_title:"" }} 
        </h3>
        <div class="box-tool">
        </div>
      </div>
      <div class="box-content">
        @include('schooladmin.layout._operation_status')
          <div class="tabbable">
            <form method="POST" id="validation-form1"  onsubmit="return addLoader()"  class="form-horizontal" action="{{ $module_url_path}}/store" enctype ='multipart/form-data'>
                  {{ csrf_field() }}              
                   
                  <div class="form-group" style="margin-top: 25px;">
                      <label class="col-sm-3 col-lg-2 control-label"> {{translation('enquiry_category')}} <i style="color:red;">*</i></label>
                      <div class="col-sm-9 col-lg-6 controls">
                          <select class="form-control chosen" name="category_id" data-rule-required="true">
                            <option value="">{{translation('select_category')}}</option>
                              @if(isset($arr_category) && sizeof($arr_category)>0)
                                @foreach($arr_category as $arr_cat)
                                   <option value="{{ $arr_cat['id'] }}">{{ isset($arr_cat['title'])?ucwords($arr_cat['title']):''}}</option>
                                @endforeach
                              @endif
                          </select>
                          <span class="help-block">{{ $errors->first('category_id') }}</span>
                      </div>
                  </div>
                               
                  <div class="clearfix"></div>
                  <div class="clearfix"></div>                                            
                            
                  <div class="form-group" style="margin-top: 25px;">
                    <label class="col-sm-3 col-lg-2 control-label">{{translation('subject')}}  
                      <i class="red">*</i> 
                    </label>
                   <div class="col-sm-9 col-lg-6 controls">
                        <input type="text" name="subject" data-rule-required='true' class="form-control" value="{{old('subject')}}" placeholder="{{translation('enter')}} {{translation('subject')}}"/>
                        <span class='help-block'>{{ $errors->first('subject')}}</span>
                    </div>
                  </div>
                              
                  <div class="clearfix"></div>  
                                  
                  <div class="form-group" style="margin-top: 25px;">
                    <label class="col-sm-3 col-lg-2 control-label"> {{translation('description')}} <i style="color:red;">*</i></label>
                    <div class="col-sm-9 col-lg-6 controls">
                        <textarea type="text" class="form-control" name="description" data-rule-required="true" data-rule-maxlength="1000" placeholder="{{translation('enter')}} {{translation('description')}}" rows="6">  </textarea>   
                        <small style="color: gray!imported;font-size: 10px;">{{translation('noteallowed_1000_characters_only')}}</small>
                        <span class="help-block">{{ $errors->first('description') }}</span>
                    </div>
                  </div>
                
                </div> <br/>
                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                               <button type="submit"  id="submit_button" class="btn btn-primary"> {{translation('send')}}</button>               
                    </div>
                </div> 
              </form>     
            </div>  
      </div>
 </div>
 </div>
  <!-- END Main Content -->  
@endsection
