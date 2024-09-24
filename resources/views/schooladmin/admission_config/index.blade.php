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
        </span>
        <li>
          <i class="{{$module_icon}}"></i>
          {{$module_title}}
        </li>
       
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


            <!-- BEGIN Tiles -->
           <div class="row">
                <div class="col-md-12">
                    <div class="box  box-navy_blue">
                        <div class="box-title">
                            <h3><i class="fa fa-list"></i>{{$page_title}}</h3>
                            <div class="box-tool">
                            </div>
                        </div>
                        <div class="box-content">
                        @include('schooladmin.layout._operation_status')  
                         @if(array_key_exists('admission_config.create', $arr_current_user_access))  
                            @if(count($arr_edit_admission_config)>0)
                                <form method="post" onsubmit="return addLoader()" action="{{$module_url_path}}/update/{{$enc_id}}"  class="form-horizontal" id="validation-form1">
                            @else
                                <form method="post" onsubmit="return addLoader()" action="{{$module_url_path}}/store"  class="form-horizontal" id="validation-form1">
                            @endif    
                                {{ csrf_field() }}
                                <div class="row">
                                <div class="form-group-nms">
                                    <div class="col-sm-3 col-lg-2"></div>
                                    <div class="col-sm-12 col-lg-8"></div>
                                    <div class="clearfix"></div>
                                </div>
                                  
                                <div class="col-sm-6 col-md-6 col-lg-6">
                                    <div class="row">                                        
                                       
                                        <div class="form-group">
                                            <label class="col-sm-3 col-lg-4 control-label">{{translation('education_board')}}<i class="red">*</i></label>
                                            <div class="col-sm-9 col-lg-8 controls">
                                                <select name="educational_board" class="form-control">
                                                    @if(isset($arr_boards) && count($arr_boards)>0)
                                                        @foreach($arr_boards as $boards)
                                                            <option value="{{$boards['id']}}" @if(isset($arr_edit_admission_config['educational_board']) && $arr_edit_admission_config['educational_board']== $boards['id']) selected @endif>{{$boards['board']}}</option>
                                                        @endforeach
                                                    @endif    
                                                </select>
                                                <span class='help-block'>{{ $errors->first('educational_board') }}</span>    
                                            </div>
                                        </div>
                                        
                                        
                                        
                                        <div class="form-group">
                                            <label class="col-sm-3 col-lg-4 control-label">{{translation('admission_open')}}<i class="red">*</i></label>
                                            <div class="col-sm-9 col-lg-8 controls">

                                                <?php
                                                    if(isset($arr_edit_admission_config['admission_open']) && $arr_edit_admission_config['admission_open']!='0000-00-00')
                                                     {
                                                        $admission_open = date_create($arr_edit_admission_config['admission_open']);
                                                        $admission_open = date_format($admission_open,'Y-m-d');
                                                     }
                                                     else
                                                     {
                                                        $admission_open =date('Y-m-d');
                                                     }
                                                ?>
                                                <input class="form-control datepikr" name="admission_open" id="datepicker2" placeholder="{{translation('enter')}} {{translation('admission_open')}}" type="text" data-rule-required="true"  value="{{$admission_open}}"  readonly data-rule-date="true" style="cursor: pointer"   />
                                                <span class='help-block'>{{ $errors->first('admission_open') }}</span>    
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 col-lg-4 control-label">{{translation('application_fee')}}<i class="red">*</i></label>
                                            <div class="col-sm-9 col-lg-8 controls">
                                                <input class="form-control" name="application_fee" type="text" data-rule-required="true" data-rule-digits="true"  min="0"  placeholder="Application Fee" @if(isset($arr_edit_admission_config['application_fee'])) value="{{$arr_edit_admission_config['application_fee']}}" @endif   >
                                                <span class='help-block'>{{ $errors->first('application_fee') }}</span>    
                                            </div>
                                        </div>                                       

                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6 col-lg-6">
                                    <div class="row">
                                        <div class="form-group">
                                            <label class="col-sm-3 col-lg-4 control-label">{{translation('level')}}</label>
                                            <div class="col-sm-9 col-lg-8 controls">
                                                <select name="level" id="" class="form-control">
                                                    @if(count($arr_levels)>0)
                                                    @if(count($arr_edit_admission_config)<=0)
                                                        <option value="all">{{translation('all')}}</option>
                                                    @endif    
                                                        @foreach($arr_levels as $level)
                                                            <option value="{{$level['level_id']}}"  @if(isset($arr_edit_admission_config['level_id']) && $arr_edit_admission_config['level_id']== $level['level_id']) selected @endif>{{$level['level_details']['level_name']}}</option>
                                                        @endforeach
                                                    @endif   
                                                </select>
                                                <span class='help-block'>{{ $errors->first('level') }}</span>        
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 col-lg-4 control-label">{{translation('admission_close')}}<i class="red">*</i></label>
                                            <div class="col-sm-9 col-lg-8 controls">
                                            <?php 
                                                if(isset($arr_edit_admission_config['admission_close']) && $arr_edit_admission_config['admission_close']!='0000-00-00')
                                                { 
                                                    $admission_close = date_create($arr_edit_admission_config['admission_close']);
                                                    $admission_close = date_format($admission_close,'Y-m-d');

                                                }
                                                else
                                                {
                                                    $admission_close = date('Y-m-d');
                                                }
                                            ?>
                                                <input class="form-control datepikr" name="admission_close" id="datepicker" data-rule-required="true"  placeholder="Enter Admission Close Date" type="text" value="{{$admission_close}}" readonly data-rule-date="true"  style="cursor: pointer" />
                                                <span class='help-block'>{{ $errors->first('admission_close') }}</span>    

                                            </div>
                                        </div>
                                    </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 col-lg-4 control-label">{{translation('number_of_seats')}}<i class="red">*</i></label>
                                            <div class="col-sm-9 col-lg-8 controls">
                                                <input class="form-control" data-rule-digits="true" min="1"  name="no_of_seats" type="text" data-rule-required="true"    placeholder="Seats Available" @if(isset($arr_edit_admission_config['no_of_seats'])) value="{{$arr_edit_admission_config['no_of_seats']}}" @endif  >
                                                <span class='help-block'>{{ $errors->first('no_of_seats') }}</span>    
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="row">
                                <div class="form-group">
                                    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                                        @if(count($arr_edit_admission_config)>0)
                                            <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('update')}}</button>
                                        @else
                                            <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('save')}}</button>
                                        @endif    
                                    </div>
                                </div>
                                    </div>
                            </form>
                            @endif
                            <div class="table-responsive" style="border:0">
                                <input type="hidden" name="multi_action" value="" />
                                <table class="table table-advance" id="table_module">
                                    <thead>
                                        <tr>
                                            <th>
                                                {{translation('sr_no')}}.
                                            </th>
                                            <th>
                                                  {{translation('academic_year')}}
                                            </th>
                                            <th>
                                                  {{translation('level')}}
                                            </th>
                                            <th>
                                                  {{translation('educational_board')}}
                                            </th>
                                            <th>
                                                   {{translation('number_of_seats')}}
                                            </th>
                                            <th>
                                                   {{translation('admission_open')}}
                                            </th>
                                            <th>
                                                   {{translation('admission_close')}}
                                            </th>
                                            <th>
                                                 {{translation('application_fee')}}                              
                                            </th> 
                                            @if(array_key_exists('admission_config.update', $arr_current_user_access) || array_key_exists('admission_config.delete', $arr_current_user_access))  
                                            <th>
                                                {{translation('action')}}
                                            </th>                         
                                            @endif                   
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no =0;?>
                                    @foreach($arr_admission_configs as $value)
                                    
                                        <tr role="row">
                                            <td> {{++$no}}</td>
                                            <td>{{$value['get_academic_year']['academic_year'] or '-'}}</td>
                                            <td>{{$value['get_level']['level_name'] or '-'}}</td>
                                            <td>{{$value['get_education_board']['board'] or '-'}}</td>
                                            <td>{{$value['no_of_seats'] or '-'}}</td>
                                            <td>
                                            <?php
                                                if(isset($value['admission_open']) && $value['admission_open'] !="0000-00-00") 
                                                {
                                                    $admission_open  =$value['admission_open'];
                                                    $admission_open = date_create($admission_open);
                                                    $admission_open = date_format($admission_open,'d M Y');
                                                } 
                                                else
                                                {
                                                    $admission_open = '-'; 
                                                }
                                                    
                                            ?>
                                                {{$admission_open}}
                                            </td>
                                            <td>
                                            <?php
                                                if(isset($value['admission_close']) && $value['admission_close'] !="0000-00-00") 
                                                {
                                                    $admission_close  =$value['admission_close'];
                                                    $admission_close = date_create($admission_close);
                                                    $admission_close = date_format($admission_close,'d M Y');
                                                } 
                                                else
                                                {
                                                    $admission_close = '-'; 
                                                }
                                                    
                                            ?>
                                                {{$admission_close}}
                                            </td>
                                            <td>{{$value['application_fee'] or '-'}}</td>
                                            @if(array_key_exists('admission_config.update', $arr_current_user_access) || array_key_exists('admission_config.delete', $arr_current_user_access))
                                            <td width="100px">
                                                @if(array_key_exists('admission_config.update', $arr_current_user_access))
                                                <a  href="{{ $module_url_path.'/'.base64_encode($value['id']) }}" title="{{translation('edit')}}" class="orange-color">
                                                    <i class="fa fa-edit" ></i>
                                                </a>
                                                @endif
                                                @if(array_key_exists('admission_config.delete', $arr_current_user_access))
                                                <a href="{{$module_url_path.'/delete/'.base64_encode($value['id'])}}" title="{{translation('delete')}}" onclick="return confirm_action(this,event,'{{translation('do_you_really_want_to_delete_this_record')}}','{{translation("are_you_sure")}}','{{translation('yes')}}','{{translation("no")}}')" class="red-color"><i class="fa fa-trash" ></i></a>
                                                @endif
                                            </td>
                                            @endif
                                        </tr>                                       
                                    @endforeach    
                                    </tbody>
                                </table>
                            </div>                            
                        </div>
                    </div>
                </div>
               </div> 
            <script>
            $(function () {
                $("#datepicker").datepicker({
                    todayHighlight: true,
                    autoclose: true,
                    format:'yyyy-mm-dd',
                    startDate: "{{\Session::get('start_date')}}",
                    endDate: "{{\Session::get('end_date')}}"
                });
                $("#datepicker2").datepicker({
                    todayHighlight: true,
                    autoclose: true,
                    format:'yyyy-mm-dd',
                    startDate: "{{\Session::get('start_date')}}",
                    endDate: "{{\Session::get('end_date')}}",
                    onChangeDate:function(e){
                        console.log(e);
                    }
                });
            });
            
        </script>
        <!-- END Main Content -->
        <script type="text/javascript">
        $(document).ready(function() {
         var oTable = $('#table_module').dataTable({
          "pageLength": 10,
          "searching":false,      
          "aoColumnDefs": [
                          { 
                            "bSortable": false, 
                            "aTargets": [0,1,3,4,5,6] // <-- gets last column and turns off sorting
                           } 
                        ]
            });  
            $.fn.dataTable.ext.errMode = 'none';      
         
        });

        $("#datepicker2").change(function(){
            date = $("#datepicker2").val();
            $("#datepicker").datepicker('setStartDate',date);
        });

        </script>
 
@endsection