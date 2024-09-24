@extends('admin.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="{{ url($admin_panel_slug.'/dashboard') }}"> {{translation('dashboard')}} </a>
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
        <h1><i class="{{$module_icon}}"></i>{{str_plural($module_title)}}</h1>
    </div>
</div>
<!-- END Page Title -->

<!-- BEGIN Main Content -->
<div class="row">
  <div class="col-md-12">
    <div class="box {{ $theme_color }}">
      <div class="box-title pr0">
        <h3>
          <i class="fa fa-list"></i>
          {{ isset($page_title)?str_plural($page_title):"" }}
        </h3>
        <div class="box-tool"> 
          @if(array_key_exists('role.create', $arr_current_user_access))             
           <a href="{{ $module_url_path.'/create' }}"  title="Add {{ str_singular($module_title) }}">{{translation('add')}} {{ str_singular($module_title) }}</a> 
          @endif
        </div>
      </div>
      <div class="box-content">  
          @include('admin.layout._operation_status') 
          {!! Form::open([ 'url' => $module_url_path.'/multi_action',
                                 'method'=>'POST',
                                 'enctype' =>'multipart/form-data',   
                                 'class'=>'form-horizontal', 
                                 'id'=>'frm_manage' 
                                ]) !!} 

            {{ csrf_field() }}
            <div class="col-md-10">
              <div class="alert alert-danger" id="no_select" style="display:none;"></div>
              <div class="alert alert-warning" id="warning_msg" style="display:none;"></div>
            </div>
          <div class="btn-toolbar pull-right clearfix">

          
          <div class="btn-group">
          
          </div>

            
          </div>
          <br/>
          <div class="clearfix"></div>
          <div class="table-responsive" style="border:0">

            <input type="hidden" name="multi_action" value="" />

            <table class="table table-advance"  id="table4" >
              <thead>
                <tr>
                  <th>{{translation('sr_no')}}.</th>
                  <th>{{ translation('role')}}</th> 
                  <th> {{translation('action')}}</th>
                </tr>
              </thead>
              <tbody>
                <?php $no = 0;?>
                @if(sizeof($arr_data)>0)
                  @foreach($arr_data as $data)
                  <tr>
                    <td width="70px">{{++$no}}</td>
                    <td> {{ ucfirst($data['name']) }} </td>
                     
                     @if(array_key_exists('role.create', $arr_current_user_access))  
                     <td>           
                      <a class="orange-color" href="{{ $module_url_path.'/edit/'.base64_encode($data['id']) }}" title="Edit">
                          <i class="fa fa-edit" ></i>
                      </a>
                      </td>    
                  @endif
                  </tr>
                  @endforeach
                @endif
              </tbody>
            </table>
          </div>
        <div> </div>
          {!! Form::close() !!}
      </div>
  </div>
</div>

@stop                    


