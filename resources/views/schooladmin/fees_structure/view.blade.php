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
          <a href="{{$module_url_path}}">{{$page_title}}</a>
        </li>

        <span class="divider">
          <i class="fa fa-angle-right"></i>
        </span>
            <i class="fa fa-eye"></i>
            <li class="active">{{translation('fees_structure')}}</li>
    </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1>{{$page_title}}</h1>

    </div>
</div>
<!-- END Page Title -->



<!-- BEGIN Main Content -->
{{-- <div class="row"> --}}
<div class="row">
   <div class="box {{ $theme_color }}">
                    <div class="box-title pr0">
                        <h3><i class="fa fa-user"></i>Fee Structure</h3>
                        <div class="box-tool">
                            
                        </div>
                    </div>
                    <div class="box-content studt-padding">
                        <form class="form-horizontal" id="frm_manage" method="POST" action="admin/activity_log/multi_action">
                                       
                            
                            <div class="border-box">
                               
                               <div class="row">    
                               <div class="col-md-12">
                                  <div class="school-img-paynt">
                                       <div class="table-image-school-grad">
                                          @if($school_logo!='')
                                             <img src="{{$school_logo}}" alt="" />
                                          @else
                                              <img src="{{url('/').'/images/default-old.png'}}" alt="" />
                                          @endif

                                       </div>
                                       <div class="tbl-txt-school">{{$school_name}}</div>
                                       <div class="addres-pymts">{{$school_address}}</div>
                                       <div class="addres-pymts">{{$school_email}}</div>
                                        @if(isset($arr_data[0]['get_level']) && !empty($arr_data[0]['get_level']))
                                        <div class="addres-pymts">{{translation('level')}} :

                                                    {{$arr_data[0]['get_level']['level_name'] or ''}}
                                        </div>
                                        @endif
                                         
                                  </div>
                               </div>
                              
                               </div>
                               
                               
                                <div class="table-responsive" style="border:0">
                                    <input type="hidden" name="multi_action" value="" />
                                    <table class="table table-advance table-bordered" id="table_module">
                                        <thead>
                                            <tr>
                                                <th>
                                                    {{translation('sr_no')}}                      
                                                </th>
                                                <th colspan="2">
                                                    {{translation('title')}}
                                                </th>
                                                <th>
                                                    {{translation('frequency')}}
                                                </th>
                                                <th>
                                                    {{translation('optional')}}
                                                </th>
                                                <th>
                                                    {{translation('amount')}} (MAD)
                                                </th>
                                                <th>
                                                    {{translation('total_amount')}} (MAD)
                                                </th>

                                               
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php $count=1; ?>
                                            @if(isset($arr_data) && count($arr_data)>0)
                                            @foreach($arr_data as $data)
                                              <tr role="row">
                                                <td width="10%">{{$count}}</td>
                                                <td colspan="2" width="10%">{{$data['get_fees']['title']}}</td>
                                                <td width="10%">{{$data['frequency']}}</td>
                                                <td width="10%">
                                                  @if($data['is_optional']==1)
                                                    Yes
                                                  @else
                                                    No
                                                  @endif

                                                </td>
                                                <td width="10%">{{$data['amount']}}</td>
                                                <td width="10%">
                                                  @if($data['frequency']=='MONTHLY')
                                                    {{number_format($data['amount']*12,2)}}
                                                  @elseif($data['frequency']=='BIMONTHLY')
                                                    {{number_format($data['amount']*6,2)}}  
                                                  @elseif($data['frequency']=='QUARTERLY')
                                                    {{number_format($data['amount']*4,2)}}  
                                                  @else
                                                    {{number_format($data['amount']*1,2)}}  
                                                  @endif

                                                </td>
                                              </tr>
                                              <?php $count++; ?>
                                            @endforeach
                                            @endif
                                            @if(isset($arr_brother) && count($arr_brother)>0)
                                            @foreach($arr_brother as $key=> $brother)
                                             <tr role="row">
                                                <td width="10%">{{$count}}</td>
                                                @if($key==0)
                                                <td  rowspan="{{count($arr_brother)}}" width="10%">{{translation('brotherhood')}}</td>
                                                @endif
                                                <td width="10%">{{$brother['kid_no']}} Kids </td>
                                                <td width="10%">
                                                -
                                                </td>
                                                <td width="10%">{{$brother['discount']}}%</td>
                                                <td width="10%">-</td>
                                                <td width="10%">-</td>
                                            </tr>
                                            <?php $count++; ?>
                                            @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </form>
                    </div>
    
{{-- </div> --}}
<!-- END Main Content -->
@stop

