@extends('schooladmin.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{ url($school_admin_panel_slug) }}/dashboard">{{translation('dashboard')}}</a>
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
            <i class="fa fa-eye"></i>
            <li class="active">{{$page_title}}</li>
    </ul>
</div>
<!-- END Breadcrumb -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="{{$module_icon}}"></i> {{$page_title}}</h1>
    </div>
</div>

<!-- BEGIN Main Content -->
{{-- <div class="row"> --}}

   <div class="box {{ $theme_color }}">
      <div class="box-title">
         <h3>
            <i class="fa fa-eye"></i>
           {{$module_title}}  
         </h3>
         <div class="box-tool">
         </div>
      </div>

        <?php
        
          $board               = isset($arr_data['board']) ?$arr_data['board']:"-";
         
        ?>
          <div class="clearfix"></div>
          <div class="box-content view-details-seciton-main details-section-main-block">
            <div class="row">
                <div class="col-md-12">
                      <div class="details-infor-section-block">
                          {{translation('education_board')}}
                      </div>  
                 
                      <div class="form-group">
                         <label class="col-sm-4 col-lg-3 control-label"><b> {{translation('name')}}  </b> : </label>
                         <div class="col-sm-9 col-lg-4 controls" style="text-align:left;">
                            {{$board}}
                         </div>
                         <div class="clearfix"></div>
                      </div>
                      <br/>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="user-details-section-main ">
                            <div class="details-infor-section-block">
                                {{translation('professor_details')}}
                            </div>
                            <div class="table-responsive attendance-create-table-section">
                              @if(isset($arr_professors) && count($arr_professors)>0)
                                <table class="table table-advance" id="table_module">
                                  <thead>
                                    <tr>
                                      <th width="15%">{{translation('sr_no')}}</th>
                                      <th>{{translation('name')}}</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                  @foreach($arr_professors as $key => $prof)
                                    <tr>
                                      <td>{{($key+1)}}</td>
                                      <td>{{$prof}}</td>
                                    </tr>
                                  @endforeach
                                  </tbody>
                                </table>
                              @endif
                            </div>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="user-details-section-main">
                            <div class="details-infor-section-block">
                                {{translation('employee_details')}}
                            </div>
                              <div class="table-responsive attendance-create-table-section">
                              @if(isset($arr_employees) && count($arr_employees)>0)
                                <table class="table table-advance" id="table_module">
                                  <thead>
                                    <tr>
                                      <th width="15%">{{translation('sr_no')}}</th>
                                      <th>{{translation('name')}}</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                  @foreach($arr_employees as $key => $emp)
                                    <tr>
                                      <td>{{($key+1)}}</td>
                                      <td>{{$emp}}</td>
                                    </tr>
                                  @endforeach
                                  </tbody>
                                </table>
                              @endif
                            </div>
                          </div>
                        </div>
                      </div>

                  
                      <div class="form-group back-btn-form-block">
                         <div class="controls">
                            <a href="{{ $module_url_path}}" class="btn btn-primary" style="float: right;margin-top: 20px;" > <i class="fa fa-arrow-left"></i>{{translation('back')}} </a>
                         </div>
                      </div>
                  </div>
                  
                </div>

            </div>

          </div>
    
    
{{-- </div> --}}
<!-- END Main Content -->
<script>
  
</script>
@stop

