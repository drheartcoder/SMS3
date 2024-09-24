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
        <h1><i class="{{$module_icon}}"></i> {{$module_title}}</h1>
    </div>
</div>

<!-- BEGIN Main Content -->
{{-- <div class="row"> --}}

   <div class="box {{ $theme_color }}">
      <div class="box-title">
         <h3>
            <i class="fa fa-eye"></i>
           {{$page_title}}  
         </h3>
         <div class="box-tool">
         </div>
      </div>

        <?php
        //dd($arr_details);
          $arr_notation = $arr_comment = [];
          $id                         = $arr_details['id'];
          $first_name                 = isset($arr_details['get_user_details']['first_name']) ?$arr_details['get_user_details']['first_name']:"-";
          $last_name                  = isset($arr_details['get_user_details']['last_name']) ?$arr_details['get_user_details']['last_name']:"-";
          $name                       = ucfirst($first_name).' '.ucfirst($last_name);
          $level                      = isset($arr_details['get_level_class']['get_level']['level_name']) ?$arr_details['get_level_class']['get_level']['level_name']:"-";
          $class                      = isset($arr_details['get_level_class']['get_class']['class_name']) ?$arr_details['get_level_class']['get_class']['class_name']:"-";
          $no                         = isset($arr_details['student_no']) ?$arr_details['student_no']:"-";
        ?>
          <div class="clearfix"></div>
          <div class="box-content view-details-seciton-main details-section-main-block" style="min-height: 200px">
            <div class="row">
                <div class="col-md-12">
                 <div class="details-infor-section-block">
                      {{translation('student_details')}}
                 </div>  

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('student_name')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$name}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('student_number')}}</b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$no}}
                     </div>
                     <div class="clearfix"></div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('level')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$level}}
                     </div>
                     <div class="clearfix"></div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('class')}} </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$class}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  </div>
                </div>
            </div>


            <div class="clearfix"></div>
             @if(isset($arr_details['get_behaviour']) && count($arr_details['get_behaviour'])>0)
              <div class="box-content view-details-seciton-main details-section-main-block">
                <div class="row">
                  <div class="col-md-12">
                   <div class="details-infor-section-block">
                        {{translation('behaviour_details')}} {{translation('for')}} {{isset($arr_details['get_behaviour'][0]['get_course']['course_name'])?ucwords($arr_details['get_behaviour'][0]['get_course']['course_name']):''}}
                   </div>  
                    <div class="clearfix"></div>
                    <br/>
                    <div class="table-responsive attendance-create-table-section" id="table_div" >
                        <table class="table table-advance" id="table_module" cellpadding="35px">
                            <thead>
                                <tr>
                                    <th>
                                      {{translation('sr_no')}}
                                    </th>
                                    <th>

                                        @if($arr_details['get_behaviour'][0]['get_behaviour_period']['period'] == 'WEEKLY')
                                          {{translation('behaviour_date')}} 
                                        @elseif($arr_details['get_behaviour'][0]['get_behaviour_period']['period'] == 'MONTHLY')
                                          {{translation('month')}} 
                                        @elseif($arr_details['get_behaviour'][0]['get_behaviour_period']['period'] == 'ANNUALLY')
                                          {{translation('behaviour_date')}} 
                                        @endif
                                    </th>
                                    <th>
                                        {{translation('notation')}}
                                    </th>
                                    <th>
                                      {{translation('comment')}}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php $no=0;?>
                              @foreach($arr_details['get_behaviour'] as $behaviour)
                        
                                <?php 
                                  $arr_notation = json_decode($behaviour['behaviour_notation'],true);
                                  $arr_comment  = json_decode($behaviour['behaviour_comments'],true);
                                ?> 
                                <tr>
                                  <td>{{(++$no)}}</td>
                                  <td>
                                      @if($arr_details['get_behaviour'][0]['get_behaviour_period']['period'] == 'WEEKLY')
                                        {{isset($behaviour['created_at'])? getDateFormat($behaviour['created_at']):''}}
                                      @elseif($arr_details['get_behaviour'][0]['get_behaviour_period']['period'] == 'MONTHLY')
                                        {{isset($behaviour['week_month'])? ucwords($behaviour['week_month']):''}}

                                      @elseif($arr_details['get_behaviour'][0]['get_behaviour_period']['period'] == 'ANNUALLY')
                                        {{isset($behaviour['created_at'])? getDateFormat($behaviour['created_at']):''}}
                                      @endif
                                  </td>
                                  <td>
                                      @if(array_key_exists($id,$arr_notation))
                                        {{$arr_notation[$id] or '-'}}
                                      @endif
                                  </td>
                                  <td>
                                      @if(array_key_exists($id,$arr_comment))

                                        <?php
                                          $notation = $arr_notation[$id];
                                          if($notation<=10 && $notation>=0 && $notation!='')
                                          {
                                        ?>
                                            @if($notation>=8 && $notation<=10)
                                               <label style="color:#0fa12f"> {{$arr_comment[$id] or '-'}}</label>
                                            @elseif($notation>=6 && $notation<8)
                                              <label style="color:#007ef9"> {{$arr_comment[$id] or '-'}}</label>
                                            @elseif($notation>=4 && $notation<6)
                                                <label style="color: #ff840d"> {{$arr_comment[$id] or '-'}}</label>
                                            @elseif($notation>=0 && $notation<4)
                                              <label style="color: #f2dede"> {{$arr_comment[$id] or '-'}}</label>
                                            @endif
                                        <?php
                                          }
                                        ?>
                                        
                                      @endif
                                    
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                        </table>
                        <div class="form-group back-btn-form-block">
                           <div class="controls">
                              <a href="{{ $module_url_path}}" class="btn btn-primary" style="float: right;margin-top: 20px;" > <i class="fa fa-arrow-left"></i> {{translation('back')}} </a>
                           </div>
                        </div>
                    </div>

                  </div>

              </div>
            @endif

          </div>
    
    
{{-- </div> --}}
<!-- END Main Content -->
@stop

