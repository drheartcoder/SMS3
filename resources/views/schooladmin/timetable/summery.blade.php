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
                    <i class="{{$module_icon}}"></i>
                </span>
                <li><a href="{{$module_url_path}}">{{$page_title}}</a></li>
                <span class="divider">
                    <i class="fa fa-angle-right"></i>
                    <i class="{{$module_icon}}"></i>
                </span>
                <li class="active">{{$module_title}}</li>                
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
                    <div class="box-title pr0">
                        <h3><i class="fa fa-list"></i>{{translation('summary')}}</h3>
                    </div>
                    <div class="box-content studt-padding">                            
                        <div class="border-box">
                                <div class="row">
                                    @if(!empty($arr_level))
                                    <div class="col-lg-7">
                                        <div class="teacher-list-section">
                                          {{$page_title}}
                                        </div>
                                        <div class="table-responsive" style="border:0">
                                    <input type="hidden" name="multi_action" value="" />
                                    <table class="table table-advance summery-table-section" id="table_module">
                                        <thead>
                                            <tr>
                                                <th>&nbsp;</th>
                                                @foreach($arr_class as $arr_class123)
                                                    @if(isset($arr_class123['get_class']['class_name']))
                                                    <th style="width: 150px;">
                                                        <a class="sort-descs" href="#">{{$arr_class123['get_class']['class_name'] or ''}}</a>
                                                    </th>
                                                    @endif
                                                @endforeach
                                                
                                            </tr>
                                        </thead>
                                      
                                        <tbody>
                                             @foreach($arr_level as $arr_levels)
                                            <tr role="row">
                                                <td>{{ $arr_levels['level_details']['level_name'] or ''}}</td>
                                                 @foreach($arr_class as $key1 => $arr_classes)
                                                      <?php $is_section_timetable = 0; ?>
                                                    <td>
                                                    @foreach($summery_data as $data)
                                                     
                                                     @if(($data['level_id'] == $arr_levels['level_id']) && ($data['class_id'] == $arr_classes['class_id']) && ($data['is_created'] ==1))
                                                         <?php $is_section_timetable = 1; ?>
                                                        <i class="fa fa-check"></i>
                                                         
                                                    @endif
                                                    @endforeach
                                                    @if($is_section_timetable==0)
                                                    <i class="fa fa-close"></i>
                                                    @endif
                                                    </td>
                                                 @endforeach
                                            </tr>
                                            @endforeach
                                          </tbody>
                                    </table>
                                </div>
                              </div>
                              @endif
                              @if(!empty($arr_teachers))
                                    <div class="col-lg-5">
                                        <div class="teacher-list-section">
                                           {{translation('professor')}}
                                        </div>
                                        <div class="table-responsive" style="border:0">
                                            <input type="hidden" name="multi_action" value="" />
                                            <table class="table table-advance" id="table_module">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            <a class="sort-descs" href="#">{{translation('professor')}} {{translation('name')}}</a>                                                    
                                                        </th>
                                                        <th>
                                                            <a class="sort-descs" href="#">{{translation('total')}} {{translation('periods')}}</a>
                                                        </th>
                                                        <th>
                                                            <a class="sort-descs" href="#">{{translation('assigned_periods')}}</a>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($arr_teachers as $data)
                                                    <tr role="row">
                                                        <td>{{$data['user_details']['first_name'] or ''}} {{$data['user_details']['last_name'] or ''}}</td>
                                                        <td>{{$data['total_periods'] or ''}}</td>
                                                        <td>{{$data['assigned_periods'] or ''}}</td>
                                                    </tr>
                                                @endforeach
                                                                                               
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>                        
                    </div>
                </div>
            </div>
        </div>    
            <!-- END Main Content -->
@endsection