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
      <i class="fa fa-envelope"></i>   
    </span> 
    <li class="active"> {{ $module_title or ''}} </li>
   
  </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
<div class="page-title new-agetitle">
    <div>
        <h1><i class="fa fa-envelope"></i> {{ $module_title or ''}}</h1>
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
          {{ $page_title or ''}}
        </h3>
        <div class="box-tool">
          <a title="{{translation('refresh')}}" 
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
        <br/>
        <div class="clearfix">
        </div>
          
        <div class="table-responsive attendance-create-table-section"  style="border:0">
          <table class="table table-advance"  id="table_module" >
            <thead>
              <tr>
                <th></th>
                <th>   {{translation('name')}}     </th> 
                <th>   {{translation('from')}}     </th> 
                <th> {{translation('from_email')}} </th>
                <th>  {{translation('subject')}}   </th>
                <th>{{translation('is_enabled')}}  </th>
                @if(array_key_exists('email_template.update', $arr_current_user_access))         
                <th>   {{translation('action')}}   </th>
                @endif
              </tr>
            </thead>
            <tbody>
              @if(count($arr_slug)>0)
                @foreach($arr_slug as $key => $slug)
                  <tr>
                    <td colspan="6"><b>{{(++$key)}}) {{translation($slug['slug'])}}</b></td>
                  </tr>
                  @if(sizeof($arr_data)>0)
                  <?php $count = 0;?>
                    @foreach($arr_data as $key1 => $email_template)
                    
                      @if($slug['slug'] == $email_template['slug'])

                        <tr>
                          <td></td>
                          <td> {{ translation('template')}} {{ (++$count) }}      </td> 

                          <td> {{ $email_template['template_from'] or '' }}       </td> 

                          <td> {{ $email_template['template_from_mail']  or '' }} </td>

                          <td> {{ $email_template['template_subject'] or '' }}    </td>

                          <td>
                                <label><input type="radio" name="{{$email_template['slug']}}" id="{{$email_template['id']}}" 
                                  @if($email_template['is_enabled']==1) 
                                    checked="checked" 
                                  @endif value="{{$email_template['id']}}" onChange="enableOption(this);"></label>
                          </td>

                          @if(array_key_exists('email_template.update', $arr_current_user_access))      
                          <td> 
                            <a href="{{ $module_url_path.'/edit/'.base64_encode($email_template['id']) }}"  title="{{translation('edit')}}" class="orange-color">
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
        <div>   
        </div>
        {!! Form::close() !!}
      </div>
    </div>
  </div>

<!-- END Main Content -->

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

@stop