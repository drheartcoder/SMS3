@extends('student.layout.master')                
@section('main_content')
     
<style>
    .content-txt1{height: 331px;}
    .notification-content.content-txt1 { height: 400px;}
</style> 
     <!-- BEGIN Breadcrumb -->
        <div id="breadcrumbs">
            <ul class="breadcrumb">
                <li class="active"><i class="fa fa-home"></i> {{translation('home')}}</li>
            </ul>
        </div>
        <!-- END Breadcrumb -->

     <!-- BEGIN Page Title -->
        <div class="page-title new-agetitle student-dashtitl-pg">
            <div>
                <h1><i class="fa fa-dashboard"></i> {{translation('dashboard')}} </h1>
            </div>
        </div>
        <!-- END Page Title -->
        
        

        <!-- BEGIN Tiles -->
        <div class="row">
            <div class="col-md-12">
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="dashboard-statistics dash-bg">
                            <div class="panel-heading-dash"> {{ translation('site_statistics') }}</div>
                            <div class="panel-bodys dashbords">
                                @if(isset($arr_final_tile) && !empty($arr_final_tile))
                                <div class="row">
                                    @foreach($arr_final_tile as  $key => $value)
                                    <div class="col-sm-12 col-md-6 col-lg-3 manual-width-section">
                                        <a class="ttl-express {{$value['tile_color'] or ''}}" href="{{$value['module_url']}}">
                                            <span class="left-icns-tp dash-circle1">
                                                <img src="{{ $value['images'] }}" alt="{{$value['module_title'] or ''}}" />
                                            </span>
                                            <span id="normal_rider_count" class="info-box-contentts">                                            
                                                <span class="name-tilt">{{$value['module_title'] or ''}}</span>
                                                <span class="site-statistics-semi-content">
                                                    {{$value['module_sub_title'] or ''}}
                                                </span>
                                            </span>
                                            <span class="left-icns-tp site-statistic-count">
                                                <span class="">{{$value['total_count'] or ''}}</span>
                                            </span>
                                        </a>
                                    </div>  
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="clearfix"></div>
            <!--Dash End -->

            <div class="col-sm-12 col-md-4 col-lg-4">
            
                <div class="dashboard-statistics">
                    <div class="panel-heading-dash line-lft">Student average marks subjectwise column Chart</div>
                    <div class="panel-bodys">
                        <div id="chartContainer" style="height: 370px; width: 100%;"></div>
                    </div>
                </div>
            </div>
           
            <div class="col-sm-12 col-md-4 col-lg-4">
                <div class="chat-panel panel panel-danger panel-mst-rd">
                    
                    <div class="panel-heading">
                        <div class="panel-title-box">
                            {{translation('todo_list')}}
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    
                    <div class="notification-sectn todo-list-sction">
                        <ul class="todolistul content-txt1 content-d todolistul">
                            @if(!empty($arrToDo))
                            @foreach($arrToDo as $key => $dataRs)
                            <li class="@if($dataRs['status']==1) dash-line-block @endif removeToDo{{$dataRs['id']}}">
                                <div class="checkbox-doto">
                                    <div class="check-box">
                                        <input class="markAsDone filled-in" value="{{$dataRs['id']}}" name="checked_record[]" id="mult_changecheck{{$dataRs['id']}}" type="checkbox" @if($dataRs['status']==1) checked="checked" @endif>
                                        <label for="mult_changecheck{{$dataRs['id']}}"></label>
                                    </div>
                                </div>
                                <div class="title-to-do-li" >
                                    {{ $dataRs['todo_description'] or '' }}
                                </div>
                            </li>
                            @endforeach
                            @endif
                        </ul>
                        <div class="footer-to-do-list">
                        <div class="row">
                             <form class="form-horizontal"  name="addTodo" id="addTodo" method="POST"  action="{{url('/')}}/{{ $student_panel_slug }}/todo/store" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="col-md-10">
                                <input type="text"  name="todo_description" id="todo_description"  class="form-control-todo" placeholder="{{translation('enter')}} {{translation('todo')}}"   maxlength="1000">
                                <span class="help-block" id="error-todo_description"></span>
                            </div>
                             <div class="col-md-2">
                                <input type="hidden" name="isSubmit" id="isSubmit" value="1">
                                 <a href="javascript:void(0);" class="btn-todo-list float-right mt-1"
                                  id="submit_button">
                                    <i class="fa fa-paper-plane"></i>
                                </a>
                             </div>
                         </form>
                        </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    
                </div>
            </div>
            
            
            <div class="col-sm-12 col-md-4 col-lg-4">
                <div class="chat-panel panel panel-danger panel-mst-rd">
                    <div class="panel-heading">
                        <div class="panel-title-box">
                            Notifications Alerts Panel
                            <div class="clearfix"></div>
                        </div>
                    </div>

                    <div class="notification-sectn">
                        <ul class="notification-content content-txt1 content-d">
                            <li>
                                <div class="align-itma">
                                    <div class="st-alphabet clr-lamtr"><i class="fa fa-envelope-o"></i></div>
                                    <div class="media-body-mstr">
                                        <p class="mb-0">
                                            <a href="#" class="text-purple-light">John Doe</a>
                                        </p>
                                        <span class="text-muted-mst">4 min ago</span>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="align-itma">
                                    <div class="st-alphabet clr-lamtr2"><i class="fa fa-bell"></i></div>
                                    <div class="media-body-mstr">
                                        <p class="mb-0">
                                            <a href="#" class="text-purple-light2">John Doe</a>
                                        </p>
                                        <span class="text-muted-mst">2 min ago</span>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="align-itma">
                                    <div class="st-alphabet clr-lamtr3"><i class="fa fa-comments-o"></i></div>
                                    <div class="media-body-mstr">
                                        <p class="mb-0">
                                            <a href="#" class="text-purple-light3">John Doe</a>
                                        </p>
                                        <span class="text-muted-mst">1 min ago</span>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="align-itma">
                                    <div class="st-alphabet clr-lamtr4"><i class="fa fa-envelope-o"></i></div>
                                    <div class="media-body-mstr">
                                        <p class="mb-0">
                                            <a href="#" class="text-purple-light4">John Doe</a>
                                        </p>
                                        <span class="text-muted-mst">12 min ago</span>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="align-itma">
                                    <div class="st-alphabet clr-lamtr5"><i class="fa fa-warning"></i></div>
                                    <div class="media-body-mstr">
                                        <p class="mb-0">
                                            <a href="#" class="text-purple-light5">John Doe</a>
                                        </p>
                                        <span class="text-muted-mst">12 min ago</span>
                                    </div>
                                </div>
                            </li>

                            <li>
                                <div class="align-itma">
                                    <div class="st-alphabet clr-lamtr6"><i class="fa fa-envelope-o"></i></div>
                                    <div class="media-body-mstr">
                                        <p class="mb-0">
                                            <a href="#" class="text-purple-light6">John Doe</a>
                                        </p>
                                        <span class="text-muted-mst">4 min ago</span>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="align-itma">
                                    <div class="st-alphabet clr-lamtr7"><i class="fa fa-bell"></i></div>
                                    <div class="media-body-mstr">
                                        <p class="mb-0">
                                            <a href="#" class="text-purple-light7">John Doe</a>
                                        </p>
                                        <span class="text-muted-mst">2 min ago</span>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade view-modals" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Transaction Preview</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                  </button>

                        <div class="modal-body">
                            <div class="review-detais">
                                <div class="boldtxts">ID</div>
                                <div class="rightview-txt">7</div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="review-detais">
                                <div class="boldtxts">Order Id</div>
                                <div class="rightview-txt">Event- ABCXDF596DJF3</div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="review-detais">
                                <div class="boldtxts">Date</div>
                                <div class="rightview-txt">11 Dec 2018</div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="review-detais">
                                <div class="boldtxts">Time</div>
                                <div class="rightview-txt">11:56 AM</div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="review-detais">
                                <div class="boldtxts">Event Type</div>
                                <div class="rightview-txt">Paid</div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Print</button>
                        </div>
                    </div>
                </div>
            </div>

            <script type="text/javascript">
                var _gaq = _gaq || [];
                _gaq.push(['_setAccount', 'UA-36251023-1']);
                _gaq.push(['_setDomainName', 'jqueryscript.net']);
                _gaq.push(['_trackPageview']);

                (function() {
                    var ga = document.createElement('script');
                    ga.type = 'text/javascript';
                    ga.async = true;
                    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                    var s = document.getElementsByTagName('script')[0];
                    s.parentNode.insertBefore(ga, s);
                })();
            </script>

            <!-- canvas Chart Start -->
            <script src="https://canvasjs.com/assets/script/canvasjs.min.js">
            </script>
            <script type="text/javascript">
                window.onload = function() {
                    var chart = new CanvasJS.Chart("chartContainer", {
                        theme: "light1", // "light2", "dark1", "dark2"
                        animationEnabled: false, // change to true      
                        title: {
                            text: "Basic Column Chart"
                        },
                        data: [{
                            // Change type to "bar", "area", "spline", "pie",etc.
                            type: "column",
                            dataPoints: [{
                                    label: "apple",
                                    y: 10
                                },
                                {
                                    label: "orange",
                                    y: 15
                                },
                                {
                                    label: "banana",
                                    y: 25
                                },
                                {
                                    label: "mango",
                                    y: 30
                                },
                                {
                                    label: "grape",
                                    y: 28
                                }
                            ]
                        }]
                    });
                    chart.render();
                }
            </script>

            <script>
                $(function() {
                    var $ppc = $('.progress-pie-chart'),
                        percent = parseInt($ppc.data('percent')),
                        deg = 360 * percent / 100;
                    if (percent > 50) {
                        $ppc.addClass('gt-50');
                    }
                    $('.ppc-progress-fill').css('transform', 'rotate(' + deg + 'deg)');
                    $('.ppc-percents span').html(percent + '%');
                });
            </script>
            <!-- canvas Chart End -->
            <script src="{{url('/')}}/js/admin/ajax_loader.js"></script>
            <!--  used for todo-->
            <script src="{{url('/')}}/js/jquery.form.min.js" type="text/javascript"></script>
            <script type="text/javascript">
                var SITE_URL  = "{{ url('/') }}/{{$student_panel_slug}}";
                 var csrf_token = "{{ csrf_token() }}";
                $('body').on('click','.markAsDone',function(event){
                  var todoId = $(this).val();
                  if(todoId>0){

                       $.ajax({
                            url: SITE_URL+'/todo/mark_as_read_todo',
                            type:'POST',
                            data:{
                              '_token' : csrf_token,
                              'todoId' : todoId
                            },
                            success: function( resData ) {
                              var result = resData.split('####');
                              var res = result[0];
                              var status = result[1];
                              console.log(result);
                              if(res=='done'){
                                
                                if(status==1){
                                  $('.removeToDo'+todoId).addClass('dash-line-block')
                                }else if(status==0){
                                  $('.removeToDo'+todoId).removeClass('dash-line-block')
                                }



                              }else if(res =='InvalidTodo'){  
                                swal("{{translation('invalid_todo')}}");  
                              }else if(res =='oopsSomething'){ 
                                swal("{{translation('oopssomething_went_wrong_while_updating_the_todo')}}");  
                              }else{
                                swal("{{translation('something_went_wrong_please_try_again_later')}}");  
                              }
                                

                            },
                            error: function( res ){
                              swal("{{translation('something_went_wrong_please_try_again_later')}}");
                            }
                          });
                  }

            }); 

            $(document).ready(function ()
            { 
              var runToDoAdd = null;
              $(document).on('click', "#submit_button", function(){
                  var todo_description      = $("#todo_description").val();
                  var flag = 0;
                 

                  if($.trim(todo_description) == '')
                  {
                  
                    $("#error-todo_description").html("{{translation('please_enter_todo')}}");
                    flag = 1;
                  }else if(todo_description.length > 1000){
                    $("#error-todo_description").html("{{translation('todo_should_not_be_more_than_1000_characters')}}");
                    flag = 1;
                  }else{
                    $("#error-todo_description").html('');
                  }

                  if(flag == 1)
                  {  
                    return false;
                  }
                  else
                  {
                    $('.error-red').html('');
                    runToDoAdd = $("#addTodo").ajaxSubmit({
                    headers   :{'X-CSRF-Token': $('input[name="_token"]').val()},
                    data  : 'json',
                    beforeSend:function(data, statusText, xhr, wrapper) 
                    {

                       if(runToDoAdd != null){
                         return false;
                       }
                    
                      $("#submit_button").attr('disabled', true);
                      $("#submit_button").html("<i class='fa fa-spinner fa-spin'></i>");
                   },
                        success :function(data){ 
                          runToDoAdd = null;
                          
                          $("#submit_button").attr('disabled', false);
                          if(data.status == 'success'){
                            $("#submit_button").html("<i class='fa fa-paper-plane'></i>");
                            $("#todo_description").val(''); 
                            swal(data.customError);
                            setTimeout(function(){
                              window.location.reload();
                            }, 2000);
                        }else if(data.customError!='' || data.errors != ''){
                                swal(data.customError+' '+data.errors);
                          }
                          
                        },
                        error  :function(data, statusText, xhr, wrapper)
                        { 
                          runToDoAdd = null;
                          console.log(data);
                          swal('Oops,Something went wrong,Please try again later.');
                        }
                      });
                  }

              });
            });
            $(document).ready(function() {
              $(window).keydown(function(event){
                if(event.keyCode == 13) {
                  event.preventDefault();
                  return false;
                }
              });
            });
            </script>
        </div>
        <!-- END Main Content -->
    
@stop