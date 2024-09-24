@extends('schooladmin.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="{{ url($school_admin_panel_slug.'/dashboard') }}"> {{translation('dashboard')}} </a>
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
        <h1><i class="{{$module_icon}}"></i>{{$module_title}}</h1>
    </div>
</div>
<!-- END Page Title -->

<!-- BEGIN Main Content -->
<div class="row">
  <div class="col-md-12">
    <div class="box {{ $theme_color }}">
      <div class="box-title">
        <h3>
          <i class="fa fa-list"></i>
          {{ isset($page_title)?$page_title:"" }}
        </h3>
        <div class="box-tool">
            @if(array_key_exists('fees_structure.create', $arr_current_user_access))  
              <a href="{{$module_url_path}}/create" >{{translation("add").' '.translation("fees_structure") }}</a> 
            @endif
               <a 
                  title="{{translation('refresh')}}" 
                  href="{{ $module_url_path }}"
                  style="text-decoration:none;">
               <i class="fa fa-repeat"></i>
               </a> 
           
            </div>
      </div>
      <div class="box-content">  
          @include('schooladmin.layout._operation_status') 
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
          <br/>
          <div class="clearfix"></div>
          <div class="table-responsive" style="border:0">

            <input type="hidden" name="multi_action" value="" />

            <table class="table table-advance"  id="table4" >
              <thead>
                <tr>
                  <th>{{translation('sr_no')}}.</th>
                  <th>{{ translation('level')}}</th> 
                  <th> {{translation('action')}}</th>
                </tr>
              </thead>
              <tbody>
                @if(sizeof($arr_data)>0)
                <?php $count=1; ?>
                  @foreach($arr_data as $data)
                      <tr>
                        <td>{{$count}}</td>
                        <td>{{  isset($data['get_level']['level_name']) ? $data['get_level']['level_name'] : '' }}</td>
                        <td>
                          <a class="green-color" href="{{$module_url_path.'/view/'.base64_encode($data['level_id'])}}" title="{{translation('view')}}">
                            <i class="fa fa-eye" ></i>
                          </a>
                          @if(array_key_exists('fees_structure.update', $arr_current_user_access))  
                          <a class="orange-color" href="{{$module_url_path.'/edit/'.base64_encode($data['level_id'])}}" title="{{translation('edit')}}">
                            <i class="fa fa-edit" ></i>
                          </a>
                          @endif

                        </td>
                      </tr>
                    <?php $count++; ?>
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
</div>
@stop                    


