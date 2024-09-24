@extends('schooladmin.layout.master')                
@section('main_content')

<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
      <li>
          <i class="fa fa-home"></i>
          <a href="{{ url($school_admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
      </li>
      <span class="divider">
          <i class="fa fa-angle-right"></i>
          <i class="{{$module_icon}}"></i>                
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

<!-- BEGIN Main Content -->
<div class="row">
<div class="col-md-12">
   <div class="box {{ $theme_color }}">
      <div class="box-title pr0">
         <h3>
            <i class="fa fa-list"></i>
            {{ isset($page_title)?$page_title:"" }}
         </h3>

          
         <div class="box-tool">
            
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
         <div class="col-md-12 ajax_messages">
            <div class="alert alert-success" id="success" style="display:none;">
            </div>
            <div class="alert alert-danger" id="error" style="display:none;">
            </div>
         </div>  
        
         <br/>
         <div class="clearfix"></div>
         <div class="filter-section">
            
          </div>
         <div class="table-responsive attendance-create-table-section" style="border:0">
            <input type="hidden" name="multi_action" value="" />
            <table class="table table-advance"  id="table_module" >
              <thead >
                <tr >
                 <th></th>
                 <th>{{translation('sms_template_name')}}</th>
                 <th>{{translation('sms_template_subject')}}</th>
                 <th></th>
                 @if(array_key_exists('sms_template.update', $arr_current_user_access))  
                 <th>{{translation('action')}}</th> 
                 @endif
                
                </tr>
              </thead>
              <tbody>
                @if(count($arr_slug)>0)
                  @foreach($arr_slug as $key => $slug)
                    <tr>
                      <td colspan="6"><b>{{(++$key)}}) {{translation($slug['template_slug'])}}</b></td>
                    </tr>
                    @if(count($arr_data)>0)
                    <?php $count = 0;?>
                      @foreach($arr_data as  $key => $page)
                        @if($slug['template_slug'] == $page['template_slug'])
                          <tr>
                            <td></td>
                            <td> {{ translation('template')}} {{ (++$count) }}      </td> 
                            <td> {{ $page['template_subject'] or '' }} </td>
                            <td>
                                {{-- <div class="radio-btns">
                                  <div class="radio-btn"> --}}
                                    <input type="radio" name="{{$page['template_slug']}}" id="{{$page['id']}}" 
                                    @if($page['is_enabled']==1) 
                                      checked="checked" 
                                    @endif value="{{$page['id']}}" onChange="enableOption(this);" @if(!array_key_exists('sms_template.update', $arr_current_user_access)) disabled @endif>
                                    {{-- <label for="{{$page['id']}}"></label>
                                    <div class="check"></div>
                                </div>
                                </div> --}}
                                
                            </td>  
                            @if(array_key_exists('sms_template.update', $arr_current_user_access))  
                            <td> 
                              <a class="orange-color" href="{{ $module_url_path.'/edit/'.base64_encode($page['id']) }}"  title="Edit" >
                                <i class="fa fa-edit" ></i>
                              </a>
                            </td>
                            @endif
                          </tr>
                        @endif
                      @endforeach
                    @endif
                  @endforeach
                @endif
                </tbody>
            </table>
          </div>
         {!! Form::close() !!}
      </div>
    </div>
   </div>
</div>

@if(array_key_exists('room_assignment.update', $arr_current_user_access))  
 <script type="text/javascript">

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

 function enableOption(obj)
 {
    var template_id = $(obj).val();
    $.ajax({
                  url:"{{$module_url_path.'/change_enabled'}}",
                  type:'POST',
                  data:{'template_id':template_id,'_token':'<?php echo csrf_token();?>'},           
                    success:function(data)
                    {
                      if(data.status == "success")
                      {
                          $('.ajax_messages').show();
                          $('#success').show();
                          $('#success').text(data.msg);
                          setTimeout(function(){
                              $('.ajax_messages').hide();
                          }, 3000);
                      }
                      if(data.status=='error')
                      {
                        $('.ajax_messages').show();
                        $('#error').css('display','block');
                        $('#error').text(data.msg);
                        setTimeout(function(){
                            $('.ajax_messages').hide();
                        }, 3000);
                      }
                    }

          });

  }
 </script> 
@endif 
@stop