@extends('professor.layout.master')                
@section('main_content')
       
       <!-- BEGIN Breadcrumb -->
        <div id="breadcrumbs">
            <ul class="breadcrumb">
                <li class="active"><i class="fa fa-home"></i> {{translation('home')}}</li>

            </ul>
        </div>
        <!-- END Breadcrumb -->

       <!-- BEGIN Page Title -->
        <div class="page-title new-agetitle">
            <div>
                <h1> <i class="fa fa-dashboard"></i> {{translation('dashboard')}}</h1>

            </div>
        </div>
        <!-- END Page Title -->

        

        <!-- BEGIN Tiles -->
        <div class="row">
            <div class="col-md-12">
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="dashboard-statistics dash-color-bg">
                            <div class="panel-heading-dash">{{translation('site_statistics')}}</div>
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
            
            <div class="col-sm-12 col-md-6 col-lg-6">
                <div class="dashboard-statistics">
                    <div class="panel-heading-dash line-lft">Normal Rides</div>
                    <div class="panel-bodys">
                        <div id="chartContainer" style="height: 370px; width: 100%;"></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-6">
                <div class="dashboard-statistics">
                    <div class="panel-heading-dash line-lft">Emergency Rides</div>
                    <div class="panel-bodys">
                        <div class="progressDiv">
                            <div class="statChartHolder">
                                <div class="progress-pie-chart" data-percent="67"><!--Pie Chart -->
                                    <div class="ppc-progress">
                                        <div class="ppc-progress-fill"></div>
                                    </div>
                                    <div class="ppc-percents">
                                    <div class="pcc-percents-wrapper">
                                        <span>%</span>
                                    </div>
                                    </div>
                                </div><!--End Chart -->
                            </div>
                            <div class="statRightHolder">
                                <ul>
                                    <li> <h3 class="blue">39.4k</h3> <span>Interactions</span></li>
                                    <li> <h3 class="purple">1.8k</h3> <span>Posts</span></li>
                                </ul>

                                <ul class="statsLeft">
                                    <li><h3 class="yellow">22%</h3> <span>Comments</span></li>
                                    <li><h3 class="red">37%</h3> <span>Cheers</span></li>
                                </ul>
                                <ul class="statsRight">
                                    <li><h3>18%</h3> <span>Tasks</span></li>
                                    <li><h3>23%</h3> <span>Goals</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

             <div class="col-sm-12 col-md-6 col-lg-6">
                <div class="chat-panel panel panel-success panel-mst-rd">
                    <div class="panel-heading ">
                        <div class="panel-title-box">
                            <i class="icon-comments"></i> Lorem ipsum dolor <a class="btn btn-info btn-sm ride-view-all001 btn-btn pull-right" href="JavaScript:Void(0);">View All</a>
                            <!-- <div class="clearfix"></div>-->
                        </div>
                    </div>
                   <div class="space-inx-bld content-txt1 content-d" style="background:none;">
                          <ul class="chat">
                                  <li class="left clearfix">
                                      <div class="chat-body clearfix">
                                         <div class="left-img-chat"><img src="{{url('/')}}/images/admin/client-staff.jpg" alt="" /></div>
                                          <div class="right-avtr">
                                              <div class="header header-right">
                                                  <strong class="primary-font "> Paul M. Stevens </strong>
                                              </div>
                                              <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias quas repellat labore culpa, excepturi natus quis necessitatibus, voluptates. Debitis magnam veritatis qui, ipsum aperiam saepe consequatur facilis reprehenderit possimus accusamus harum. Numquam, impedit, tempore!</p>
                                                  <small class="text-muted label label-danger"><i class="icon-time"></i>56 Minutes ago  </small>
                                                  <a class="replay-link" href="#"><i class="fa fa-reply"></i></a>
                                          </div>
                                      </div>
                                  </li>
                                  
                                  <li class="left right-sections-cht clearfix">
                                      <div class="chat-body clearfix">
                                         <div class="left-img-chat"><img src="{{url('/')}}/images/admin/client-img-1.jpg" alt="" /></div>
                                          <div class="right-avtr">
                                              <div class="header header-right">
                                                  <strong class="primary-font ">Tracy S. Shadwick </strong>
                                              </div>
                                              <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias quas repellat labore culpa, excepturi natus quis necessitatibus, voluptates. Debitis magnam veritatis qui, ipsum aperiam saepe consequatur facilis reprehenderit possimus accusamus harum. Numquam, impedit, tempore!</p>
                                                  <small class="text-muted label label-danger"><i class="icon-time"></i>56 Minutes ago  </small>
                                                  <a class="replay-link" href="#"><i class="fa fa-reply"></i></a>
                                          </div>
                                      </div>
                                  </li>
                                  <li class="left clearfix">
                                      <div class="chat-body clearfix">
                                         <div class="left-img-chat"><img src="{{url('/')}}/images/admin/client-staff.jpg" alt="" /></div>
                                          <div class="right-avtr">
                                              <div class="header header-right">
                                                  <strong class="primary-font "> Paul M. Stevens </strong>
                                              </div>
                                              <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias quas repellat labore culpa, excepturi natus quis necessitatibus, voluptates. Debitis magnam veritatis qui, ipsum aperiam saepe consequatur facilis reprehenderit possimus accusamus harum. Numquam, impedit, tempore!</p>
                                                  <small class="text-muted label label-danger"><i class="icon-time"></i>56 Minutes ago  </small>
                                                  <a class="replay-link" href="#"><i class="fa fa-reply"></i></a>
                                          </div>
                                      </div>
                                  </li>
                                  
                                  <li class="left right-sections-cht clearfix">
                                      <div class="chat-body clearfix">
                                         <div class="left-img-chat"><img src="{{url('/')}}/images/admin/client-img-1.jpg" alt="" /></div>
                                          <div class="right-avtr">
                                              <div class="header header-right">
                                                  <strong class="primary-font ">Tracy S. Shadwick </strong>
                                              </div>
                                              <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias quas repellat labore culpa, excepturi natus quis necessitatibus, voluptates. Debitis magnam veritatis qui, ipsum aperiam saepe consequatur facilis reprehenderit possimus accusamus harum. Numquam, impedit, tempore!</p>
                                                  <small class="text-muted label label-danger"><i class="icon-time"></i>56 Minutes ago  </small>
                                                  <a class="replay-link" href="#"><i class="fa fa-reply"></i></a>
                                          </div>
                                      </div>
                                  </li>
                                  <li class="left clearfix">
                                      <div class="chat-body clearfix">
                                         <div class="left-img-chat"><img src="{{url('/')}}/images/admin/client-staff.jpg" alt="" /></div>
                                          <div class="right-avtr">
                                              <div class="header header-right">
                                                  <strong class="primary-font "> Paul M. Stevens </strong>
                                              </div>
                                              <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias quas repellat labore culpa, excepturi natus quis necessitatibus, voluptates. Debitis magnam veritatis qui, ipsum aperiam saepe consequatur facilis reprehenderit possimus accusamus harum. Numquam, impedit, tempore!</p>
                                                  <small class="text-muted label label-danger"><i class="icon-time"></i>56 Minutes ago  </small>
                                                  <a class="replay-link" href="#"><i class="fa fa-reply"></i></a>
                                          </div>
                                      </div>
                                  </li>
                                  
                                  <li class="left right-sections-cht clearfix">
                                      <div class="chat-body clearfix">
                                         <div class="left-img-chat"><img src="{{url('/')}}/images/admin/client-img-1.jpg" alt="" /></div>
                                          <div class="right-avtr">
                                              <div class="header header-right">
                                                  <strong class="primary-font ">Tracy S. Shadwick </strong>
                                              </div>
                                              <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias quas repellat labore culpa, excepturi natus quis necessitatibus, voluptates. Debitis magnam veritatis qui, ipsum aperiam saepe consequatur facilis reprehenderit possimus accusamus harum. Numquam, impedit, tempore!</p>
                                                  <small class="text-muted label label-danger"><i class="icon-time"></i>56 Minutes ago  </small>
                                                  <a class="replay-link" href="#"><i class="fa fa-reply"></i></a>
                                          </div>
                                      </div>
                                  </li>
                                  
                                 
                          </ul>
                      </div>
                </div>
            </div>

            <div class="col-sm-12 col-md-6 col-lg-6">
                <div class="chat-panel panel panel-danger panel-mst-rd">
                    <div class="panel-heading">
                        <div class="panel-title-box">
                            Notifications Alerts Panel
                            <div class="clearfix"></div>
                        </div>
                    </div>

                    <div class="notification-sectn">
                        <ul>
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
            <script src="{{ url('/') }}/js/admin/ajax_loader.js"></script>  
        </div>
        <!-- END Main Content -->
    
@stop